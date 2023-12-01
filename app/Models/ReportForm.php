<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;
use App\Models\InHouseSystem;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportForm extends Model
{
    use HasFactory;
    use SoftDeletes;

    public static $fiveMinutesId = 1;
    protected $casts = ['diary' => 'json'];
    protected $fillable = [
        'id',
        'name',
        'diary',
        'required',
        'company_code',
        'created_at',
        'update_at',
        'deleted_at',
    ];

    public function in_house_system()
    {
        return $this->hasMany(InHouseSystem::class);
    }

    // datetime型のフォーマット指定、変更
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y/m/d');
    }
}
