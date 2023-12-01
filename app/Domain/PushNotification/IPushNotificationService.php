<?php

namespace App\Domain\PushNotification;

use App\Models\User;
use App\Models\Device;
use Illuminate\Database\Eloquent\Collection;

interface IPushNotificationService
{
    public const ANDROID = 'Android';
    public const IOS = 'iOS';
    public function getEndpoint(string $endpointId);
    public function updateEndpoint(
        Device $device,
        string $address,
        string $channelType
    );
    public function putEvents(string $eventsKey);
    public function getAddressList(
        array $addressList,
        string $title,
        string $message
    );
    public function sendPush(
        string $title,
        string $message,
        array $deviceList,
        string $pushUrl
    );
    public function allowNotificationTime();
}
