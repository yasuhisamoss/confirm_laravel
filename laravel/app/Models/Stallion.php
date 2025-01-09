<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stallion extends Model
{
    use HasFactory;
    // タイムスタンプなし
    public $timestamps = false;

    protected $fillable = [
        'stallion_id',
        'stallion_name',
        'memo'
    ];
    protected $primaryKey = 'stallion_id';
}
