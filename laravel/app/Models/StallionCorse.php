<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class StallionCorse extends Model
{
    use HasFactory;

    // タイムスタンプなし
    public $timestamps = false;
    
    protected $fillable = [
        'id',
        'stallion_id',
        'corse_id',
        'point',
    ];

    /**
     * 種牡馬IDについているバイアスポイントリストを取得
     * @param int $stallion_id
     * @return object
     */
    public function get_corse_point($stallion_id)
    {
        $where[] = ['stallion_corses.stallion_id', '=', $stallion_id];
        return DB::table('stallion_corses')
            ->select(
                'stallion_corses.*',
                'places.place_name',
                'places.place_code_name',
                'places.corner_direction',
                'corses.distance',
                'corses.track_type',
                'corses.corner_count',
                'corses.corner_level',
                'corses.corner_level_point'
                )
            ->leftJoin('corses', 'corses.corse_id', '=', 'stallion_corses.corse_id')
            ->leftJoin('places', 'places.place_id', '=', 'corses.place_id')

            ->where($where)
            ->get();
    }
}
