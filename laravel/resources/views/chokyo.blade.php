@extends('layouts.app')
@section('title', '調教')
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/js/jquery.tablesorter.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/css/theme.default.min.css">
<script>
    $(document).ready(function() {
    $('#wood, #hanro').tablesorter({
            sortList: [[0,1]],
        });
    });
</script>
@section('content')
<div class="row">
    <div class="col">
        <p>◆wood</p>
        <table id="wood" class="table table-sm table-hover" style="font-size: 10pt; line-height: 200%;">
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
        <table id="hanro" class="table table-sm table-hover" style="font-size: 10pt; line-height: 200%;">
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
