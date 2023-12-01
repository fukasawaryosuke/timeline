<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiaryBrowsHistory extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = ['diary_id', 'history', 'created_at', 'update_at', 'deleted_at'];
    protected $casts = [
        'history'  => 'json',
    ];
    
    public function diary()
    {
        return $this->belongsTo(Diary::class);
    }

}
