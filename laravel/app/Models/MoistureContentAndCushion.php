<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoistureContentAndCushion extends Model
{
    use HasFactory;

    // タイムスタンプなし
    public $timestamps = false;
    
    protected $fillable = [
        'place_id',
        'race_date',
        'turf_moisture_content',
        'dart_moisture_content',
        'cushion',
    ];
}
