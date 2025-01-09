<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Place extends Model
{
    use HasFactory;
    // タイムスタンプなし
    public $timestamps = false;

    protected $fillable = [
        'place_name',
        'place_name_code',
    ];
    protected $primaryKey = 'place_id';

    /**
     * コースIDからPlaceのデータを取る
     * @param int $corse_id
     * @return object Placesのデータ
     */
    public function get_place_data_by_corse_id($corse_id)
    {
        $where[] = ['corses.corse_id', '=', $corse_id];
        
        return DB::table('places')
            ->select('places.*')
            ->leftJoin('corses', 'corses.place_id', '=', 'places.place_id')
            ->where($where)
            ->get();
    }
}
