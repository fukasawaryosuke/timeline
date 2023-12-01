<?php

namespace App\Domain\PushNotification;

use DateTime;
/**
 * AWSサービスの結果のモデル
 */
class AwsData
{
    public int $status;
    public $result;
    public string $id;

    function __construct(int $status, $result, string $id = '')
    {
        $this->status = $status;
        $this->result = $result;
        $this->id = $id;
    }

    public function isError()
    {
        return $this->status >= 400;
    }
}
