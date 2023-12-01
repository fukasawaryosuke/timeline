<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Draft extends Model
{
    use HasFactory;

    protected $fillable = ['in_house_systems_id', 'user_id', 'draft', 'created_at', 'updated_at'];
    protected $casts = ['draft' => 'json'];

    public function InHouseSystems()
    {
        return $this->belongsTo(InHouseSystem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
