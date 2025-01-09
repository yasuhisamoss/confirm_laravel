@extends('layouts.app')
@section('title', '出馬表')
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/js/jquery.tablesorter.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/css/theme.default.min.css">
<script>
    $(document).ready(function() {
    $('#race_card_list').tablesorter({
            sortList: [[0,0]],
        });
    });

    function hanro_open(horse_id, race_id) {
      window.open("/chokyo/horse/"+horse_id+"/"+race_id , "horse", 'width=1000, height=800');
          }

    function wood_open(race_date, place, num) {
        window.open("/chokyo/race/"+race_date+"/"+place+"/"+num, "race", 'width=1000, height=800');
    }

</script>
@section('content')
<div class="card">
  <div class="card-header">
    {{substr($race_data["race_id"], 0, 8)}} {{$race_data["place"]}}{{$race_data["race_num"]}}
    @foreach($same_schedule as $ss)
    <a href="/race_card/{{$ss->race_date}}/{{$ss->place_code_name}}/{{$race_data["race_num"]}}"><button type="button" class="btn btn-outline-primary">{{$ss->place_name}}</button></a> 
    @endforeach
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-12">
        <h5 class="card-title">{{$race_data["race_name"]}} {{$race_data["track"]}} {{$race_data["distance"]}} {{$race_data["track_bias"]}} {{$race_data["joken"]}}</h5>
      </div>
      <div class="col-md-12">
        <p class="card-text">{{$race_data["race_mark_1"]}} {{$race_data["race_mark_2"]}} {{$race_data["race_mark_3"]}}</p>
      </div>
      <div class="col-md-4">
        <h5 class="card-title">当日含水率</h5>
        <p class="card-text">芝含水：{{$race_data["turf_moisture_content"]}} ダ含水：{{$race_data["dart_moisture_content"]}} C:{{$race_data["cushion"]}}</p>
      </div>
      <div class="col-md-4">
        <h5 class="card-title">同クラスの勝馬コース別補９情報</h5>
        <p class="card-text">AVE：{{$race_data["h9ave"]}} MIN：{{$race_data["h9min"]}} MAX:{{$race_data["h9max"]}}</p>
      </div>
      <div class="col-md-4">
        <h5 class="card-title">コースペース情報</h5>
        <p class="card-text">PACE：{{$corse_info[0]->race_mark_2 ?? ""}} RPCI:{{round($corse_info[0]->pci_ave ?? "0", 1)}} COUNT:{{$corse_info[0]->count ?? ""}}</p>
        <p class="card-text">PACE：{{$corse_info[1]->race_mark_2 ?? ""}} RPCI:{{round($corse_info[1]->pci_ave ?? "0", 1)}} COUNT:{{$corse_info[1]->count ?? ""}}</p>
      </div>
      @php
      $race_id = $race_data["race_id"];
      $race_date = $page_info['race_date'];
      $place = $page_info['place'];
      $num = $page_info['num'];
      @endphp
      <div class="col-md-1">
        <p class="card-text"><a href="#" onclick="wood_open({{$race_date}}, '{{$place}}', {{$num}} )"><button type="button" class="btn btn-outline-secondary">調教</button></a></p>
      </div>
      <div class="col-md-1">
        <form action="/search_exec" method="POST">
          @csrf
          <input type="hidden" name="distance_s" value="{{$race_data["distance"]}}">
          <input type="hidden" name="distance_e" value="{{$race_data["distance"]}}">
          <input type="hidden" name="place[]" value="{{$race_data["place_id"]}}">
          <input type="hidden" name="track_code[]" value="{{$race_data["track_code"]}}">
          <input type="hidden" name="track_bias[]" value="{{array_search($race_data["track_bias"], \MpConsts::TRACK_BIAS_CODE)}}">
          <input type="submit" class="btn btn-outline-dark" value="同条件">
        </form>
      </div>
    </div>
  </div>
</div>

<div class="row">
  @if(!empty($ranking_summary["hanro"]))
  <div class="col">
    <p>◆hanro</p>
    <table id="analyze2" class="table table-sm table-hover" style="font-size: 7pt; line-height: 200%;">
      <tbody>
      <thead class="thead-dark">
        <tr>
          <th>no</th>
          <th>name</th>
          <th>p</th>
          <th>r</th>
        </tr>
      </thead>
      @foreach($ranking_summary["hanro"] as $rank)
        <tr>
          <td><a href="{{$race_data['race_num']}}?h={{$rank['no']}}">{{$rank["no"]}}</a></td>
          @if($h == $rank["no"])
          <td class="first">
          @else
          <td>
          @endif
            {{$rank["name"]}}
          </td>
          <td>{{$rank["point"]}}</td>
          @if($rank["rank"] == 1)
          <td class="first">
          @elseif($rank["rank"] == 2)
          <td class="second">
          @elseif($rank["rank"] == 3)
          <td class="third">
          @else
          <td>
          @endif
          {{$rank["rank"]}}</td>
        </tr>
        @endforeach
     </tbody>
  </table>
  </div>
  @endif
  @if(!empty($ranking_summary["wood"]))
  <div class="col">
    <p>◆wood</p>
    <table id="analyze3" class="table table-sm table-hover" style="font-size: 7pt; line-height: 200%;">
      <tbody>
      <thead class="thead-dark">
        <tr>
          <th>no</th>
          <th>name</th>
          <th>p</th>
          <th>r</th>
        </tr>
      </thead>
      @foreach($ranking_summary["wood"] as $rank)
        <tr>
          <td><a href="{{$race_data['race_num']}}?h={{$rank['no']}}">{{$rank["no"]}}</a></td>
          @if($h == $rank["no"])
          <td class="first">
          @else
          <td>
          @endif
            {{$rank["name"]}}
          </td>
          <td>{{$rank["point"]}}</td>
          @if($rank["rank"] == 1)
          <td class="first">
          @elseif($rank["rank"] == 2)
          <td class="second">
          @elseif($rank["rank"] == 3)
          <td class="third">
          @else
          <td>
          @endif
          {{$rank["rank"]}}</td>
        </tr>
        @endforeach
     </tbody>
  </table>
  </div>
  @endif
  @if(!empty($ranking_summary["mop"]))
  <div class="col">
    <p>◆mop</p>
    <table id="analyze1" class="table table-sm table-hover" style="font-size: 7pt; line-height: 200%;">
      <tbody>
      <thead class="thead-dark">
        <tr>
          <th>no</th>
          <th>name</th>
          <th>p</th>
          <th>r</th>
        </tr>
      </thead>
      @foreach($ranking_summary["mop"] as $rank)
        <tr>
          <td><a href="{{$race_data['race_num']}}?h={{$rank['no']}}">{{$rank["no"]}}</a></td>
          @if($h == $rank["no"])
          <td class="first">
          @else
          <td>
          @endif
            {{$rank["name"]}}
          </td>
          <td>{{$rank["point"]}}</td>
          @if($rank["rank"] == 1)
          <td class="first">
          @elseif($rank["rank"] == 2)
          <td class="second">
          @elseif($rank["rank"] == 3)
          <td class="third">
          @else
          <td>
          @endif
          {{$rank["rank"]}}</td>
        </tr>
        @endforeach
     </tbody>
  </table>
  </div>
  @endif
 
  @if(!empty($ranking_summary["ho_9"]))
  <div class="col">
    <p>◆ho_9</p>
    <table id="analyze1" class="table table-sm table-hover" style="font-size: 7pt; line-height: 200%;">
      <tbody>
      <thead class="thead-dark">
        <tr>
          <th>no</th>
          <th>name</th>
          <th>p</th>
          <th>r</th>
        </tr>
      </thead>
      @foreach($ranking_summary["ho_9"] as $rank)
        <tr>
          <td><a href="{{$race_data['race_num']}}?h={{$rank['no']}}">{{$rank["no"]}}</a></td>
          @if($h == $rank["no"])
          <td class="first">
          @else
          <td>
          @endif
            {{$rank["name"]}}
          </td>
          <td>{{$rank["point"]}}</td>
          @if($rank["rank"] == 1)
          <td class="first">
          @elseif($rank["rank"] == 2)
          <td class="second">
          @elseif($rank["rank"] == 3)
          <td class="third">
          @else
          <td>
          @endif
          {{$rank["rank"]}}</td>
        </tr>
        @endforeach
     </tbody>
  </table>
  </div>
  @endif
  @if(!empty($ranking_summary["point"]))
  <div class="col">
    <p>◆point</p>
    <table id="analyze4" class="table table-sm table-hover" style="font-size: 7pt; line-height: 200%;">
      <tbody>
      <thead class="thead-dark">
        <tr>
          <th>no</th>
          <th>name</th>
          <th>p</th>
          <th>r</th>
        </tr>
      </thead>
      @foreach($ranking_summary["point"] as $rank)
        <tr>
          <td><a href="{{$race_data['race_num']}}?h={{$rank['no']}}">{{$rank["no"]}}</a></td>
          @if($h == $rank["no"])
          <td class="first">
          @else
          <td>
          @endif
            {{$rank["name"]}}
          </td>
          <td>{{$rank["point"]}}</td>
          @if($rank["rank"] == 1)
          <td class="first">
          @elseif($rank["rank"] == 2)
          <td class="second">
          @elseif($rank["rank"] == 3)
          <td class="third">
          @else
          <td>
          @endif
          {{$rank["rank"]}}</td>
        </tr>
        @endforeach
     </tbody>
  </table>
  </div>
  @endif
  @if(!empty($ranking_summary["match"]))
  <div class="col">
    <p>◆match</p>
    <table id="analyze5" class="table table-sm table-hover" style="font-size: 7pt; line-height: 200%;">
      <tbody>
      <thead class="thead-dark">
        <tr>
          <th>no</th>
          <th>name</th>
          <th>p</th>
          <th>r</th>
        </tr>
      </thead>
      @foreach($ranking_summary["match"] as $rank)
        <tr>
          <td><a href="{{$race_data['race_num']}}?h={{$rank['no']}}">{{$rank["no"]}}</a></td>
          @if($h == $rank["no"])
          <td class="first">
          @else
          <td>
          @endif
            {{$rank["name"]}}
          </td>
          <td>{{$rank["point"]}}</td>
          @if($rank["rank"] == 1)
          <td class="first">
          @elseif($rank["rank"] == 2)
          <td class="second">
          @elseif($rank["rank"] == 3)
          <td class="third">
          @else
          <td>
          @endif
          {{$rank["rank"]}}</td>
        </tr>
        @endforeach
     </tbody>
  </table>
  </div>
  @endif
  @if(!empty($ranking_summary["cushion"]))
  <div class="col">
    <p>◆cushion</p>
    <table id="analyze6" class="table table-sm table-hover" style="font-size: 7pt; line-height: 200%;">
      <tbody>
      <thead class="thead-dark">
        <tr>
          <th>no</th>
          <th>name</th>
          <th>p</th>
          <th>r</th>
        </tr>
      </thead>
      @foreach($ranking_summary["cushion"] as $rank)
        <tr>
          <td><a href="{{$race_data['race_num']}}?h={{$rank['no']}}">{{$rank["no"]}}</a></td>
          @if($h == $rank["no"])
          <td class="first">
          @else
          <td>
          @endif
            {{$rank["name"]}}
          </td>
          <td>{{$rank["point"]}}</td>
          @if($rank["rank"] == 1)
          <td class="first">
          @elseif($rank["rank"] == 2)
          <td class="second">
          @elseif($rank["rank"] == 3)
          <td class="third">
          @else
          <td>
          @endif
          {{$rank["rank"]}}</td>
        </tr>
        @endforeach
     </tbody>
  </table>
  </div>
  @endif
  @if(!empty($ranking_summary["bias"]))
  <div class="col">
    <p>◆bias</p>
    <table id="analyze6" class="table table-sm table-hover" style="font-size: 7pt; line-height: 200%;">
      <tbody>
      <thead class="thead-dark">
        <tr>
          <th>no</th>
          <th>name</th>
          <th>p</th>
          <th>r</th>
        </tr>
      </thead>
      @foreach($ranking_summary["bias"] as $rank)
        <tr>
          <td><a href="{{$race_data['race_num']}}?h={{$rank['no']}}">{{$rank["no"]}}</a></td>
          @if($h == $rank["no"])
          <td class="first">
          @else
          <td>
          @endif
            {{$rank["name"]}}
          </td>
          <td>{{$rank["point"]}}</td>
          @if($rank["rank"] == 1)
          <td class="first">
          @elseif($rank["rank"] == 2)
          <td class="second">
          @elseif($rank["rank"] == 3)
          <td class="third">
          @else
          <td>
          @endif
          {{$rank["rank"]}}</td>
        </tr>
        @endforeach
     </tbody>
  </table>
  </div>
  @endif
  @if(!empty($ranking_summary["stallion"]))
  <div class="col">
    <p>◆stallion</p>
    <table id="analyze6" class="table table-sm table-hover" style="font-size: 7pt; line-height: 200%;">
      <tbody>
      <thead class="thead-dark">
        <tr>
          <th>no</th>
          <th>name</th>
          <th>target</th>
          <th>p</th>
          <th>r</th>
        </tr>
      </thead>
      @foreach($ranking_summary["stallion"] as $rank)
        <tr>
          <td><a href="{{$race_data['race_num']}}?h={{$rank['no']}}">{{$rank["no"]}}</a></td>
          @if($h == $rank["no"])
          <td class="first">
          @else
          <td>
          @endif
            {{$rank["name"]}}
          </td>
          <td>{{$rank["target_name"]}}</td>
          <td>{{$rank["point"]}}</td>
          @if($rank["rank"] == 1)
          <td class="first">
          @elseif($rank["rank"] == 2)
          <td class="second">
          @elseif($rank["rank"] == 3)
          <td class="third">
          @else
          <td>
          @endif
          {{$rank["rank"]}}</td>
        </tr>
        @endforeach
     </tbody>
  </table>
  </div>
  @endif
  @if(!empty($ranking_summary["jockey"]))
  <div class="col">
    <p>◆jockey</p>
    <table id="analyze6" class="table table-sm table-hover" style="font-size: 7pt; line-height: 200%;">
      <tbody>
      <thead class="thead-dark">
        <tr>
          <th>no</th>
          <th>name</th>
          <th>target</th>
          <th>p</th>
          <th>r</th>
        </tr>
      </thead>
      @foreach($ranking_summary["jockey"] as $rank)
        <tr>
          <td><a href="{{$race_data['race_num']}}?h={{$rank['no']}}">{{$rank["no"]}}</a></td>
          @if($h == $rank["no"])
          <td class="first">
          @else
          <td>
          @endif
            {{$rank["name"]}}
          </td>
          <td>{{$rank["target_name"]}}</td>
          <td>{{$rank["point"]}}</td>
          @if($rank["rank"] == 1)
          <td class="first">
          @elseif($rank["rank"] == 2)
          <td class="second">
          @elseif($rank["rank"] == 3)
          <td class="third">
          @else
          <td>
          @endif
          {{$rank["rank"]}}</td>
        </tr>
        @endforeach
     </tbody>
  </table>
  </div>
  @endif
</div>

<nav aria-label="Page navigation example">
  <ul class="pagination">
    @if($page_info["num"]!= 1)
    <li class="page-item">
      <a class="page-link" href="/race_card/{{$page_info['race_date']}}/{{$page_info['place']}}/{{$page_info['num']-1}}" aria-label="Previous">
        <span aria-hidden="true">&laquo;</span>
      </a>
    </li>
    @endif
    @for($i=1; $i <= 12; $i++)
    <li class="page-item"><a class="page-link" href="/race_card/{{$page_info['race_date']}}/{{$page_info['place']}}/{{$i}}">{{$i}}</a></li>
    @endfor

    @if($page_info["num"]!= 12)
    <li class="page-item">
      <a class="page-link" href="/race_card/{{$page_info['race_date']}}/{{$page_info['place']}}/{{$page_info['num']+1}}" aria-label="Next">
        <span aria-hidden="true">&raquo;</span>
      </a>
    </li>
    @endif
  </ul>
</nav>

<table class="table table-hover table-sm"  style="font-size: 7pt; line-height: 200%;" id="race_card_list">
<thead>
    <tr class="table-secondary">
    <th scope="col">#</th>
        <th scope="col">name</th>
        <th scope="col">M1</th>
        <th scope="col">M2</th>
        <th scope="col">HP</th>
        <th scope="col">WP</th>
        <th scope="col">M4</th>
        <th scope="col">M5</th>
        <th scope="col">M6</th>
        <th scope="col">間</th>
        <th scope="col">体重</th>
        <th scope="col">斤量</th>
        <th scope="col">斤比</th>
        <th scope="col">J</th>
        <th scope="col">RAP</th>
        <th scope="col">前付P</th>
        <th scope="col">wpp</th>
        <th scope="col">FUP</th>
        <th scope="col">人気</th>
        <th scope="col">ODDS</th>
        <th scope="col">RANK</th>
        <th scope="col">MOP</th>
        <th scope="col">MTC</th>
        <th scope="col">h9</th>
        <th scope="col">距h9</th>
        <th scope="col">P</th>
        
        <th scope="col">前P</th>
        <th scope="col">場P</th>
        <th scope="col">距P</th>
        <th scope="col">ペP</th>
        <th scope="col">Cu_P</th>
        <th scope="col">TBP</th>
        <th scope="col">CTP</th>
        <th scope="col">MCP</th>

        <th scope="col">父</th>
        <th scope="col">race_C</th>
        <th scope="col">kol_C</th>
        <th scope="col">C</th>
    </tr>
  </thead>
  <tbody class="table-group-divider">
    @foreach($race_card as $no => $card)
    <tr>
        <td><a href="{{$race_data['race_num']}}?h={{$no}}">{{$no}}</a></th>
        <td class="first"><a href="/race_history/{{$card['horse_id']}}">{{$card["horse_name"]}}</a></td>
        <td>{{$card["mark_1"]}}</td>
        <td>{{$card["mark_2"]}}</td>
        @if($card["hanro_point"] >= 70)
        <td class="first">
        @elseif($card["hanro_point"] <= 70 && $card["hanro_point"] >= 66)
        <td class="second">
        @elseif($card["hanro_point"] <= 65 && $card["hanro_point"] >= 60)
        <td class="third">
        @elseif($card["hanro_point"] <= 59 && $card["hanro_point"] >= 55)
        <td class="fourth">
        @else
        <td>
        @endif
        <a href="#" onclick="hanro_open({{$card['horse_id']}}, {{$race_id}})">{{$card["hanro_point"]}}</a></td>
        @if($card["wood_point"] >= 70)
        <td class="first">
        @elseif($card["wood_point"] <= 70 && $card["wood_point"] >= 66)
        <td class="second">
        @elseif($card["wood_point"] <= 65 && $card["wood_point"] >= 60)
        <td class="third">
        @elseif($card["wood_point"] <= 59 && $card["wood_point"] >= 55)
        <td class="fourth">
        @else
        <td>
        @endif
        <a href="#" onclick="hanro_open({{$card['horse_id']}}, {{$race_id}})">{{$card["wood_point"]}}</a></td>
        <td>{{$card["mark_4"]}}</td>
        <td>{{$card["mark_5"]}}</td>
        <td>{{$card["mark_6"]}}</td>
        <td>{{$card["date_diff"]}}</td>
        <td>{{$card["body_weight"]}}</td>
        <td>{{$card["weight"]}}</td>
        <td>{{$card["hande_weight_per"]}}</td>
        @if($card["jockey_rank"] == 6)
        <td class="first">
        @elseif($card["jockey_rank"] == 5)
        <td class="second">
        @elseif($card["jockey_rank"] == 4)
        <td class="third">
        @elseif($card["jockey_rank"] == 3)
        <td class="fourth">
        @else
        <td>       
        @endif
        {{$card["jockey"]}}</td>

        <td>{{$card["rank_sum"]}}</td>

        @if($card["point"]["front"] > 10)
        <td class="pif_1">
        @elseif($card["point"]["front"] > 7)
        <td class="pif_2">
        @elseif($card["point"]["front"] > 5)
        <td class="pif_3">
        @elseif($card["point"]["front"] > 3)
        <td class="pif_4">
        @elseif($card["point"]["front"] > 1)
        <td class="pif_5">
        @else
        <td>       
        @endif
        {{$card["point"]["front"]}}</td>

        <td>{{$card["point"]["wpp"]}}</td>
        <td>{{$card["fup"]}}</td>

        <td>{{$card["ninki"]}}</td>
        <td>{{$card["odds"]}}</td>
        @if($card["rank"] == 1)
        <td class="first">
        @elseif($card["rank"] == 2)
        <td class="second">
        @elseif($card["rank"] == 3)
        <td class="third">
        @elseif($card["rank"] == 4)
        <td class="fourth">
        @elseif($card["rank"] == 5)
        <td class="fifth">
        @else
        <td>
        @endif{{$card["rank"]}}</td>

        @if($card["race_mark_point"]["rank"] == 1)
        <td class="first">
        @elseif($card["race_mark_point"]["rank"] == 2)
        <td class="second">
        @elseif($card["race_mark_point"]["rank"] == 3)
        <td class="third">
        @elseif($card["race_mark_point"]["rank"] == 4)
        <td class="fourth">
        @elseif($card["race_mark_point"]["rank"] == 5)
        <td class="fifth">
        @else
        <td>
        @endif
        {{$card["race_mark_point"]["point"]}}</td>
        
        @if($card["match"]["rank"] == 1)
        <td class="first">
        @elseif($card["match"]["rank"] == 2)
        <td class="second">
        @elseif($card["match"]["rank"] == 3)
        <td class="third">
        @elseif($card["match"]["rank"] == 4)
        <td class="fourth">
        @elseif($card["match"]["rank"] == 5)
        <td class="fifth">
        @else
        <td>
        @endif
        {{$card["match"]["point"]}}</td>

        @if($card["point"]["ho_9_rank"] == 1)
        <td class="first">
        @elseif($card["point"]["ho_9_rank"] == 2)
        <td class="second">
        @elseif($card["point"]["ho_9_rank"] == 3)
        <td class="third">
        @elseif($card["point"]["ho_9_rank"] == 4)
        <td class="fourth">
        @elseif($card["point"]["ho_9_rank"] == 5)
        <td class="fifth">
        @else
        <td>
        @endif
        {{$card["point"]["h9_ave"]}}</td>
        
        @if($card["same_distance_point"]["ho_9_rank"] == 1)
        <td class="first">
        @elseif($card["same_distance_point"]["ho_9_rank"] == 2)
        <td class="second">
        @elseif($card["same_distance_point"]["ho_9_rank"] == 3)
        <td class="third">
        @elseif($card["same_distance_point"]["ho_9_rank"] == 4)
        <td class="fourth">
        @elseif($card["same_distance_point"]["ho_9_rank"] == 5)
        <td class="fifth">
        @else
        <td>
        @endif
        {{$card["same_distance_point"]["h9_ave"]}}</td>

        @if($card["point"]["rank"] == 1)
        <td class="first">
        @elseif($card["point"]["rank"] == 2)
        <td class="second">
        @elseif($card["point"]["rank"] == 3)
        <td class="third">
        @elseif($card["point"]["rank"] == 4)
        <td class="fourth">
        @elseif($card["point"]["rank"] == 5)
        <td class="fifth">
        @else
        <td>
        @endif
        {{$card["point"]["point"]}}</td>
        
        @if($card["previous_race_point"]["rank"] == 1)
        <td class="first">
        @elseif($card["previous_race_point"]["rank"] == 2)
        <td class="second">
        @elseif($card["previous_race_point"]["rank"] == 3)
        <td class="third">
        @elseif($card["previous_race_point"]["rank"] == 4)
        <td class="fourth">
        @elseif($card["previous_race_point"]["rank"] == 5)
        <td class="fifth">
        @else
        <td>
        @endif
        {{$card["previous_race_point"]["point"]}}</td>

        @if($card["same_course_race_point"]["rank"] == 1)
        <td class="first">
        @elseif($card["same_course_race_point"]["rank"] == 2)
        <td class="second">
        @elseif($card["same_course_race_point"]["rank"] == 3)
        <td class="third">
        @elseif($card["same_course_race_point"]["rank"] == 4)
        <td class="fourth">
        @elseif($card["same_course_race_point"]["rank"] == 5)
        <td class="fifth">
        @else
        <td>
        @endif
        {{$card["same_course_race_point"]["point"]}}</td>

        @if($card["same_distance_point"]["rank"] == 1)
        <td class="first">
        @elseif($card["same_distance_point"]["rank"] == 2)
        <td class="second">
        @elseif($card["same_distance_point"]["rank"] == 3)
        <td class="third">
        @elseif($card["same_distance_point"]["rank"] == 4)
        <td class="fourth">
        @elseif($card["same_distance_point"]["rank"] == 5)
        <td class="fifth">
        @else
        <td>
        @endif
        {{$card["same_distance_point"]["point"]}}</td>

        @if($card["same_pace_point"]["rank"] == 1)
        <td class="first">
        @elseif($card["same_pace_point"]["rank"] == 2)
        <td class="second">
        @elseif($card["same_pace_point"]["rank"] == 3)
        <td class="third">
        @elseif($card["same_pace_point"]["rank"] == 4)
        <td class="fourth">
        @elseif($card["same_pace_point"]["rank"] == 5)
        <td class="fifth">
        @else
        <td>
        @endif
        {{$card["same_pace_point"]["point"]}}</td>

        @if($card["cushion_pace_point"]["rank"] == 1)
        <td class="first">
        @elseif($card["cushion_pace_point"]["rank"] == 2)
        <td class="second">
        @elseif($card["cushion_pace_point"]["rank"] == 3)
        <td class="third">
        @elseif($card["cushion_pace_point"]["rank"] == 4)
        <td class="fourth">
        @elseif($card["cushion_pace_point"]["rank"] == 5)
        <td class="fifth">
        @else
        <td>
        @endif
        {{$card["cushion_pace_point"]["point"]}}</td>

        @if($card["track_bias_race_point"]["rank"] == 1)
        <td class="first">
        @elseif($card["track_bias_race_point"]["rank"] == 2)
        <td class="second">
        @elseif($card["track_bias_race_point"]["rank"] == 3)
        <td class="third">
        @elseif($card["track_bias_race_point"]["rank"] == 4)
        <td class="fourth">
        @elseif($card["track_bias_race_point"]["rank"] == 5)
        <td class="fifth">
        @else
        <td>
        @endif
        {{$card["track_bias_race_point"]["point"]}}</td>

        @if($card["corner_type_race_point"]["rank"] == 1)
        <td class="first">
        @elseif($card["corner_type_race_point"]["rank"] == 2)
        <td class="second">
        @elseif($card["corner_type_race_point"]["rank"] == 3)
        <td class="third">
        @elseif($card["corner_type_race_point"]["rank"] == 4)
        <td class="fourth">
        @elseif($card["corner_type_race_point"]["rank"] == 5)
        <td class="fifth">
        @else
        <td>
        @endif
        {{$card["corner_type_race_point"]["point"]}}</td>

        @if($card["moisture_content"]["rank"] == 1)
        <td class="first">
        @elseif($card["moisture_content"]["rank"] == 2)
        <td class="second">
        @elseif($card["moisture_content"]["rank"] == 3)
        <td class="third">
        @elseif($card["moisture_content"]["rank"] == 4)
        <td class="fourth">
        @elseif($card["moisture_content"]["rank"] == 5)
        <td class="fifth">
        @else
        <td>
        @endif
        {{$card["moisture_content"]["point"]}}</td>

        @if($card["stallion_point"] >= 7)
        <td class="first">
        @elseif($card["stallion_point"]  >= 6)
        <td class="second">
        @elseif($card["stallion_point"] >= 5)
        <td class="third">
        @elseif($card["stallion_point"]  >= 4)
        <td class="fourth">
        @elseif($card["stallion_point"] >= 2)
        <td class="fifth">
        @else
        <td>
        @endif
        <a href="/stallion?stallion={{$card['father_id']}}&distance_s={{$race_data["distance"]}}&distance_e={{$race_data["distance"]}}&track_code[]={{$race_data["track_code"]}}&track_bias[]={{array_search($race_data["track_bias"], \MpConsts::TRACK_BIAS_CODE)}}">{{$card["father"]}}({{$card["stallion_point"]}})</a>
        </td>
        <td>{{$card["ago_race_comment"]}}</td>
        <td>{{$card["ago_kol_comment"]}}</td>
        <td>{{$card["horse_comment"]}}</td>
    </tr>
    @endforeach
  </tbody>
</table>
@endsection
