<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainer extends Model
{
    use HasFactory;
    // タイムスタンプなし
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'trainer_id',
        'trainer_name'
    ];
    protected $primaryKey = 'trainer_id';
    protected $keyType = 'string';
}
