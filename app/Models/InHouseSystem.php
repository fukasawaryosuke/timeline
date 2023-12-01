<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;
use App\Models\Diary;
use App\Models\ReportForm;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class InHouseSystem extends Model
{
    use HasFactory;
    use SoftDeletes;

    public static $fiveMinutesId = 1;

    protected $fillable = [
        'id',
        'system_name',
        'icon_path',
        'release_status',
        'description',
        'parent_id',
        'company_code',
        'division_cd',
        'created_at',
        'update_at',
        'deleted_at',
        'sequence',
        'report_form_id',
    ];

    public function diary()
    {
        return $this->hasMany(Diary::class);
    }

    public function report_form()
    {
        return $this->belongsTo(ReportForm::class);
    }

    public function organization()
    {
        return $this->hasMany(Organization::class);
    }
    public function notification_setting()
    {
        return $this->hasMany(NotificationSetting::class);
    }

    public static function isFiveMinutesReport(int $id)
    {
        // NOTE: idが1のものを5分日報とする
        return $id == self::$fiveMinutesId;
    }
    // datetime型のフォーマット指定、変更
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y/m/d');
    }

    public static function boot()
    {
        parent::boot();

        // リレーション先を論理削除
        static::deleted(function ($ihs) {
            // 制度に紐づく日報に紐づくコメントに紐づく通知を削除
            $ihs->diary()->each(function ($diary) {
                $diary->comment()->each(function ($comment) {
                    $comment->notifications()->delete();
                });
            });
            // 制度に紐づく日報に紐づく必読通知を削除
            $ihs->diary()->each(function ($diary) {
                $diary->diary_notification()->delete();
            });
            // 制度に紐づく日報に紐づく閲覧履歴を削除
            $ihs->diary()->each(function ($diary) {
                $diary->brows_history()->delete();
            });
            // 制度に紐づく日報にコメントを削除
            $ihs->diary()->each(function ($diary) {
                $diary->comment()->delete();
            });
            // 制度に紐づく日報を削除
            $ihs->diary()->delete();
        });
    }
}
