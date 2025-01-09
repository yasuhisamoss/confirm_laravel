<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RaceSchedule;

class RaceScheduleController extends Controller
{
    /**
     * スケジュールのデータリスト
     */
    public function index($start_date = 0, $end_date = 0)
    {
        $mp_libs = new \MpLibs();
        if (!$end_date)
        {
            $end_date = date("Ymd", time()+(86400*3));
        }

        if (!$start_date)
        {
            $start_date = $mp_libs->format_date($end_date, 365);
        }   

        $rs = new RaceSchedule();
        $items = $rs->get_schedule($start_date, $end_date);

        $csv_dir = $mp_libs->get_date_place_list();
        $format_items = $this->_format_data_list($items);
        
        return view('race_schedule', [
            'place_list' => \MpConsts::PLACE_ID_LIST,
            'race_schedule_list' => $format_items
        ]);
    }

    /**
     * リクエストデータを登録してインデックスにリダイレクト
     * @param array $request フォーム入力のリクエストデータ
     * @return void
     */
    public function regist(Request $request)
    {
        /*
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
        */
    }

    /**
     * スケジュールテーブルの中身を見てくれをよくする
     * @param array $data_list スケジュールテーブルのデータリスト
     * @return array $data_merge_csv データを整形した配列
     */
    private function _format_data_list($data_list)
    {
        $format_data = [];
        foreach ($data_list as $d)
        {
            $format_data[$d->race_date][] = [
                'place_id' => $d->place_id,
                'place_code_name' => $d->place_code_name,
                'place_name' => $d->kaizi . '回' . $d->place_name . $d->nichizi . '日目',
                'kaizi' => $d->kaizi,
                'nichizi' => $d->nichizi,
                'turf_moisture_content' => $d->turf_moisture_content,
                'dart_moisture_content' => $d->dart_moisture_content,
                'cushion' => $d->cushion,
            ];
        }

        //dd($format_data);
        return $format_data;  
    }
}
