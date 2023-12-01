<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Device extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'token',
        'endpoint_id',
        'type',
        'user_agent',
        'user_id',
        'receive_notice',
        'expire',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 全デバイスのトークンを取得
     *
     * @return array
     */
    public static function allToken()
    {
        $now = date('Y-m-d H:i:s');
        return self::groupBy('token')
            ->where('expire', '>', $now)
            ->pluck('token')
            ->toArray();
    }

    /**
     * アカウントを指定してトークンを取得
     *
     * @return array
     */
    public static function tokenWithUserIds($noticeUserIds)
    {
        $now = date('Y-m-d H:i:s');
        return self::groupBy('token')
            ->whereIn('user_id', $noticeUserIds)
            ->where('expire', '>', $now)
            ->pluck('token')
            ->toArray();
    }

    /**
     * 会社コードを指定してトークンを取得
     *
     * @param $userCompanyCode
     * @return array
     */
    public static function tokenWithCompanyCd($userCompanyCode)
    {
        $now = date('Y-m-d H:i:s');
        return self::join('users', function ($join) use ($userCompanyCode) {
                $join->on('devices.user_id', '=', 'users.id')
                    ->whereNull('users.deleted_at')
                    ->where('users.is_active', '=', 1)
                    ->where('users.company_code', '=', $userCompanyCode);
            })
            ->groupBy('token')
            ->where('expire', '>', $now)
            ->pluck('token')
            ->toArray();
    }

    /**
     * プッシュ通知トークンの有効期限(SSOクッキーのexpireと同値)が切れているか確認
     *
     * @return void
     */
    public function enableToken()
    {
        $now = date('Y-m-d H:i:s');
        if ($this->expire) {
            return $this->expire > $now;
        } else {
            return false;
        }
    }

    /**
     * AWS Pinpoint用のEndpoint ID文字列を作成
     *
     * @param string $token
     * @return string
     */
    public static function createEndpointId(string $token): string
    {
        return "hatUser{$token}";
    }
}
