<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Horse;
use App\Models\HorseRaceHistory;
use App\Models\Hanro;
use App\Models\Wood;

class RaceHistoryController extends Controller
{
    /**
     * Horse_idをキーにデータを取得
     *
     */
    public function index($horse_id, $race_date = 0)
    {
        $mp_libs = new \MpLibs();
        $horses = new Horse();
        $horse = $horses->get_horse_with_father($horse_id);

        $hrh = new HorseRaceHistory();
        $hrh_data = $hrh->get_history_by_range($horse_id, $race_date);

		$hrh_data = $this->_set_chokyo_data($mp_libs, $horse_id, $hrh_data);

//dump($hrh_data);
        $arirr = $this->_analyze_race_individual_result_rank($mp_libs, $hrh_data);

        $date_s = $mp_libs->format_date(date("Ymd", time()), 1000);
        $date_e = date("Ymd", time());

        $hanro = new Hanro();
        $hanro_data = $hanro->get_target_date_range($horse_id, $date_s, $date_e);
        $wood = new Wood();
        $wood_data = $wood->get_target_date_range($horse_id, $date_s, $date_e);

        return view('race_history', [
            'horse' => $horse[0] ?? [],
            'hrh_data' => $hrh_data,
			'rank_analyze' => $arirr,
			'place_list' => \MpConsts::PLACE_ID_LIST,
            'wood_data' => $wood_data,
            'hanro_data' => $hanro_data,
        ]);
    }

	private function _set_chokyo_data($mp_libs, $horse_id, $hrh_data)
	{
		if (empty($hrh_data)) return $hrh_data;

		$target_date_s = 20200101;
		$hanro = new Hanro();
		$hanro_data = $hanro->get_target_date_range($horse_id, $target_date_s, 0);

		$wood = new Wood();
		$wood_data = $wood->get_target_date_range($horse_id, $target_date_s,  0);

		$add_chokyo_hrh_list = [];
		foreach ($hrh_data as $hrh)
		{
			$target_date_s = $hrh->race_date;
			$target_date_e = $mp_libs->format_date($hrh->race_date, 14);

			$point_list_w = [];
			$point_list_h = [];
			if (!empty($hanro_data))
			{
				foreach ($hanro_data as $hanro)
				{
					if ($target_date_s >= $hanro->training_date &&  $target_date_e <= $hanro->training_date)
					{
						$point_list_h[] = $hanro->point;
					}
				}
			}
			if (!empty($wood_data))
			{
				foreach ($wood_data as $wood)
				{
					if ($target_date_s >= $wood->training_date &&  $target_date_e <= $wood->training_date)
					{
						$point_list_w[] = $wood->point;
					}
				}
			}
			$hrh->hanro_point = 0;
			$hrh->wood_point = 0;
			if (!empty($point_list_h))
			{
				$hrh->hanro_point = max($point_list_h);
			} 
			if (!empty($point_list_w))
			{
				$hrh->wood_point = max($point_list_w);
			} 

			$add_chokyo_hrh_list[] = $hrh;

		}

		return $add_chokyo_hrh_list;
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
    		}
    	
        }
		return $analyze_race_data;
    }
}
