<?php

namespace App\Domain\StatisticsCsv;

use DateTime;
/**
 * 統計用CSVにて利用する日付モデル
 */
class StatisticsDate
{
    private string $filter;
    public string $year;
    public string $month;
    public string $day;

    function __construct(string $dateStr, string $filter)
    {
        $this->filter = $filter;
        $date = new DateTime($dateStr);
        $this->year = $date->format('Y');
        $this->month = $filter != 'year' ? $date->format('m') : '';
        $this->day = $filter == 'day' ? $date->format('d') : '';
    }

    /**
     * Barチャート表示用の日付ラベルを返却
     *
     * @return string Barチャート表示用の日付ラベル
     */
    public function getDateString(): string
    {
        if ($this->filter == 'year') {
            return "{$this->year}年";
        } elseif ($this->filter == 'month') {
            return "{$this->year}年{$this->month}月";
        } else {
            return "{$this->year}年{$this->month}月{$this->day}日";
        }
    }
}
