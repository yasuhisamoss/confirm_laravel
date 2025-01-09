<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Race extends Model
{
    use HasFactory;
    // タイムスタンプなし
    public $timestamps = false;
    public $incrementing = false;

    protected $primaryKey = 'race_id';
    protected $keyType = 'string';

    protected $fillable = [
        'race_id',
        'race_date',
        'kaizi',
        'nichizi',
        'corse_id',
        'race_num',
        'race_name',
        'class_code',
        'track_bias',
        'tenki',
        'entry_count',
        'full_gate',
        'race_mark_1',
        'race_mark_2',
        'race_mark_3',
        'rpci',
        'base_time',
    ];

    /**
     * 距離、馬場状態などのパラメータをキーにレースを検索を取得
     * @param array $search_params
     * @return object
     */
    public function race_search($search_params)
    {
        //\DB::enableQueryLog();
        $where = [];
        if ($search_params['distance_s'])
        {
            $where[] = ['corses.distance', '>=', $search_params['distance_s']];
        }

        if ($search_params['distance_e'])
        {
            $where[] = ['corses.distance', '<=', $search_params['distance_e']];
        }

        if (!empty($search_params['cushion']))
        {
            $cushion_e = max($search_params['cushion']) +0.3;
            $cushion_s = min($search_params['cushion']) -0.3;

            $where[] = ['moisture_content_and_cushions.cushion', '>=', $cushion_s];
            $where[] = ['moisture_content_and_cushions.cushion', '<=', $cushion_e];
        }

        if (!empty($search_params['stallion']))
        {   
            $where[] = ['horses.father_id', '=', $search_params['stallion']];
        }
        
        $race_search = DB::table('races')
            ->select(
                'races.*',
                'corses.*',
                'places.*',
                'moisture_content_and_cushions.*',
                'horse_race_histories.result_time',
                'horses.horse_name',
                'horses.horse_id',
                'horse_race_histories.no',
                'horse_race_histories.waku_no',
                'race_schedules.nichizi',
                'race_schedules.kaizi',
                'horse_race_histories.clincher',
                'horse_race_histories.passing_rank_1',
                'horse_race_histories.passing_rank_2',
                'horse_race_histories.passing_rank_3',
                'horse_race_histories.passing_rank_4',
                'horse_race_histories.hosei_9',
                'stallions.stallion_name',

            )
            ->leftJoin('corses', 'corses.corse_id', '=', 'races.corse_id')
            ->leftJoin('places', 'places.place_id', '=', 'corses.place_id')
            ->leftJoin('moisture_content_and_cushions', function ($join) {
                $join->on('moisture_content_and_cushions.race_date', '=', 'races.race_date')->on('moisture_content_and_cushions.place_id', '=', 'places.place_id');
            })
            ->leftJoin('race_schedules', function ($join) {
                $join->on('race_schedules.race_date', '=', 'races.race_date')->on('race_schedules.place_id', '=', 'places.place_id');
            })
            ->leftJoin('horse_race_histories', 'horse_race_histories.race_id', '=', 'races.race_id')
            ->leftJoin('horses', 'horses.horse_id', '=', 'horse_race_histories.horse_id')
            ->leftJoin('stallions', 'horses.father_id', '=', 'stallions.stallion_id');

        if (!empty($where))
        {
            $where[] = ['horse_race_histories.result_rank', '=', 1];
            $race_search->where($where);
        }    
        if (!empty($search_params['track_bias']))
        {
            $race_search->whereIn('races.track_bias', $search_params['track_bias']);
        }

        if (!empty($search_params['track_code']))
        {
            $race_search->whereIn('corses.track_type', $search_params['track_code']);
        }

        if (!empty($search_params['place']))
        {
            $race_search->whereIn('places.place_id', $search_params['place']);
        }

/*        if (!empty($search_params['r_level']))
        {
            $race_search->whereIn('races.race_mark_1', $search_params['r_level']);
        }
*/
        $search = $race_search->orderBy('races.race_date', 'desc')->get();
        //\DB::enableQueryLog();

        return $search;
    }

    /**
     * 日付、場所、レース番号を指定してレースデータを取得
     * @param int $race_date レース日付
     * @param int $place_id 場所ID
     * @param int $race_num レース番号
     * @return object
     */
    public function get_race_data($race_date, $place_id, $race_num)
    {
        $race_search = DB::table('races')
        ->select(
            'races.*',
            'corses.*',
            'places.*'
        )
        ->leftJoin('corses', 'corses.corse_id', '=', 'races.corse_id')
        ->leftJoin('places', 'places.place_id', '=', 'corses.place_id');

        $where[] = ['races.race_date', '=', $race_date];
        $where[] = ['races.race_num', '=', $race_num];
        $where[] = ['places.place_id', '=', $place_id];
        $race_search->where($where);
        return $race_search->first();
    }

    /**
     * コースIDを指定してペースとPCI平均を取る
     * @param int $corse_id コースID
     * @param int $class_code クラスコード
     * @return object
     */
    public function get_target_corse_info($corse_id, $class_code = 0)
    {
        $where = [
            ['races.corse_id', '=', $corse_id]
        ];

        if ($class_code)
        {
            $where[] = ['races.class_code', '=', $class_code];
        }

        return DB::table('races')
            ->select(
                'races.race_mark_2',
                DB::raw('COUNT(races.race_mark_2) as count'),
                DB::raw('AVG(races.rpci) as pci_ave'),
            )

            ->where($where)
            ->groupBy('races.race_mark_2')
            ->orderBy('count', 'desc')
            ->get();    
    }
}
