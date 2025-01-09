<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Corse extends Model
{
    use HasFactory;

    // タイムスタンプなし
    public $timestamps = false;
    protected $primaryKey = 'corse_id';
    // 更新対象
    protected $fillable = [
        'place_id',
        'track_type',
        'distance',
        'corner_count',
        'corner_size',
    ];
}
