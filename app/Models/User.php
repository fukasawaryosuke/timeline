<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Diary;
use App\Models\Organization;
use App\Models\SystemRole;
use App\Models\Device;
use Illuminate\Support\Facades\Crypt;

class User extends Model
{
    use HasFactory;

    const TIMELINE_ID_PREFIX = 'TIMELINE_ORIGIN_';

    protected $fillable = [
        'id',
        'human_id',
        'system_role_id',
        'user_name',
        'mailadd',
        'division_cd',
        'company_code',
        'password',
        'is_initial_password',
        'is_initial_user',
        'is_active',
    ];
    protected $attributes = [
        'human_id' => '',
    ];

    public function Diary()
    {
        return $this->hasMany(Diary::class, 'created_by');
    }

    public function Comment()
    {
        return $this->hasMany(Comment::class, 'created_by');
    }

    public function Organization()
    {
        return $this->hasOne(Organization::class, 'division_cd', 'division_cd');
    }

    public function SystemRole()
    {
        return $this->belongsTo(SystemRole::class);
    }

    public function Device()
    {
        return $this->hasMany(Device::class);
    }

    public function Company()
    {
        return $this->belongsTo(Company::class, 'company_code', 'company_code');
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Crypt::encryptString($value);
    }

    public function getPasswordAttribute()
    {
        if ($this->attributes['password']) {
            return Crypt::decryptString($this->attributes['password']);
        }
        return null;
    }

    public static function createUser($companyCode, $formData)
    {
        $isHumanAcademy = Company::isHumanAcademy($companyCode);

        $user =  self::create([
            'user_name' => $formData['userName'],
            'mailadd' => $formData['mailadd'],
            'division_cd' => $formData['divisionCd'] ?? null,
            'company_code' => $companyCode,
            //NOTE:初期パスワードはメールアドレスにする
            'password' => $formData['mailadd'],
            'is_initial_password' => $isHumanAcademy ? 0 : 1,
            'is_active' => $formData['isActive'],
            'system_role_id' => $formData['systemRoleId'],
        ]);

        $user->timelineHumanId();

        return $user;
    }

    public static function createInitialUser($companyCode, $formData)
    {
        //NOTE:初期ユーザーはシステム管理者
        $user =  self::create([
            'user_name' => $formData['userName'],
            'mailadd' => $formData['mailadd'],
            'company_code' => $companyCode,
            //NOTE:初期パスワードはメールアドレスにする
            'password' => $formData['mailadd'],
            'is_initial_password' => 1,
            'is_initial_user' => 1,
            'is_active' => 1,
            'system_role_id' => 2,
        ]);

        $user->timelineHumanId();

        return $user;
    }

    public function updateUser($formData)
    {
        $this->update([
            'user_name' => $formData['userName'],
            'mailadd' => $formData['mailadd'],
            'division_cd' => $formData['divisionCd'] ?? null,
            'is_active' => $formData['isActive'],
            'system_role_id' => $formData['systemRoleId'],
        ]);

        $this->diary()->update([
            'division_cd' => $formData['divisionCd'] ?? null,
        ]);

        return $this;
    }

    public function updatePassword($formData)
    {
        return $this->update([
            'password' => $formData['password'],
            'is_initial_password' => 0,
        ]);
    }

    public static function resetPassword($userId)
    {
        $user = self::findOrFail($userId);

        //NOTE:初期パスワードはメールアドレスにする
        return $user->update([
            'password' => $user->mailadd,
            'is_initial_password' => 1,
        ]);
    }

    // タイムライン独自でhuman_id相当のidを生成する
    public function timelineHumanId()
    {
        if (empty($this->human_id)) {
            $this->human_id = self::TIMELINE_ID_PREFIX . $this->id;
            $this->save();
        }
    }
}
