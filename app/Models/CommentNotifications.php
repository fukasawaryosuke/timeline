<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Comment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommentNotifications extends Model
{
    protected $table = "comment_notifications";
    protected $fillable = [
        "user_id",
        "comment_id",
        "notification_type",
        "created_at",
        "update_at",
        "deleted_at",
    ];

    use HasFactory;
    use SoftDeletes;

    public function user()
    {
        return $this->belongsTo(User::class, "user_id")->select([
            "id",
            "human_id",
            "user_name",
        ]);
    }

    public function comment()
    {
        return $this->belongsTo(Comment::class, "comment_id");
    }

    public function getCreatedAtAttribute($date)
    {
        return $date
            ? Carbon::parse($date)
                ->timezone("Asia/Tokyo")
                ->format("Y/m/d H:i")
            : null;
    }
}
