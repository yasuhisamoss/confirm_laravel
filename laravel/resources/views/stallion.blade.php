@extends('layouts.app')
@section('title', '種牡馬')
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/js/jquery.tablesorter.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/css/theme.default.min.css">
<script>
    $(document).ready(function() {
    $('#race_history, #analyze1, #analyze2, #analyze3, #analyze4, #analyze5, #analyze6, #analyze7, #analyze8').tablesorter({
            sortList: [[0,1]],
        });
    });

    $(function(){
        $('.select').select2();
    });

    function corse_regist_open(stallion_id) {
        window.open("/corse_point_regist/"+stallion_id, "race", 'width=700, height=800');
    }
</script>
@section('content')
<form action="/stallion" method="GET">
<div class="row">
    <div class="col">
    <label for="staticEmail" class="col-sm-1 col-form-label">種牡馬</label>
      <select class="select" name="stallion">
      <option value="0">--</option>
        @foreach($stallion_list as $stallion)
        <option value="{{$stallion->stallion_id}}" @if($stallion_id == $stallion->stallion_id) selected @endif>{{$stallion->stallion_name}}</option>
        @endforeach
    </select>
    </div>  
  </div>
  <div class="row">
  <label for="staticEmail" class="col-sm-1 col-form-label">距離</label>
    <div class="col-md-1">
      <input type="number" class="form-control" name="distance_s" max="5000" min="0" step="100" value="{{$params['distance_s'] ?? 1000}}">
    </div>
    ～
    <div class="col-md-1">
      <input type="number" class="form-control" name="distance_e" max="5000" min="0" step="100" value="{{$params['distance_e'] ?? 5000}}">
    </div>
  </div>
  <div class="row">
    <div class="col">
    <label for="staticEmail" class="col-sm-1 col-form-label">トラック</label>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="track_code[]" id="inlineCheckbox1" value="1" @if(isset($params['track_code']) && in_array(1, $params['track_code']) ) checked @endif>
        <label class="form-check-label" for="inlineCheckbox1">芝</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="track_code[]" id="inlineCheckbox2" value="2" @if(isset($params['track_code']) && in_array(2, $params['track_code']) ) checked @endif>
        <label class="form-check-label" for="inlineCheckbox2">ダート</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="track_code[]" id="inlineCheckbox2" value="3" @if(isset($params['track_code']) && in_array(3, $params['track_code']) ) checked @endif>
        <label class="form-check-label" for="inlineCheckbox2">障害</label>
      </div>
    </div>  
  </div>
  <div class="row">
    <div class="col">
    <label for="staticEmail" class="col-sm-1 col-form-label">馬場状態</label>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="track_bias[]" id="inlineCheckbox1" value="1" @if(isset($params['track_bias']) && in_array(1, $params['track_bias']) ) checked @endif>
        <label class="form-check-label" for="inlineCheckbox1">良</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="track_bias[]" id="inlineCheckbox2" value="2" @if(isset($params['track_bias']) && in_array(2, $params['track_bias']) ) checked @endif>
        <label class="form-check-label" for="inlineCheckbox2">稍</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="track_bias[]" id="inlineCheckbox2" value="3" @if(isset($params['track_bias']) && in_array(3, $params['track_bias']) ) checked @endif>
        <label class="form-check-label" for="inlineCheckbox2">重</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="track_bias[]" id="inlineCheckbox2" value="4" @if(isset($params['track_bias']) && in_array(4, $params['track_bias']) ) checked @endif>
        <label class="form-check-label" for="inlineCheckbox2">不</label>
      </div>
    </div>  
  </div>
  <div class="row">
    <div class="col">
      <label for="staticEmail" class="col-sm-1 col-form-label">場所</label>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="place[]" id="inlineCheckbox1" value="1" @if(isset($params['place']) && in_array(1, $params['place']) ) checked @endif>
        <label class="form-check-label" for="inlineCheckbox1">札幌</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="place[]" id="inlineCheckbox2" value="2" @if(isset($params['place']) && in_array(2, $params['place']) ) checked @endif>
        <label class="form-check-label" for="inlineCheckbox2">函館</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="place[]" id="inlineCheckbox1" value="3" @if(isset($params['place']) && in_array(3, $params['place']) ) checked @endif>
        <label class="form-check-label" for="inlineCheckbox1">福島</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="place[]" id="inlineCheckbox2" value="4" @if(isset($params['place']) && in_array(4, $params['place']) ) checked @endif>
        <label class="form-check-label" for="inlineCheckbox2">新潟</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="place[]" id="inlineCheckbox1" value="5" @if(isset($params['place']) && in_array(5, $params['place']) ) checked @endif>
        <label class="form-check-label" for="inlineCheckbox1">東京</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="place[]" id="inlineCheckbox2" value="6" @if(isset($params['place']) && in_array(6, $params['place']) ) checked @endif>
        <label class="form-check-label" for="inlineCheckbox2">中山</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="place[]" id="inlineCheckbox1" value="7" @if(isset($params['place']) && in_array(7, $params['place']) ) checked @endif>
        <label class="form-check-label" for="inlineCheckbox1">中京</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="place[]" id="inlineCheckbox2" value="8" @if(isset($params['place']) && in_array(8, $params['place']) ) checked @endif>
        <label class="form-check-label" for="inlineCheckbox2">京都</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="place[]" id="inlineCheckbox1" value="9" @if(isset($params['place']) && in_array(9, $params['place']) ) checked @endif>
        <label class="form-check-label" for="inlineCheckbox1">阪神</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="place[]" id="inlineCheckbox2" value="10" @if(isset($params['place']) && in_array(10, $params['place']) ) checked @endif>
        <label class="form-check-label" for="inlineCheckbox2">小倉</label>
      </div>
    </div>  
  </div>
  <div class="row">
    <div class="col-md-3">
      <input type="submit" class="btn btn-success" value="exec">
    </div>
  </div>
</form>

@if(!empty($stallion_id))
<form action="/update_stallion_memo" method="POST">
@csrf
  <div class="row">
    <div class="col">
      <textarea name="memo" rows="5" cols="100" >{{$stallion_data->memo}}</textarea>
    </div>  
  </div>
  <input type="hidden" name="stallion_id" value="{{$stallion_id}}">
  <div class="row">
    <div class="col-md-3">
      <input type="submit" class="btn btn-success" value="メモ登録">
    </div>
  </div>
</form>

<a href="#" onclick="corse_regist_open({{$stallion_id}})">ポイントを登録</a>

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
          <th>勝率</th>
          <th>複勝率</th>
        </tr>
      </thead>
      @foreach($rank_analyze["race_waku_rank"] as $key => $rank)
        @php
          $rank_sum = ($rank[1] ?? 0)+ ($rank[2] ?? 0) + ($rank[3] ?? 0) + ($rank[4] ?? 0);
          $tan_per = round((($rank[1] ?? 0) / $rank_sum) * 100, 2);

          $fuku_sum = ($rank[1] ?? 0) + ($rank[2] ?? 0) + ($rank[3] ?? 0);
          $fuku_per = round(($fuku_sum / $rank_sum) * 100, 2);
        @endphp
        <tr>
          <td>{{$key}}</td>
          <td>
            {{$rank[1] ?? 0}}-{{$rank[2] ?? 0}}-{{$rank[3] ?? 0}}-{{$rank[4] ?? 0}}
          </td>
          @if($tan_per >= 30)
          <td class="first">
          @elseif($tan_per >= 20)
          <td class="second">
          @elseif($tan_per >= 10)
          <td class="third">
          @else
          <td>
          @endif
            {{ $tan_per }}
          </td>
          @if($fuku_per >= 40)
          <td class="first">
          @elseif($fuku_per >= 30)
          <td class="second">
          @elseif($fuku_per >= 20)
          <td class="third">
          @else
          <td>
          @endif
            {{ $fuku_per }}
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
          <th>勝率</th>
          <th>複勝率</th>
        </tr>
      </thead>
      @foreach($rank_analyze["course_condition_rank"] as $key => $rank)
        @php
          $rank_sum = ($rank[1] ?? 0)+ ($rank[2] ?? 0) + ($rank[3] ?? 0) + ($rank[4] ?? 0);
          $tan_per = round((($rank[1] ?? 0) / $rank_sum) * 100, 2);

          $fuku_sum = ($rank[1] ?? 0) + ($rank[2] ?? 0) + ($rank[3] ?? 0);
          $fuku_per = round(($fuku_sum / $rank_sum) * 100, 2);
        @endphp
        <tr>
          <td>{{$key}}</td>
          <td>
            {{$rank[1] ?? 0}}-{{$rank[2] ?? 0}}-{{$rank[3] ?? 0}}-{{$rank[4] ?? 0}}
          </td>
          @if($tan_per >= 30)
          <td class="first">
          @elseif($tan_per >= 20)
          <td class="second">
          @elseif($tan_per >= 10)
          <td class="third">
          @else
          <td>
          @endif
            {{ $tan_per }}
          </td>
          @if($fuku_per >= 40)
          <td class="first">
          @elseif($fuku_per >= 30)
          <td class="second">
          @elseif($fuku_per >= 20)
          <td class="third">
          @else
          <td>
          @endif
            {{ $fuku_per }}
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
          <th>勝率</th>
          <th>複勝率</th>
        </tr>
      </thead>
      @foreach($rank_analyze["race_corner_rank"] as $key => $rank)
      @foreach($rank as $k => $r)
        @php
          $rank_sum = ($r[1] ?? 0)+ ($r[2] ?? 0) + ($r[3] ?? 0) + ($r[4] ?? 0);
          $tan_per = round((($r[1] ?? 0) / $rank_sum) * 100, 2);

          $fuku_sum = ($r[1] ?? 0) + ($r[2] ?? 0) + ($r[3] ?? 0);
          $fuku_per = round(($fuku_sum / $rank_sum) * 100, 2);
        @endphp
        <tr>
          <td>{{$key}}{{$k}}</td>
          <td>
            {{$r[1] ?? 0}}-{{$r[2] ?? 0}}-{{$r[3] ?? 0}}-{{$r[4] ?? 0}}
          </td>
          @if($tan_per >= 30)
          <td class="first">
          @elseif($tan_per >= 20)
          <td class="second">
          @elseif($tan_per >= 10)
          <td class="third">
          @else
          <td>
          @endif
            {{ $tan_per }}
          </td>
          @if($fuku_per >= 40)
          <td class="first">
          @elseif($fuku_per >= 30)
          <td class="second">
          @elseif($fuku_per >= 20)
          <td class="third">
          @else
          <td>
          @endif
            {{ $fuku_per }}
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
          <th>勝率</th>
          <th>複勝率</th>
        </tr>
      </thead>
      @foreach($rank_analyze["race_place_rank"] as $key => $rank)
        @php
          $rank_sum = ($rank[1] ?? 0)+ ($rank[2] ?? 0) + ($rank[3] ?? 0) + ($rank[4] ?? 0);
          $tan_per = round((($rank[1] ?? 0) / $rank_sum) * 100, 2);

          $fuku_sum = ($rank[1] ?? 0) + ($rank[2] ?? 0) + ($rank[3] ?? 0);
          $fuku_per = round(($fuku_sum / $rank_sum) * 100, 2);
        @endphp
        <tr>
          <td>{{$key}}</td>
          <td>
            {{$rank[1] ?? 0}}-{{$rank[2] ?? 0}}-{{$rank[3] ?? 0}}-{{$rank[4] ?? 0}}
          </td>
          @if($tan_per >= 30)
          <td class="first">
          @elseif($tan_per >= 20)
          <td class="second">
          @elseif($tan_per >= 10)
          <td class="third">
          @else
          <td>
          @endif
            {{ $tan_per }}
          </td>
          @if($fuku_per >= 40)
          <td class="first">
          @elseif($fuku_per >= 30)
          <td class="second">
          @elseif($fuku_per >= 20)
          <td class="third">
          @else
          <td>
          @endif
            {{ $fuku_per }}
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
          <th>勝率</th>
          <th>複勝率</th>
        </tr>
      </thead>
      @foreach($rank_analyze["race_cushion_rank"] as $key => $rank)
        @php
          $rank_sum = ($rank[1] ?? 0)+ ($rank[2] ?? 0) + ($rank[3] ?? 0) + ($rank[4] ?? 0);
          $tan_per = round((($rank[1] ?? 0) / $rank_sum) * 100, 2);

          $fuku_sum = ($rank[1] ?? 0) + ($rank[2] ?? 0) + ($rank[3] ?? 0);
          $fuku_per = round(($fuku_sum / $rank_sum) * 100, 2);
        @endphp
        <tr>
          <td>{{$key/10}}</td>
          <td>
            {{$rank[1] ?? 0}}-{{$rank[2] ?? 0}}-{{$rank[3] ?? 0}}-{{$rank[4] ?? 0}}
          </td>
          @if($tan_per >= 30)
          <td class="first">
          @elseif($tan_per >= 20)
          <td class="second">
          @elseif($tan_per >= 10)
          <td class="third">
          @else
          <td>
          @endif
            {{ $tan_per }}
          </td>
          @if($fuku_per >= 40)
          <td class="first">
          @elseif($fuku_per >= 30)
          <td class="second">
          @elseif($fuku_per >= 20)
          <td class="third">
          @else
          <td>
          @endif
            {{ $fuku_per }}
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
          <th>勝率</th>
          <th>複勝率</th>
        </tr>
      </thead>
      @foreach($rank_analyze["race_bias_rank"] as $key => $rank)
      @foreach($rank as $k => $r)
        @php
          $rank_sum = ($r[1] ?? 0)+ ($r[2] ?? 0) + ($r[3] ?? 0) + ($r[4] ?? 0);
          $tan_per = round((($r[1] ?? 0) / $rank_sum) * 100, 2);

          $fuku_sum = ($r[1] ?? 0) + ($r[2] ?? 0) + ($r[3] ?? 0);
          $fuku_per = round(($fuku_sum / $rank_sum) * 100, 2);
        @endphp
        <tr>
          <td>{{$key}}{{$k}}</td>
          <td>
            {{$r[1] ?? 0}}-{{$r[2] ?? 0}}-{{$r[3] ?? 0}}-{{$r[4] ?? 0}}
          </td>
          @if($tan_per >= 30)
          <td class="first">
          @elseif($tan_per >= 20)
          <td class="second">
          @elseif($tan_per >= 10)
          <td class="third">
          @else
          <td>
          @endif
            {{ $tan_per }}
          </td>
          @if($fuku_per >= 40)
          <td class="first">
          @elseif($fuku_per >= 30)
          <td class="second">
          @elseif($fuku_per >= 20)
          <td class="third">
          @else
          <td>
          @endif
            {{ $fuku_per }}
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
          <th>勝率</th>
          <th>複勝率</th>
        </tr>
      </thead>
      @foreach($rank_analyze["race_distance_rank"] as $key => $rank)
      @foreach($rank as $k => $r)
        @php
          $rank_sum = ($r[1] ?? 0)+ ($r[2] ?? 0) + ($r[3] ?? 0) + ($r[4] ?? 0);
          $tan_per = round((($r[1] ?? 0) / $rank_sum) * 100, 2);

          $fuku_sum = ($r[1] ?? 0) + ($r[2] ?? 0) + ($r[3] ?? 0);
          $fuku_per = round(($fuku_sum / $rank_sum) * 100, 2);
        @endphp
        <tr>
          <td>{{$key}}{{$k}}</td>
          <td>
            {{$r[1] ?? 0}}-{{$r[2] ?? 0}}-{{$r[3] ?? 0}}-{{$r[4] ?? 0}}
          </td>
          @if($tan_per >= 30)
          <td class="first">
          @elseif($tan_per >= 20)
          <td class="second">
          @elseif($tan_per >= 10)
          <td class="third">
          @else
          <td>
          @endif
            {{ $tan_per }}
          </td>
          @if($fuku_per >= 40)
          <td class="first">
          @elseif($fuku_per >= 30)
          <td class="second">
          @elseif($fuku_per >= 20)
          <td class="third">
          @else
          <td>
          @endif
            {{ $fuku_per }}
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
          <th>勝率</th>
          <th>複勝率</th>
        </tr>
      </thead>
      @foreach($rank_analyze["race_pace_rank"] as $key => $rank)
        @php
          $rank_sum = ($rank[1] ?? 0)+ ($rank[2] ?? 0) + ($rank[3] ?? 0) + ($rank[4] ?? 0);
          $tan_per = round((($rank[1] ?? 0) / $rank_sum) * 100, 2);

          $fuku_sum = ($rank[1] ?? 0) + ($rank[2] ?? 0) + ($rank[3] ?? 0);
          $fuku_per = round(($fuku_sum / $rank_sum) * 100, 2);
        @endphp
        <tr>
          <td>{{$key}}</td>
          <td>
            {{$rank[1] ?? 0}}-{{$rank[2] ?? 0}}-{{$rank[3] ?? 0}}-{{$rank[4] ?? 0}}
          </td>
          @if($tan_per >= 30)
          <td class="first">
          @elseif($tan_per >= 20)
          <td class="second">
          @elseif($tan_per >= 10)
          <td class="third">
          @else
          <td>
          @endif
            {{ $tan_per }}
          </td>
          @if($fuku_per >= 40)
          <td class="first">
          @elseif($fuku_per >= 30)
          <td class="second">
          @elseif($fuku_per >= 20)
          <td class="third">
          @else
          <td>
          @endif
            {{ $fuku_per }}
          </td>
        </tr>
        @endforeach
     </tbody>
  </table>
  </div>
  @endif
  @if(!empty($rank_analyze["race_class_rank"]))
  <div class="col">
    <p>◆クラス</p>
    <table id="analyze8" class="table table-sm table-hover" style="font-size: 7pt; line-height: 200%;">
      <tbody>
      <thead class="thead-dark">
        <tr>
          <th>class</th>
          <th>着順</th>
          <th>勝率</th>
          <th>複勝率</th>
        </tr>
      </thead>
      @foreach($rank_analyze["race_class_rank"] as $key => $rank)
        @php
          $rank_sum = ($rank[1] ?? 0)+ ($rank[2] ?? 0) + ($rank[3] ?? 0) + ($rank[4] ?? 0);
          $tan_per = round((($rank[1] ?? 0) / $rank_sum) * 100, 2);

          $fuku_sum = ($rank[1] ?? 0) + ($rank[2] ?? 0) + ($rank[3] ?? 0);
          $fuku_per = round(($fuku_sum / $rank_sum) * 100, 2);
        @endphp
        <tr>
          <td>{{\MpConsts::CLASS_CODE_NAME_LIST[$key]}}</td>
          <td>
              {{$rank[1] ?? 0}}-{{$rank[2] ?? 0}}-{{$rank[3] ?? 0}}-{{$rank[4] ?? 0}}
          </td>
          @if($tan_per >= 30)
          <td class="first">
          @elseif($tan_per >= 20)
          <td class="second">
          @elseif($tan_per >= 10)
          <td class="third">
          @else
          <td>
          @endif
            {{ $tan_per }}
          </td>
          @if($fuku_per >= 40)
          <td class="first">
          @elseif($fuku_per >= 30)
          <td class="second">
          @elseif($fuku_per >= 20)
          <td class="third">
          @else
          <td>
          @endif
            {{ $fuku_per }}
          </td>
        </tr>
        @endforeach
     </tbody>
  </table>
  </div>
  @endif
</div>

<div class="row">
@if(!empty($corse_point_list) && $corse_point_list->count() != 0)
  <div class="col">
    <p>◆コースポイント</p>
    <table id="corse" class="table table-sm table-hover" style="font-size: 7pt; line-height: 200%;">
      <tbody>
      <thead class="thead-dark">
        <tr>
          <th>競馬場</th>
          <th>距離</th>
          <th>トラックタイプ</th>
          <th>ポイント</th>
        </tr>
      </thead>
      @foreach($corse_point_list as $point)
        <tr>
          <td>{{$point->place_name}}</td>
          <td>{{$point->distance}}</td>
          <td>{{\MpConsts::TRACK_TYPE_LIST[$point->track_type]}}</td>
          <td>{{$point->point }}</td>
        </tr>
        @endforeach
     </tbody>
    </table>
  </div>
  @endif
  @if(!empty($bias_point_list) && $bias_point_list->count() != 0)
  <div class="col">
    <p>◆バイアスポイント</p>
    <table id="bias" class="table table-sm table-hover" style="font-size: 7pt; line-height: 200%;">
      <tbody>
      <thead class="thead-dark">
        <tr>
          <th>バイアスタイプ</th>
          <th>ポイント</th>
        </tr>
      </thead>
      @foreach($bias_point_list as $point)
        <tr>
          <td>{{\MpConsts::TRACK_CODE_BIAS_TYPE[$point->bias_type]['type_name']}}</td>
          <td>{{$point->point }}</td>
        </tr>
        @endforeach
    </tbody>
    </table>
  </div>
  @endif
</div>
<hr />

@if(isset($stallion_id))
<a href="#" onclick="corse_regist_open({{$stallion_id}})">ポイントを登録</a>
<table class="table table-hover table-sm" id="race_history">
  <thead>
    <tr class="table-secondary">
        <th scope="col">レース日</th>
        <th scope="col">レース名</th>
        <th scope="col">場</th>
        <th scope="col">距離</th>
        <th scope="col">トラック</th>
        <th scope="col">天気</th>
        <th scope="col">Bias</th>
        <th scope="col">ペース</th>
        <th scope="col">馬名</th>
        <th scope="col">馬番</th>
        <th scope="col">人気</th>
        <th scope="col">脚質</th>
        <th scope="col">rank</th>
        <th scope="col">RPCI</th>
        <th scope="col">tmc</th>
        <th scope="col">dmc</th>
        <th scope="col">cushion</th>
        <th scope="col">BT</th>
        <th scope="col">Time</th>
        <th scope="col">3F差</th>
        <th scope="col">着差</th>
        <th scope="col">補9</th>
    </tr>
  </thead>
  <tbody class="table-group-divider">
  </tr>
    @foreach($search_data as $race)
    <tr>
      <th scope="row">{{$race->race_date}}</th>
      <td><a href="/race_card/{{$race->race_date}}/{{$race->place_code_name}}/{{$race->race_num}}">{{$race->race_name}}</a></td>
      <td>{{$race->place_name}}</td>
      <td>{{$race->distance}}</td>
      <td>{{\MpConsts::TRACK_TYPE_LIST[$race->track_type]}}</td>
      <td>{{\MpConsts::TENKI_CODE[$race->tenki]}}</td>
      <td>{{\MpConsts::TRACK_BIAS_CODE[$race->track_bias]}}</td>
      <td>{{$race->race_mark_2}}</td>
      <td><a href="/race_history/{{$race->horse_id}}">{{$race->horse_name}}</a></td>
      <td>{{$race->no}}</td>
      <td>{{$race->ninki}}</td>
      <td>{{$race->clincher}}</td>
 
      @if($race->result_rank == 1)
      <td class="first">
      @elseif($race->result_rank == 2)
      <td class="second">
      @elseif($race->result_rank == 3)
      <td class="third">
      @elseif($race->result_rank == 4)
      <td class="fourth">
      @elseif($race->result_rank == 5)
      <td class="fifth">
      @else
      <td>
      @endif
      {{$race->result_rank}}
      
      </td>
      <td>{{$race->rpci}}</td>
      <td>{{$race->turf_moisture_content}}</td>
      <td>{{$race->dart_moisture_content}}</td>
      <td>{{$race->cushion}}</td>
      <td>{{$race->base_time}}</td>
      <td>{{$race->result_time}}</td>
      <td>{{$race->last_3f_sa}}</td>
      <td>{{$race->chakusa}}</td>
      <td>{{$race->hosei_9}}</td>
    </tr>
    @endforeach
  </tbody>
</table>
@endif
@endif
@endsection