<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use function Ramsey\Uuid\v1;

class HorseRaceHistory extends Model
{
    use HasFactory;
    // タイムスタンプなし
    public $timestamps = false;

    protected $fillable = [
        'race_id',
        'horse_id',
        'mark_1',
        'mark_2',
        'mark_3',
        'mark_4',
        'mark_5',
        'mark_6',
        'no',
        'waku_no',
        'sex',
        'age',
        'jockey_id',
        'weight',
        'is_blinker',
        'result_rank',
        'ninki',
        'odds',
        'chakusa',
        'result_time',
        'hosei_9',
        'passing_rank_1',
        'passing_rank_2',
        'passing_rank_3',
        'passing_rank_4',
        'clincher',
        'last_3f',
        'last_3f_rank',
        'ave_3f',
        'pci',
        'last_3f_sa',
        'body_weight',
        'fluctuation_weight',
        'race_comment',
        'kol_comment',
    ];

    /**
     * 馬IDをキーにレース履歴を取得
     * @param int $horse_id
     * @param int $target_date 日付 default:0
     * @return object
     */
    public function get_history_by_range($horse_id, $target_date = 0)
    {
        $where[] = ['horse_race_histories.horse_id', '=', $horse_id];

        if ($target_date)
        {
            $where[] = ['races.race_date', '<', $target_date];
        }

        return DB::table('horse_race_histories')
            ->select(
                'horse_race_histories.*',
                'races.*',
                'jockeys.jockey_name',
                'jockeys.jockey_rank',
                'places.place_name',
                'places.place_code_name',
                'places.corner_direction',
                'corses.distance',
                'corses.track_type',
                'corses.corner_count',
                'corses.corner_level',
                'corses.corner_level_point',
                'moisture_content_and_cushions.turf_moisture_content',
                'moisture_content_and_cushions.dart_moisture_content',
                'moisture_content_and_cushions.cushion'
                )
            ->leftJoin('races', 'horse_race_histories.race_id', '=', 'races.race_id')
            ->leftJoin('jockeys', 'horse_race_histories.jockey_id', '=', 'jockeys.jockey_id')
            ->leftJoin('corses', 'corses.corse_id', '=', 'races.corse_id')
            ->leftJoin('places', 'places.place_id', '=', 'corses.place_id')
            ->leftJoin('moisture_content_and_cushions', function ($join) {
                $join->on('moisture_content_and_cushions.race_date', '=', 'races.race_date')->on('moisture_content_and_cushions.place_id', '=', 'places.place_id');
            })
            ->where($where)
            ->orderBy('races.race_date', 'desc')
            ->get();
    }

    /**
     * 馬IDをキーに条件にあうレース履歴を取得
     * @param int $horse_id
     * @param int $target_date 日付 default:0
     * @return object
     */
    public function serach_race_history($horse_id, $target_date = 0)
    {
        $where[] = ['horse_race_histories.horse_id', '=', $horse_id];

        if ($target_date)
        {
            $where[] = ['races.race_date', '<', $target_date];
        }
        
        //\DB::enableQueryLog();
        $result = DB::table('horse_race_histories')
            ->select(
                'horses.horse_name',
                'horse_race_histories.*',
                'races.*',
                'jockeys.jockey_name',
                'jockeys.jockey_rank',
                'places.place_id',
                'places.place_name',
                'places.place_code_name',
                'places.corner_direction',
                'corses.distance',
                'corses.track_type',
                'corses.corner_count',
                'corses.corner_level',
                'corses.corner_level_point',
                'moisture_content_and_cushions.turf_moisture_content',
                'moisture_content_and_cushions.dart_moisture_content',
                'moisture_content_and_cushions.cushion'
                )
            ->leftJoin('horses', 'horse_race_histories.horse_id', '=', 'horses.horse_id')
            ->leftJoin('races', 'horse_race_histories.race_id', '=', 'races.race_id')
            ->leftJoin('jockeys', 'horse_race_histories.jockey_id', '=', 'jockeys.jockey_id')
            ->leftJoin('corses', 'corses.corse_id', '=', 'races.corse_id')
            ->leftJoin('places', 'places.place_id', '=', 'corses.place_id')
            ->leftJoin('moisture_content_and_cushions', function ($join) {
                $join->on('moisture_content_and_cushions.race_date', '=', 'races.race_date')->on('moisture_content_and_cushions.place_id', '=', 'places.place_id');
            })
            ->where($where)
            ->orderBy('races.race_date', 'desc')
            ->get();

            //dd(\DB::getQueryLog());
            return $result;
    }
    /**
     * 種牡馬IDをキーにレース履歴を取得
     * @param int $horse_id
     * @param int $target_date 日付 default:0
     * @param array $track_type
     * @param array $track_bias
     * @param array $place
     * @return object
     */
    public function get_history_by_stallion($stallion_id, $target_date = 0, $distance_s = 1000, $distance_e = 5000, $track_type = [], $track_bias = [], $place = [])
    {
        $where[] = ['horses.father_id', '=', $stallion_id];
        $where[] = ['horse_race_histories.result_rank', '!=', 0];
        if ($distance_s)
        {
            $where[] = ['corses.distance', '>=', $distance_s];
        }

        if ($distance_e)
        {
            $where[] = ['corses.distance', '<=', $distance_e];
        }
        if ($target_date)
        {
            $where[] = ['races.race_date', '<', $target_date];
        }
        //\DB::enableQueryLog();
        $search_stallion = DB::table('horse_race_histories')
            ->select(
                'horses.horse_name',
                'horse_race_histories.*',
                'races.*',
                'jockeys.jockey_name',
                'jockeys.jockey_rank',
                'places.place_id',
                'places.place_name',
                'places.place_code_name',
                'places.corner_direction',
                'corses.distance',
                'corses.track_type',
                'corses.corner_count',
                'corses.corner_level',
                'corses.corner_level_point',
                'moisture_content_and_cushions.turf_moisture_content',
                'moisture_content_and_cushions.dart_moisture_content',
                'moisture_content_and_cushions.cushion',
                'horses.father_id',
                )
            ->leftJoin('horses', 'horse_race_histories.horse_id', '=', 'horses.horse_id')
            ->leftJoin('races', 'horse_race_histories.race_id', '=', 'races.race_id')
            ->leftJoin('jockeys', 'horse_race_histories.jockey_id', '=', 'jockeys.jockey_id')
            ->leftJoin('corses', 'corses.corse_id', '=', 'races.corse_id')
            ->leftJoin('places', 'places.place_id', '=', 'corses.place_id')
            ->leftJoin('moisture_content_and_cushions', function ($join) {
                $join->on('moisture_content_and_cushions.race_date', '=', 'races.race_date')->on('moisture_content_and_cushions.place_id', '=', 'places.place_id');
            })
            ->where($where);

            if (!empty($track_bias))
            {
                $search_stallion->whereIn('races.track_bias', $track_bias);
            }
    
            if (!empty($track_type))
            {
                $search_stallion->whereIn('corses.track_type', $track_type);
            }
            if (!empty($place))
            {
                $search_stallion->whereIn('places.place_id', $place);
            }
            else 
            {
                // 一旦中央の競馬場のみで算出
                $search_stallion->whereIn('places.place_id', [1,2,3,4,5,6,7,8,9,10]);
            }
            $result = $search_stallion->orderBy('races.race_date', 'desc')->get();

            return $result;
    }

    /**
     * コース別のtarget_rankよりも上位な補９の平均、MAX,MINを取得
     * @param int $target_rank ランク
     * @param int $corse_id コースID
     * @param int $class_code クラスコード
     * @return object
     */
    public function get_target_corse_point($target_rank, $corse_id, $class_code)
    {
        $where = [
            ['horse_race_histories.result_rank', '<=', $target_rank],
            ['horse_race_histories.hosei_9', '!=', 0],
            ['corses.corse_id', '=', $corse_id],
            ['races.class_code', '=', $class_code]
        ];
        return DB::table('horse_race_histories')
            ->select(
                DB::raw('AVG(horse_race_histories.hosei_9) as h9ave'),
                DB::raw('MIN(horse_race_histories.hosei_9) as h9min'),
                DB::raw('MAX(horse_race_histories.hosei_9) as h9max'),
                DB::raw('count(horse_race_histories.hosei_9) as h9count'),
                'places.place_name',
                'races.class_code',
                'corses.corse_id',
                'corses.distance'
            )
            ->leftJoin('races', 'horse_race_histories.race_id', '=', 'races.race_id')
            ->leftJoin('corses', 'corses.corse_id', '=', 'races.corse_id')
            ->leftJoin('places', 'places.place_id', '=', 'corses.place_id')
            ->where($where)
            ->groupBy('races.class_code', 'corses.corse_id')
            ->get();    
    }
}
