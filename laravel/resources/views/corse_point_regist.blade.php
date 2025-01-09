@extends('layouts.app')
@section('title', '過去レース情報')
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/js/jquery.tablesorter.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/css/theme.default.min.css">

@section('content')
<div class="card">
  <div class="card-header">
    {{$stallion_data->stallion_name}}
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-12">
        <h5 class="card-title">{{$stallion_data->memo}}</h5>
      </div>
    </div>
  </div>
</div>
<form action=/corse_point_regist/{{$stallion_data->stallion_id}} method="GET">
<div class="row">
    <div class="col">
    <label for="staticEmail" class="col-sm-1 col-form-label">競馬場</label>
      <select class="select" name="place_id">
      <option value="0">--</option>
        @foreach($place_list as $place)
        <option value="{{$place->place_id}}" @if(isset($place->place_id) && $place->place_id == $place_id ) selected @endif>{{$place->place_name}}</option>
        @endforeach
    </select>
    <input type="submit" class="btn btn-success" value="コースを検索">
    </div>  
  </div>
</div>
</form>
<hr />
@if(!empty($place_id))
<h4>コースポイント入力</h4>
@if(!empty(session('result')))
<div class="alert alert-success" role="alert">
{{ session('result') }}
</div>
@endif
<table id="race_data_list" class="table table-hover table-sm">
    <thead class="thead-dark sticky-top">
        <tr>
            <th>ID</th>
            <th>トラックタイプ</th>
            <th>距離</th>
            <th>point</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    @foreach ($corse_list as $corse)
        <tr>
            <form action="/regist_stallion_corse" method="POST">
            @csrf
            <td>{{ $corse->corse_id }}</td>
            <td>{{ \MpConsts::TRACK_TYPE_LIST[$corse->track_type] }}</td>
            <td>{{ $corse->distance }}</td>
            <td><input class="form-control" type="number" name="point" value="@if(isset($stallion_corse_list[$corse->corse_id])){{$stallion_corse_list[$corse->corse_id]}}@endif"></td>
            <input type="hidden" name="stallion_id" value="{{ $stallion_data->stallion_id }}">
            <input type="hidden" name="corse_id" value="{{ $corse->corse_id }}">
            <input type="hidden" name="place_id" value="{{ $place_id }}">
            <td><button type="submit" class="btn btn-primary">登録</button></td>
            </form>
        </tr>
        @endforeach
    </tbody>
</table>
<hr />
@endif
<h4>バイアスポイント入力</h4>
@if(!empty(session('result')))
<div class="alert alert-success" role="alert">
{{ session('result') }}
</div>
@endif
<table id="race_data_list" class="table table-hover table-sm">
    <thead class="thead-dark sticky-top">
        <tr>
            <th>type_id</th>
            <th>type_name</th>
            <th>トラックタイプ</th>
            <th>馬場状態</th>
            <th>point</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    @foreach (\MpConsts::TRACK_CODE_BIAS_TYPE as $key => $bias)
        <tr>
            <form action="/regist_stallion_bias" method="POST">
            @csrf
            <td>{{ $key }}</td>
            <td>{{ $bias["type_name"] }}</td>
            <td>{{ $bias["track_type"] }}</td>
            <td>{{ $bias["track_bias"] }}</td>
            <td><input class="form-control" type="number" name="point" value="@if(isset($stallion_bias_list[$key])){{$stallion_bias_list[$key]}}@endif"></td>
            <input type="hidden" name="stallion_id" value="{{ $stallion_data->stallion_id }}">
            <input type="hidden" name="bias_type" value="{{ $key }}">
            <input type="hidden" name="place_id" value="{{ $place_id }}">
            <td><button type="submit" class="btn btn-primary">登録</button></td>
            </form>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
