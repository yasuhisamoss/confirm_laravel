<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Horse extends Model
{
    use HasFactory;
    // タイムスタンプなし
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'horse_id';

    protected $fillable = [
        'horse_id',
        'horse_name',
        'trainer_id',
        'father_id',
        'grandfather_id',
        'horse_comment',
    ];

    /**
     * Horsesを馬名で曖昧検索、種牡馬データとトレーナー名をくっつけて返却
     * @param string $horse_name
     * @return object
     */
    public function get_horse_name_search($horse_name)
    {
        return DB::table('horses')
            ->select('horses.*', 'stallion_1.stallion_name as father_name', 'stallion_2.stallion_name as grandfather_name', 'trainers.trainer_name')
            ->rightJoin('stallions as stallion_1', 'horses.father_id', '=', 'stallion_1.stallion_id')
            ->rightJoin('stallions as stallion_2', 'horses.grandfather_id', '=', 'stallion_2.stallion_id')
            ->rightJoin('trainers', 'horses.trainer_id', '=', 'trainers.trainer_id')
            ->where('horses.horse_name', 'LIKE', '%'.$horse_name.'%')
            ->get();
    }

    /**
     * Horsesに種牡馬データとトレーナー名をくっつけて返却
     * @param int $horse_id
     * @return object
     */
    public function get_horse_with_father($horse_id)
    {
        return DB::table('horses')
            ->select('horses.*', 'stallion_1.stallion_name as father_name', 'stallion_2.stallion_name as grandfather_name', 'trainers.trainer_name')
            ->rightJoin('stallions as stallion_1', 'horses.father_id', '=', 'stallion_1.stallion_id')
            ->rightJoin('stallions as stallion_2', 'horses.grandfather_id', '=', 'stallion_2.stallion_id')
            ->rightJoin('trainers', 'horses.trainer_id', '=', 'trainers.trainer_id')
            ->where('horses.horse_id', '=', $horse_id)
            ->get();
    }
}
