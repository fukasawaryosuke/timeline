<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Diary;
use App\Models\InHouseSystem;
use App\Models\User;
use App\Models\DiaryNotification;
use App\Models\Device;
use Facade\FlareClient\Http\Exceptions\NotFound;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use App\Domain\StatisticsCsv\IStatisticsCsvService;
use App\Domain\StatisticsCsv\StatisticsDate;
use App\Domain\MoviePlayer\IMoviePlayerService;
use App\Domain\FileStorage\IFileStorageService;
use App\Models\Organization;
use App\Domain\PushNotification\IPushNotificationService;
use App\Models\Draft;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Domain\User\UserService;

class DairyController extends Controller
{
    //
    public function newDairyCreate(
        Request $request,
        IPushNotificationService $pushService
    ) {
        $contents = $request->input('diaryTemplate');
        $requierNotice = $request->input('requierNotice');
        $humanId = request()->getHumanObject()->human_id;
        $user = User::where('human_id', $humanId)->first();
        $ihsId = $request->ihs_id;
        $ihs_name = InHouseSystem::where('id', $ihsId)->value('system_name');
        $diary = Diary::create([
            'diary' => $contents,
            'created_by' => $user->id,
            'in_house_system_id' => $ihsId,
            'division_cd' => $user->division_cd,
        ]);

        $bool = Draft::where([
            ['user_id', $user->id],
            ['in_house_systems_id', $ihsId],
        ])->exists();

        if ($bool) {
            Draft::where([
                ['user_id', $user->id],
                ['in_house_systems_id', $ihsId],
            ])->update([
                'draft' => null,
                'in_house_systems_id' => null,
            ]);
        }
        // diary_notificationsに追加するかrequierNoticeで判定
        if ($requierNotice) {
            DiaryNotification::create([
                'diary_id' => $diary->id,
                'user_id' => $user->id,
            ]);
            // プッシュ通知送信
            if ($pushService->allowNotificationTime()) {
                $title = 'タイムライン';
                $message = "{$user->user_name}さんが{$ihs_name}で必読投稿しました。";

                // 限定した通知先
                $needsNoticeFilter = false;
                $noticeUserIds = [];
                $forPushNotice = DiaryNotification::with('diary.in_house_system.notification_setting')
                    ->where('diary_id', $diary->id)
                    ->first();
                $noticeSetting = $forPushNotice->diary->in_house_system->notification_setting;
                if (count($noticeSetting) > 0) {
                    $needsNoticeFilter = true;
                    // 通知の設定で指定されている組織に対して
                    // 配下の組織コードを再起的に取得する
                    $organization = new Organization();
                    $noticeDivisionCodes = $organization->getDivisionCodesWithUnderOrganizations($noticeSetting->pluck('division_cd')->toArray());
                    Log::debug('通知設定の組織コード', $noticeDivisionCodes);
                    $noticeUserIds = User::whereIn('division_cd', $noticeDivisionCodes)->pluck('id')->toArray();
                    Log::debug('組織内のユーザ', $noticeUserIds);
                }

                $deviceTokenList = [];
                if ($needsNoticeFilter) {
                    // 組織は指定したが該当者がいない場合がある
                    if (!empty($noticeUserIds)) {
                        $deviceTokenList = Device::tokenWithUserIds($noticeUserIds);
                    }
                    Log::debug('取得したトークン', $deviceTokenList);
                } else {
                    $userCompanyCode = UserService::getUserCompanyCode($request);
                    $deviceTokenList = Device::tokenWithCompanyCd($userCompanyCode);
                }

                $addressList = $pushService->getAddressList(
                    $deviceTokenList,
                    $title,
                    $message
                );
                $pushUrl = config('app.url') . '/sphome?push=' . $diary->id;
                $result = $pushService->sendPush(
                    $title,
                    $message,
                    $addressList,
                    $pushUrl
                );
                return $this->successJsonResponse(200, '', $result);
            }
        }
    }

    public function createS3image()
    {
        $file = request()->file('files');

        if (empty($file)) {
            return 'isEmpty';
        }

        $path = Storage::disk('s3')->putFile(
            'dairy/images',
            $file[0],
            'public'
        );

        $image_path = Storage::disk('s3')->url($path);

        return $image_path;
    }

    public function show(Request $request)
    {
        $userCompanyCode = UserService::getUserCompanyCode($request);

        // 制度があれば制度でフィルタリング
        $ihs = $request->input('ihs');
        $dairy = Diary::with([
            'in_house_system',
            'comment.user.organization',
            'comment.mensions.user',
        ])
            ->select(
                'diaries.id',
                'diary',
                'user_name',
                'human_id',
                'diaries.created_at',
                'diary_brows_histories.history',
                'diaries.created_by',
                'diaries.in_house_system_id',
                'diary_notifications.diary_id as must_read'
            )
            ->join('users', 'diaries.created_by', '=', 'users.id')
            ->leftjoin(
                'diary_brows_histories',
                'diaries.id',
                '=',
                'diary_brows_histories.diary_id'
            )
            ->join(
                'in_house_systems',
                'diaries.in_house_system_id',
                '=',
                'in_house_systems.id'
            )
            ->leftjoin(
                'diary_notifications',
                'diaries.id',
                '=',
                'diary_notifications.diary_id'
            )
            ->where('in_house_systems.company_code', $userCompanyCode)
            ->where('in_house_systems.release_status', true);

        // $dairy = $dairy
        //     ->join(
        //         'in_house_systems',
        //         'diaries.in_house_system_id',
        //         '=',
        //         'in_house_systems.id'
        //     )
        //     ->where('in_house_systems.id', 1);
        if ($request->has('created_by')) {
            $dairy = $dairy->where(
                'diaries.created_by',
                $request->get('created_by')
            );
        }
        if ($request->has('division_cd')) {
            // 5分日報orヒューマンアカデミー株式会社以外の場合はフィルたリングする
            if (
                $request->has('ihs') &&
                InHouseSystem::isFiveMinutesReport(intval($request->get('ihs')))
                || $userCompanyCode !== config('app.human_academy_company_code')
            ) {
                $orgz = new Organization();
                $withUnderDivisions = $orgz->getDivisionCodesWithUnderOrganizations(
                    $request->get('division_cd')
                );
                $dairy = $dairy->whereIn(
                    'diaries.division_cd',
                    $withUnderDivisions
                );
            }
        }
        if ($request->has('search_date')) {
            $search = $request->get('search_date');
            // $searchFrom = date("Y-m-d 00:00:00", strtotime($search));
            $searchTo = date('Y-m-d 23:59:59', strtotime($search));
            $dairy = $dairy->where('diaries.created_at', '<=', $searchTo);
        }
        if ($request->has('ihs')) {
            $ihs = $request->get('ihs');
            $dairy = $dairy->where('diaries.in_house_system_id', $ihs);
        }
        $dairy = $dairy->latest('diaries.created_at');
        // 10件だと1ページに収まってしまう場合がある
        $dairy = $dairy->paginate(30);
        // $dairy->getCollection()->each(function($d){
        //     // $d->comment;
        //     $d->comment->each(function($e){
        //         $user_name = $e->created_by()->value('user_name');
        //         $e['user_name'] = $user_name;
        //         // $e->mensions->each(function($m){
        //         //     $mension_user_name = $m->user()->value('user_name');
        //         //     $m['user_name'] = $mension_user_name;
        //         // });
        //     });
        // });

        return json_encode($dairy);
    }

    public function latest(Request $request)
    {
        $humanId = request()->getHumanObject()->human_id;
        $user = User::where('human_id', $humanId)->first();
        $ihsId = $request->ihs_id;
        $latestDiary = Diary::where('created_by', $user->id)
            ->where('in_house_system_id', intval($ihsId))
            ->latest()
            ->first();
        return response()->json($latestDiary);
    }

    public function detail(Request $request)
    {
        $diary_id = $request->get('diary_id');
        $dairy = Diary::with([
            'in_house_system',
            'comment.user.organization',
            'comment.mensions.user',
        ])
            ->select(
                'diaries.id',
                'diary',
                'user_name',
                'human_id',
                'diaries.created_at',
                'diary_brows_histories.history',
                'diaries.in_house_system_id'
            )
            ->join('users', 'diaries.created_by', '=', 'users.id')
            ->leftjoin(
                'diary_brows_histories',
                'diaries.id',
                '=',
                'diary_brows_histories.diary_id'
            )
            ->find($diary_id);
        return json_encode($dairy);
    }

    public function myDiary(Request $request)
    {
        $diary_id = $request->get('diary_id');
        $humanId = request()->getHumanObject()->human_id;
        $user = User::where('human_id', $humanId)->first();
        $dairy = Diary::with([
            'in_house_system',
            'comment.user.organization',
            'comment.mensions.user',
        ])
            ->select(
                'diaries.id',
                'diary',
                'user_name',
                'diaries.created_at',
                'system_name',
                'description',
                'diaries.in_house_system_id'
            )
            ->join('users', 'diaries.created_by', '=', 'users.id')
            ->where('created_by', $user->id)
            ->join(
                'in_house_systems',
                'diaries.in_house_system_id',
                '=',
                'in_house_systems.id'
            )
            ->find($diary_id);
        return json_encode($dairy);
    }

    public function update(Request $request)
    {
        $contents = $request->input();
        $diary_id = $request->diary_id;
        $humanId = request()->getHumanObject()->human_id;
        $user = User::where('human_id', $humanId)->first();
        $target_diary = Diary::where('id', $diary_id)
            ->where('created_by', $user->id)
            ->first();
        if ($target_diary != null) {
            $target_diary->diary = $contents;
            $result = $target_diary->update();
            if ($result) {
                return response()->json([
                    'status' => 200,
                    'message' => '日報を更新しました。',
                ]);
            } else {
                throw new InternalErrorException('日報の更新に失敗しました。');
            }
        } else {
            throw new NotFound('該当の日報が存在しません。');
        }
    }

    public function delete(
        Request $request,
        IFileStorageService $fService,
        IMoviePlayerService $mService
    ) {
        $diary_id = $request->get('diary_id');
        $humanId = request()->getHumanObject()->human_id;
        $user = User::where('human_id', $humanId)->first();
        $target_diary = Diary::where('id', $diary_id)
            ->where('created_by', $user->id)
            ->first();
        if ($target_diary != null) {
            $result = $target_diary->delete();
            if ($result) {
                // 画像、動画があったら削除する
                foreach ($target_diary->diary['contents'] as $d) {
                    if ($d['contentType'] == 'dairyFixImage' && $d['path']) {
                        $fService->deleteFile($d['path']);
                    } elseif ($d['contentType'] == 'diaryMovie' && $d['path']) {
                        $mService->delete($d['path']);
                    }
                }
                return response()->json([
                    'status' => 200,
                    'message' => '日報を削除しました。',
                ]);
            } else {
                throw new InternalErrorException('日報の削除に失敗しました。');
            }
        } else {
            throw new NotFound('該当の日報が存在しません。');
        }
    }

    public function inHouseSystem(
        Request $request,
        IStatisticsCsvService $service
    ) {
        $ihsId = $request->get('ihsId');
        $startAt = $request->get('startAt');
        $endAt = $request->get('endAt');
        $filter = $request->get('filter');
        $ihs = $this->getIhsData($ihsId, $startAt, $endAt);
        $diaryList = $ihs->diary;
        // 日付期間リスト作成
        $date_add = $service->getDateValue($filter);
        $date_list = $service->getBetweenDate($startAt, $endAt, $date_add);
        $ihsPostList = []; // 制度ごとの投稿数
        $ihsLabelList = []; // 期間ラベル
        foreach ($date_list as $dl) {
            $targetDate = new StatisticsDate($dl, $filter);
            $filterDiary = $service->filterDateCollection(
                $diaryList,
                $filter,
                $targetDate
            );
            array_push($ihsLabelList, $targetDate->getDateString());
            array_push($ihsPostList, count($filterDiary));
        }
        return $this->successJsonResponse(200, '', [
            'labels' => $ihsLabelList,
            'posts' => $ihsPostList,
        ]);
    }

    public function csvDownload(
        Request $request,
        IStatisticsCsvService $service
    ) {
        $csvList = [['制度名', '集計年', '集計月', '集計日', '投稿数']];
        $ihsId = $request->get('ihsId');
        $startAt = $request->get('startAt');
        $endAt = $request->get('endAt');
        $filter = $request->get('filter');
        // 制度別データ取得
        $ihs = $this->getIhsData($ihsId, $startAt, $endAt);
        $diaryList = $ihs->diary;
        // 日付期間リスト作成
        $dateAdd = $service->getDateValue($filter);
        $dateList = $service->getBetweenDate($startAt, $endAt, $dateAdd);

        foreach ($dateList as $dl) {
            $targetDate = new StatisticsDate($dl, $filter);
            $filterDiary = $service->filterDateCollection(
                $diaryList,
                $filter,
                $targetDate
            );
            $datas = [
                $ihs->system_name, // 制度名
                $targetDate->year, // 集計年
                $targetDate->month, // 集計月
                $targetDate->day, // 集計日
                count($filterDiary), // 投稿数
            ];
            array_push($csvList, $datas);
        }

        return $service->writeCsv($csvList);
    }

    private function getIhsData(int $ihsId, string $startAt, string $endAt)
    {
        $ihs = InHouseSystem::where('id', $ihsId)
            ->with([
                'diary' => function ($q) use ($startAt, $endAt) {
                    $q->whereBetween('created_at', [$startAt, $endAt])
                        ->whereNotNull('created_at')
                        ->orderBy('created_at', 'asc');
                },
            ])
            ->first();
        return $ihs;
    }
}
