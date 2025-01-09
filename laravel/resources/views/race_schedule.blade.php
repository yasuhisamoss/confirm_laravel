@extends('layouts.app')

@section('title', 'レーススケジュール')
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/js/jquery.tablesorter.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/css/theme.default.min.css">
<script>
    $(document).ready(function() {
    $('#race_data_list').tablesorter({
            sortList: [[1]],
        });
    });
</script>
@section('content')
<h1>スケジュール</h1>
<table id="race_data_list" class="table table-hover table-sm">
    <tbody>
        @foreach ($race_schedule_list as $date_time => $dir_data)
        <tr>
            <td>{{ $date_time }}</td>
            @foreach ($dir_data as $d)
            <td>
                <a href="/race_card/{{ $date_time }}/{{ $d['place_code_name'] }}/11">{{ $d["place_name"] }}</a><br />
                T:{{$d['turf_moisture_content']}} D:{{$d['dart_moisture_content']}} C:{{$d['cushion']}}
            </td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
