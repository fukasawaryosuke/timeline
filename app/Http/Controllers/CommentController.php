<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Comment;
use App\Models\CommentNotifications;
use App\Models\Diary;
use App\Models\InHouseSystem;
use App\Models\User;
use Carbon\Carbon;
use App\Domain\StatisticsCsv\IStatisticsCsvService;
use App\Domain\StatisticsCsv\StatisticsDate;
use App\Domain\PushNotification\IPushNotificationService;
use Illuminate\Support\Facades\Log;
use App\Domain\User\UserService;

class CommentController extends Controller
{
    public function create(
        Request $request,
        IPushNotificationService $pushService
    ) {
        $comment = $request->input('comment');
        $diary_id = $request->input('diary_id');
        $humanId = request()->getHumanObject()->human_id;
        $user = User::where('human_id', $humanId)->first();
        $mention_users = $request->input('mentions');
        $pushUrl = config('app.url') . '/sphome?push=' . $diary_id;
        // Comment::create(['created_by' => $user_id , 'diary_id' => $diary_id , 'comment' => $comment]);

        $h_id_list = [];
        try {
            DB::beginTransaction();
            $create_comment = Comment::create([
                'created_by' => $user->id,
                'diary_id' => $diary_id,
                'comment' => $comment,
            ]);
            // TODO: 直でユーザー取得方法がわかったら修正 投稿者とメンションされたユーザー取得
            $d_user_id = Diary::find($diary_id)->created_by;
            $d_human_id = User::find($d_user_id)->human_id;
            // $h_id_list = $request->input('h_id_list'); // TODO: リスト取得方法が決まり次第修正

            foreach ($mention_users as $mention_user) {
                array_push($h_id_list, $mention_user['human_id']);
            }

            // 自分の日報に自分でコメントした場合は通知しない
            // メンションに含まれている場合もメンション側で通知するので
            // コメントとしては通知しない
            if ($d_human_id != $humanId && !in_array($d_human_id, $h_id_list)) {
                // コメントの通知
                CommentNotifications::insert([
                    'user_id' => $d_user_id,
                    'comment_id' => $create_comment->id,
                    'notification_type' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // コメントされたユーザーに通知
                if ($pushService->allowNotificationTime()) {
                    $title = 'タイムライン';
                    $message = "{$user->user_name}さんからコメントされました。";
                    $addressList = [];
                    foreach (User::find($d_user_id)->Device as $device) {
                        if ($device->enableToken()) {
                            array_push($addressList, $device->token);
                        }
                    }
                    $deviceList = $pushService->getAddressList(
                        $addressList,
                        $title,
                        $message
                    );
                    $result = $pushService->sendPush(
                        $title,
                        $message,
                        $deviceList,
                        $pushUrl
                    );
                    if ($result->isError()) {
                        // DB処理は止めたくないのでログ出力のみ
                        Log::warning($result->result);
                    }
                }
            }

            $all_user = User::whereIn('human_id', $h_id_list)->get();
            foreach ($all_user as $u) {
                // メンションの通知
                CommentNotifications::insert([
                    'user_id' => $u->id,
                    'comment_id' => $create_comment->id,
                    'notification_type' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            DB::commit();
            // メンションされたユーザーに通知
            if ($pushService->allowNotificationTime()) {
                $mention_address = [];
                $title = 'タイムライン';
                $message = "{$user->user_name}さんからメンションされました。";
                $addressList = [];
                foreach ($all_user as $u) {
                    $m_deviceList = $u->Device;
                    foreach ($m_deviceList as $m_device) {
                        if ($m_device->enableToken()) {
                            array_push($mention_address, $m_device->token);
                        }
                    }
                }
                $mentionDeviceList = $pushService->getAddressList(
                    $mention_address,
                    $title,
                    $message
                );
                $result = $pushService->sendPush(
                    $title,
                    $message,
                    $mentionDeviceList,
                    $pushUrl
                );
                if ($result->isError()) {
                    // DB処理は止めたくないのでログ出力のみ
                    Log::warning($result->result);
                }
            }

            $res_comment = Comment::with('user.organization', 'mensions.user')
                ->where('id', $create_comment->id)
                ->first();

            return response()->json([
                'success' => 1,
                'notification_users' => $h_id_list,
                'received_comment' => $res_comment,
            ]);
        } catch (\Throwable $th) {
            // TODO: 後で考える
            $aaa = 1;
        }

        return response()->json([
            'success' => 0,
        ]);
        // return response()->json($request->session()->get('ha_time_session')['name']);
    }

    public function show(Request $request)
    {
        $comment = Comment::select('comment', 'diary_id');
        $comment = $comment->latest('created_at')->get();

        return $comment;
    }

    public function showNotice(Request $request)
    {
        $userCompanyCode = UserService::getUserCompanyCode($request);

        $humanId = request()->getHumanObject()->human_id;
        $user_id = User::where('human_id', $humanId)->value('id');
        $notice = CommentNotifications::where('user_id', $user_id)
            ->where('created_at', '>=', Carbon::now()->subDays(2))
            ->with([
                'comment.created_by',
                'comment.diary',
                'user',
                'comment.diary.in_house_system',
            ])
            ->whereHas('comment.diary.in_house_system', function ($query) use ($userCompanyCode) {
                $query->where('company_code', $userCompanyCode);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return $notice;
    }

    public function csvDownload(
        Request $request,
        IStatisticsCsvService $service
    ) {
        // 一行目セット
        $csvList = [
            [
                'アカウント名',
                '制度名',
                '投稿数',
                'コメント送信数',
                'コメント受信数',
                '集計年',
                '集計月',
                '集計日',
            ],
        ];

        $startAt = $request->get('startAt');
        $endAt = $request->get('endAt');
        $filter = $request->get('filter');
        $user_id = $request->get('user_id');

        $user_datas = User::query();
        if ($user_id) {
            $user_datas = $user_datas->where('id', $user_id);
        }

        // 指定期間のユーザーデータ取得
        $user_datas = $user_datas
            ->with([
                'Comment.diary' => function ($q) use ($startAt, $endAt) {
                    $q->whereBetween('created_at', [
                        $startAt,
                        $endAt,
                    ])->whereNotNull('created_at');
                },
            ])
            ->with([
                'Diary.comment' => function ($q) use ($startAt, $endAt) {
                    $q->whereBetween('created_at', [
                        $startAt,
                        $endAt,
                    ])->whereNotNull('created_at');
                },
            ])
            ->get();

        // 日付期間リスト作成
        $date_add = $service->getDateValue($filter);
        $date_list = $service->getBetweenDate($startAt, $endAt, $date_add);

        // 投稿数、コメントした回数、コメントされた回数をユーザーかつ制度かつ指定の日付ごとに集計
        foreach ($user_datas as $user_data) {
            foreach (InHouseSystem::where('release_status', 1)->get() as $ihs) {
                foreach ($date_list as $dl) {
                    $target_date = new StatisticsDate($dl, $filter);
                    $diary = $user_data->Diary->where(
                        'in_house_system_id',
                        $ihs->id
                    );
                    $filterDiary = $service->filterDateCollection(
                        $diary,
                        $filter,
                        $target_date
                    );
                    // 投稿数
                    $ihsDiaryCount = count($filterDiary);
                    // コメントした回数
                    $send_comment_count = 0;
                    if (count($user_data->Comment) > 0) {
                        $send_comment = $user_data->comment->filter(function (
                            $row
                        ) use ($ihs) {
                            return $row->diary->in_house_system_id == $ihs->id;
                        });
                        $send_comment = $service->filterDateCollection(
                            $send_comment,
                            $filter,
                            $target_date
                        );
                        $send_comment_count = count($send_comment);
                    }
                    // コメントされた回数
                    $receive_comment_count = 0;
                    if (count($filterDiary) > 0) {
                        foreach ($filterDiary as $d) {
                            $dc = $d->comment;
                            $receive_comment = $service->filterDateCollection(
                                $dc,
                                $filter,
                                $target_date
                            );
                            $receive_comment_count += count($receive_comment);
                        }
                    }
                    $datas = [
                        $user_data->user_name, // アカウント名
                        $ihs->system_name, // 制度名
                        $ihsDiaryCount, // 制度ごとの投稿数
                        $send_comment_count, // 制度ごとのコメントをした回数
                        $receive_comment_count, // 制度ごとのコメントされた回数
                        "{$target_date->year}", // 集計年
                        "{$target_date->month}", // 集計月
                        "{$target_date->day}", // 集計日
                    ];
                    array_push($csvList, $datas);
                }
            }
        }

        return $service->writeCsv($csvList);
    }
}
