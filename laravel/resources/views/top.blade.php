@extends('layouts.app')

@section('title', 'トップページ')
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/js/jquery.tablesorter.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/css/theme.default.min.css">
<script>
  $(function(){
    $('.select').select2({width: 'resolve'});
  });
</script>
@section('content')
<!--日付検索のナビバー　-->
<nav class="navbar bg-body-tertiary">
  <div class="container-fluid">
      <form class="d-flex" role="search">
        <select class="select" name="d" style="width:100px;">
          <option value="0">--</option>
            @foreach($csv_dir_list as $date => $items)
            <option value="{{$date}}" @if($target_date == $date) selected @endif>{{$date}}</option>
            @endforeach
        </select>
        <button class="btn btn-outline-success btn-sm" type="submit">Search</button>
      </form>
  </div>
  <div class="container-fluid">
  <a href="/?d={{$ba_date['before']}}">{{$ba_date['before']}}</a>
  <a href="/?d={{$ba_date['after']}}">{{$ba_date['after']}}</a>
  </div>
</nav>

<div class="row">
  @foreach($race_card as $place_code => $race)
  <div class="col">
    <p>◆{{$place_code}} C:{{$race['mcc']->cushion}} 芝含水：{{$race['mcc']->turf_moisture_content}} ダ含水：{{$race['mcc']->dart_moisture_content}}</p>
    <table class="table table-sm table-hover" style="font-size: 7pt; line-height: 200%;">
      <tbody>
      <thead class="thead-dark">
        <tr>
          <th>no</th>
          <th>レース名</th>
          <th>距離</th>
          <th>芝/ダ</th>
          <th>馬場状態</th>
          <th>ペース</th>
          <th>R-level</th>
        </tr>
      </thead>
      @foreach($race as $num => $race)
        @if(!is_numeric($num)) @continue @endif
        <tr>
          <td>{{$num}}</td>
          <td><a href="/race_card/{{$target_date}}/{{$place_code}}/{{$num}}">{{$race['race_name']}}</a></td>
          <td>{{$race['distance']}}</td>
          <td>{{$race['turf_dart']}}</td>
          <td>{{$race['bias']}}</td>
          <td>{{$race['pace']}}</td>
          <td>{{$race['race_level']}}</td>
        </tr>
      @endforeach 
     </tbody>
    </table>
  </div>
  @endforeach
</div>
@endsection