<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Diary;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    protected $fillable = [
        'comment',
        'diary_id',
        'created_by',
        'created_at',
        'update_at',
        'deleted_at',
    ];

    use HasFactory;
    use SoftDeletes;

    public function diary()
    {
        return $this->belongsTo(Diary::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by')->select([
            'id',
            'human_id',
            'user_name',
            'division_cd',
        ]);
    }

    public function mensions()
    {
        return $this->hasMany(CommentNotifications::class)
            ->select(['id', 'user_id', 'comment_id'])
            ->where('notification_type', '=', 1);
    }

    public function notifications()
    {
        return $this->hasMany(CommentNotifications::class);
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by')->select([
            'id',
            'human_id',
            'user_name',
        ]);
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
        static::deleted(function ($comment) {
            // コメントに紐づく通知を削除
            $comment->notifications()->delete();
        });
    }
}
