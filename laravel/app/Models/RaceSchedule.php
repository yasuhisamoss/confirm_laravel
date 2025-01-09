<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RaceSchedule extends Model
{
    use HasFactory;

    // タイムスタンプなし
    public $timestamps = false;
    
    protected $fillable = [
        'race_date',
        'place_id',
        'kaizi',
        'nichizi',
    ];

    /**
     * レーススケジュールを取得
     * @param int $target_date_start 日付 default:0
     * @param int $target_date_end 日付 default:0
     * @return object
     */
    public function get_schedule($target_date_start = 0, $target_date_end = 0)
    {
        $where = [];

        if ($target_date_start != 0)
        {
            $where[] = ['race_schedules.race_date', '>=', $target_date_start];
            $where[] = ['race_schedules.race_date', '<=', $target_date_end];
        }

        return DB::table('race_schedules')
            ->select(
                'race_schedules.*',
                'places.place_name',
                'places.place_code_name',
                'places.corner_direction',
                'moisture_content_and_cushions.turf_moisture_content',
                'moisture_content_and_cushions.dart_moisture_content',
                'moisture_content_and_cushions.cushion'
                )
            ->leftJoin('places', 'places.place_id', '=', 'race_schedules.place_id')
            ->leftJoin('moisture_content_and_cushions', function ($join) {
                $join->on('moisture_content_and_cushions.race_date', '=', 'race_schedules.race_date')->on('moisture_content_and_cushions.place_id', '=', 'race_schedules.place_id');
            })
            ->where($where)
            ->orderBy('race_schedules.race_date', 'desc')
            ->get();
    }

    public function get_same_schedule_place($target_date)
    {
        return DB::table('race_schedules')
            ->select(
                'race_schedules.*',
                'places.place_name',
                'places.place_code_name'
                )
            ->leftJoin('places', 'places.place_id', '=', 'race_schedules.place_id')
            ->where([['race_date', "=", $target_date]])
            ->get();
    }
}
