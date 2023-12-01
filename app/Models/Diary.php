<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Comment;
use App\Models\DiaryBrowsHistory;
use App\Models\InHouseSystem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Diary extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'diary',
        'in_house_system_id',
        'division_cd',
        'created_by',
        'created_at',
        'update_at',
        'deleted_at',
    ];

    protected $casts = [
        'diary' => 'json',
    ];

    protected $appends = ['title'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comment()
    {
        return $this->hasMany(Comment::class);
    }

    public function InHouseSystems()
    {
        return $this->belongsTo(InHouseSystems::class);
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by')->select([
            'id',
            'user_name',
        ]);
    }

    public function created_user()
    {
        return $this->belongsTo(User::class, 'created_by')->select([
            'id',
            'user_name',
            'division_cd',
        ]);
    }

    public function brows_history()
    {
        return $this->hasOne(DiaryBrowsHistory::class);
    }

    public function diary_notification()
    {
        return $this->hasOne(DiaryNotification::class);
    }

    public function in_house_system()
    {
        return $this->belongsTo(InHouseSystem::class);
    }

    public function getTitleAttribute()
    {
        if (isset($this->diary)) {
            foreach ($this->diary['contents'] as $content) {
                if ($content['contentType'] == 'text') {
                    return $content['content'];
                }
            }
        }
        return null;
    }

    public function getCreatedAtAttribute($date)
    {
        return $date
            ? Carbon::parse($date)
                ->timezone('Asia/Tokyo')
                ->format('Y/m/d H:i')
            : null;
    }

    public static function boot()
    {
        parent::boot();

        // リレーション先を論理削除
        static::deleted(function ($diary) {
            // 日報に紐づくコメントに紐づく通知を削除
            $diary->comment()->each(function ($comment) {
                $comment->notifications()->delete();
            });

            // 日報に紐づくコメントを削除
            $diary->comment()->delete();

            // 日報に紐づく閲覧履歴を削除
            $diary->brows_history()->delete();

            // 日報に紐づく必読通知を削除
            $diary->diary_notification()->delete();
        });
    }
}
