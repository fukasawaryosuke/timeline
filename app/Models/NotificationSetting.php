<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory;
    protected $table = 'notification_settings';

    protected $fillable = ['in_house_system_id', 'division_cd'];

    public function inHouseSystem()
    {
        return $this->belongsTo(InHouseSystem::class);
    }
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'division_cd', 'division_cd');
    }
}
