<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\User;
use App\Domain\PushNotification\IPushNotificationService;
use Illuminate\Support\Facades\Log;

class WebPushController extends Controller
{
    /**
     * Deviceテーブル作成 or 更新
     *
     * @param Request $request
     * @return void
     */
    public function createDevice(Request $request)
    {
        $humanId = request()->getHumanObject()->human_id;
        $expire = request()->getHumanObject()->expire;
        $user = User::where('human_id', $humanId)->first();
        if ($user == null) {
            return $this->successJsonResponse(
                404,
                'ユーザー情報が存在しません。'
            );
        }
        $contents = request()->input();
        $token = $contents['token'];
        $type = $contents['type'];
        $userAgent = $contents['userAgent'];
        $endpointId = "hatUser{$token}"; // もう少し検討　DBにカラム追加するか
        $userDevice = Device::where([
            ['user_id', $user->id],
            ['type', $type],
            ['token', $token],
            ['endpoint_id', $endpointId],
            ['user_agent', $userAgent],
        ])->first();
        // ユーザーが同じタイプの端末情報を保持していなければ作成
        if ($userDevice) {
            $userDevice->expire = date('Y-m-d H:i:s', $expire);
            return $this->updateDeviceTimestamp($userDevice);
        }
        $result = Device::create([
            'token' => $token,
            'endpoint_id' => $endpointId,
            'type' => $type,
            'user_id' => $user->id,
            'user_agent' => $userAgent,
            'exipre' => date('Y-m-d H:i:s', $expire),
        ]);
        if ($result) {
            return $this->successJsonResponse(201, '端末情報を更新しました。');
        } else {
            return $this->errorJsonResponse(
                500,
                '端末情報の更新に失敗しました。'
            );
        }
    }
    /**
     * Deviceテーブルのupdated_atを更新
     *
     * @param Device $device
     * @return void
     */
    public function updateDeviceTimestamp(Device $device)
    {
        $result = $device->touch();

        if ($result) {
            return $this->successJsonResponse(200, '端末情報を更新しました。');
        } else {
            return $this->errorJsonResponse(
                500,
                '端末情報の更新に失敗しました。'
            );
        }
    }
    /**
     * エンドポイント情報を取得
     *
     * @param IPushNotificationService $service
     * @return void
     */
    public function getUserEndpoint(IPushNotificationService $service)
    {
        $contents = request()->input();
        if (isset($contents['endpointid'])) {
            $endpointId = $contents['endpointid'];
            $result = $service->getEndpoint($endpointId);
            if ($result->isError()) {
                return $this->errorJsonResponse(
                    $result->status,
                    'イベント情報を通知に失敗しました。',
                    (array) $result->result
                );
            } else {
                return $this->successJsonResponse(
                    $result->status,
                    'イベント情報を通知しました。',
                    (array) $result->result
                );
            }
        } else {
            return $this->errorJsonResponse(
                404,
                'endpoinidの指定がありません。'
            );
        }
    }

    /**
     * イベント通知
     *
     * @param IPushNotificationService $service
     * @return void
     */
    public function putEvents(IPushNotificationService $service)
    {
        $result = $service->putEvents('ampevent');
        if ($result->isError()) {
            return $this->errorJsonResponse(
                $result->status,
                'イベント情報を通知に失敗しました。',
                (array) $result->result
            );
        } else {
            return $this->successJsonResponse(
                $result->status,
                'イベント情報を通知しました。',
                (array) $result->result
            );
        }
    }

    /**
     * エンドポイント情報を更新
     *
     * @param IPushNotificationService $service
     * @return array
     */
    public function updateEndpoint(IPushNotificationService $service)
    {
        $humanId = request()->getHumanObject()->human_id;
        $user = User::where('human_id', $humanId)->first();
        if ($user == null) {
            return $this->successJsonResponse(
                404,
                'ユーザー情報が存在しません。'
            );
        }
        // 入力値設定
        $contents = request()->input();
        $address = $contents['address'];
        $channelType = $contents['channelType'];
        $divice = Device::where('token', $address)->first();
        $result = $service->updateEndpoint($divice, $address, $channelType);
        if ($result->isError()) {
            Log::error($result->result);
            return $this->errorJsonResponse(
                $result->status,
                'エンドポイント情報の更新に失敗しました。',
                (array) $result->result
            );
        } else {
            return $this->successJsonResponse(
                $result->status,
                'エンドポイント情報を更新しました。',
                (array) $result->result
            );
        }
    }
}
