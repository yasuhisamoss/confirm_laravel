@extends('layouts.app')
@section('title', '過去レース情報')
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
</script>

@section('content')
<form action="/search_exec" method="POST">
  @csrf
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
<!--
  <div class="row">
    <div class="col">
    <label for="staticEmail" class="col-sm-1 col-form-label">R-level</label>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="r_level[]" id="inlineCheckbox1" value="R-高">
        <label class="form-check-label" for="inlineCheckbox1">R-高</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="r_level[]" id="inlineCheckbox2" value="R-普">
        <label class="form-check-label" for="inlineCheckbox2">R-普</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="r_level[]" id="inlineCheckbox2" value="R-低">
        <label class="form-check-label" for="inlineCheckbox2">R-低</label>
      </div>
    </div>  
  </div>
  -->
  <div class="row">
    <div class="col">
    <label for="staticEmail" class="col-sm-1 col-form-label">クッション</label>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="cushion[]" id="inlineCheckbox1" value="7.0" @if(isset($params['cushion']) && in_array(7.0, $params['cushion']) ) checked @endif>
        <label class="form-check-label" for="inlineCheckbox1">7.0</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="cushion[]" id="inlineCheckbox2" value="7.5" @if(isset($params['cushion']) && in_array(7.5, $params['cushion']) ) checked @endif>
        <label class="form-check-label" for="inlineCheckbox2">7.5</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="cushion[]" id="inlineCheckbox2" value="8.0" @if(isset($params['cushion']) && in_array(8.0, $params['cushion']) ) checked @endif>
        <label class="form-check-label" for="inlineCheckbox2">8.0</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="cushion[]" id="inlineCheckbox1" value="8.5" @if(isset($params['cushion']) && in_array(8.5, $params['cushion']) ) checked @endif>
        <label class="form-check-label" for="inlineCheckbox1">8.5</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="cushion[]" id="inlineCheckbox2" value="9.0" @if(isset($params['cushion']) && in_array(9.0, $params['cushion']) ) checked @endif>
        <label class="form-check-label" for="inlineCheckbox2">9.0</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="cushion[]" id="inlineCheckbox2" value="9.5" @if(isset($params['cushion']) && in_array(9.5, $params['cushion']) ) checked @endif>
        <label class="form-check-label" for="inlineCheckbox2">9.5</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="cushion[]" id="inlineCheckbox1" value="10.0" @if(isset($params['cushion']) && in_array(10.0, $params['cushion']) ) checked @endif>
        <label class="form-check-label" for="inlineCheckbox1">10.0</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="cushion[]" id="inlineCheckbox2" value="10.5" @if(isset($params['cushion']) && in_array(10.5, $params['cushion']) ) checked @endif>
        <label class="form-check-label" for="inlineCheckbox2">10.5</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="cushion[]" id="inlineCheckbox2" value="11.0" @if(isset($params['cushion']) && in_array(11.0, $params['cushion']) ) checked @endif>
        <label class="form-check-label" for="inlineCheckbox2">11.0</label>
      </div>
    </div>  
  </div>
  <div class="row">
    <div class="col">
    <label for="staticEmail" class="col-sm-1 col-form-label">種牡馬</label>
      <select class="select" name="stallion">
      <option value="0">--</option>
        @foreach($stallion_list as $stallion)
        <option value="{{$stallion->stallion_id}}">{{$stallion->stallion_name}}</option>
        @endforeach
    </select>
    </div>  
  </div>
  
  <div class="row">
    <div class="col-md-3">
      <input type="submit" class="btn btn-success" value="exec">
    </div>
  </div>
</form>

@if(!empty($search_data))
<hr />
<div class="row">
@if(!empty($analyze_search["pace"]))

  <div class="col">
    <p>◆ペース別</p>
    <table id="analyze1" class="table table-sm table-hover" style="font-size: 7pt; line-height: 200%;">
      <tbody>
      <thead class="thead-dark">
        <tr>
          <th>ペース</th>
          <th>回数</th>
          <th>%</th>
        </tr>
      </thead>
      @foreach($analyze_search["pace"] as $key => $count)
        <tr>
          <td>{{$key}}</td>
          <td>
              {{$count["count"]}}
          </td>
          <td>
              {{ round(($count["count"] / count($search_data)) * 100 , 2) }}
          </td>
        </tr>
        @endforeach
     </tbody>
  </table>
  </div>
  @endif

  @if(!empty($analyze_search["waku"]))
  <div class="col">
    <p>◆枠別</p>
    <table id="analyze1" class="table table-sm table-hover" style="font-size: 7pt; line-height: 200%;">
      <tbody>
      <thead class="thead-dark">
        <tr>
          <th>枠</th>
          <th>回数</th>
          <th>%</th>
        </tr>
      </thead>
      @foreach($analyze_search["waku"] as $key => $count)
        <tr>
          <td>{{$key}}</td>
          <td>
              {{$count["count"]}}
          </td>
          <td>
              {{ round(($count["count"] / count($search_data)) * 100 , 2) }}
          </td>
        </tr>
        @endforeach
     </tbody>
  </table>
  </div>
  @endif

  @if(!empty($analyze_search["clincher"]))
  <div class="col">
    <p>◆脚質別</p>
    <table id="analyze1" class="table table-sm table-hover" style="font-size: 7pt; line-height: 200%;">
      <tbody>
      <thead class="thead-dark">
        <tr>
          <th>脚質</th>
          <th>回数</th>
          <th>%</th>
        </tr>
      </thead>
      @foreach($analyze_search["clincher"] as $key => $count)
        <tr>
          <td>{{$key}}</td>
          <td>
              {{$count["count"]}}
          </td>
          <td>
              {{ round(($count["count"] / count($search_data)) * 100 , 2) }}
          </td>
        </tr>
        @endforeach
     </tbody>
  </table>
  </div>
  @endif
</div>
<hr />
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
        <th scope="col">RPCI</th>
        <th scope="col">tmc</th>
        <th scope="col">dmc</th>
        <th scope="col">cushion</th>
        <th scope="col">BT</th>
        <th scope="col">Time</th>
        <th scope="col">補9</th>
        <th scope="col">回次</th>
        <th scope="col">no</th>
        <th scope="col">1着馬</th>
        <th scope="col">1着馬父</th>
        <th scope="col">脚質</th>
        <th scope="col">道中</th>
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
      <td>{{$race->rpci}}</td>
      <td>{{$race->turf_moisture_content}}</td>
      <td>{{$race->dart_moisture_content}}</td>
      <td>{{$race->cushion}}</td>
      <td>{{$race->base_time}}</td>
      <td>{{$race->result_time}}</td>
      <td>{{$race->hosei_9}}</td>
      <td>{{$race->nichizi}}回{{$race->kaizi}}日目</td>
      </td>
        @if($race->waku_no == 1)
        <td class="waku_1">
        @elseif($race->waku_no == 2)
        <td class="waku_2">
        @elseif($race->waku_no == 3)
        <td class="waku_3">
        @elseif($race->waku_no == 4)
        <td class="waku_4">
        @elseif($race->waku_no == 5)
        <td class="waku_5">
        @elseif($race->waku_no == 6)
        <td class="waku_6">
        @elseif($race->waku_no == 7)
        <td class="waku_7">
        @elseif($race->waku_no == 8)
        <td class="waku_8">
        @endif  
        {{$race->no}}</td>
      <td><a href="/race_history/{{$race->horse_id}}">{{$race->horse_name}}</a></td>
      <td>{{$race->stallion_name}}</td>
      <td>{{$race->clincher}}</td>
      <td>{{$race->passing_rank_1}}-{{$race->passing_rank_2}}-{{$race->passing_rank_3}}-{{$race->passing_rank_4}}</td>
    </tr>
    @endforeach
  </tbody>
</table>
@endif
@endsection
