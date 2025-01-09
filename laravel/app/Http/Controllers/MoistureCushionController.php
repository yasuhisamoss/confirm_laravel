<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MoistureContentAndCushion;
class MoistureCushionController extends Controller
{
    /**
     * クッション、含水率データの表示と入力フォーム
     * todo ソートや絞り込みに対応する
     */
    public function index()
    {
        $items = MoistureContentAndCushion::all();
        $mp_libs = new \MpLibs();
        $csv_dir = $mp_libs->get_date_place_list();
        $format_items = $this->_format_mcc_data_list($items, $csv_dir);
        
        return view('moisture_cushion', [
            'place_list' => \MpConsts::PLACE_ID_LIST,
            'csv_dir_list' => $format_items
        ]);
        //
        return view('moisture_cushion');
    }

    /**
     * リクエストデータを登録してインデックスにリダイレクト
     * @param array $request フォーム入力のリクエストデータ
     * @return void
     */
    public function regist(Request $request)
    {
        MoistureContentAndCushion::updateOrCreate([
            'race_date' => (int)$request["race_date"],
            'place_id' => (int)$request["place_id"]
        ],
        [
            'turf_moisture_content' => (float)$request["tmc"],
            'dart_moisture_content' => (float)$request["dmc"],
            'cushion' => (float)$request["cushion"]
        ]);

        return redirect('/moisture_cushion')->with('result', '完了');
    }

    /**
     * strageのディレクトリリストとDBに入っている含水率データをマージする
     * @param array $data_list MoistureContentAndCushionのデータリスト
     * @param array $csv CSVのデータディレクトリリスト
     * @return array $data_merge_csv 引数２つの配列をマージした配列
     */
    private function _format_mcc_data_list($data_list, $csv)
    {
        $format_data = [];
        foreach ($data_list as $data)
        {
            $key_name = $data->race_date ."_".$data->place_id;
            $format_data[$key_name] = [
                'race_date' => $data->race_date,
                'place_id' => $data->place_id,
                'turf_moisture_content' => $data->turf_moisture_content,
                'dart_moisture_content' => $data->dart_moisture_content,
                'cushion' => $data->cushion,
            ];
        }

        $data_merge_csv = [];
        foreach ($csv as $c)
        {
            $place_id = array_search($c['2'], \MpConsts::PLACE_ID_LIST);
            $key = $c['1'].'_'.$place_id;
            if (isset($format_data[$key]))
            {
                $data_merge_csv[] = [
                    'race_date' => $c['1'],
                    'place_id' => $place_id,
                    'turf_moisture_content' => $format_data[$key]['turf_moisture_content'],
                    'dart_moisture_content' => $format_data[$key]['dart_moisture_content'],
                    'cushion' => $format_data[$key]['cushion']
                ];

            }
            else
            {
                $data_merge_csv[] = [
                    'race_date' => $c['1'],
                    'place_id' => $place_id,
                    'turf_moisture_content' => '',
                    'dart_moisture_content' => '',
                    'cushion' => ''
                ];
            }
        }
        return $data_merge_csv;  
    }
}
