<?php

namespace App\Domain\PushNotification;

use Illuminate\Database\Eloquent\Collection;
use Aws\Pinpoint\PinpointClient;
use Aws\Exception\AwsException;
use App\Domain\PushNotification\IPushNotificationService;
use App\Domain\PushNotification\AwsData;
use App\Models\User;
use App\Models\Device;
use DateTime;
use Illuminate\Support\Facades\Log;
use Throwable;

class AwsPinpointService implements IPushNotificationService
{
    // Document https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-pinpoint-2016-12-01.html

    // Pinpoint アプリケーションID
    private $appId;
    private $pushStartTime = '08:00:00';
    private $pushEndTime = '20:00:00';

    public function __construct()
    {
        $this->appId = config('app.aws_pinpoint_id');
    }
    /**
     * Pinpointのエンドポイント情報を取得
     *
     * @param string $endpointId エンドポイントID
     * @return array エンドポイント情報
     */
    public function getEndpoint(string $endpointId)
    {
        $client = $this->getClient();
        try {
            $result = $client->getUserEndpoints([
                'ApplicationId' => $this->appId,
                'UserId' => $endpointId,
            ]);
            return new AwsData(200, $result['EndpointsResponse']);
        } catch (AwsException $th) {
            return $this->getAwsExceptionData($th, $endpointId);
        } catch (Throwable $th) {
            return new AwsData(500, $th->getMessage());
        }
    }

    /**
     * Pinpointのエンドポイント更新
     *
     * @param Device $device ユーザーの端末(ブラウザ)
     * @param string $address 端末のユニークID
     * @param string $channelType GCM: FCM(Firebase Cloud Messaging)
     * @return AwsData
     */
    public function updateEndpoint(
        Device $device,
        string $address,
        string $channelType
    ) {
        // 入力値設定
        $user = $device->user;
        $divisionCode = $user->division_cd;
        $endpointId = $device->createEndpointId($address); // もう少し検討　DBにカラム追加するか
        // $baseCode = $user->base_cd;
        // $baseName = $user->base_cd;
        $orgName = $user->Organization->first()->organization_name;
        $now = date('Y-m-d H:i:s');
        $client = $this->getClient();
        try {
            $result = $client->updateUserEndpoint([
                'ApplicationId' => $this->appId,
                'EndpointId' => $endpointId,
                'EndpointRequest' => [
                    'Address' => $address,
                    'Attributes' => [
                        'divisionCode' => [$divisionCode],
                        'divisionName' => [$orgName],
                        'lastUpdated' => [$now],
                        // 'base_cd' => [$user->base_cd],
                    ],
                    'ChannelType' => $channelType,
                    'Demographic' => [
                        'Timezone' => 'Asia/Tokyo',
                    ],
                    'EndpointStatus' => 'ACTIVE',
                    // 'Location' => [],
                    // 'Metrics' => [1.1, 2.1], // TODO: 確認
                    'OptOut' => 'NONE',
                    'User' => [
                        'UserAttributes' => [
                            'divisionCode' => [$divisionCode],
                            'divisionName' => [$orgName],
                            'lastUpdated' => [$now],
                            // 'base_cd' => [$user->base_cd],
                        ],
                        'UserId' => $endpointId,
                    ],
                ],
            ]);
            return new AwsData(200, $result);
        } catch (AwsException $th) {
            Log::error($th);
            return $this->getAwsExceptionData($th, $endpointId);
        } catch (Throwable $th) {
            Log::error($th);
            return new AwsData(500, $th->getMessage());
        }
    }

    /**
     * Pinpointにイベントを通知
     *
     * @param string $eventsKey イベントのユニークキー
     * @return void
     */
    public function putEvents(string $eventsKey)
    {
        $client = $this->getClient();
        $events = [];
        // ユーザーの配列作成
        $now = new DateTime();
        $timestamp = $now->format(DateTime::ATOM);

        // イベントの配列作成
        foreach (Device::all() as $device) {
            $events[$device->token] = [
                'EventType' => $eventsKey,
                'Timestamp' => $timestamp,
                // 'Attributes' => [
                //     'eventatr' => '5555',
                //     'gorira' => 'rappa',
                // ],
            ];
        }
        $requestId = 'uniqueID01'; // NOTE: リクエストIDは任意の値でいい
        try {
            $result = $client->putEvents([
                'ApplicationId' => $this->appId,
                'EventsRequest' => [
                    'BatchItem' => [
                        $requestId => [
                            'Endpoint' => [],
                            'Events' => $events,
                        ],
                    ],
                ],
            ]);
            return new AwsData(200, $result['EventsResponse']);
        } catch (AwsException $th) {
            return $this->getAwsExceptionData($th, $requestId);
        } catch (Throwable $th) {
            return new AwsData(500, $th->getMessage());
        }
    }

    /**
     * PinpointでWebプッシュ送信
     *
     * @param string $title プッシュ通知のタイトル
     * @param string $message プッシュ通知のメッセージ
     * @param array $deviceList プッシュ通知対象端末
     * @param string $pushUrl プッシュ通知クリック後のリダイレクトURL
     * @return void
     */
    public function sendPush(
        string $title,
        string $message,
        array $deviceList,
        string $pushUrl
    ) {
        if (count($deviceList) < 1) {
            return new AwsData(200, '');
        }
        $client = $this->getClient();
        $msg = [
            'ApplicationId' => $this->appId,
            'MessageRequest' => [
                'Addresses' => $deviceList,
                'MessageConfiguration' => [
                    'DefaultPushNotificationMessage' => [
                        'Action' => 'URL',
                        'Body' => $message,
                        'Title' => $title,
                        // 'SlientPush' => true,
                        'Url' => $pushUrl,
                    ],
                ],
            ],
        ];
        try {
            $result = $client->sendMessages($msg);
            return new AwsData(200, $result['MessageResponse']);
        } catch (AwsException $th) {
            return $this->getAwsExceptionData($th);
        } catch (Throwable $th) {
            return new AwsData(500, $th->getMessage());
        }
    }
    // TODO: 必要なければ削除 or コメントアウト
    public function sendEmail()
    {
        $client = $this->getClient();
        $deviceList = [
            'oyamada_k@thinkone.jp' => [
                'ChannelType' => 'EMAIL',
            ],
            'oyamada.k.thinkone@gmail.com' => [
                'ChannelType' => 'EMAIL',
            ],
        ];
        // $deviceList = [];
        // 該当端末のAddressesデータ作成
        // foreach (Device::all() as $device) {
        //     $deviceList[$device->token] = [
        //         'ChannelType' => 'GCM', // NOTE: iOSブラウザの通知対応次第では下記のコメントアウトのロジックを適用する
        //         // 'ChannelType' => 'EMAIL',
        //         // 'ChannelType' => $device->type == 'iOS' ? 'APNS' : 'GCM',
        //     ];
        // }
        $msg = [
            'ApplicationId' => $this->appId,
            'MessageRequest' => [
                'Addresses' => $deviceList,
                'MessageConfiguration' => [
                    'EmailMessage' => [
                        'Body' => 'test message',
                        'SimpleEmail' => [
                            'HtmlPart' => [
                                'Charset' => 'UTF-8',
                                'Data' => '<html>html message</html>',
                            ],
                            'Subject' => [
                                'Charset' => 'UTF-8',
                                'Data' => 'title data pinpoint',
                            ],
                            'TextPart' => [
                                'Charset' => 'UTF-8',
                                'Data' => 'text part',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        try {
            $result = $client->sendMessages($msg);
            return new AwsData(200, $result['MessageResponse']);
        } catch (AwsException $th) {
            return $this->getAwsExceptionData($th);
        } catch (Throwable $th) {
            return new AwsData(500, $th->getMessage());
        }
    }

    /**
     * 通知対象となる端末のリストを取得
     *
     * @param array $addressList 端末リスト
     * @param string $title プッシュ通知のタイトル
     * @param string $message プッシュ通知のメッセージ
     * @return array Pinpoint用のエンドポイントリスト
     */
    public function getAddressList(
        array $addressList,
        string $title,
        string $message
    ) {
        $deviceList = [];
        foreach ($addressList as $address) {
            $deviceList[$address] = [
                'BodyOverride' => $message,
                'ChannelType' => 'GCM', // NOTE: iOSブラウザの通知対応次第では下記のコメントアウトのロジックを適用する
                // 'ChannelType' => $device->type == 'iOS' ? 'APNS' : 'GCM',
                'TitleOverride' => $title,
            ];
        }
        return $deviceList;
    }

    /**
     * 8:00 - 20:00の間は通知を送信しない
     *
     * @return void
     */
    public function allowNotificationTime()
    {
        $now = date('H:i:s');
        if (
            strtotime($now) > strtotime($this->pushStartTime) &&
            strtotime($now) < strtotime($this->pushEndTime)
        ) {
            return true;
        } else {
            return false;
        }
    }

    /** private */

    /**
     * Pinpointクライアント取得
     *
     * @return PinpointClient
     */
    private function getClient()
    {
        $settings = [
            'region' => config('app.aws_pinpoint_region'),
            'version' => '2016-12-01', // パッケージのアップデートに合わせて更新すること
        ];
        return new PinpointClient($settings);
    }

    /**
     * AwsExceptionからデータ取得
     *
     * @param AwsException $th
     * @return array AwsException情報 status: int, result: any
     */
    private function getAwsExceptionData(AwsException $th, string $id = '')
    {
        $response = $th->getResponse();
        $status = $response->getStatusCode();
        $message = $response->getReasonPhrase();
        return new AwsData($status, $message, $id);
    }
}
