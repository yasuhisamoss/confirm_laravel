<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Wood extends Model
{
    use HasFactory;
    // タイムスタンプなし
    public $timestamps = false;

    protected $fillable = [
        'horse_id',
        'training_date',
        'youbi',
        'time_8',
        'time_7',
        'time_6',
        'time_5',
        'time_4',
        'time_3',
        'time_2',
        'time_1',
        'lap_8',
        'lap_7',
        'lap_6',
        'lap_5',
        'lap_4',
        'lap_3',
        'lap_2',
        'lap_1',
        'point',
        'trainer_id',
    ];

    /**
     * Horse_idと時間を指定してウッドデータを取る
     * @param int $horse_id
     * @param int $target_date_s
     * @param int $target_date_e
     * @param int $target_befoer_day
     * @return object
     */
    public function get_target_date_range($horse_id = 0, $target_date_s = 0, $target_date_e = 0, $target_befoer_day = 14)
    {
        if ($horse_id != 0)
        {
            $where[] = ['wood.horse_id', '=', $horse_id];
        }

        $mp_libs = new \MpLibs();
        if ($target_date_s == 0)
        {
            $target_date_s =$mp_libs->format_date(date("Ymd", time()), $target_befoer_day);
        }

        if ($target_date_e == 0)
        {
            $target_date_e = date("Ymd", time());
        }

        $where[] = ['wood.training_date', '>=', $target_date_s];
        $where[] = ['wood.training_date', '<=', $target_date_e];
        
        return DB::table('wood')
        ->select('wood.*', 'horses.horse_name', 'trainers.trainer_name')
        ->leftJoin('horses', 'horses.horse_id', '=', 'wood.horse_id')
        ->leftJoin('trainers', 'trainers.trainer_id', '=', 'wood.trainer_id')
        ->where($where)
        ->orderBy('wood.point', 'desc')
        ->get();
    }

    /**
     * Horse_idと時間を指定してウッドデータを取る
     * @param array $horse_ids
     * @param int $target_date_s
     * @param int $target_date_e
     * @return object
     */
    public function get_target_date_range_horse_ids($horse_ids = [], $target_date_s = 0, $target_date_e = 0)
    {
        $mp_libs = new \MpLibs();
        if ($target_date_s == 0)
        {
            $target_date_s =$mp_libs->format_date(date("Ymd", time()));
        }

        if ($target_date_e == 0)
        {
            $target_date_e = date("Ymd", time());
        }

        $where[] = ['wood.training_date', '>=', $target_date_s];
        $where[] = ['wood.training_date', '<=', $target_date_e];
        
        return DB::table('wood')
        ->select('wood.*', 'horses.horse_name', 'trainers.trainer_name')
        ->leftJoin('horses', 'horses.horse_id', '=', 'wood.horse_id')
        ->leftJoin('trainers', 'trainers.trainer_id', '=', 'wood.trainer_id')
        ->whereIn('wood.horse_id', $horse_ids)
        ->where($where)
        ->orderBy('wood.point', 'desc')
        ->get();
    }
    
}
