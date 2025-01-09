<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StallionBias extends Model
{
    use HasFactory;

    // タイムスタンプなし
    public $timestamps = false;
    
    protected $fillable = [
        'id',
        'stallion_id',
        'bias_type',
        'point',
    ];
}
