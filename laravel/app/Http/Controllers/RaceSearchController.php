<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Models\Horse;
//use App\Models\HorseRaceHistory;
//use App\Models\Hanro;
//use App\Models\Wood;
use App\Models\Race;
use App\Models\Stallion;

class RaceSearchController extends Controller
{
    public function index()
    {
        $mp_libs = new \MpLibs();
        $stallion_list = Stallion::all();
        return view('race_search', [
            //'horse' => $horse[0] ?? [],
            //'hrh_data' => $hrh_data,
			'stallion_list' => $stallion_list
        ]);
    }

    public function exec(Request $request)
    {
        $mp_libs = new \MpLibs();

        $race = new Race();
        $race_search = $race->race_search($request);

        $analyze_search = $this->_analyze_search_data($race_search);
        $stallion_list = Stallion::all();
        //dd($stallion_list);

        return view('race_search', [
            'params' => $request,
            'search_data' => $race_search ?? [],
            'analyze_search' => $analyze_search,
            'stallion_list' => $stallion_list
        ]);
    }

    public function _analyze_search_data($race_search)
    {
        $return_array = [
            "pace" => [],
            "waku" => [],
            "clincher" => [],
        ];
        
        if (empty($race_search)) return $return_array;
        $array_count = count($race_search);
        foreach ($race_search as $rs)
        {
            // pace
            if (!isset($return_array["pace"][$rs->race_mark_2]["count"]))
            {
                $return_array["pace"][$rs->race_mark_2]["count"] = 1;
            }
            else 
            {
                $return_array["pace"][$rs->race_mark_2]["count"]++;
            }

            // waku
            if (!isset($return_array["waku"][$rs->waku_no]["count"]))
            {
                $return_array["waku"][$rs->waku_no]["count"] = 1;
            }
            else 
            {
                $return_array["waku"][$rs->waku_no]["count"]++;
            }

            // clincher
            if (!isset($return_array["clincher"][$rs->clincher]["count"]))
            {
                $return_array["clincher"][$rs->clincher]["count"] = 1;
            }
            else 
            {
                $return_array["clincher"][$rs->clincher]["count"]++;
            }

        }
        return $return_array;
    }

}
