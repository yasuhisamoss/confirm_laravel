<?php

namespace App\Libs;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Storage;

class PointLibs extends Facade
{
    private $target_date_time;
    private $place;
    private $race_num;
    private $track_bias = NULL;
    private $cushion = NULL;
    private $moisture_content;
    private $track_type;
    private $distance;
    private $distance_s;
    private $distance_e;
    private $pace;
    private $class_code;
    private $corner_type;
    private $h9ave;
    private $h9min;
    private $h9max;
    private $h9count;
    
    const POINT_ANALYZE_TYPE = [
        'point' => [],
        'match' => [],
        'point_all' => [],
        'previous_race_point' => [],
        'same_course_race_point' => [],
        'same_distance_point' => [],
        'same_pace_point' => [],
        'cushion_pace_point' => [],
        'track_bias_race_point' => [],
        'corner_type_race_point' => [],
        'moisture_content' => [],
        //‘slope_race_point' => [],
        //‘straight_type_race_point' => [],
        //'corner_size_race_point' => [],
    ];

    const ANALYZE_CONDITION_ARRAY = [
        'track_type' => 0,
        'track_bias' => 0,
        'cushion' => 0,
        'moisture_content' => 0,
        'distance' => 0,
        'distance_s' => 0,
        'distance_e' => 0,
        'corner_type' => 0,
        'corner_count' => 0,
        'pace' => 0,
        'class_code' => 0,
        'loop_count' => 0,
        'place' => ""
    ];

    const BASE_POINT = 50;

    /**
     * 当日のレース情報をClass変数に詰める
     * @param array $race_data
     */
    public function __construct($race_data)
    {
        // 距離
        $this->distance = (int)$race_data["distance"];
        $this->distance_s = $race_data["distance"] - 200;
        $this->distance_e = $race_data["distance"] + 200;

        // Track,含水率、クッション
        if ($race_data["track"] == '芝')
        {
            $this->track_type = 1;
            $this->moisture_content = $race_data["turf_moisture_content"];
            $this->cushion = $race_data["cushion"];
        }
        else if ($race_data["track"] == 'ダート')
        {
            $this->track_type = 2;
            $this->moisture_content = $race_data["dart_moisture_content"];
        }
        else if ($race_data["track"] == '障害・芝')
        {
            $this->track_type = 3;
            $this->moisture_content = $race_data["turf_moisture_content"];
            $this->cushion = $race_data["cushion"];
        }
        else if ($race_data["track"] == '障害・ダ')
        {
            $this->track_type = 4;
            $this->moisture_content = $race_data["dart_moisture_content"];
            $this->cushion = $race_data["cushion"];
        }

        // Track_bias
        if ($race_data["track_bias"] == '良')
        {
            $this->track_bias = 1;
        }
        else if ($race_data["track_bias"] == '稍')
        {
            $this->track_bias = 2;
        }
        else if ($race_data["track_bias"] == '重')
        {
            $this->track_bias = 3;
        }
        else if ($race_data["track_bias"] == '不')
        {
            $this->track_bias = 4;
        }

        // corner_type
        $this->corner_type = $race_data["corner_direction"];
        $this->corner_count = $race_data["corner_count"];
        $this->corner_level = $race_data["corner_level"];
        $this->corner_level_point = $race_data["corner_level_point"];
        // pace
        $this->pace = trim($race_data["race_mark_2"]) ?? '';
        // ClassCode
        $this->class_code = $race_data["class_code"];
        // 競馬場
        $this->place = $race_data["place"];
        // コースID（障害戦で使う予定）
        $this->corse_id = $race_data["corse_id"];
        // Corse + Class_codeの補９の平均、MAX,MINを入れ込む
        $this->h9ave = $race_data["h9ave"] ?? 0;
        $this->h9min = $race_data["h9min"] ?? 0;
        $this->h9max = $race_data["h9max"] ?? 0;
        $this->h9count = $race_data["h9count"] ?? 0;
    }

    /**
     * ポイントをつけるためのレース情報解析
     * @param array $hrh_data ヒストリデータ
     * @return array $point_data ポイントデータ
     */
    public function analyze_point($hrh_data)
    {
        // point種別ごとのコンディションを取得する
        $condition_list = $this->_set_condition();
        $target_race_data = [];
        $point_data = [];

        foreach ($condition_list as $condition_name => $condition)
        {
            $target_race_data[$condition_name] = [];

            foreach ($hrh_data as $hrh)
            {
                // 補９が取れない（競走中止、海外等）
                if (!$hrh->hosei_9)
                {
                    continue;
                }
                if (!$this->_check_analyze($condition, $hrh))
                {
                    //dump($condition_name);
                    // 条件外ならコンティニュー
                    continue;
                }
                $target_race_data[$condition_name][] = $hrh;
                // 設定したLOOP数になったらBreak
                if (count($target_race_data[$condition_name]) >= $condition['loop_count']) break;
            }

            // todo ここで障害戦と分ける
            $point_data[$condition_name] = $this->_get_race_time_ave($target_race_data[$condition_name]);
            $point_data[$condition_name]['front'] = $this->_put_in_front_point($target_race_data[$condition_name]);
            $point_data[$condition_name]['last_3f_p'] = $this->_end_3f_point($target_race_data[$condition_name]);
            $point_data[$condition_name]['wpp'] = $this->_winning_position_point($target_race_data[$condition_name]);
            //exit;
        }
        return $point_data;
    }

    /**
     * 6番手以内の好ポジでのポイントをつける
     * @param array $race レース単位の情報
     * @return float
     */
    private function _winning_position_point($race)
    {
        if (empty($race)) return 0;
        $loop_count = 0;
        $position_point_sum = 0;
        foreach ($race as $no => $r)
        {
            $position_point = 0;
            // 4角ポジションポイント
            if ($r->passing_rank_4 <= 6 && $r->passing_rank_4 >= 4)
            {
                $position_point += 3;
            }
            else if ($r->passing_rank_4 <= 3 && $r->passing_rank_4 >= 2)
            {
                $position_point += 4;
            }
            else if ($r->passing_rank_4 == 1)
            {
                $position_point += 1;
            }

            // 3角ポジションポイント
            if ($r->passing_rank_3 <= 6 && $r->passing_rank_3 >= 4)
            {
                $position_point += 1;
            }
            else if ($r->passing_rank_3 <= 3 && $r->passing_rank_3 >= 2)
            {
                $position_point += 2;
            }
            else if ($r->passing_rank_3 == 1)
            {
                $position_point += 1;
            }

            // 3角からの押し上げポイント
            if ($r->passing_rank_3 < $r->passing_rank_4)
            {
                $position_point += 2;
            }

            $text = '3k_posi:' . $r->passing_rank_3;
            $text .= ' 4k_posi:' . $r->passing_rank_4;
            $text .= ' result:' . $r->result_rank;
            $text .= ' last_3f_sa:' . $r->last_3f_sa;
            $text .= ' chakusa:' . $r->chakusa;

            $position_point_sum += $position_point;
            $loop_count++;
        
        }
        return round($position_point_sum / $loop_count, 1);
        //return $point;
    }

    /**
     * 各ポイントのランク付けをする
     * @param array $race_card_data カード配列
     * @return array $race_card_data ランクをいれたカード配列
     */
    public function set_point_rank($race_card_data)
    {
        $analyze_type_list = self::POINT_ANALYZE_TYPE;
                
        foreach ($race_card_data as $no => $card)
        {
            foreach ($analyze_type_list as $point_key_name => $val)
            {
                $card[$point_key_name]['no'] = $no;
                $analyze_type_list[$point_key_name][$no] = $card[$point_key_name];
            }
        }

        $mp_libs = new \MpLibs();
        $rank_sum = [];
        foreach ($analyze_type_list as $key => $point_rank_list)
        {
            $rank = $mp_libs->sort_by_key('point', SORT_DESC, $point_rank_list);
            $ho_9_rank = $mp_libs->sort_by_key('h9_ave', SORT_DESC, $point_rank_list);

            // ポイントのランクを取る
            foreach ($rank as $k => $r)
            {
                if (!isset($rank_sum[$r['no']]))
                {
                    $rank_sum[$r['no']] = $k + 1;
                }
                else
                {
                    $rank_sum[$r['no']] += $k + 1;
                }
                $race_card_data[$r['no']][$key]['rank'] = $k + 1;
            }

            // 補正タイムのランクを取る
            foreach ($ho_9_rank as $k => $r)
            {
                $race_card_data[$r['no']][$key]['ho_9_rank'] = $k + 1;
            }
        }
        foreach ($rank_sum as $n => $v) 
        {
            $race_card_data[$n]['rank_sum'] = $v;
        }

        return $race_card_data;
    }

    /**
     * 各種ポイントのランキング指定順位までをサマって配列に格納
     * 
     * @param array $race_card 集計済カードデータ
     * @return array サマったあとのランキングデータ配列
     */
    public function get_point_rank_summary($race_card)
    {
        $mp_libs = new \MpLibs();
        $rank_summary_list = [
            "ho_9" => [],
            "hanro" => [],
            "wood" => [],
            "point" => [],
            "match" => [],
            "cushion" => [],
            "bias" => [],
            "stallion" => [],
            "jockey" => [],
            "mop" => [],
        ];

        $ho9_summary = [];
        $hanro_summary = [];
        $wood_summary = [];
        $point_summary = [];
        $match_summary = [];
        $cushion_summary = [];
        $bias_summary = [];
        $stallion_summary = [];
        $jockey_summary = [];
        $mop_summary = [];
        foreach ($race_card as $no => $card)
        {  
            $ho9_summary[$no] = [
                "no" => $no,
                "name" => $card["horse_name"],
                "point" => max([$card["point"]["h9_ave"], $card["same_distance_point"]["h9_ave"]]),
                "rank" => $card["rank"],
            ];
            $hanro_summary[$no] = [
                "no" => $no,
                "name" => $card["horse_name"],
                "point" => $card["hanro_point"],
                "rank" => $card["rank"],
            ];
            $wood_summary[$no] = [
                "no" => $no,
                "name" => $card["horse_name"],
                "point" => $card["wood_point"],
                "rank" => $card["rank"],
            ];
            $point_summary[$no] = [
                "no" => $no,
                "name" => $card["horse_name"],
                "point" => $card["point"]["point"],
                "rank" => $card["rank"],
            ];
            $match_summary[$no] = [
                "no" => $no,
                "name" => $card["horse_name"],
                "point" => $card["match"]["point"],
                "rank" => $card["rank"],
            ];
            $cushion_summary[$no] = [
                "no" => $no,
                "name" => $card["horse_name"],
                "point" => $card["cushion_pace_point"]["point"],
                "rank" => $card["rank"],
            ];
            $bias_summary[$no] = [
                "no" => $no,
                "name" => $card["horse_name"],
                "point" => $card["track_bias_race_point"]["point"],
                "rank" => $card["rank"],
            ];
            $stallion_summary[$no] = [
                "no" => $no,
                "name" => $card["horse_name"],
                "target_name" => $card["father"],
                "point" => $card["stallion_point"],
                "rank" => $card["rank"],
            ];
            $jockey_summary[$no] = [
                "no" => $no,
                "name" => $card["horse_name"],
                "target_name" => $card["jockey"],
                "point" => $card["jockey_rank"],
                "rank" => $card["rank"],
            ];
            $mop_summary[$no] = [
                "no" => $no,
                "name" => $card["horse_name"],
                "point" => $card["race_mark_point"]["point"],
                "rank" => $card["rank"],
            ];
        }

        $summary_count = 8;

        $ho9_summary = $mp_libs->sort_by_key('point', SORT_DESC, $ho9_summary);
        $rank_summary_list["ho_9"] = array_slice($ho9_summary, 0, $summary_count);

        $hanro_summary = $mp_libs->sort_by_key('point', SORT_DESC, $hanro_summary);
        $rank_summary_list["hanro"] = array_slice($hanro_summary, 0, $summary_count);

        $wood_summary = $mp_libs->sort_by_key('point', SORT_DESC, $wood_summary);
        $rank_summary_list["wood"] = array_slice($wood_summary, 0, $summary_count);

        $point_summary = $mp_libs->sort_by_key('point', SORT_DESC, $point_summary);
        $rank_summary_list["point"] = array_slice($point_summary, 0, $summary_count);

        $match_summary = $mp_libs->sort_by_key('point', SORT_DESC, $match_summary);
        $rank_summary_list["match"] = array_slice($match_summary, 0, $summary_count);

        $cushion_summary = $mp_libs->sort_by_key('point', SORT_DESC, $cushion_summary);
        $rank_summary_list["cushion"] = array_slice($cushion_summary, 0, $summary_count);

        $bias_summary = $mp_libs->sort_by_key('point', SORT_DESC, $bias_summary);
        $rank_summary_list["bias"] = array_slice($bias_summary, 0, $summary_count);

        $stallion_summary = $mp_libs->sort_by_key('point', SORT_DESC, $stallion_summary);
        $rank_summary_list["stallion"] = array_slice($stallion_summary, 0, $summary_count);
  
        $jockey_summary = $mp_libs->sort_by_key('point', SORT_DESC, $jockey_summary);
        $rank_summary_list["jockey"] = array_slice($jockey_summary, 0, $summary_count);

        $mop_summary = $mp_libs->sort_by_key('point', SORT_DESC, $mop_summary);
        $rank_summary_list["mop"] = array_slice($mop_summary, 0, $summary_count);

        return $rank_summary_list;
    }

    /**
     * １つ目のpassing_rankとPCIを見て前進気勢のポイントをつける
     * @param object $race レース単位の情報
     * @return float 
     */
    private function _put_in_front_point($race)
    {
        $rank_pl = [
            1 => 12,
            2 => 10,
            3 => 10,
            4 => 8,
            5 => 6,
            6 => 5,
            7 => 3,
            8 => 2,
            9 => 1,
            10 => 0,
            11 => 0,
            12 => 0,
            13 => 0,
            14 => 0,
            15 => 0,
            16 => 0,
            17 => 0,
            18 => 0,
        ];

        // レース情報がない
        if (!count($race))
        {
            return 0;
        }

        $pl1_sum = 0;
        foreach ($race as $pl)
        {
            // レースに出たがリザルトがない（競争中止など）
            if ($pl->result_rank == 0)
            {
                continue;
            }

            $passing_rank = $pl->passing_rank_1;
            if ($pl->passing_rank_1 == 0 && $pl->passing_rank_2 != 0)
            {
                $passing_rank = $pl->passing_rank_2;
            }
            if ($passing_rank == 0 && $pl->passing_rank_3 != 0)
            {
                $passing_rank = $pl->passing_rank_3;
            }
            if ($passing_rank == 0 && $pl->passing_rank_4 != 0)
            {
                $passing_rank = $pl->passing_rank_4;
            }
            if ($passing_rank == 0)
            {
                $passing_rank = 18;
            }

            $alaryze_pci = (($pl->rpci - $pl->pci) * 0.6) + $rank_pl[$passing_rank];
            $pl1_sum += $alaryze_pci;
        }
        return round($pl1_sum/count($race), 2);
    }

    /**
     * 3F_rankとPCIを見て3Fのポイントをつける
     * @param object $race レース単位の情報
     * @return float 
     */
    private function _end_3f_point($race)
    {
        $rank_pl = [
            1 => 12,
            2 => 10,
            3 => 10,
            4 => 8,
            5 => 6,
            6 => 5,
            7 => 3,
            8 => 2,
            9 => 1,
            10 => 0,
            11 => 0,
            12 => 0,
            13 => 0,
            14 => 0,
            15 => 0,
            16 => 0,
            17 => 0,
            18 => 0,
        ];

        if (!count($race))
        {
            return 0;
        }

        $mp_libs = new \MpLibs();

        $pl1_sum = 0;
        foreach ($race as $pl)
        {
            // レースに出たがリザルトがない（競争中止など）
            if ($pl->result_rank == 0)
            {
                continue;
            }
            if ($pl->last_3f_rank == 0)
            {
                continue;
            }
 
            //dd($pl);
            $fup = $pl->last_3f_sa - $pl->chakusa;
            //$pci = $pl->pci; // 自分のPCI
            //$rpc = $pl->rpci; // 自分のPCI
            //$pl->passing_rank_4; // 最終測定時の順位
            //$pl->last_3f; // 3F TIME
            // $pl->last_3f_sa - $pl->chakusa 3f時点からゴールまでで詰めた秒数
            // $pl->race_mark_1 R-高、R-普、R-低
            // $pl->race_mark_2 SL3F など
            $clincher_type = $mp_libs->add_clincher_type(
                [],
                $pl->passing_rank_1,
                $pl->passing_rank_2,
                $pl->passing_rank_3,
                $pl->passing_rank_4,
                $pl->result_rank,
                $pl->entry_count
            );
            //dump(key($clincher_type));

            $clincher_name = key($clincher_type);
            
            $text = "clincher=".$clincher_name;
            $text .= " marl=".$pl->race_mark_2;
            $text .= " rank=".$pl->result_rank;
            $text .= " 3f=".$pl->last_3f_sa;
            $text .= " chakusa=".$pl->chakusa;
            $text .= " fup=".$pl->last_3f_sa - $pl->chakusa;
            $text .= " posi=".$pl->passing_rank_1."-".$pl->passing_rank_2."-".$pl->passing_rank_3."-".$pl->passing_rank_4;
            $text .= " pci=".$pl->pci;
            $text .= " rpci=".$pl->rpci;

            $race_level_point = [
                'R-高' => 1.08,
                'R-普' => 1.0,
                'R-低' => 0.92,
            ];
            $rlp = $race_level_point[$pl->race_mark_2] ?? 1;

            $base_point = 10;
            if ($pl->chakusa <= 0)
            {
                $base_point += 3;
            }
            else if ($pl->chakusa > 0 && $pl->chakusa <= 0.2)
            {
                $base_point += 2;
            }
            else if ($pl->chakusa > 0.2 && $pl->chakusa <= 0.5)
            {
                $base_point += 1;
            }
            else if ($pl->chakusa > 0.5 && $pl->chakusa <= 1)
            {
                $base_point -= 3;
            }
            else if ($pl->chakusa > 1)
            {
                $base_point -= 5;
            }

            if ($fup >= 1)
            {
                $base_point += 3;
            }
            else if ($fup < 1 && $fup >= 0.5)
            {
                $base_point += 3;
            }
            else if ($fup < 0.5 && $fup >= 0)
            {
                $base_point += 2;
            }
            else if ($fup < 0 && $fup >= -0.3)
            {
                $base_point += 1;
            }
            else if ($fup > -0.3 && $fup <= -0.6)
            {
                $base_point -= 1;
            }
            else if ($fup > -0.6 && $fup <= -1)
            {
                $base_point -= 2;
            }
            else if ($fup > -1)
            {
                $base_point -= 3;
            }
            $pl1_sum += $base_point * $rlp;

//            $alaryze_pci = (($pl->rpci - $pl->pci) * 0.7) + $rank_pl[$pl->last_3f_rank];
//            $pl1_sum += $alaryze_pci;
        }

        return round($pl1_sum/count($race), 2);
    }

    /**
     * 条件に沿って取れたレースデータの数値の平均なんかをとる
     * todo 障害戦と分ける
     * @param array $race_list
     * @param array 平均なんかをまとめた配列
     */
    private function _get_race_time_ave($race_list)
    {
        $analyze_list = [
            "point" => self::BASE_POINT,
            "h9_ave" => 0,
            "chakusa_ave" => 0,
            "bt_sa_ave" => 0,
        ];

        if (empty($race_list))
        {
            return $analyze_list;
        }

        $loop_count = count($race_list);
        $h9_sum = 0;
        $chakusa_sum = 0;
        $bt_sa_sum = 0;
        $point_sum = 0;

        foreach ($race_list as $race)
        {
            $h9_sum += $race->hosei_9;
            // time
            $bt_sa_sum  += $race->result_time - $race->base_time;
            // ３f差 - 着差
            $chakusa_sum += $race->chakusa;
            // レースごとのポイントを算出
            $point = $this->_set_point($race);

            $point_sum += $point;
//        dd($race);
        }

        $analyze_list['h9_ave'] = round($h9_sum / $loop_count, 2);
        $analyze_list['chakusa_ave'] = round($chakusa_sum / $loop_count, 2);
        $analyze_list['bt_sa_ave'] = round($bt_sa_sum / $loop_count, 2);
        $analyze_list['point'] = round($point_sum / $loop_count, 2);
        return $analyze_list;
    }

    /**
     * レースごとのポイントを算出
     * todo 障害戦と分ける
     * @param object $race レース単位の情報
     * @return int レース情報集計後のポイント
     */
    private function _set_point($race)
    {
        $base_point = self::BASE_POINT;

        //$base_point += $this->_set_h9_ave_point($race->hosei_9);
        $base_point += $this->_chakusa_point($race->chakusa);
        $base_point += $this->_set_bt_ave_point($race->result_time - $race->base_time);

        $point = $this->_get_race_level_point($race, $base_point);

        return $point;
    }

    /**
     * レースのレベル、レースクラス、着順でレースポイントを査定
     * todo 障害戦と分ける
     * @param object $race レース単位の情報
     * @param int $point
     * @return int レース情報集計後のポイント
     */
    private function _get_race_level_point($race, $point)
    {
        // 着順ポイント
        $rank_point_array = [
            1 => 7,
            2 => 6,
            3 => 5,
            4 => 4,
            5 => 3,
            6 => 0,
            7 => 0,
            8 => 0,
            9 => 0,
            10 => -6,
            11 => -6,
            12 => -8,
            13 => -8,
            14 => -8,
            15 => -10,
            16 => -10,
            17 => -10,
            18 => -10,
        ];

        // レースレベル倍率査定
        $race_level_array = [
            "R-高" => 1.08,
            "L-高" => 1.08,
            "R-普" => 1,
            "L-普" => 1,
            "R-低" => 0.92,
            "L-低" => 0.92,
        ];
        // レースクラス倍率査定
        $class_code_array = [
            7 => 10, //未勝利
            11 => 10, //未出走
            15 => 10, //新馬
            19 => 15, //400万下
            23 => 15, //500万下
            39 => 20, //900万下
            43 => 20, //1000万下
            63 => 30, //1500万下
            67 => 30, //1600万下
            131 => 32, //重賞以外のオープン
            147 => 32, //グレード無し重賞
            163 => 35, //G3
            179 => 37, //G2
            195 => 40, //G1
        ];
        // レースレベル未査定の場合はとりあえず低で査定
        if (empty($race->race_mark_1))
        {
            $race->race_mark_1 = "R-普";
        }

        // クラスコード未設定（地方など）
        if (!isset($class_code_array[$race->class_code]))
        {
            $race->class_code = 7;
        }

        $rece_level_point = isset($race_level_array[$race->race_mark_1]) ? $race_level_array[$race->race_mark_1] : 1;

        return ($point + $rank_point_array[$race->result_rank] + $class_code_array[$race->class_code]) * $rece_level_point;
    }

    /**
     * 補正９でポイントをつける
     * @param int $h9_ave
     * @return int 補９の足し込みポイント
     */
    private function _set_h9_ave_point($h9_ave)
    {
        $h9_ave_diff = round($this->h9ave - $h9_ave, 2);

        if ($h9_ave_diff >= 0)
        {
            $point = 15;
        }
        else if ($h9_ave_diff < 0 && $h9_ave_diff  >= 2)
        {
            $point = 10;
        }
        else if ($h9_ave_diff < 2 && $h9_ave_diff  >= 5)
        {
            $point = 6;
        }
        else if ($h9_ave_diff < 5 && $h9_ave_diff  >= 10)
        {
            $point = 3;
        }
        else
        {
            $point = 0;
        }
        return $point;
    }

    /**
     * 着差でポイントをつける
     * @param int $chakusa_ave
     * @return int 着差の足し込みポイント
     */
    private function _chakusa_point($chakusa_ave)
    {
        if ($chakusa_ave <= -0.5)
        {
            $point = 12;
        }
        else if ($chakusa_ave > -0.5 && $chakusa_ave <= -0.2)
        {
            $point = 11;
        }
        else if ($chakusa_ave > -0.2 && $chakusa_ave <= 0)
        {
            $point = 10;
        }
        else if ($chakusa_ave <= 0)
        {
            $point = 10;
        }
        else if ($chakusa_ave > 0 && $chakusa_ave <= 0.1)
        {
            $point = 8;
        }
        else if ($chakusa_ave > 0.1 && $chakusa_ave <= 0.3)
        {
            $point = 6;
        }
        else if ($chakusa_ave > 0.3 && $chakusa_ave <= 0.5)
        {
            $point = 3;
        }
        else if ($chakusa_ave > 0.5 && $chakusa_ave <= 0.9)
        {
            $point = 2;
        }
        else
        {
            $point = 0;
        }
        return $point;
    }

    /**
     * ベースタイムと走破タイムの差でポイントをつける
     * @param int $bt_sa_ave
     * @return int ベースタイムの足し込みポイント
     */
    private function _set_bt_ave_point($bt_sa_ave)
    {
        if ($bt_sa_ave <= -0.2)
        {
            $point = 12;
        }
        else if ($bt_sa_ave <= 0)
        {
            $point = 10;
        }
        else if ($bt_sa_ave > 0 && $bt_sa_ave <= 0.1)
        {
            $point = 8;
        }
        else if ($bt_sa_ave > 0.1 && $bt_sa_ave <= 0.3)
        {
            $point = 6;
        }
        else if ($bt_sa_ave > 0.3 && $bt_sa_ave <= 0.5)
        {
            $point = 3;
        }
        else if ($bt_sa_ave > 0.5 && $bt_sa_ave <= 0.9)
        {
            $point = 2;
        }
        else
        {
            $point = 0;
        }
        return $point;
    }

    /**
     * setするポイントごとにコンディションを設定する
     * @return array condition_list
     */
    private function _set_condition()
    {
        $point_analyze_type = self::POINT_ANALYZE_TYPE;
        foreach ($point_analyze_type as $analyze_type => $tmp)
        {
            $contition = self::ANALYZE_CONDITION_ARRAY;
            switch($analyze_type)
            {
                case 'point_all':
                    // 全体で直近３レース
                    $contition['loop_count'] = 3;
                    break;
                case 'point':
                    // ベースポイント 距離とトラック
                    $contition['loop_count'] = 3;
                    $contition['distance_s'] = $this->distance_s;
                    $contition['distance_e'] = $this->distance_e;
                    $contition['track_type'] = $this->track_type;
                    break;
                case 'match':
                    // 距離とトラック,コーナー、含水、クッション
                    $contition['loop_count'] = 3;
                    $contition['distance_s'] = $this->distance_s;
                    $contition['distance_e'] = $this->distance_e;
                    $contition['track_type'] = $this->track_type;
                    $contition['corner_level'] = $this->corner_level;
                    $contition['corner_count'] = $this->corner_count;
                    //$contition['moisture_content'] = $this->moisture_content;
                    // ダートじゃなければクッション値追加
                    if ($this->track_type != 2)
                    {
                        $contition['cushion'] = $this->cushion;
                    }
                    //$contition['pace'] = $this->pace;
                    break;
                case 'previous_race_point':
                    // 全体で直近１レース
                    $contition['loop_count'] = 1;
                    break;
                case 'same_course_race_point':
                    // 競馬場とトラック
                    $contition['loop_count'] = 3;
                    $contition['place'] = $this->place;
                    $contition['track_type'] = $this->track_type;
                    break;
                case 'same_distance_point':
                    // 同一距離とトラック
                    $contition['loop_count'] = 3;
                    $contition['distance_s'] = $this->distance;
                    $contition['distance_e'] = $this->distance;
                    $contition['track_type'] = $this->track_type;
                    break;
                case 'same_pace_point':
                    // 同一ペース
                    $contition['loop_count'] = 3;
                    $contition['pace'] = $this->pace;
                    break;
                case 'cushion_pace_point':
                    // 近似クッションとトラック
                    $contition['loop_count'] = 3;
                    $contition['track_type'] = $this->track_type;
                    $contition['cushion'] = $this->cushion;
                    break;
                case 'track_bias_race_point':
                    // トラックバイアスとトラックと含水率
                    $contition['loop_count'] = 3;
                    $contition['track_type'] = $this->track_type;
                    $contition['track_bias'] = $this->track_bias;
                    //$contition['moisture_content'] = $this->moisture_content;
                    break;
                case 'corner_type_race_point':
                    // トラックとコーナー向き
                    $contition['loop_count'] = 3;
                    $contition['track_type'] = $this->track_type;
                    $contition['corner_type'] = $this->corner_type;
                    $contition['corner_count'] = $this->corner_count;
                    $contition['corner_level'] = $this->corner_level;
                    //$contition['corner_level_point'] = $this->corner_level_point;
                    // $contition['moisture_content'] = $this->moisture_content;
                    break;
                case 'moisture_content':
                    // トラックと含水率
                    $contition['loop_count'] = 3;
                    $contition['track_type'] = $this->track_type;
                    $contition['moisture_content'] = $this->moisture_content;
                    break;
            }
            $point_analyze_type[$analyze_type] = $contition;
        }
        //dd($point_analyze_type);
        return $point_analyze_type;
    }

    /**
     * コンディションに応じてチェックすべきレースかどうかを確認してフラグで返却
     * @param array $condition 解析する項目でのチェックリストの検索用データ
     * @param object $race ヒストリから引いたレースデータ
     * @return int $check_result 1:チェック対象 2:チェック非対象
     */
    private function _check_analyze($condition, $race)
    {
        $check_result = 1;
        if ($condition["track_type"] != 0 && $condition["track_type"] != $race->track_type)
        {
            $check_result = 0;
        }

        if ($condition["track_bias"] != 0 && $this->check_target_track_bias($race->track_bias, $condition["track_bias"]) == false)
        {
            $check_result = 0;
        }

        if ($condition["place"] != "" && $condition["place"] != $race->place_name)
        {
            $check_result = 0;
        }
        
        // TrackTypeでどちらの含水率を入れるか
        $moisture_content = $race->turf_moisture_content;
        if($race->track_type == 2)
        {
            $moisture_content = $race->dart_moisture_content;
        }

        if ($condition["moisture_content"] != 0 && $this->check_target_moisture_content($moisture_content, $condition["moisture_content"]) == false)
        {
            $check_result = 0;
        }

        if ($condition["cushion"] != 0 && $this->check_target_cushion($race->cushion, $condition["cushion"]) == false)
        {
            $check_result = 0;
        }

        // 距離チェック
        if (!empty($condition['distance_s']))
        {
            if ($race->distance >= $condition['distance_s'] && $race->distance <= $condition['distance_e'])
            {
                // マッチしたら何もしない
            }
            else
            {
                $check_result = 0;
            }
        }

        // レースペースのチェック
        if (!empty($condition["pace"]) && trim($race->race_mark_2) != $condition['pace'])
        {
            $check_result = 0;
        }

        // コーナーレベルのチェック
        if (!empty($condition["corner_level"]) && trim($race->corner_level) != $condition['corner_level'])
        {
            $check_result = 0;
        }

        // コーナータイプのチェック
/*        if (!empty($condition["corner_type"]) && trim($race->corner_direction) != $condition['corner_type'])
        {
            $check_result = 0;
        }
*/

        // コーナー数のチェック
/*        if (!empty($condition["corner_count"]) && trim($race->corner_count) != $condition['corner_count'])
        {
            $check_result = 0;
        }
*/
        // JSONでレースコメが設定されていてis_ignoreが１のものは除外
        if ($decode_comment = json_decode($race->race_comment))
        {
            if ($decode_comment->is_ignore == 1)
            {
                $check_result = 0;
            }
        }
        // rankがゼロ（競走中止は除外）
        if (!$race->result_rank)
        {
            $check_result = 0;
        }
        return $check_result;
    }

    /**
     * track_biasがセットされていた場合にチェックすべき馬場状態かどうか調べる
     * @param int $rece_cushion チェックするレースのtrack_bias値
     * @param int $cushion 今回のtrack_bias値
     * @return bool
     */
    private function check_target_track_bias($rece_track_bias, $track_bias)
    {
        $check_bias_array = [
            1 => [1],
            2 => [2,3],
            3 => [3,4],
            4 => [3,4],
        ];

        if (!$rece_track_bias)
        {
            return false;
        }

        if (!in_array($rece_track_bias, $check_bias_array[$track_bias]))
        {
            return false;
        }

        return true;
    }

    /**
     * cushionがセットされていた場合にチェックすべきクッションかどうか調べる
     * @param float $rece_cushion チェックするレースのクッション値
     * @param float $cushion 今回のクッション値
     * @return bool
     */
    private function check_target_cushion($rece_cushion, $cushion)
    {
        $min_cushion = $cushion - 0.5;
        $max_cushion = $cushion + 0.5;

        if (!$rece_cushion)
        {
            return false;
        }

        if ($min_cushion > $rece_cushion || $max_cushion < $rece_cushion)
        {
            return false;
        }

        return true;
    }

    /**
     * moisture_contentがセットされていた場合にチェックするかどうか調べる
     * @param float $rece_moisture_content チェックするレースの含水率
     * @param float $moisture_content 今回の含水率
     * @return bool
     */
    private function check_target_moisture_content($rece_moisture_content, $moisture_content)
    {
        $min_mc = $moisture_content - 2;
        $max_mc = $moisture_content + 2;

        if (!$rece_moisture_content)
        {
            return false;
        }

        if ($min_mc > $rece_moisture_content || $max_mc < $rece_moisture_content)
        {
            return false;
        }
        return true;
    }
}
