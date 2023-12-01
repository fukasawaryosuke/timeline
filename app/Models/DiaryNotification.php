<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\User;
use App\Models\Diary;

class DiaryNotification extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'diary_id',
        'user_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function diary()
    {
        return $this->belongsTo(Diary::class);
    }
}
