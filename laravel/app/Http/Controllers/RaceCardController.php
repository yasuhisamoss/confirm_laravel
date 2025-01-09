<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hanro;
use App\Models\Wood;
use App\Models\MoistureContentAndCushion;
use App\Models\Place;
use App\Models\Corse;
use App\Models\HorseRaceHistory;
use App\Models\Jockey;
use App\Models\Race;
use App\Models\RaceSchedule;
use App\Models\Stallion;
use App\Libs\PointLibs;

class RaceCardController extends Controller
{
    /**
     * 出馬表データ
     * @param int $race_date レース日
     * @param string  $place レース場所
     * @param int $num レース番号
     */
    public function index($race_date, $place, $num, Request $request)
    {
        $mp_libs = new \MpLibs();
        $p = Place::where('place_code_name', $place)->get();
        $mcc = MoistureContentAndCushion::where(['place_id' => $p[0]->place_id, 'race_date' => $race_date])->get();
        list($race_data, $race_card, $corse_info, $ranking_summary) = $this->get_csv_data($mp_libs, $race_date, $place, $num, $p, $mcc[0] ?? null);
        
        $rs = new RaceSchedule();
        $same_schedule = $rs->get_same_schedule_place($race_date);

        return view('race_card', [
            'page_info' => ["race_date" => $race_date, "place" => $place, "num" => $num],
            'race_data' => $race_data,
            'race_card' => $race_card,
            'corse_info' => $corse_info,
            'same_schedule' => $same_schedule,
            'ranking_summary' => $ranking_summary,
            'h' => $request["h"] ?? 0
        ]);
        
        return view('race_card');
    }

    /**
     * race_*.csvからデータを取得
     * @param object $mp_libs
     * @param int $race_date レース日
     * @param string  $place レース場所
     * @param int $num レース番号
     * @param object $mcc
     * @param object $p Placesテーブルデータ
     * @return array
     */
    private function get_csv_data($mp_libs, $race_date, $place, $num, $p, $mcc)
    {
        $race_data = [];
        $race_card = [];
        $csv_name = "race_" .$num. ".csv";
        $csv = $mp_libs->get_csv($race_date, $place, $csv_name);
        $hrh = new HorseRaceHistory();

        foreach ($csv as $c)
        {
            if (!is_numeric($c[1])) continue;

            $race_id = substr($c[9], 0, 16);

            $hanro = new Hanro();
            $hanro_data = $hanro->get_target_date_range($c[1], $mp_libs->format_date($race_date, 14), $race_date);

            $wood = new Wood();
            $wood_data = $wood->get_target_date_range($c[1], $mp_libs->format_date($race_date, 14), $race_date);

            if (empty($race_data))
            {
                $corse = Corse::where(['place_id' =>  $p[0]->place_id, 'track_type' =>$mp_libs->convert_track_type($c[13]), 'distance' => $c[12]])->get();
                $corse_rack_h9 = $hrh->get_target_corse_point(1, $corse[0]->corse_id, $c[22]);

                // カード情報にバイアスがまだ無いとき含水率テーブルのバイアスを取る
                if ($mp_libs->convert_track_type($c[13]) == 1)
                {
                    $mcc_bias = \MpConsts::TRACK_BIAS_CODE[$mcc->turf_track_bias];
                } 
                elseif ($mp_libs->convert_track_type($c[13]) == 2)
                {
                    $mcc_bias = \MpConsts::TRACK_BIAS_CODE[$mcc->dart_track_bias];
                }
                elseif ($mp_libs->convert_track_type($c[13]) == 3)
                {
                    $mcc_bias = \MpConsts::TRACK_BIAS_CODE[$mcc->turf_track_bias];
                }
                elseif ($mp_libs->convert_track_type($c[13]) == 4)
                {
                    $mcc_bias = \MpConsts::TRACK_BIAS_CODE[$mcc->dart_track_bias];
                }
            
                $r = new Race();
                $corse_info = $r->get_target_corse_info($corse[0]->corse_id);
                $race_data = [
                    "race_id" => $race_id,
                    "race_num" => $num,
                    "race_name" => isset($c[38]) ? $c[38] : "",
                    "distance" => $c[12] ?? 0,
                    "place" => $c[10],
                    "place_id" => $p[0]->place_id,
                    "corse_id" => $corse[0]->corse_id,
                    "corner_direction" => $p[0]->corner_direction,
                    "corner_count" => $corse[0]->corner_count,
                    "corner_level" => $corse[0]->corner_level,
                    "corner_level_point" => $corse[0]->corner_level_point,
                    "track" => $c[13],
                    "track_code" => $mp_libs->convert_track_type($c[13]),
                    "track_bias" => !empty($c[34]) ? $c[34] : $mcc_bias,
                    "class_name" => $c[11],
                    "class_code" => $c[22],
                    "joken" => $c[21],
                    "race_mark_1" => $c[35] ?? "",
                    "race_mark_2" => !empty($c[36]) ? $c[36] : $corse_info[0]->race_mark_2,
                    "race_mark_3" => $c[37] ?? "",
                    "turf_moisture_content" => $mcc["turf_moisture_content"] ?? 0,
                    "dart_moisture_content" => $mcc["dart_moisture_content"] ?? 0,
                    "cushion" => $mcc["cushion"] ?? 0,
                    "h9ave" => $corse_rack_h9[0]->h9ave ?? 0,
                    "h9min" => $corse_rack_h9[0]->h9min ?? 0,
                    "h9max" => $corse_rack_h9[0]->h9max ?? 0,
                    "h9count" => $corse_rack_h9[0]->h9count ?? 0,
                ];
            }

            // 馬別のレースヒストリを取得
            $hrh_data = $hrh->serach_race_history($c[1], $race_date);
            
            // 父IDを取る
            $father_data = Stallion::where(['stallion_name' =>  $c[23]])->get();
            //dd( $hrh_data);
            $ago_race_body_weight = isset($hrh_data[0]) ? $hrh_data[0]->body_weight : 0;
            $ago_race_comment = isset($hrh_data[0]->race_comment) ? json_decode($hrh_data[0]->race_comment) : [];
            if (isset($ago_race_comment->comment))
            {
                $ago_race_comment = $ago_race_comment->comment;
            }
            else
            {
                $ago_race_comment = "";
            }

            $ago_kol_comment = isset($hrh_data[0]->kol_comment) ? $hrh_data[0]->kol_comment : "";
            $date_diff = isset($hrh_data[0]) ? ceil(((strtotime($race_date) - strtotime($hrh_data[0]->race_date)) /86400) / 7) : 0;

            // ジョッキーのランクを取る
            $jocokey_data = Jockey::where('jockey_id', $c[16])->first();

            $kasoku_mark = $mp_libs->get_kasoku_mark($hanro_data[0] ?? [], $wood_data[0] ?? []);

            //$corse_id, $bias, $cushion
            $race_card[$c[14]] = [
                "horse_name" => $c[0],
                "mark_1" => $c[2],
                "mark_2" => $c[3],
                "mark_3" => $c[4],
                "mark_4" => $c[5],
                "mark_5" => $kasoku_mark,//$c[6],
                "mark_6" => $c[7],
                "date_diff" => $date_diff,
                "horse_id" => $c[1],
                "body_weight" => !empty($c[19]) ? $c[19] : $ago_race_body_weight,
                "weight" => $c[28],
                "sex" => $c[31],
                "ninki" => $c[30] ?? 0,
                "odds" => $c[33] ?? 0,
                "rank" => $c[27] ?? '-',
                "track_bias" => !empty($c[34]) ? $c[34] : $mcc_bias,
                "horse_comment" => $c[26],
                "is_blinker" => $c[18],
                "jockey" => $c[15],
                "jockey_id" => $c[16],
                "jockey_rank" => isset($jocokey_data) ? $jocokey_data->jockey_rank : 0,
                "father" => $c[23],
                "father_id" => $father_data[0]->stallion_id,
                "grandfather" => $c[24],
                "grandfather_id" => "",
                "card_comment" => $c[29],
                "ago_race_comment" => $ago_race_comment,
                "ago_kol_comment" => $ago_kol_comment,
                "hanro_point" => isset($hanro_data[0]) ? $hanro_data[0]->point : 0,
                "wood_point" => isset($wood_data[0]) ? $wood_data[0]->point : 0,
            ];
            $race_card[$c[14]]["hande_weight_per"] = "初";
            if (!empty($race_card[$c[14]]["body_weight"])) 
            {
                $race_card[$c[14]]["hande_weight_per"] = round(((float)$race_card[$c[14]]["weight"]/$race_card[$c[14]]["body_weight"])*100, 1);
            }
            // card_commentの文字列を判断して初ブリンカーを判別する
            $race_card[$c[14]] = $this->_search_comment($race_card[$c[14]]);

            // pointをセット
            $race_card[$c[14]] = $this->set_point($race_data, $race_card[$c[14]], $hrh_data);

            // 条件のあう種牡馬ポイントを取得
            $race_card[$c[14]]['stallion_point'] = $mp_libs->get_stallion_point(
                $father_data[0]->stallion_id,
                $corse[0]->corse_id,
                $c[13],
                [1 => $mcc->turf_track_bias, 2 => $mcc->dart_track_bias, 3 => $mcc->turf_track_bias, 4 => $mcc->dart_track_bias],
                $mcc["cushion"]
            );
        }
        $pl = new PointLibs($race_data);
        $race_card = $pl->set_point_rank($race_card);

        $race_card = $mp_libs->set_race_mark_point($race_card);

        $rank_sammary = $pl->get_point_rank_summary($race_card);
        return [$race_data, $race_card, $corse_info, $rank_sammary];
    }

    /**
     * 今走情報を分解してブリンカーと乗り替わりをおのおのの配列に詰める
     * @param array $race_card
     * @return $race_card 初ブリンカーと乗り替わり情報を詰めた出馬表
     */
    private function _search_comment($race_card)
    {
        // 初ブリンカー判定
        if (str_contains($race_card["card_comment"], '今走ブリンカー'))
        {
            //dump($race_card["card_comment"]);
            $race_card["mark_6"] = "初";
        }
        
        // 乗り替わり情報をコメントに詰める
        if (str_contains($race_card["card_comment"], '乗り替り'))
        {
            preg_match('/乗り替り\(.+?\)/', $race_card["card_comment"], $match);
            //乗り替り(江田照男→田辺裕信)
            $race_card["horse_comment"] .= $match[0];
        }
        
        return $race_card;
    }

    /**
     * ポイント情報を取得して出馬表配列にマージ
     * @param array array $race_data レース情報
     * @param array array $race_card 出馬表
     * @param array array $hrh_data ヒストリデータ
     * @return array ポイントデータを詰めた出馬表
     */
    private function set_point($race_data, $race_card, $hrh_data)
    {
        $pl = new PointLibs($race_data);
        $point_data = $pl->analyze_point($hrh_data);
        return array_merge($race_card, $point_data);
    }
}
