@extends('layouts.app')
@section('title', '過去レース情報')
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/js/jquery.tablesorter.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/css/theme.default.min.css">
<script>
    $(document).ready(function() {
    $('#race_history, #analyze1, #analyze2, #analyze3, #analyze4, #analyze5, #analyze6, #analyze7, #analyze8, #hanro, #wood').tablesorter({
            sortList: [[0,1]],
        });
    });
</script>
@section('content')
<div class="card">
  <div class="card-header">
    {{$horse->horse_name}} ({{$horse->father_name}}/{{$horse->grandfather_name}})
  </div>
  <div class="card-body">
    <p class="card-text">{{$horse->trainer_name}}</p>
    <p class="card-text">{{$horse->horse_comment}}</p>
  </div>
</div>
<hr />

@if(!empty($rank_analyze["race_waku_rank"]))
<div class="row">
  <div class="col">
    <p>◆枠別</p>
    <table id="analyze1" class="table table-sm table-hover" style="font-size: 7pt; line-height: 200%;">
      <tbody>
      <thead class="thead-dark">
        <tr>
          <th>馬番</th>
          <th>着順</th>
        </tr>
      </thead>
      @foreach($rank_analyze["race_waku_rank"] as $key => $rank)
        <tr>
          <td>{{$key}}</td>
          <td>
              {{$rank[1] ?? 0}}-{{$rank[2] ?? 0}}-{{$rank[3] ?? 0}}-{{$rank[4] ?? 0}}
          </td>
        </tr>
        @endforeach
     </tbody>
  </table>
  </div>
  @endif
  @if(!empty($rank_analyze["course_condition_rank"]))
  <div class="col">
    <p>◆芝/ダ</p>
    <table id="analyze2" class="table table-sm table-hover" style="font-size: 7pt; line-height: 200%;">
      <tbody>
      <thead class="thead-dark">
        <tr>
          <th>芝/ダ</th>
          <th>着順</th>
        </tr>
      </thead>
      @foreach($rank_analyze["course_condition_rank"] as $key => $rank)
        <tr>
          <td>{{$key}}</td>
          <td>
              {{$rank[1] ?? 0}}-{{$rank[2] ?? 0}}-{{$rank[3] ?? 0}}-{{$rank[4] ?? 0}}
          </td>
        </tr>
        @endforeach
     </tbody>
  </table>
  </div>
  @endif
  @if(!empty($rank_analyze["race_corner_rank"]))
  <div class="col">
    <p>◆左右</p>
    <table id="analyze3" class="table table-sm table-hover" style="font-size: 7pt; line-height: 200%;">
      <tbody>
      <thead class="thead-dark">
        <tr>
          <th>左右</th>
          <th>着順</th>
        </tr>
      </thead>
      @foreach($rank_analyze["race_corner_rank"] as $key => $rank)
      @foreach($rank as $k => $r)
        <tr>
          <td>{{$key}}{{$k}}</td>
          <td>
              {{$r[1] ?? 0}}-{{$r[2] ?? 0}}-{{$r[3] ?? 0}}-{{$r[4] ?? 0}}
          </td>
        </tr>
        
      @endforeach
      @endforeach
     </tbody>
  </table>
  </div>
  @endif
  @if(!empty($rank_analyze["race_place_rank"]))
  <div class="col">
    <p>◆場所別</p>
    <table id="analyze4" class="table table-sm table-hover" style="font-size: 7pt; line-height: 200%;">
      <tbody>
      <thead class="thead-dark">
        <tr>
          <th>場所</th>
          <th>着順</th>
        </tr>
      </thead>
      @foreach($rank_analyze["race_place_rank"] as $key => $rank)
        <tr>
          <td>{{$key}}</td>
          <td>
              {{$rank[1] ?? 0}}-{{$rank[2] ?? 0}}-{{$rank[3] ?? 0}}-{{$rank[4] ?? 0}}
          </td>
        </tr>
        @endforeach
     </tbody>
  </table>
  </div>
  @endif
  @if(!empty($rank_analyze["race_cushion_rank"]))
  <div class="col">
    <p>◆クッション</p>
    <table id="analyze5" class="table table-sm table-hover" style="font-size: 7pt; line-height: 200%;">
      <tbody>
      <thead class="thead-dark">
        <tr>
          <th>クッション</th>
          <th>着順</th>
        </tr>
      </thead>
      @foreach($rank_analyze["race_cushion_rank"] as $key => $rank)
        <tr>
          <td>{{$key/10}}</td>
          <td>
              {{$rank[1] ?? 0}}-{{$rank[2] ?? 0}}-{{$rank[3] ?? 0}}-{{$rank[4] ?? 0}}
          </td>
        </tr>
        @endforeach
     </tbody>
  </table>
  </div>
  @endif
  @if(!empty($rank_analyze["race_bias_rank"]))
  <div class="col">
    <p>◆Bias</p>
    <table id="analyze6" class="table table-sm table-hover" style="font-size: 7pt; line-height: 200%;">
      <tbody>
      <thead class="thead-dark">
        <tr>
          <th>Bias</th>
          <th>着順</th>
        </tr>
      </thead>
      @foreach($rank_analyze["race_bias_rank"] as $key => $rank)
      @foreach($rank as $k => $r)
        <tr>
          <td>{{$key}}{{$k}}</td>
          <td>
              {{$r[1] ?? 0}}-{{$r[2] ?? 0}}-{{$r[3] ?? 0}}-{{$r[4] ?? 0}}
          </td>
        </tr>
        
      @endforeach
      @endforeach
     </tbody>
  </table>
  </div>
  @endif
  @if(!empty($rank_analyze["race_distance_rank"]))
  <div class="col">
    <p>◆距離</p>
    <table id="analyze7" class="table table-sm table-hover" style="font-size: 7pt; line-height: 200%;">
      <tbody>
      <thead class="thead-dark">
        <tr>
          <th>距離</th>
          <th>着順</th>
        </tr>
      </thead>
      @foreach($rank_analyze["race_distance_rank"] as $key => $rank)
      @foreach($rank as $k => $r)
        <tr>
          <td>{{$key}}{{$k}}</td>
          <td>
              {{$r[1] ?? 0}}-{{$r[2] ?? 0}}-{{$r[3] ?? 0}}-{{$r[4] ?? 0}}
          </td>
        </tr>
        
      @endforeach
      @endforeach
     </tbody>
  </table>
  </div>
  @endif
  @if(!empty($rank_analyze["race_pace_rank"]))
  <div class="col">
    <p>◆ペース</p>
    <table id="analyze8" class="table table-sm table-hover" style="font-size: 7pt; line-height: 200%;">
      <tbody>
      <thead class="thead-dark">
        <tr>
          <th>pace</th>
          <th>着順</th>
        </tr>
      </thead>
      @foreach($rank_analyze["race_pace_rank"] as $key => $rank)
        <tr>
          <td>{{$key}}</td>
          <td>
              {{$rank[1] ?? 0}}-{{$rank[2] ?? 0}}-{{$rank[3] ?? 0}}-{{$rank[4] ?? 0}}
          </td>
        </tr>
        @endforeach
     </tbody>
  </table>
  </div>
  @endif
</div>

<table class="table table-hover table-sm" id="race_history">
  <thead>
    <tr class="table-secondary">
        <th scope="col">#</th>
        <th scope="col">name</th>
        <th scope="col">p</th>
        <th scope="col">距離</th>
        <th scope="col">track</th>
        <th scope="col">bias</th>
        <th scope="col">J</th>
        <th scope="col">馬体重</th>
        <th scope="col">斤量</th>
        <th scope="col">B</th>
        <th scope="col">M1</th>
        <th scope="col">HP</th>
        <th scope="col">WP</th>
        <th scope="col">加</th>
        <th scope="col">No</th>
        <th scope="col">人気</th>
        <th scope="col">ODDS</th>
        <th scope="col">RL</th>
        <th scope="col">RP</th>
        <th scope="col">time</th>
        <th scope="col">3f</th>
        <th scope="col">補９</th>
        <th scope="col">pci</th>
        <th scope="col">rpci</th>
        <th scope="col">rank</th>
        <th scope="col">着差</th>
        <th scope="col">3F差</th>
        <th scope="col">脚質</th>
        <th scope="col">道中</th>
        <th scope="col" style="width: 15%">C</th>
        <th scope="col" style="width: 15%">関係者C</th>
    </tr>
  </thead>
  <tbody class="table-group-divider">
    @foreach($hrh_data as $race)
    <tr>
        <th scope="row">{{$race->race_date}}</th>
        <td><a href="/race_card/{{$race->race_date}}/{{$race->place_code_name}}/{{$race->race_num}}">{{$race->race_name}}</a></td>
        <td>{{$race->place_name}}</td>
        <td>{{$race->distance}}</td>
        <td>
        @if($race->track_type == 1)
        芝
        @elseif($race->track_type == 2)
        ダ
        @elseif($race->track_type == 3)
        障芝
        @elseif($race->track_type == 4)
        障ダ
        @elseif($race->track_type == 8)
        芝外
        @endif
        </td>
        <td>
        @if($race->track_bias == 1)
        良
        @elseif($race->track_bias == 2)
        稍
        @elseif($race->track_bias == 3)
        重
        @elseif($race->track_bias == 4)
        不
        @endif
        </td>
        @if($race->jockey_rank == 6)
        <td class="first">
        @elseif($race->jockey_rank == 5)
        <td class="second">
        @elseif($race->jockey_rank == 4)
        <td class="third">
        @elseif($race->jockey_rank == 3)
        <td class="fourth">
        @else
        <td>
        @endif
            {{$race->jockey_name}}</td>
        <td>{{$race->body_weight}}({{$race->fluctuation_weight}})</td>
        <td>{{$race->weight}}</td>
        <td>{{$race->is_blinker ? "B" : "-"}}</td>
        <td>{{\MpConsts::HORSE_MARK_1_LIST[$race->mark_1]}}</td>

        @if($race->hanro_point >= 70)
        <td class="first">
        @elseif($race->hanro_point <= 70 && $race->hanro_point >= 66)
        <td class="second">
        @elseif($race->hanro_point <= 65 && $race->hanro_point >= 60)
        <td class="third">
        @elseif($race->hanro_point <= 59 && $race->hanro_point >= 55)
        <td class="fourth">
        @else
        <td>
        @endif  
        {{$race->hanro_point}}</td>

        @if($race->wood_point >= 70)
        <td class="first">
        @elseif($race->wood_point <= 70 && $race->wood_point >= 66)
        <td class="second">
        @elseif($race->wood_point <= 65 && $race->wood_point >= 60)
        <td class="third">
        @elseif($race->wood_point <= 59 && $race->wood_point >= 55)
        <td class="fourth">
        @else
        <td>
        @endif  
        {{$race->wood_point}}</td>
        
        <td>{{\MpConsts::HORSE_MARK_5_LIST[$race->mark_5]}}</td>
        <td>{{$race->no}}</td>
        <td>{{$race->ninki}}</td>
        <td>{{$race->odds}}</td>
        @if($race->race_mark_1 == 'R-高')
        <td class="first">
        @else
        <td>
        @endif
            {{$race->race_mark_1}}</td>
        <td>{{$race->race_mark_2}}</td>
        <td>{{$race->result_time}}</td>
        <td>{{$race->last_3f}}</td>
        <td>{{$race->hosei_9}}</td>
        <td>{{$race->pci}}</td>
        <td>{{$race->rpci}}</td>
        @if($race->result_rank == 1)
        <td class="first">
        @elseif($race->result_rank == 2)
        <td class="second">
        @elseif($race->result_rank == 3)
        <td class="third">
        @elseif($race->result_rank == 4 || $race->result_rank == 5)
        <td class="fourth">
        @else
        <td>
        @endif
            {{$race->result_rank}}</td>
        <td>{{$race->chakusa}}</td>
        <td>{{$race->last_3f_sa}}</td>
        <td>{{$race->clincher}}</td>
        <td>{{$race->passing_rank_1}}-{{$race->passing_rank_2}}-{{$race->passing_rank_3}}-{{$race->passing_rank_4}}</td>
        <td>
            @if(isset($race->race_comment))
                @if(json_validate($race->race_comment))
                    {{json_decode($race->race_comment)->comment}}
                @else
                    {{$race->race_comment}}
                @endif
            @endif
        </td>
        <td>{{$race->kol_comment}}</td>
    </tr>
    @endforeach
  </tbody>
</table>

<div class="row">
    <div class="col">
        <p>◆wood</p>
        <table id="wood" class="table table-sm table-hover" style="font-size: 8pt; line-height: 200%;">
            <thead class="thead-dark">
                <tr>
                    <th>horse_id</th>
                    <th>date</th>
                    <th>youbi</th>
                    <th>trainer</th>
                    <th>name</th>
                    <th>time</th>
                    <th>6F</th>
                    <th>5F</th>
                    <th>4F</th>
                    <th>3F</th>
                    <th>2F</th>
                    <th>1F</th>
                    <th>point</th>
                </tr>
            </thead>
            <tbody>
                @foreach($wood_data as $wood)
                <tr>
                    <td>{{$wood->horse_id}}</td>
                    <td>{{$wood->training_date}}</td>
                    <td>{{\MpConsts::YOUBI_LIST[$wood->youbi]}}</td>
                    <td>{{$wood->trainer_name}}</td>
                    <td><a href="/race_history/{{$wood->horse_id}}">{{$wood->horse_name}}</a></td>
                    @if(!empty($wood->time_6) &&  $wood->time_6 <= 80.9)
                    <td class="first">
                    @elseif(!empty($wood->time_6) &&  $wood->time_6 <= 81.9)
                    <td class="second">
                    @elseif(!empty($wood->time_6) &&  $wood->time_6 <= 82.9)
                    <td class="third">
                    @else
                    <td>
                    @endif
                        {{$wood->time_6}}-{{$wood->time_5}}-{{$wood->time_4}}-{{$wood->time_3}}-{{$wood->time_2}}-{{$wood->time_1}}</td>
                    <td>{{$wood->lap_6}}</td>
                    <td>{{$wood->lap_5}}</td>
                    <td>{{$wood->lap_4}}</td>
                    <td>{{$wood->lap_3}}</td>
                    @if(!empty($wood->lap_2) &&  $wood->lap_2 <= 11.0)
                    <td class="first">
                    @elseif(!empty($wood->lap_2) &&  $wood->lap_2  <= 11.3)
                    <td class="second">
                    @elseif(!empty($wood->lap_2) &&  $wood->lap_2  <= 11.5)
                    <td class="third">
                    @else
                    <td>
                    @endif
                        {{$wood->lap_2}}</td>
                    @if(!empty($wood->lap_1) &&  $wood->lap_1 <= 11.0)
                    <td class="first">
                    @elseif(!empty($wood->lap_1) &&  $wood->lap_1  <= 11.3)
                    <td class="second">
                    @elseif(!empty($wood->lap_1) &&  $wood->lap_1  <= 11.5)
                    <td class="third">
                    @else
                    <td>
                    @endif
                        {{$wood->lap_1}}</td>
                    <td>{{$wood->point}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="col">
        <p>◆hanro</p>
        <table id="hanro" class="table table-sm table-hover" style="font-size: 8pt; line-height: 200%;">
            <thead class="thead-dark">
                <tr>                                                                                                                                                                     
                    <th>horse_id</th>
                    <th>date</th>
                    <th>youbi</th>
                    <th>trainer</th>
                    <th>name</th>
                    <th>time</th>
                    <th>4F</th>
                    <th>3F</th>
                    <th>2F</th>
                    <th>1F</th>
                    <th>point</th>
                </tr>                                                                                                                                                                
            </thead>
            <tbody>
                @foreach($hanro_data as $hanro)
                <tr>
                    <td>{{$hanro->horse_id}}</td>
                    <td>{{$hanro->training_date}}</td>
                    <td>{{\MpConsts::YOUBI_LIST[$hanro->youbi]}}</td>
                    <td>{{$hanro->trainer_name}}</td>
                    <td><a href="/race_history/{{$hanro->horse_id}}">{{$hanro->horse_name}}</a></td>
                    @if(!empty($hanro->time_4) && $hanro->time_4 != 0.0 && $hanro->time_4 <= 50.9)
                    <td class="first">
                    @elseif(!empty($hanro->time_4) && $hanro->time_4 != 0.0 && $hanro->time_4 <= 51.9)
                    <td class="second">
                    @elseif(!empty($hanro->time_4) && $hanro->time_4 != 0.0 && $hanro->time_4 <= 52.9)
                    <td class="third">
                    @else
                    <td>
                    @endif
                        {{$hanro->time_4}}-{{$hanro->time_3}}-{{$hanro->time_2}}-{{$hanro->time_1}}</td>
                    <td>{{$hanro->lap_4}}</td>
                    <td>{{$hanro->lap_3}}</td>
                    @if (!empty($hanro->lap_2) && $hanro->lap_2 != 0.0 && $hanro->lap_2 <= 12.0)
                    <td class="first">
                    @elseif(!empty($hanro->lap_2) && $hanro->lap_2 != 0.0 && $hanro->lap_2 <= 12.3)
                    <td class="second">
                    @elseif(!empty($hanro->lap_2) && $hanro->lap_2 != 0.0 && $hanro->lap_2 <= 12.5)
                    <td class="third">
                    @else
                    <td>
                    @endif
                        {{$hanro->lap_2}}</td>
                    @if(!empty($hanro->lap_1) && $hanro->lap_1 != 0.0 && $hanro->lap_1 <= 12.0)
                    <td class="first">
                    @elseif(!empty($hanro->lap_1) && $hanro->lap_1 != 0.0 && $hanro->lap_1 <= 12.3)
                    <td class="second">
                    @elseif(!empty($hanro->lap_1) && $hanro->lap_1 != 0.0 && $hanro->lap_1 <= 12.5)
                    <td class="third">
                    @else
                    <td>
                    @endif
                        {{$hanro->lap_1}}</td>
                    <td>{{$hanro->point}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
