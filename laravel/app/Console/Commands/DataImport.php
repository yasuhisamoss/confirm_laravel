<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Place;
use App\Models\Corse;
use App\Models\Hanro;
use App\Models\Wood;
use App\Models\Jockey;
use App\Models\Trainer;
use App\Models\HorseRaceHistory;
use App\Models\Horse;
use App\Models\Race;
use App\Models\Stallion;

### php artisan app:data-import
class DataImport extends Command
{
    const MODE_POINT = 1;
    const MODE_WOOD = 2;
    const MODE_HANRO = 3;
    const MODE_HORSE = 4;
    const MODE_ALL = 9;

    const BASE_CHOKYO_POINT = 50;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:data-import {csv_type?} {datetime?} {place?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'データインポート';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // csv_type 1:race_history 2:hanro 3:wood 9:all 
        $csv_type = $this->argument('csv_type') ?? self::MODE_ALL;
        $datetime = $this->argument('datetime') ?? null;
        $place = $this->argument('place') ?? null;
        
        // app/Libs/MpLibs.phpからの読み込み
        $mp_libs = new \MpLibs();
        // input内のディレクトリリストを取得
        $target_csv = $this->_get_target_csv_data($mp_libs, $datetime, $place);

        if (empty($target_csv))
        {
            echo 'No match CSV_DATA ' . $datetime .' '. $place;
            exit;
        }

        if ($csv_type == self::MODE_ALL || $csv_type == self::MODE_POINT)
        {
            // datetime/place/XX.csvのデータを取得
            $this->_get_horse_history_csv($mp_libs, $target_csv);
        }
        if ($csv_type == self::MODE_ALL || $csv_type == self::MODE_HANRO)
        {
            // datetime/place/hanro_XX.csvのデータを取得
            $this->_get_hanro_csv($mp_libs, $target_csv);
        }
        if ($csv_type == self::MODE_ALL || $csv_type == self::MODE_WOOD)
        {
            // datetime/place/wood_XX.csvのデータを取得
            $this->_get_wood_csv($mp_libs, $target_csv);
        }
        if ($csv_type == self::MODE_ALL || $csv_type == self::MODE_HORSE)
        {
            // datetime/place/wood_XX.csvのデータを取得
            $this->_get_race_csv($mp_libs, $target_csv);
        }

    }

    /**
     * InputにあるCSVデータを取得
     * @param object $mp_libs mp_libsインスタンス 
     * @param int|null $datetime
     * @param string|null $place
     * @return array $target_csv_list 対象のCSVデータ
     */
    private function _get_target_csv_data($mp_libs, $datetime, $place)
    {
        // ディレクトリリストを取得
        $csv_dir = $mp_libs->get_date_place_list();

        $target_csv_list = [];
        // 日付も場所も指定がなければ全対象
        if (is_null($datetime) && is_null($place))
        {
            $target_csv_list = $csv_dir;
        } 
        else 
        {
            // どちらかに指定があれば比較して配列に詰める
            foreach ($csv_dir as $csv)
            {
                if (isset($datetime) && isset($place))
                {
                    if (str_contains($csv[1], $datetime) && $place == $csv[2])
                    {
                        $target_csv_list[] = $csv;
                    } 
                } 
                else if (isset($datetime) && str_contains($csv[1], $datetime))
                {
                    $target_csv_list[] = $csv;
                }
                else if (isset($place) && $place == $csv[2])
                {
                    $target_csv_list[] = $csv;
                }
            }
        }
        return $target_csv_list;
    }

    /**
     * datetime/place/race_XX.csvのデータを取得
     * @param object $mp_libs mp_libsインスタンス
     * @param array $target_csv 対象のCSVディレクトリ
     */
    private function _get_race_csv($mp_libs, $target_csv)
    {
        foreach ($target_csv as $csv)
        {
            $target_date = $csv[1];
            $place = $csv[2];
            // レース数で回す
            for ($i = 1; $i <= 12; $i++)
            {
                // レース番号からCSV名作成
                $csv_name = 'race_' . $i . '.csv';
                $csv_data = $mp_libs->get_csv($target_date, $place, $csv_name);
                if (!$csv_data) continue;

                foreach ($csv_data as $line)
                {
                    if (!is_numeric($line[1]))
                    {
                        continue;
                    }
                    // 種牡馬(父,)
                    $stallion_name = $line[23];//23:父
                    $ins_stallion = $this->_set_stallion($stallion_name);
                    $father_id = $ins_stallion->stallion_id;
                    // 種牡馬(母父)
                    $stallion_name_2 = $line[24];//24:母父
                    $ins_stallion_2 = $this->_set_stallion($stallion_name_2);
                    $grandfather_id = $ins_stallion_2->stallion_id;
                    $trainer_id = null;
                    // 坂路データからトレーナーIDを取ってみる
                    $hanro = Hanro::where(['horse_id' =>  $line[1]])->first();
                    $trainer_id = $hanro->trainer_id ?? null;
                    if (!$trainer_id)
                    {
                        // 坂路から取れなければウッドから取ってみる
                        $wood = Wood::where(['horse_id' =>  $line[1]])->first();
                        $trainer_id = $wood->trainer_id ?? null;
                        if (!$trainer_id)
                        {
                            // どっちからも取れなければ一旦諦める
                            continue;
                        }
                    }
                    $insert_string = 'insert horse ' . $line[0];
                    dump($insert_string);
                    $this->_set_horse($line[1], $line[0], $trainer_id, $father_id, $grandfather_id, $line[26]);
                }
            }
        }
    }

    /**
     * datetime/place/XX.csvのデータを取得
     * @param object $mp_libs mp_libsインスタンス
     * @param array $target_csv 対象のCSVディレクトリ
     */
    private function _get_horse_history_csv($mp_libs, $target_csv)
    {
        $tenki_code_list = \MpConsts::TENKI_CODE;
        $track_bias_list = \MpConsts::TRACK_BIAS_CODE;
        $horse_mark_1_list = \MpConsts::HORSE_MARK_1_LIST;
        $horse_mark_2_list = \MpConsts::HORSE_MARK_2_LIST;
        $horse_mark_3_list = \MpConsts::HORSE_MARK_3_LIST;
        $horse_mark_4_list = \MpConsts::HORSE_MARK_4_LIST;
        $horse_mark_5_list = \MpConsts::HORSE_MARK_5_LIST;
        $horse_mark_6_list = \MpConsts::HORSE_MARK_6_LIST;
        $horse_sex_list = \MpConsts::HORSE_SEX_LIST;
        $blinker_list = \MpConsts::BLINKER_LIST;

        foreach ($target_csv as $csv)
        {
            $target_date = $csv[1];
            $place = $csv[2];
            // レース数で回す
            for ($i = 1; $i <= 12; $i++)
            {
                // レース番号からCSV名作成
                $csv_name = $i.'.csv';
                $csv_data = $mp_libs->get_csv($target_date, $place, $csv_name);
                if (!$csv_data) continue;

                foreach ($csv_data as $line)
                {
                    // 13:芝・ダ 16:距離 0:年 1:月 2:日 5:場所 8:略レース名 9:レース名 10:クラス名
                    $race_name = empty($this->mbTrim($line[9])) ? $this->mbTrim($line[9]) : $this->mbTrim($line[8]);
                    // レース名がない場合クラス名を入れる
                    $race_name = !empty($race_name) ? $race_name : $this->mbTrim($line[10]);
                    $race_date =  '20' . $line[0] . $line[1] . $line[2];
                    // レースIDを使用する１６文字に詰める
                    $race_id = substr($line[33], 0, 16);
                    $debug_string = $race_date . " " . $line[5] . " " . $line[13] . $line[16] . " " . $race_name;
                    dump($debug_string);

                    // Prace
                    $ins_place = $this->_set_place($line[5]);// 14:競馬場
                    $place_id = $ins_place->place_id;

                    // corse
                    $track_code = \MpConsts::TRACK_CODE_TO_TRACK_TYPE[$line[14]];// 14:トラックコード
                    $distance = (int)$line[16];// 16:距離
                    $corner_count = (int)$line[15];// 15:コーナー回数
                    $ins_corse = $this->_set_corse($place_id, $track_code['track_type'], $distance, $track_code['corner_size'], $corner_count);
                    $corse_id = $ins_corse->corse_id;

                    // ジョッキー
                    $jockey_id = $line[65];//65:騎手コード
                    $jockey_name = $line[39];//39:騎手                    
                    $ins_jockey = $this->_set_jockey($jockey_id, $jockey_name);
                    $jockey_id = $ins_jockey->jockey_id;

                    // 調教師
                    $trainer_id = $line[66];//66:調教師コード
                    $trainer_name = $line[62];//62:調教師
                    $ins_trainer = $this->_set_trainer($trainer_id, $trainer_name);
                    $trainer_id = $ins_trainer->trainer_id;

                    // 種牡馬(父,)
                    $stallion_name = $line[77];//77:父
                    $ins_stallion = $this->_set_stallion($stallion_name);
                    $father_id = $ins_stallion->stallion_id;
                    // 種牡馬(母父)
                    $stallion_name_2 = $line[79];//79:母父
                    $ins_stallion_2 = $this->_set_stallion($stallion_name_2);
                    $grandfather_id = $ins_stallion_2->stallion_id;
                    // 馬データ

                    $horse_id = (int)trim($line[64]);
                    $horse_name = trim($line[36]);
                    $horse_comment = trim($line[82]);
                    $horse = $this->_set_horse($horse_id, $horse_name, $trainer_id, $father_id, $grandfather_id, $horse_comment);
                    
                    if (!is_float($line[24]))
                    {
                        $line[24] = 0.0;
                    }

                    // レース印１に間違ったデータが入っていたらカラにする
                    if (!in_array($line[22], ['R-高', 'R-普', 'R-低']))
                    {
                        $line[22] = '';
                    }

                    // レース情報
                    $race_data = [
                        'race_id' => $race_id, // 33:レースID新
                        'race_date' => $race_date,
                        'kaizi' => is_int($line[4]) ? $line[4] : 0, // 4:回次
                        'nichizi' => is_int($line[6]) ? $line[6] : 0, // 6:日次
                        'corse_id' => $corse_id,
                        'race_num' => $line[7], // 7:レース番号
                        'race_name' => $race_name,
                        'class_code' => $line[11], // 11:クラスコード
                        'track_bias' => array_search($line[18], $track_bias_list), // 18:馬場状態
                        'tenki' => array_search($line[19], $tenki_code_list), // 19:天候
                        'entry_count' => $line[20], // 20:頭数
                        'full_gate' => !empty($line[21]) ? $line[21] : 0, // フルゲート頭数
                        'race_mark_1' => trim($line[22]), // 22:レース印１
                        'race_mark_2' => trim($line[23]), // 23:レース印２
                        'race_mark_3' => !empty($this->mbTrim($line[24])) ? $this->mbTrim($line[24]) : 0.0, // 24:レース印３
                        'rpci' => !empty($line[25]) ? $line[25] : 0.0, // 25:RPCI
                        'base_time' => !empty($line[27]) ? $line[27] : 0.0,  // 27:基準タイム
                    ];

                    $this->_set_race($race_id, $race_data);
                    
                    if ($line[43] == '----')
                    {
                        $line[43] = 0.0;
                    }

                    if (!isset($line[83]))
                    {
                        $line[83] = '';
                    }

                    if (!isset($line[84]))
                    {
                        $line[84] = '';
                    }
                    // レースヒストリ
                    $race_history_data = [
                        'race_id' => $race_id, // 33:レースID新
                        'horse_id' => $horse_id,
                        'mark_1' => array_search($line[67], $horse_mark_1_list), // 67:馬印1
                        'mark_2' => array_search($line[68], $horse_mark_2_list), // 68:馬印2
                        'mark_3' => array_search($line[69], $horse_mark_3_list), // 69:馬印3
                        'mark_4' => array_search($line[70], $horse_mark_4_list), // 70:馬印4
                        'mark_5' => array_search($line[71], $horse_mark_5_list), // 71:馬印5
                        'mark_6' => array_search($line[72], $horse_mark_6_list), // 72:馬印6
                        'no' => trim($line[34]), // 34:馬番
                        'waku_no' => trim($line[35]), // 35:枠番
                        'sex' => array_search($line[37], $horse_sex_list), // 37:性別
                        'age' => trim($line[38]), // 38:年齢
                        'jockey_id' => $jockey_id, // 
                        'weight' => trim($line[40]), //40:斤量
                        'is_blinker' => array_search($line[41], $blinker_list), // 41:ブリンカー
                        'result_rank' => trim($line[42]), // 42:確定着順
                        'ninki' => !empty($line[44]) ? $line[44] : 0,// 44:人気
                        'odds' => !empty($line[45]) ? $line[45] : 0, // 45:単勝オッズ
                        'chakusa' => trim($line[43]), // 43:着差タイム
                        'result_time' => trim($line[46]), // 46:走破タイム
                        'hosei_9' => !empty($line[49]) ? $line[49] : 0, // 49:補９
                        'passing_rank_1' => !empty($line[50]) ? $line[50] : 0, // 50:通過1
                        'passing_rank_2' => !empty($line[51]) ? $line[51] : 0, //51:通過2
                        'passing_rank_3' => !empty($line[52]) ? $line[52] : 0, //52:通過3
                        'passing_rank_4' => !empty($line[53]) ? $line[53] : 0, //53:通過4
                        'clincher' => trim($line[54]), //54:脚質
                        'last_3f' => !empty($line[55]) ? $line[55] : 0, // 55:上がり３Fタイム
                        'last_3f_rank' => !empty($line[56]) ? $line[56] : 0, // 56:上がり３F順
                        'ave_3f' => !empty($line[57]) ? $line[57] : 0, // 57:AVE３F
                        'pci' => !empty($line[58]) ? $line[58] : 0, // 58:PCI
                        'last_3f_sa' => !empty($line[59]) ? $line[59] : 0, // 59:３F差
                        'body_weight' => !empty($line[60]) ? $line[60] : 0, // 60:馬体重
                        'fluctuation_weight' => !empty($line[61]) ? $line[61] : 0, // 61:増減
                        'race_comment' => trim($line[83]), // 83:レースコメ
                        'kol_comment' => trim($line[84]), //84:関係者コメ
                    ];
                    $this->_set_race_horse_history($race_id, $horse_id, $race_history_data);
                }
            }
        }
    }

    /**
     * xx.csvからHorseデータをセット
     * @param int $horse_id 64:血統番号
     * @param string $horse_name 36:馬名
     * @param string $trainer_id 調教師ID
     * @param int $father_id 父ID
     * @param int $grandfather_id 母父ID
     * @param string $horse_comment 82:馬コメ
     * @return object horse_data
     */
    private function _set_horse($horse_id, $horse_name, $trainer_id, $father_id, $grandfather_id, $horse_comment)
    {
        $horse = Horse::firstOrNew(['horse_id' => $horse_id]);

        if (!$horse->exists)
        {
            $data = [
                'horse_id' => $horse_id,
                'horse_name' => $horse_name,
                'trainer_id' => $trainer_id,
                'father_id' => $father_id,
                'grandfather_id' => $grandfather_id,
                'horse_comment' => $horse_comment,
            ];
            $horse->fill($data)->save();
        }
        else if (!$horse->horse_comment && !empty($horse_comment))
        {
            $horse->horse_comment = $horse_comment;
            $horse->save();
        }
        
        return $horse;
    }

    /**
     * xx.csvからHorseデータをセット
     * @param int $race_id race_id
     * @param array $race_data 作成したレースデータ
     * @return object race_data
     */
    private function _set_race($race_id, $race_data)
    {
/**
                        'race_id' => $race_id, // 33:レースID新
                        'race_date' => $race_date,
                        'kaizi' => is_int($line[4]) ? $line[4] : 0, // 4:回次
                        'nichizi' => is_int($line[6]) ? $line[6] : 0, // 6:日次
                        'corse_id' => $corse_id,
                        'race_num' => $line[7], // 7:レース番号
                        'race_name' => $race_name,
                        'class_code' => $line[11], // 11:クラスコード
                        'track_bias' => array_search($line[18], $track_bias_list), // 18:馬場状態
                        'tenki' => array_search($line[19], $tenki_code_list), // 19:天候
                        'entry_count' => $line[20], // 20:頭数
                        'full_gate' => !empty($line[21]) ? $line[21] : 0, // フルゲート頭数
                        'race_mark_1' => trim($line[22]), // 22:レース印１
                        'race_mark_2' => trim($line[23]), // 23:レース印２
                        'race_mark_3' => !empty($this->mbTrim($line[24])) ? $this->mbTrim($line[24]) : 0.0, // 24:レース印３
                        'rpci' => !empty($line[25]) ? $line[25] : 0.0, // 25:RPCI
                        'base_time' => !empty($line[27]) ? $line[27] : 0.0,  // 27:基準タイム
 */
        $race = Race::firstOrNew(['race_id' => $race_id]);

        $is_update = 0;
        if (!$race->exists)
        {
            $race->fill($race_data)->save();
        }
        else
        {
            if ($race->race_mark_1 != $race_data['race_mark_1'])
            {
                $race->race_mark_1 = $race_data['race_mark_1'];
                $is_update = 1;
            }
            if ($race->race_mark_2 != $race_data['race_mark_2'])
            {
                $race->race_mark_2 = $race_data['race_mark_2'];
                $is_update = 1;
            }
            if ($race->race_mark_3 != $race_data['race_mark_3'])
            {
                $race->race_mark_3 = $race_data['race_mark_3'];
                $is_update = 1;
            }
        }

        if ($is_update)
        {
            $race->save();
        }
        // todo 日次、回次がうまく入ってない可能性がある

        return $race;
    }

    /**
     * xx.csvからHorseデータをセット
     * @param int $race_id race_id
     * @param array $race_data 作成したレースデータ
     * @return object race_data
     */
    private function _set_race_horse_history($race_id, $horse_id, $race_history_data)
    {
        return HorseRaceHistory::firstOrCreate(['race_id' => $race_id, 'horse_id' => $horse_id], $race_history_data);
    }

    /**
     * xx.csvからPlaceデータをセット
     * @param string $place_name 東京、中山など
     * @return object place_data
     */
    private function _set_trainer($trainer_id, $trainer_name)
    {
        return Trainer::firstOrCreate([
            'trainer_id' => $trainer_id,
        ],
        [
            'trainer_name' => $trainer_name,
        ]);
    }

    /**
     * xx.csvからPlaceデータをセット
     * @param string $place_name 東京、中山など
     * @return object place_data
     */
    private function _set_jockey($jockey_id, $jockey_name)
    {
        return Jockey::firstOrCreate([
            'jockey_id' => $jockey_id,
        ],
        [
            'jockey_name' => $jockey_name,
        ]);
    }

    /**
     * xx.csvからStallionデータをセット
     * @param string $place_name 東京、中山など
     * @return object place_data
     */
    private function _set_stallion($stallion_name)
    {
        return Stallion::firstOrCreate([
            'stallion_name' => $stallion_name,
        ],
        [
            'stallion_name' => $stallion_name,
        ]);
    }

    /**
     * xx.csvからデータをセット
     * @param string $place_name 東京、中山など
     * @return object place_data
     */
    private function _set_place($place_name)
    {
        return Place::firstOrCreate([
            'place_name' => $place_name,
        ],
        [
            'place_code_name' => 'None'
        ]);
    }

    /**
     * xx.csvからCorseデータをセット
     * @param int $place_id PlaceテーブルのID
     * @param int $track_type 
     * @param int $distance 距離
     * @param int $corner_size 1:設定なし 2:外回り 3:内回り
     * @param int $corner_count コーナー回数
     * @return object corse_data 
     */
    private function _set_corse($place_id, $track_type, $distance, $corner_size, $corner_count)
    {
        return Corse::firstOrCreate([
            'place_id' => $place_id,
            'track_type' => $track_type,
            'distance' => $distance,
            'corner_size' => $corner_size
        ],
        [
            'corner_count' => $corner_count
        ]);
    }

    /**
     * datetime/place/XX.csvのデータを取得
     * @param object $mp_libs mp_libsインスタンス
     * @param array $target_csv 対象のCSVディレクトリ
     */
    private function _get_hanro_csv($mp_libs, $target_csv)
    {    
        $youbi_list = \MpConsts::YOUBI_LIST;
        
        $trainer_list = Trainer::all('trainer_id', 'trainer_name')->toArray();
        $trainer_id_list = array_column($trainer_list, 'trainer_name', 'trainer_id');

        foreach ($target_csv as $csv)
        {
            $target_date = $csv[1];
            $place = $csv[2];
            echo '[' . $target_date . ']'. $place . "\n";
            // レース数で回す
            for ($i = 1; $i <= 12; $i++)
            {
                // レース番号からCSV名作成
                $csv_name = 'hanro_' . $i . '.csv';
                $csv_data = $mp_libs->get_csv($target_date, $place, $csv_name);
                if (!$csv_data) continue;
                foreach ($csv_data as $line)
                {
                    if (!is_numeric($line[0])) 
                    {
                        continue;
                    }
                    if (!is_numeric($line[2])) 
                    {
                        continue;
                    }
                    // データ破損は除外する
                    if (
                        $line[15] == ''
                        || $line[16] == ''
                        || $line[17] == ''
                        || $line[18] == ''
                    ) {
                        continue;
                    }

                    $line_assoc = [
                        'horse_id' => $line[0],
                        'place' => $line[1],
                        'training_date' => $line[2],
                        'youbi' => $line[3],
                        'hanro_time' => $line[4],
                        'horse_name' => $line[5],
                        'sex' => $line[7],
                        'age' => $line[8],
                        'syoukin' => $line[9],
                        'trainer_name' => $line[10],
                        'time_1' => $line[11],
                        'time_2' => $line[12],
                        'time_3' => $line[13],
                        'time_4' => $line[14],
                        'lap_4' => $line[15],
                        'lap_3' => $line[16],
                        'lap_2' => $line[17],
                        'lap_1' => $line[18],
                    ];

                    $line_assoc = $this->hanro_time_analyze($line_assoc);
                    $h_data = [
                        'horse_id' => $line_assoc['horse_id'],
                        'training_date' => $line_assoc['training_date'],
                        'youbi' => array_search($line_assoc['youbi'], $youbi_list),
                        'time_4' => $line_assoc['time_1'],
                        'time_3' => $line_assoc['time_2'],
                        'time_2' => $line_assoc['time_3'],
                        'time_1' => $line_assoc['time_4'],
                        'lap_4' => $line_assoc['lap_4'],
                        'lap_3' => $line_assoc['lap_3'],
                        'lap_2' => $line_assoc['lap_2'],
                        'lap_1' => $line_assoc['lap_1'],
                        'point' => $line_assoc['point'],
                        'trainer_id' => array_search($line_assoc['trainer_name'], $trainer_id_list),
                    ];
                    
                    
                    $hanro = Hanro::firstOrNew([
                        'horse_id' => $line[0],
                        'training_date' => $line[2],
                        'lap_1' => $line[18]
                        ]);

                    if (!$hanro->exists)
                    {
                        $hanro->fill($h_data)->save();
                    }
                    
                    if ($hanro->point == 0)
                    {
                        $hanro->point = $line_assoc['point'];
                        $hanro->save();
                    }
                    //dump($line_assoc);
                }
            }
        }
    }

    /**
     * datetime/place/XX.csvのデータを取得してDBにインポート
     * @param object $mp_libs mp_libsインスタンス
     * @param array $target_csv 対象のCSVディレクトリ
     */
    private function _get_wood_csv($mp_libs, $target_csv)
    {
        echo 'inport wood' . "\n";
        $youbi_list = \MpConsts::YOUBI_LIST;
        
        $trainer_list = Trainer::all('trainer_id', 'trainer_name')->toArray();
        $trainer_id_list = array_column($trainer_list, 'trainer_name', 'trainer_id');

        foreach ($target_csv as $csv)
        {
            $target_date = $csv[1];
            $place = $csv[2];
            echo '[' . $target_date . ']'. $place . "\n";

            // レース数で回す
            for ($i = 1; $i <= 12; $i++)
            {
                // レース番号からCSV名作成
                $csv_name = 'wood_' . $i . '.csv';
                $csv_data = $mp_libs->get_csv($target_date, $place, $csv_name);
                
                if (!$csv_data)
                {
                    continue;
                }
                
                foreach ($csv_data as $line)
                {
                    if (!is_numeric($line[0])) 
                    {
                        continue;
                    }
                    if (!is_numeric($line[4])) 
                    {
                        continue;
                    }
                    if (
                        $line[27] == ''
                        || $line[28] == ''
                        || $line[29] == ''
                    ) {
                        continue;
                    }

                    $line_assoc = [
                        'horse_id' => $line[0],
                        'place' => $line[1],
                        'training_date' => $line[4],
                        'youbi' => $line[5],
                        'hanro_time' => $line[6],
                        'horse_name' => $line[7],
                        'sex' => $line[8],
                        'age' => $line[9],
                        'trainer_name' => $line[10],
                        '8F' => $line[13],
                        '7F' => $line[14],
                        '6F' => $line[15],
                        '5F' => $line[16],
                        '4F' => $line[17],
                        '3F' => $line[18],
                        '2F' => $line[19],
                        '1F' => $line[20],
                        'lap_8' => $line[22],
                        'lap_7' => $line[23],
                        'lap_6' => $line[24],
                        'lap_5' => $line[25],
                        'lap_4' => $line[26],
                        'lap_3' => $line[27],
                        'lap_2' => $line[28],
                        'lap_1' => $line[29],
                    ];

                    $line_assoc = $this->wood_time_analyze($line_assoc);

                    $w_data = [
                        'horse_id' => $line_assoc['horse_id'],
                        'training_date' => $line_assoc['training_date'],
                        'youbi' => array_search($line_assoc['youbi'], $youbi_list),
                        'time_8' => !empty($line_assoc['8F']) ? $line_assoc['8F'] : 999.9,
                        'time_7' => !empty($line_assoc['7F']) ? $line_assoc['7F'] : 999.9,
                        'time_6' => !empty($line_assoc['6F']) ? $line_assoc['6F'] : 999.9,
                        'time_5' => !empty($line_assoc['5F']) ? $line_assoc['5F'] : 999.9,
                        'time_4' => !empty($line_assoc['4F']) ? $line_assoc['4F'] : 999.9,
                        'time_3' => !empty($line_assoc['3F']) ? $line_assoc['3F'] : 999.9,
                        'time_2' => $line_assoc['2F'],
                        'time_1' => $line_assoc['1F'],
                        'lap_8' => !empty($line_assoc['lap_8']) ? $line_assoc['lap_8'] : 999.9,
                        'lap_7' => !empty($line_assoc['lap_7']) ? $line_assoc['lap_7'] : 999.9,
                        'lap_6' => !empty($line_assoc['lap_6']) ? $line_assoc['lap_6'] : 999.9,
                        'lap_5' => !empty($line_assoc['lap_5']) ? $line_assoc['lap_5'] : 999.9,
                        'lap_4' => !empty($line_assoc['lap_4']) ? $line_assoc['lap_4'] : 999.9,
                        'lap_3' => !empty($line_assoc['lap_3']) ? $line_assoc['lap_3'] : 999.9,
                        'lap_2' => $line_assoc['lap_2'],
                        'lap_1' => $line_assoc['lap_1'],
                        'point' => $line_assoc['point'],
                        'trainer_id' => array_search($line_assoc['trainer_name'], $trainer_id_list),
                    ];

                    $wood = Wood::firstOrNew([
                        'horse_id' => $line_assoc['horse_id'],
                        'training_date' => $line_assoc['training_date'],
                        'lap_1' => $line_assoc['lap_1']
                        ]);

                    if (!$wood->exists)
                    {
                        $wood->fill($w_data)->save();
                    }
                    if ($wood->point == 0)
                    {
                        $wood->point = $line_assoc['point'];
                        $wood->save();
                    }
                }
            }
        }
    }
    
    /**
     * WOOD解析
	 * @param array $time_data CSVの行データ
	 * @return array $time_data [[horse_id => [data, data,...]],...]
	 */
	private function wood_time_analyze($time_data)
	{
		// ベースポイント
		$point = self::BASE_CHOKYO_POINT;
		if (
			$time_data['lap_1'] == 0.0
			|| $time_data['lap_2'] == 0.0
			|| $time_data['lap_3'] == 0.0
			|| $time_data['lap_4'] == 0.0
		) {
				$time_data['point'] = $point;
				return $time_data;
		}

        // 6F-1Fの全加速フラグ
        $kasoku_all = true;
		// 3F加速判定
		if (($time_data['lap_3'] >= $time_data['lap_2']) && ($time_data['lap_2'] >= $time_data['lap_1']))
		{
            if (isset($time_data['6f']) && $time_data['6f'] <= 83.0)
            {
			    $point += 8;
            }
            // 5F~4F
			if ($time_data['lap_4'] >= $time_data['lap_3'])
			{
				$point += 2;
			}
            else
            {
                $kasoku_all = false;
            }
			// 5F~4F
			if ($time_data['lap_5'] >= $time_data['lap_4'])
			{
				$point += 2;
			}
            else
            {
                $kasoku_all = false;
            }
			// 6F~5F
			if ($time_data['lap_6'] >= $time_data['lap_5'])
			{
				$point += 2;
			}
            else
            {
                $kasoku_all = false;
            }
		}
        else
        {
            $kasoku_all = false;
        }

        if ($kasoku_all)
        {
            $point += 2;
        }

        //2F-1Fの加速度を判定
        if ($time_data['lap_2'] >= $time_data['lap_1'])
        {
            $kasokudo = $time_data['lap_2'] - $time_data['lap_1'];

            if ($kasokudo >= 0.9)
            {
                $point += 5;
            }
            else if ($kasokudo >= 0.6)
            {
                $point += 3;
            }
            else if ($kasokudo >= 0.3)
            {
                $point += 2;
            }
        }

        // ラスト1F判定
		if ($time_data['lap_1'] <= 10.8)
		{
			$point += 10;
		} 
		else if ($time_data['lap_1'] <= 11.0)
		{
			$point += 10;
		} 
		else if ($time_data['lap_1'] <= 11.3)
		{
			$point += 7;
		} 
		else if ($time_data['lap_1'] <= 11.5)
		{
			$point += 4;
		}
		else if ($time_data['lap_1'] <= 11.9)
		{
			$point += 1;
		}

		// ラスト2F判定
		if ($time_data['lap_2'] <= 11.0)
		{
			$point += 7;
		} 
		else if ($time_data['lap_2'] <= 11.3)
		{
			$point += 5;
		} 
		else if ($time_data['lap_2'] <= 11.5)
		{
			$point += 3;
		}
		else if ($time_data['lap_2'] <= 11.9)
		{
			$point += 1;
		}

		if (!empty($time_data['6f'] )) {
			// 6F判定
			if ($time_data['6f'] <= 80.0)
			{
				$point += 15;
			} 
			else if ($time_data['6f'] <= 81.0)
			{
				$point += 12;
			} 
			else if ($time_data['6f'] <= 82.0)
			{
				$point += 10;
			}
			else if ($time_data['6f'] <= 83.0)
			{
				$point += 5;
			}
			else if ($time_data['6f'] >= 84.0 && $time_data['6f'] <= 84.9)
			{
				$point -= 5;
			}
			else if ($time_data['6f'] >= 85.0 && $time_data['6f'] <= 87.9)
			{
				$point -= 10;
			}
			else if ($time_data['6f'] >= 88.0)
			{
				$point -= 15;
			}
		}

		$time_data['point'] = $point;
		return $time_data;

    }

	/**
	 * 坂路は美浦と栗東で関数を読み替える
	 * @param array $time_data CSVの行データ
	 * @return array $time_data [[horse_id => [data, data,...]],...]
	 */
	private function hanro_time_analyze($time_data)
	{
		// ベースポイント
		$point = self::BASE_CHOKYO_POINT;

		if ($time_data['place'] == '美浦')
		{
			$time_data = $this->miho_hanro_time_analyze($time_data, $point);
		} 
		else if ($time_data['place'] == '栗東') 
		{
			$time_data = $this->ritto_hanro_time_analyze($time_data, $point);
		}
		return $time_data;
	}

	/**
	 * 美浦側の坂路解析
	 * @param array $time_data
	 * @param int $point
	 * @return array $time_data
	 */
	private function miho_hanro_time_analyze($time_data, $point)
	{
		if (
			$time_data['time_1'] == 0.0
			|| $time_data['time_2'] == 0.0
			|| $time_data['time_3'] == 0.0
			|| $time_data['time_4'] == 0.0
		) {
				$time_data['point'] = $point;
				return $time_data;
		}

		// 合計タイム判定
		if ($time_data['time_1'] <= 50.9)
		{
			$point += 15;
		} 
		else if ($time_data['time_1'] <= 51.9)
		{
			$point += 13;
		}
		else if ($time_data['time_1'] <= 52.9)
		{
			$point += 12;
		}
		else if ($time_data['time_1'] <= 53.9)
		{
			$point += 8;
		}
		else if ($time_data['time_1'] <= 54.9)
		{
			$point += 0;
		}
		else if ($time_data['time_1'] >= 55.0 && $time_data['time_1'] <= 55.9)
		{
			$point -= 5;
		}
		else if ($time_data['time_1'] >= 56.0 && $time_data['time_1'] <= 57.9)
		{
			$point -= 7;
		}
		else if ($time_data['time_1'] >= 58.0)
		{
			$point -= 10;
		}

		// ラスト1F判定
		if ($time_data['lap_1'] <= 12.0)
		{
			$point += 10;
		} 
		else if ($time_data['lap_1'] <= 12.3)
		{
			$point += 6;
		} 
		else if ($time_data['lap_1'] <= 12.9)
		{
			$point += 3;
		}

		// 3F加速判定
		if (($time_data['lap_3'] >= $time_data['lap_2']) && ($time_data['lap_2'] >= $time_data['lap_1']))
		{
			$point += 5;
		}

		// 2F判定
		if ($time_data['lap_2'] <= 12.9 && $time_data['lap_1'] <= 12.9)
		{
            if ($time_data['time_1'] <= 52.9)
            {
                $point += 6;
            }
            else{
			    $point += 2;
            }
			// 2F加速度判定
			if (($time_data['lap_2'] - $time_data['lap_1']) >= 1.0)
			{
				$point += 10;
			}
			else if (($time_data['lap_2'] - $time_data['lap_1']) >= 0.6)
			{
				$point += 7;
			}
			else if (($time_data['lap_2'] - $time_data['lap_1']) >= 0.3)
			{
				$point += 5;
			}
			else if (($time_data['lap_2'] - $time_data['lap_1']) >= 0)
			{
				$point += 3;
			}
		}

		$time_data['point'] = $point;
		return $time_data;
	}

	/**
	 * 栗東側の坂路解析
	 * @param array $time_data
	 * @param int $point
	 * @return array $time_data
	 */
	private function ritto_hanro_time_analyze($time_data, $point)
	{
		if (
			$time_data['time_1'] == 0.0
			|| $time_data['time_2'] == 0.0
			|| $time_data['time_3'] == 0.0
			|| $time_data['time_4'] == 0.0
		) {
				$time_data['point'] = $point;
				return $time_data;
		}

		// 合計タイム判定
		if ($time_data['time_1'] <= 49.9)
		{
			$point += 15;
		} 
		else if ($time_data['time_1'] <= 50.9)
		{
			$point += 13;
		}
		else if ($time_data['time_1'] <= 51.9)
		{
			$point += 12;
		}
		else if ($time_data['time_1'] <= 52.9)
		{
			$point += 10;
		}
		else if ($time_data['time_1'] <= 53.9)
		{
			$point += 5;
		}
		else if ($time_data['time_1'] <= 54.9)
		{
			$point -= 1;
		}
		else if ($time_data['time_1'] >= 55.0 && $time_data['time_1'] <= 55.9)
		{
			$point -= 5;
		}
		else if ($time_data['time_1'] >= 56.0 && $time_data['time_1'] <= 57.9)
		{
			$point -= 10;
		}
		else if ($time_data['time_1'] >= 58.0)
		{
			$point -= 15;
		}

		// ラスト1F判定
		if ($time_data['lap_1'] <= 11.9)
		{
			$point += 10;
		} 
		else if ($time_data['lap_1'] <= 12.2)
		{
			$point += 8;
		} 
		else if ($time_data['lap_1'] <= 12.5)
		{
			$point += 4;
		} 
		// 3F加速判定
		if (($time_data['lap_3'] >= $time_data['lap_2']) && ($time_data['lap_2'] >= $time_data['lap_1']))
		{
			$point += 5;
		}

		// 2F判定
		if ($time_data['lap_2'] <= 12.5 && $time_data['lap_1'] <= 12.2)
		{
            if ($time_data['time_1'] <= 52.9)
            {
                $point += 5;
            }
            else{
			    $point += 2;
            }
            // 2F加速度判定
			if (($time_data['lap_2'] - $time_data['lap_1']) >= 0.7)
			{
				$point += 10;
			}
			else if (($time_data['lap_2'] - $time_data['lap_1']) >= 0.3)
			{
				$point += 7;
			}
			else if (($time_data['lap_2'] - $time_data['lap_1']) >= 0)
			{
				$point += 5;
			}
			else if (($time_data['lap_2'] - $time_data['lap_1']) >= -0.4)
			{
				$point -= 3;
			}
		}
		else if ($time_data['lap_2'] <= 12.9 && $time_data['lap_1'] <= 12.5)
		{
            if ($time_data['time_1'] <= 52.9)
            {
                $point += 5;
            }
            else{
			    $point += 2;
            }
			// 2F加速度判定
			if (($time_data['lap_2'] - $time_data['lap_1']) >= 1.0)
			{
				$point += 7;
			}
			else if (($time_data['lap_2'] - $time_data['lap_1']) >= 0.7)
			{
				$point += 5;
			}
			else if (($time_data['lap_2'] - $time_data['lap_1']) >= 0.3)
			{
				$point += 4;
			}
			else if (($time_data['lap_2'] - $time_data['lap_1']) >= 0)
			{
				$point += 3;
			}
			else if (($time_data['lap_2'] - $time_data['lap_1']) >= -0.4)
			{
				$point -= 3;
			}
		}

		$time_data['point'] = $point;
		return $time_data;
	}

    /**
     * 全角スペースを含むスペースを除外する。
     * @param string $pString
     * @return string スペースを除外した文字列
     */
    private function mbTrim($pString)
    {
        return preg_replace('/\A[\p{Cc}\p{Cf}\p{Z}]++|[\p{Cc}\p{Cf}\p{Z}]++\z/u', '', $pString);
    }
}
