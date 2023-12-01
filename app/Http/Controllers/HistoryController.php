<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DiaryBrowsHistory;
use Illuminate\Pagination\LengthAwarePaginator;

class HistoryController extends Controller
{
    public function regist(Request $request)
    {
        $params = $request->only('diary_ids');
        $diaryIds = $params['diary_ids'];

        if (
            $diaryIds &&
            request()
                ->session()
                ->has('ha_time_session')
        ) {
            $haTimeSession = request()
                ->session()
                ->get('ha_time_session');
            $humanId = $haTimeSession['human_id'];
            $humanName = $haTimeSession['name'];
            foreach ($diaryIds as $diaryId) {
                $history = DiaryBrowsHistory::where([
                    'diary_id' => $diaryId,
                ])->first();
                if (!$history) {
                    $history = new DiaryBrowsHistory();
                    $history->diary_id = $diaryId;
                }
                $historyJson = $history->history ?? [];
                $historyKey = array_search(
                    $humanId,
                    array_column($historyJson, 'human_id')
                );
                if ($historyKey === false) {
                    // アカウントの閲覧履歴がない
                    // 閲覧履歴を追加
                    array_unshift($historyJson, [
                        'human_id' => $humanId,
                        'name' => $humanName,
                        'browsed_at' => now()->format('Y/m/d H:i:s'),
                    ]);
                    $history->history = $historyJson;
                    $history->save();
                }
            }
        }
    }

    private function getHistory(Request $request, $perPage)
    {
        $diaryId = $request->query('diary_id');
        $history = DiaryBrowsHistory::where(['diary_id' => $diaryId])->first();
        $historyArr = [];
        if ($history) {
            $historyArr = $history->history;
        }

        $history = collect($historyArr);
        $history = new LengthAwarePaginator(
            $history->forPage($request->page, $perPage),
            count($history),
            $perPage,
            $request->page,
            ['path' => $request->url()]
        );

        $human_ids = [];
        foreach ($history as $d) {
            $human_ids[] = $d['human_id'];
        }

        if (count($human_ids) > 0) {
            $users = User::whereIn('human_id', $human_ids)->get();
            $historyCnt = count($history);
            $idxBase = $history->perPage() * ($history->currentPage() - 1);
            foreach ($users as $user) {
                for ($i = 0; $i < $historyCnt; $i++) {
                    $k = $idxBase + $i;
                    if ($user->human_id == $history[$k]['human_id']) {
                        $tmp = $history[$k];
                        $tmp['name'] = $user->user_name;
                        $history[$k] = $tmp;
                    }
                }
            }
        }
        return $history;
    }

    public function show(Request $request)
    {
        $history = $this->getHistory($request, 5);
        return response()->json($history);
    }

    public function spShow(Request $request)
    {
        $history = $this->getHistory($request, 20);
        return response()->json($history);
    }
}
