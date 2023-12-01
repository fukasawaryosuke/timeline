<?php

namespace App\Domain\StatisticsCsv;

use App\Domain\StatisticsCsv\IStatisticsCsvService;
use Carbon\Carbon;
use DateTime;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Domain\StatisticsCsv\StatisticsDate;

class StatisticsCsvService implements IStatisticsCsvService
{
    /**
     * 渡された値によりDateTime型の定型文を返却
     *
     * @param string $filter 'year' or 'month' or 'day'
     * @return string DateTime型の定型文
     */
    public function getDateValue(string $filter): string
    {
        if ($filter == 'year') {
            return '+1 years';
        } elseif ($filter == 'month') {
            return '+1 months';
        } else {
            return '+1 day';
        }
    }
    /**
     * 値により集計期間の配列を作成
     *
     * @param string $startAt 集計開始日
     * @param string $endAt 集計終了日
     * @param string $dateAddStr DateTime型の増分する期間の定型文 '+1 years' or '+1 months' or '+1 day'
     * @return void
     */
    public function getBetweenDate(
        string $startAt,
        string $endAt,
        string $dateAddStr
    ) {
        // 日付期間リスト
        $formatStr = '';
        if ($dateAddStr == '+1 years') {
            $formatStr = 'Y';
        } elseif ($dateAddStr == '+1 months') {
            $formatStr = 'Ym';
        } else {
            $formatStr = 'Ymd';
        }
        $date_list = [];
        $start_date = new DateTime($startAt);
        $end_date = new DateTime($endAt);
        do {
            array_push($date_list, $start_date->format('Y-m-d'));
            $start_date->modify($dateAddStr);
        } while (
            intval($start_date->format($formatStr)) <=
            intval($end_date->format($formatStr))
        );
        return $date_list;
    }
    /**
     * ORMで取得したCollectionを対象機関のみにフィルタリングする
     *
     * @param [type] $collection
     * @param string $dateKey フィルターする単位 'year' or 'month' or 'day'
     * @param StatisticsDate $date フィルター化に利用する日付データ
     * @return void
     */
    public function filterDateCollection(
        $collection,
        string $dateKey,
        StatisticsDate $date
    ) {
        if ($dateKey == 'year') {
            return $collection->filter(function ($q) use ($date) {
                return Carbon::parse($q->created_at)->year == $date->year;
            });
        } elseif ($dateKey == 'month') {
            return $collection->filter(function ($q) use ($date) {
                return Carbon::parse($q->created_at)->year == $date->year &&
                    Carbon::parse($q->created_at)->month == $date->month;
            });
        } else {
            return $collection->filter(function ($q) use ($date) {
                return Carbon::parse($q->created_at)->year == $date->year &&
                    Carbon::parse($q->created_at)->month == $date->month &&
                    Carbon::parse($q->created_at)->day == $date->day;
            });
        }
    }
    /**
     * CSVを書き出して返却
     *
     * @param array $csvList CSVデータ
     * @return void CSVレスポンス
     */
    public function writeCsv($csvList)
    {
        // CSV出力
        $response = new StreamedResponse(function () use ($csvList) {
            $stream = fopen('php://output', 'w');
            //　文字化け回避
            stream_filter_prepend($stream, 'convert.iconv.utf-8/cp932');
            // CSV書き出し
            foreach ($csvList as $value) {
                fputcsv($stream, $value);
            }
            fclose($stream);
        });
        $now = date('YmdHis');
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set(
            'Content-Disposition',
            "attachment; filename={$now}.csv"
        );

        return $response;
    }
}
