<?php

namespace App\Http\Controllers;
use App\Models\Race;
use App\Models\Stallion;
use App\Models\Place;
use App\Models\Corse;
use App\Models\HorseRaceHistory;
use App\Models\StallionCorse;
use App\Models\StallionBias;
use Illuminate\Http\Request;

class StallionController extends Controller
{
    public function index(Request $request)
    {
        $mp_libs = new \MpLibs();
        $stallion_list = Stallion::all();

        $request_param = [];
		$hrh_data = [];
		$arirr  = [];
		$sb_point_list = [];
		$sc_point_list = [];
        $stallion_id = 0;
		$stallion_data = [];
        if ($request["stallion"])
        {
            $request_param = $request;
            $stallion_id = $request["stallion"];
			$stallion_data = Stallion::where(['stallion_id' => $stallion_id])->first();
            // historyDBから種牡馬IDをキーにしてレース情報を取る
            $hrh = new HorseRaceHistory();
            $hrh_data = $hrh->get_history_by_stallion($stallion_id, 0, $request["distance_s"], $request["distance_e"], $request["track_code"], $request["track_bias"], $request["place"]);
            
            // 一覧とともにサマったコース成績とか距離成績などのデータを計算する
            $arirr = $this->_analyze_race_individual_result_rank($mp_libs, $hrh_data);

            // 上記で入れたポイントがあれば出るようにする
			$sc = new StallionCorse();
			$sc_point_list = $sc->get_corse_point($stallion_id);
			$sb_point_list = StallionBias::where(['stallion_id' => $stallion_id])->get();
        }

        return view('stallion', [
            'stallion_id' => $stallion_id,
			'stallion_data' => $stallion_data,
            'search_data' => $hrh_data,
			'stallion_list' => $stallion_list,
            'rank_analyze' => $arirr,
            'params' => $request_param,
			'corse_point_list' => $sc_point_list,
			'bias_point_list' => $sb_point_list,
        ]);
    }

	/**
     * 種牡馬IDをキーにポイントを入れられるページ
	 * @param int $stallion_id
     * @param array $request フォーム入力のリクエストデータ
     */
    public function corse_point_regist($stallion_id, Request $request)
    {
		$stallion_data = Stallion::where(['stallion_id' => $stallion_id])->first();
		$place_data = Place::all();

		$place_id = 0;
		$corse_list = [];
		if (isset($request["place_id"]))
		{
			$place_id = $request["place_id"];
			$corse_list = Corse::where(['place_id' => $place_id])->get();
		}

		$stallion_bias_list = array_column(StallionBias::where(['stallion_id' => $stallion_id])->get()->toArray(), 'point', 'bias_type');
		$stallion_corse_list = array_column(StallionCorse::where(['stallion_id' => $stallion_id])->get()->toArray(), 'point', 'corse_id');

		return view('corse_point_regist', [
			'stallion_data' => $stallion_data,
			'place_list' =>$place_data,
            'place_id' => $place_id,
			'corse_list' => $corse_list,
            'stallion_bias_list' => $stallion_bias_list,
			'stallion_corse_list' => $stallion_corse_list,
        ]);	
	}

    /**
     * リクエストデータを登録してインデックスにリダイレクト(Bias)
     * @param array $request フォーム入力のリクエストデータ
     * @return void
     */
    public function update_stallion_memo(Request $request)
    {
		$stallion_id = $request["stallion_id"];
        $stallion_data = Stallion::where(['stallion_id' => $stallion_id])->first();

		$stallion_data->memo = $request["memo"];
		$stallion_data->save();

		return redirect("/stallion?stallion=$stallion_id")->with('result', '完了');
    }

    /**
     * リクエストデータを登録してインデックスにリダイレクト(Bias)
     * @param array $request フォーム入力のリクエストデータ
     * @return void
     */
    public function regist_stallion_bias(Request $request)
    {
		$stallion_id = $request["stallion_id"];
		$place_id = $request["place_id"];
        StallionBias::updateOrCreate([
            'stallion_id' => (int)$request["stallion_id"],
            'bias_type' => (int)$request["bias_type"]
        ],
        [
            'point' => $request["point"]
        ]);
		$message = 'place:'. $place_id . ' bias_type:'.$request["bias_type"] . ' 完了';
		return redirect("/corse_point_regist/$stallion_id?place_id=$place_id")->with('result', $message);
    }

	/**
     * リクエストデータを登録してインデックスにリダイレクト(corse)
     * @param array $request フォーム入力のリクエストデータ
     * @return void
     */
    public function regist_stallion_corse(Request $request)
    {
		$stallion_id = $request["stallion_id"];
		$place_id = $request["place_id"];
        StallionCorse::updateOrCreate([
			'stallion_id' => (int)$request["stallion_id"],
			'corse_id' => (int)$request["corse_id"]
		],
		[
			'point' => $request["point"]
		]);
		$message = 'place:'. $place_id . ' corse_id:'.$request["corse_id"] . ' 完了';
        return redirect("/corse_point_regist/$stallion_id?place_id=$place_id")->with('result', $message);
    }

    /**
	 * 条件別の着順のリストを出す
	 * @param object $mp_libs
	 * @param array $race_list
	 * @return array
	 */
    private function _analyze_race_individual_result_rank($mp_libs, $race_list)
    {
    	$analyze_race_data = [
			"course_condition_rank" => [],
			"race_place_rank" => [],
			"race_cushion_rank" => [],
			"race_bias_rank" => [],
			"race_distance_rank" => [],
			"race_pace_rank" => [],
			"race_corner_rank" => [],
			"race_waku_rank" => [],
			"race_class_rank" => [],
    	];
    	
        if (empty($analyze_race_data))
    	{
    		return [];
    	}

        foreach ($race_list as $race) 
        {
    		// 芝
    		if ($race->track_type == \MpConsts::TRACK_TYPE_TERF)
    		{
                if ($race->result_rank >= 4)
    			{
    				// course_condition 芝/ダ
    				if (isset($analyze_race_data["course_condition_rank"]["芝"][4]))
    				{
    					$analyze_race_data["course_condition_rank"]["芝"][4]++;
    				}
    				else
    				{
    					$analyze_race_data["course_condition_rank"]["芝"][4] = 1;
    				}
                    // race_cushion_rank クッション
					$range_cushion = $mp_libs->get_cushion_range($race->cushion);
					if($range_cushion)
					{
						if (isset($analyze_race_data["race_cushion_rank"][$range_cushion][4]))
						{
							$analyze_race_data["race_cushion_rank"][$range_cushion][4]++;
						}
						else
						{
							$analyze_race_data["race_cushion_rank"][$range_cushion][4] = 1;
						}
					}
    				// race_bias_rank 良/やや重/重/不良
    				if (isset($analyze_race_data["race_bias_rank"]["芝"][\MpConsts::TRACK_BIAS_CODE[$race->track_bias]][4]))
    				{
    					$analyze_race_data["race_bias_rank"]["芝"][\MpConsts::TRACK_BIAS_CODE[$race->track_bias]][4]++;
    				}
    				else
    				{
    					$analyze_race_data["race_bias_rank"]["芝"][\MpConsts::TRACK_BIAS_CODE[$race->track_bias]][4] = 1;
    				}

    				// race_distance_rank 距離
    				if (isset($analyze_race_data["race_distance_rank"]["芝"][$race->distance][4]))
    				{
    					$analyze_race_data["race_distance_rank"]["芝"][$race->distance][4]++;
    				}
    				else
    				{
    					$analyze_race_data["race_distance_rank"]["芝"][$race->distance][4] = 1;
    				}
    				
    				// race_corner_rank 右左
    				if (isset($analyze_race_data["race_corner_rank"]["芝"][\MpConsts::CORNER_DIRECTION_LIST[$race->corner_direction]][4]))
    				{
    					$analyze_race_data["race_corner_rank"]["芝"][\MpConsts::CORNER_DIRECTION_LIST[$race->corner_direction]][4]++;
    				}
    				else
    				{
    					$analyze_race_data["race_corner_rank"]["芝"][\MpConsts::CORNER_DIRECTION_LIST[$race->corner_direction]][4] = 1;
    				}
                }
                else
                {
    				// course_condition 芝/ダ
    				if (isset($analyze_race_data["course_condition_rank"]["芝"][$race->result_rank]))
    				{
    					$analyze_race_data["course_condition_rank"]["芝"][$race->result_rank]++;
    				}
    				else
    				{
    					$analyze_race_data["course_condition_rank"]["芝"][$race->result_rank] = 1;
    				}
    				
					$range_cushion = $mp_libs->get_cushion_range($race->cushion);
					if($range_cushion)
					{
						if (isset($analyze_race_data["race_cushion_rank"][$range_cushion][$race->result_rank]))
						{
							$analyze_race_data["race_cushion_rank"][$range_cushion][$race->result_rank]++;
						}
						else
						{
							$analyze_race_data["race_cushion_rank"][$range_cushion][$race->result_rank] = 1;
						}
					}

    				// race_bias_rank 良/やや重/重/不良
    				if (isset($analyze_race_data["race_bias_rank"]["芝"][\MpConsts::TRACK_BIAS_CODE[$race->track_bias]][$race->result_rank]))
    				{
    					$analyze_race_data["race_bias_rank"]["芝"][\MpConsts::TRACK_BIAS_CODE[$race->track_bias]][$race->result_rank]++;
    				}
    				else
    				{
    					$analyze_race_data["race_bias_rank"]["芝"][\MpConsts::TRACK_BIAS_CODE[$race->track_bias]][$race->result_rank] = 1;
    				}

    				// race_distance_rank 距離
    				if (isset($analyze_race_data["race_distance_rank"]["芝"][$race->distance][$race->result_rank]))
    				{
    					$analyze_race_data["race_distance_rank"]["芝"][$race->distance][$race->result_rank]++;
    				}
    				else
    				{
    					$analyze_race_data["race_distance_rank"]["芝"][$race->distance][$race->result_rank] = 1;
    				}

    				// race_corner_rank 右左
    				if (isset($analyze_race_data["race_corner_rank"]["芝"][\MpConsts::CORNER_DIRECTION_LIST[$race->corner_direction]][$race->result_rank]))
    				{
    					$analyze_race_data["race_corner_rank"]["芝"][\MpConsts::CORNER_DIRECTION_LIST[$race->corner_direction]][$race->result_rank]++;
    				}
    				else
    				{
    					$analyze_race_data["race_corner_rank"]["芝"][\MpConsts::CORNER_DIRECTION_LIST[$race->corner_direction]][$race->result_rank] = 1;
    				}
                }
            }
            // ダート
            else if ($race->track_type == \MpConsts::TRACK_TYPE_DART)
            {
				if ($race->result_rank >= 4)
    			{
    				// course_condition 芝/ダ
    				if (isset($analyze_race_data["course_condition_rank"]["ダ"][4]))
    				{
    					$analyze_race_data["course_condition_rank"]["ダ"][4]++;
    				}
    				else
    				{
    					$analyze_race_data["course_condition_rank"]["ダ"][4] = 1;
    				}
                    
    				// race_bias_rank 良/やや重/重/不良
    				if (isset($analyze_race_data["race_bias_rank"]["ダ"][\MpConsts::TRACK_BIAS_CODE[$race->track_bias]][4]))
    				{
    					$analyze_race_data["race_bias_rank"]["ダ"][\MpConsts::TRACK_BIAS_CODE[$race->track_bias]][4]++;
    				}
    				else
    				{
    					$analyze_race_data["race_bias_rank"]["ダ"][\MpConsts::TRACK_BIAS_CODE[$race->track_bias]][4] = 1;
    				}

    				// race_distance_rank 距離
    				if (isset($analyze_race_data["race_distance_rank"]["ダ"][$race->distance][4]))
    				{
    					$analyze_race_data["race_distance_rank"]["ダ"][$race->distance][4]++;
    				}
    				else
    				{
    					$analyze_race_data["race_distance_rank"]["ダ"][$race->distance][4] = 1;
    				}
    				
    				// race_corner_rank 右左
    				if (isset($analyze_race_data["race_corner_rank"]["ダ"][\MpConsts::CORNER_DIRECTION_LIST[$race->corner_direction]][4]))
    				{
    					$analyze_race_data["race_corner_rank"]["ダ"][\MpConsts::CORNER_DIRECTION_LIST[$race->corner_direction]][4]++;
    				}
    				else
    				{
    					$analyze_race_data["race_corner_rank"]["ダ"][\MpConsts::CORNER_DIRECTION_LIST[$race->corner_direction]][4] = 1;
    				}
                }
                else
                {
    				// course_condition 芝/ダ
    				if (isset($analyze_race_data["course_condition_rank"]["ダ"][$race->result_rank]))
    				{
    					$analyze_race_data["course_condition_rank"]["ダ"][$race->result_rank]++;
    				}
    				else
    				{
    					$analyze_race_data["course_condition_rank"]["ダ"][$race->result_rank] = 1;
    				}

    				// race_bias_rank 良/やや重/重/不良
    				if (isset($analyze_race_data["race_bias_rank"]["ダ"][\MpConsts::TRACK_BIAS_CODE[$race->track_bias]][$race->result_rank]))
    				{
    					$analyze_race_data["race_bias_rank"]["ダ"][\MpConsts::TRACK_BIAS_CODE[$race->track_bias]][$race->result_rank]++;
    				}
    				else
    				{
    					$analyze_race_data["race_bias_rank"]["ダ"][\MpConsts::TRACK_BIAS_CODE[$race->track_bias]][$race->result_rank] = 1;
    				}

    				// race_distance_rank 距離
    				if (isset($analyze_race_data["race_distance_rank"]["ダ"][$race->distance][$race->result_rank]))
    				{
    					$analyze_race_data["race_distance_rank"]["ダ"][$race->distance][$race->result_rank]++;
    				}
    				else
    				{
    					$analyze_race_data["race_distance_rank"]["ダ"][$race->distance][$race->result_rank] = 1;
    				}

    				// race_corner_rank 右左
    				if (isset($analyze_race_data["race_corner_rank"]["ダ"][\MpConsts::CORNER_DIRECTION_LIST[$race->corner_direction]][$race->result_rank]))
    				{
    					$analyze_race_data["race_corner_rank"]["ダ"][\MpConsts::CORNER_DIRECTION_LIST[$race->corner_direction]][$race->result_rank]++;
    				}
    				else
    				{
    					$analyze_race_data["race_corner_rank"]["ダ"][\MpConsts::CORNER_DIRECTION_LIST[$race->corner_direction]][$race->result_rank] = 1;
    				}
                }

            }
            // 障害芝
            else if ($race->track_type == \MpConsts::TRACK_TYPE_SHOGAI)
            {
                if ($race->result_rank >= 4)
    			{
    				// course_condition 芝/ダ
    				if (isset($analyze_race_data["course_condition_rank"]["障芝"][4]))
    				{
    					$analyze_race_data["course_condition_rank"]["障芝"][4]++;
    				}
    				else
    				{
    					$analyze_race_data["course_condition_rank"]["障芝"][4] = 1;
    				}
                    
                    // race_cushion_rank クッション
					$range_cushion = $mp_libs->get_cushion_range($race->cushion);
					if($range_cushion)
					{
						if (isset($analyze_race_data["race_cushion_rank"][$range_cushion][4]))
						{
							$analyze_race_data["race_cushion_rank"][$range_cushion][4]++;
						}
						else
						{
							$analyze_race_data["race_cushion_rank"][$range_cushion][4] = 1;
						}
					}

    				// race_bias_rank 良/やや重/重/不良
    				if (isset($analyze_race_data["race_bias_rank"]["障芝"][\MpConsts::TRACK_BIAS_CODE[$race->track_bias]][4]))
    				{
    					$analyze_race_data["race_bias_rank"]["障芝"][\MpConsts::TRACK_BIAS_CODE[$race->track_bias]][4]++;
    				}
    				else
    				{
    					$analyze_race_data["race_bias_rank"]["障芝"][\MpConsts::TRACK_BIAS_CODE[$race->track_bias]][4] = 1;
    				}

    				// race_distance_rank 距離
    				if (isset($analyze_race_data["race_distance_rank"]["障芝"][$race->distance][4]))
    				{
    					$analyze_race_data["race_distance_rank"]["障芝"][$race->distance][4]++;
    				}
    				else
    				{
    					$analyze_race_data["race_distance_rank"]["障芝"][$race->distance][4] = 1;
    				}
    				
    				// race_corner_rank 右左
    				if (isset($analyze_race_data["race_corner_rank"]["障芝"][\MpConsts::CORNER_DIRECTION_LIST[$race->corner_direction]][4]))
    				{
    					$analyze_race_data["race_corner_rank"]["障芝"][\MpConsts::CORNER_DIRECTION_LIST[$race->corner_direction]][4]++;
    				}
    				else
    				{
    					$analyze_race_data["race_corner_rank"]["障芝"][\MpConsts::CORNER_DIRECTION_LIST[$race->corner_direction]][4] = 1;
    				}
                }
                else
                {
    				// course_condition 芝/ダ
    				if (isset($analyze_race_data["course_condition_rank"]["障芝"][$race->result_rank]))
    				{
    					$analyze_race_data["course_condition_rank"]["障芝"][$race->result_rank]++;
    				}
    				else
    				{
    					$analyze_race_data["course_condition_rank"]["障芝"][$race->result_rank] = 1;
    				}
    				
					// race_cushion_rank クッション
					$range_cushion = $mp_libs->get_cushion_range($race->cushion);
					if($range_cushion)
					{
						if (isset($analyze_race_data["race_cushion_rank"][$range_cushion][$race->result_rank]))
						{
							$analyze_race_data["race_cushion_rank"][$range_cushion][$race->result_rank]++;
						}
						else
						{
							$analyze_race_data["race_cushion_rank"][$range_cushion][$race->result_rank] = 1;
						}
					}


    				// race_bias_rank 良/やや重/重/不良
    				if (isset($analyze_race_data["race_bias_rank"]["障芝"][\MpConsts::TRACK_BIAS_CODE[$race->track_bias]][$race->result_rank]))
    				{
    					$analyze_race_data["race_bias_rank"]["障芝"][\MpConsts::TRACK_BIAS_CODE[$race->track_bias]][$race->result_rank]++;
    				}
    				else
    				{
    					$analyze_race_data["race_bias_rank"]["障芝"][\MpConsts::TRACK_BIAS_CODE[$race->track_bias]][$race->result_rank] = 1;
    				}

    				// race_distance_rank 距離
    				if (isset($analyze_race_data["race_distance_rank"]["障芝"][$race->distance][$race->result_rank]))
    				{
    					$analyze_race_data["race_distance_rank"]["障芝"][$race->distance][$race->result_rank]++;
    				}
    				else
    				{
    					$analyze_race_data["race_distance_rank"]["障芝"][$race->distance][$race->result_rank] = 1;
    				}

    				// race_corner_rank 右左
    				if (isset($analyze_race_data["race_corner_rank"]["障芝"][\MpConsts::CORNER_DIRECTION_LIST[$race->corner_direction]][$race->result_rank]))
    				{
    					$analyze_race_data["race_corner_rank"]["障芝"][\MpConsts::CORNER_DIRECTION_LIST[$race->corner_direction]][$race->result_rank]++;
    				}
    				else
    				{
    					$analyze_race_data["race_corner_rank"]["障芝"][\MpConsts::CORNER_DIRECTION_LIST[$race->corner_direction]][$race->result_rank] = 1;
    				}
                }

            }
            // 障害ダート
            else if ($race->track_type == \MpConsts::TRACK_TYPE_SHOGAI_DART)
            {
                if ($race->result_rank >= 4)
    			{
    				// course_condition 芝/ダ
    				if (isset($analyze_race_data["course_condition_rank"]["障ダ"][4]))
    				{
    					$analyze_race_data["course_condition_rank"]["障ダ"][4]++;
    				}
    				else
    				{
    					$analyze_race_data["course_condition_rank"]["障ダ"][4] = 1;
    				}
                    
					// race_cushion_rank クッション
					$range_cushion = $mp_libs->get_cushion_range($race->cushion);
					if($range_cushion)
					{
						if (isset($analyze_race_data["race_cushion_rank"][$range_cushion][4]))
						{
							$analyze_race_data["race_cushion_rank"][$range_cushion][4]++;
						}
						else
						{
							$analyze_race_data["race_cushion_rank"][$range_cushion][4] = 1;
						}
					}

    				// race_bias_rank 良/やや重/重/不良
    				if (isset($analyze_race_data["race_bias_rank"]["障ダ"][\MpConsts::TRACK_BIAS_CODE[$race->track_bias]][4]))
    				{
    					$analyze_race_data["race_bias_rank"]["障ダ"][\MpConsts::TRACK_BIAS_CODE[$race->track_bias]][4]++;
    				}
    				else
    				{
    					$analyze_race_data["race_bias_rank"]["障ダ"][\MpConsts::TRACK_BIAS_CODE[$race->track_bias]][4] = 1;
    				}

    				// race_distance_rank 距離
    				if (isset($analyze_race_data["race_distance_rank"]["障ダ"][$race->distance][4]))
    				{
    					$analyze_race_data["race_distance_rank"]["障ダ"][$race->distance][4]++;
    				}
    				else
    				{
    					$analyze_race_data["race_distance_rank"]["障ダ"][$race->distance][4] = 1;
    				}
    				
    				// race_corner_rank 右左
    				if (isset($analyze_race_data["race_corner_rank"]["障ダ"][\MpConsts::CORNER_DIRECTION_LIST[$race->corner_direction]][4]))
    				{
    					$analyze_race_data["race_corner_rank"]["障ダ"][\MpConsts::CORNER_DIRECTION_LIST[$race->corner_direction]][4]++;
    				}
    				else
    				{
    					$analyze_race_data["race_corner_rank"]["障ダ"][\MpConsts::CORNER_DIRECTION_LIST[$race->corner_direction]][4] = 1;
    				}
                }
                else
                {
    				// course_condition 芝/ダ
    				if (isset($analyze_race_data["course_condition_rank"]["障ダ"][$race->result_rank]))
    				{
    					$analyze_race_data["course_condition_rank"]["障ダ"][$race->result_rank]++;
    				}
    				else
    				{
    					$analyze_race_data["course_condition_rank"]["障ダ"][$race->result_rank] = 1;
    				}
    				
					// race_cushion_rank クッション
					$range_cushion = $mp_libs->get_cushion_range($race->cushion);
					if($range_cushion)
					{
						if (isset($analyze_race_data["race_cushion_rank"][$range_cushion][$race->result_rank]))
						{
							$analyze_race_data["race_cushion_rank"][$range_cushion][$race->result_rank]++;
						}
						else
						{
							$analyze_race_data["race_cushion_rank"][$range_cushion][$race->result_rank] = 1;
						}
					}

    				// race_bias_rank 良/やや重/重/不良
    				if (isset($analyze_race_data["race_bias_rank"]["障ダ"][\MpConsts::TRACK_BIAS_CODE[$race->track_bias]][$race->result_rank]))
    				{
    					$analyze_race_data["race_bias_rank"]["障ダ"][\MpConsts::TRACK_BIAS_CODE[$race->track_bias]][$race->result_rank]++;
    				}
    				else
    				{
    					$analyze_race_data["race_bias_rank"]["障ダ"][\MpConsts::TRACK_BIAS_CODE[$race->track_bias]][$race->result_rank] = 1;
    				}

    				// race_distance_rank 距離
    				if (isset($analyze_race_data["race_distance_rank"]["障ダ"][$race->distance][$race->result_rank]))
    				{
    					$analyze_race_data["race_distance_rank"]["障ダ"][$race->distance][$race->result_rank]++;
    				}
    				else
    				{
    					$analyze_race_data["race_distance_rank"]["障ダ"][$race->distance][$race->result_rank] = 1;
    				}

    				// race_corner_rank 右左
    				if (isset($analyze_race_data["race_corner_rank"]["障ダ"][\MpConsts::CORNER_DIRECTION_LIST[$race->corner_direction]][$race->result_rank]))
    				{
    					$analyze_race_data["race_corner_rank"]["障ダ"][\MpConsts::CORNER_DIRECTION_LIST[$race->corner_direction]][$race->result_rank]++;
    				}
    				else
    				{
    					$analyze_race_data["race_corner_rank"]["障ダ"][\MpConsts::CORNER_DIRECTION_LIST[$race->corner_direction]][$race->result_rank] = 1;
    				}
                }

            }
            // 新潟直線
            else if ($race->track_type == \MpConsts::TRACK_TYPE_TERF_STRAIGHT)
            {
                if ($race->result_rank >= 4)
    			{
    				// course_condition 芝/ダ
    				if (isset($analyze_race_data["course_condition_rank"]["直"][4]))
    				{
    					$analyze_race_data["course_condition_rank"]["直"][4]++;
    				}
    				else
    				{
    					$analyze_race_data["course_condition_rank"]["直"][4] = 1;
    				}
                    
					// race_cushion_rank クッション
					$range_cushion = $mp_libs->get_cushion_range($race->cushion);
					if($range_cushion)
					{
						if (isset($analyze_race_data["race_cushion_rank"][$range_cushion][4]))
						{
							$analyze_race_data["race_cushion_rank"][$range_cushion][4]++;
						}
						else
						{
							$analyze_race_data["race_cushion_rank"][$range_cushion][4] = 1;
						}
					}

    				// race_bias_rank 良/やや重/重/不良
    				if (isset($analyze_race_data["race_bias_rank"]["直"][\MpConsts::TRACK_BIAS_CODE[$race->track_bias]][4]))
    				{
    					$analyze_race_data["race_bias_rank"]["直"][\MpConsts::TRACK_BIAS_CODE[$race->track_bias]][4]++;
    				}
    				else
    				{
    					$analyze_race_data["race_bias_rank"]["直"][\MpConsts::TRACK_BIAS_CODE[$race->track_bias]][4] = 1;
    				}

    				// race_distance_rank 距離
    				if (isset($analyze_race_data["race_distance_rank"]["直"][$race->distance][4]))
    				{
    					$analyze_race_data["race_distance_rank"]["直"][$race->distance][4]++;
    				}
    				else
    				{
    					$analyze_race_data["race_distance_rank"]["直"][$race->distance][4] = 1;
    				}
    				
    				// race_corner_rank 右左
    				if (isset($analyze_race_data["race_corner_rank"]["直"][\MpConsts::CORNER_DIRECTION_LIST[$race->corner_direction]][4]))
    				{
    					$analyze_race_data["race_corner_rank"]["直"][\MpConsts::CORNER_DIRECTION_LIST[$race->corner_direction]][4]++;
    				}
    				else
    				{
    					$analyze_race_data["race_corner_rank"]["直"][\MpConsts::CORNER_DIRECTION_LIST[$race->corner_direction]][4] = 1;
    				}
                }
                else
                {
    				// course_condition 芝/ダ
    				if (isset($analyze_race_data["course_condition_rank"]["直"][$race->result_rank]))
    				{
    					$analyze_race_data["course_condition_rank"]["直"][$race->result_rank]++;
    				}
    				else
    				{
    					$analyze_race_data["course_condition_rank"]["直"][$race->result_rank] = 1;
    				}
    				
					// race_cushion_rank クッション
					$range_cushion = $mp_libs->get_cushion_range($race->cushion);
					if($range_cushion)
					{
						if (isset($analyze_race_data["race_cushion_rank"][$range_cushion][$race->result_rank]))
						{
							$analyze_race_data["race_cushion_rank"][$range_cushion][$race->result_rank]++;
						}
						else
						{
							$analyze_race_data["race_cushion_rank"][$range_cushion][$race->result_rank] = 1;
						}
					}

    				// race_bias_rank 良/やや重/重/不良
    				if (isset($analyze_race_data["race_bias_rank"]["直"][\MpConsts::TRACK_BIAS_CODE[$race->track_bias]][$race->result_rank]))
    				{
    					$analyze_race_data["race_bias_rank"]["直"][\MpConsts::TRACK_BIAS_CODE[$race->track_bias]][$race->result_rank]++;
    				}
    				else
    				{
    					$analyze_race_data["race_bias_rank"]["直"][\MpConsts::TRACK_BIAS_CODE[$race->track_bias]][$race->result_rank] = 1;
    				}

    				// race_distance_rank 距離
    				if (isset($analyze_race_data["race_distance_rank"]["直"][$race->distance][$race->result_rank]))
    				{
    					$analyze_race_data["race_distance_rank"]["直"][$race->distance][$race->result_rank]++;
    				}
    				else
    				{
    					$analyze_race_data["race_distance_rank"]["直"][$race->distance][$race->result_rank] = 1;
    				}

    				// race_corner_rank 右左
    				if (isset($analyze_race_data["race_corner_rank"]["直"][\MpConsts::CORNER_DIRECTION_LIST[$race->corner_direction]][$race->result_rank]))
    				{
    					$analyze_race_data["race_corner_rank"]["直"][\MpConsts::CORNER_DIRECTION_LIST[$race->corner_direction]][$race->result_rank]++;
    				}
    				else
    				{
    					$analyze_race_data["race_corner_rank"]["直"][\MpConsts::CORNER_DIRECTION_LIST[$race->corner_direction]][$race->result_rank] = 1;
    				}
                }
            }

    		if ($race->result_rank >= 4)
    		{
    			// race_place_rank
    			if (isset($analyze_race_data["race_place_rank"][$race->place_name][4]))
				{
    				$analyze_race_data["race_place_rank"][$race->place_name][4]++;
    			}
				else
				{
    				$analyze_race_data["race_place_rank"][$race->place_name][4] = 1;
   				}

   				// race_pace_rank
    			if (isset($analyze_race_data["race_pace_rank"][trim($race->race_mark_2)][4]))
    			{
    				$analyze_race_data["race_pace_rank"][trim($race->race_mark_2)][4]++;
    			}
    			else
    			{
    				$analyze_race_data["race_pace_rank"][trim($race->race_mark_2)][4] = 1;
   				}
				// race_waku_rank
    			if (isset($analyze_race_data["race_waku_rank"][$race->no][4]))
				{
    				$analyze_race_data["race_waku_rank"][$race->no][4]++;
    			}
				else
				{
    				$analyze_race_data["race_waku_rank"][$race->no][4] = 1;
   				}

				// race_class_rank
    			if (isset($analyze_race_data["race_class_rank"][$race->class_code][4]))
				{
    				$analyze_race_data["race_class_rank"][$race->class_code][4]++;
    			}
				else
				{
    				$analyze_race_data["race_class_rank"][$race->class_code][4] = 1;
   				}
    		}
    		else
    		{
    			// race_place_rank
    			if (isset($analyze_race_data["race_place_rank"][$race->place_name][$race->result_rank])) {
    				$analyze_race_data["race_place_rank"][$race->place_name][$race->result_rank]++;
    			} else {
    				$analyze_race_data["race_place_rank"][$race->place_name][$race->result_rank] = 1;
   				}

   				// race_pace_rank
    			if (isset($analyze_race_data["race_pace_rank"][trim($race->race_mark_2)][$race->result_rank]))
    			{
    				$analyze_race_data["race_pace_rank"][trim($race->race_mark_2)][$race->result_rank]++;
    			}
    			else
    			{
    				$analyze_race_data["race_pace_rank"][trim($race->race_mark_2)][$race->result_rank] = 1;
   				}

   				// race_waku_rank
				if (isset($analyze_race_data["race_waku_rank"][trim($race->no)][$race->result_rank]))
				{
					$analyze_race_data["race_waku_rank"][trim($race->no)][$race->result_rank]++;
				}
				else
				{
					$analyze_race_data["race_waku_rank"][trim($race->no)][$race->result_rank] = 1;
				}

				// race_class_rank
				if (isset($analyze_race_data["race_class_rank"][trim($race->class_code)][$race->result_rank]))
				{
					$analyze_race_data["race_class_rank"][trim($race->class_code)][$race->result_rank]++;
				}
				else
				{
					$analyze_race_data["race_class_rank"][trim($race->class_code)][$race->result_rank] = 1;
				}
    		}
    	
        }
		return $analyze_race_data;
    }
}
