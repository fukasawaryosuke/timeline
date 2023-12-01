<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class SystemRole extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'system_role_name'];

    public function User()
    {
        return $this->hasMany(User::class);
    }

    public function IsAdmin()
    {
        return $this->system_role_name === 'administrator';
    }

    public static function GetGeneralId()
    {
        $result = SystemRole::where('system_role_name', 'general')->first();
        return is_null($result) ? 0 : $result->id;
    }
    public static function GetAdminId()
    {
        $result = SystemRole::where(
            'system_role_name',
            'administrator'
        )->first();
        return is_null($result) ? 0 : $result->id;
    }
}
