<?php

namespace App\Http\Controllers;

use App\Models\Hanro;
use App\Models\Wood;
use App\Models\Race;

class ChokyoController extends Controller
{
    /**
     * 調教全体
     */
    public function index()
    {
        $mp_libs = new \MpLibs();

        $hanro = new Hanro();
        $hanro_data = $hanro->get_target_date_range(0, 0, 0,10);

        $wood = new Wood();
        $wood_data = $wood->get_target_date_range(0, 0, 0,10);

        return view('chokyo', [
            'place_list' => \MpConsts::PLACE_ID_LIST,
            'wood_data' => $wood_data,
            'hanro_data' => $hanro_data,
        ]);
    }

    /**
     * 馬別の調教データ
     * @param int $horse_id
     * @param int $race_id
     */
    public function horse($horse_id, $race_id)
    {
        $mp_libs = new \MpLibs();
        $race = Race::where('race_id', $race_id)->first();

        $date_s = 0;
        $date_e = 0;
        if (isset($race->race_date))
        {
            $date_s = $mp_libs->format_date($race->race_date);
            $date_e = $race->race_date;
        }

        $hanro = new Hanro();
        $hanro_data = $hanro->get_target_date_range($horse_id, $date_s, $date_e);
        $wood = new Wood();
        $wood_data = $wood->get_target_date_range($horse_id, $date_s, $date_e);
        return view('chokyo', [
            'place_list' => \MpConsts::PLACE_ID_LIST,
            'wood_data' => $wood_data,
            'hanro_data' => $hanro_data,
        ]);
    }

    /**
     * レース別の調教データ
     * @param int $race_date
     * @param string $place
     * @param int $race_num
     */
    public function race($race_date, $place, $race_num)
    {
        $mp_libs = new \MpLibs();

        $date_s = $mp_libs->format_date($race_date, 14);
        $date_e = $race_date;

        $horse_ids = $this->get_race_to_horse_ids($mp_libs, $race_date, $place, $race_num);
        $hanro = new Hanro();
        $hanro_data = $hanro->get_target_date_range_horse_ids($horse_ids, $date_s, $date_e);

        $wood = new Wood();
        $wood_data = $wood->get_target_date_range_horse_ids($horse_ids, $date_s, $date_e);

        return view('chokyo', [
            'place_list' => \MpConsts::PLACE_ID_LIST,
            'wood_data' => $wood_data,
            'hanro_data' => $hanro_data,
        ]);
    }

    /**
     * 日付、場所、レース番号から出走馬のIDリストを作る
     * @param object $mp_libs
     * @param int $race_date
     * @param string $place
     * @param int $race_num
     * @return array $horse_id_list
     *
     **/
    private function get_race_to_horse_ids($mp_libs, $race_date, $place, $race_num)
    {
        $csv_name = "race_" .$race_num. ".csv";
        $csv = $mp_libs->get_csv($race_date, $place, $csv_name);
        $horse_id_list = [];
        if (isset($csv))
        {
            foreach ($csv as $c)
            {
                if (!is_numeric($c[1])) continue;
                $horse_id_list[] = $c[1];
            }
        }
        return $horse_id_list;
    }
}
