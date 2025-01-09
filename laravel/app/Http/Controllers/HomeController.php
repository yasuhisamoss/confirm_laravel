<?php

namespace App\Http\Controllers;
use App\Models\Place;
use App\Models\Race;
use App\Models\MoistureContentAndCushion;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * TOPページ
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        $mp_libs = new \MpLibs();

        $format_items = $this->get_race_card_csv($mp_libs);
        $target_date = $request['d'] ?? array_key_first($format_items);
        $race_card = $this->_get_race_data($mp_libs, $target_date, $format_items[$target_date]);
        $ba_date = $this->_target_date_before_after($format_items, $target_date);
        //dump($ba_date);
        return view('top', [
            'target_date' => $target_date,
            'csv_dir_list' => $format_items,
            'race_card' => $race_card,
            'ba_date' => $ba_date
        ]);
        return view('race_card');
    }

    /**
     * 前後の日付を取得
     * @param array $date_list
     * @param string $target_date
     * @return array $ba_date
     */
    private function _target_date_before_after($date_list, $target_date)
    {
        $before = 0;
        $after = 0;
        $keys = array_keys($date_list);
        $pos = array_search($target_date, $keys);

        $prev_pos = $pos - 1;
        $next_pos = $pos + 1;
        if($prev_pos < 0) $prev_pos = null;
        if($next_pos >= count($keys)) $next_pos = null;

        return ['after' => $keys[$prev_pos] ?? null, 'before' => $keys[$next_pos] ?? null];
    }

    /**
     * レースカードデータを取得
     * @param object $mp_libs
     * @param string $race_date
     * @param array $target_place_list
     * @return array $race_card
     */
    private function _get_race_data($mp_libs, $race_date, $target_place_list)
    {
        $race_card = [];
        $r = new Race();
        foreach ($target_place_list as $place)
        {
            $p = Place::where('place_code_name', $place)->get();

            $race_card[$place] = [];
            for ($num = 1; $num <= 12; $num++)
            {
                $rd = $r->get_race_data($race_date, $p[0]->place_id, $num) ?? [];
                
                $csv_name = "race_" .$num. ".csv";
                $race_card[$place][$num] = $this->_get_race_csv($mp_libs->get_csv($race_date, $place, $csv_name), $rd);
            }
            $race_card[$place]['mcc'] = MoistureContentAndCushion::where(['place_id' => $p[0]->place_id, 'race_date' => $race_date])->first();
        }

        return $race_card;
    }

    /**
     * レースカードデータを取得
     * @param array $csv
     * @param object $race
     * @return array $rece_data
     */
    private function _get_race_csv($csv, $race)
    {
        foreach ($csv as $c)
        {
            if ($c[0] == "馬名")
            { 
                continue;
            }
            $rece_data = [
                'race_name' => $c[38], // レース名
                'distance' => $c[12], // 距離
                'turf_dart' => $c[13], // 芝/ダ
                'bias' => $c[34], // バイアス(良/稍重/重)
                'pace' => $race->race_mark_2 ?? $c[36], // ペース
                'race_level' => $race->race_mark_1 ?? '未設定', // R-level
            ];
            break;
        }
        return $rece_data;
    }

    /**
     * 取得したディレクトリを表示用にフォーマット
     * @param object $mplibs
     * @return array $format_dir_list
     */
    private function get_race_card_csv($mplibs)
    {
        $dir_name = $mplibs->get_date_place_list();

        $format_dir_list = [];

        foreach ($dir_name as $dir)
        {
            $format_dir_list[$dir[1]][] = $dir[2];
        }

        return $format_dir_list;
    }
}
