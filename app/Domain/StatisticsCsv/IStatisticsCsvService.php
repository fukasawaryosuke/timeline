<?php

namespace App\Domain\StatisticsCsv;

use App\Domain\StatisticsCsv\StatisticsDate;

interface IStatisticsCsvService
{
    public function getDateValue(string $filter): string;
    public function getBetweenDate(
        string $startAt,
        string $endAt,
        string $dateAddStr
    );
    public function filterDateCollection(
        $collection,
        string $dateKey,
        StatisticsDate $date
    );
    public function writeCsv(array $csvList);
}
