@extends('layouts.app')

@section('title', 'クッション、含水率入力')
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
<h1>クッション、含水率入力</h1>
{{ session('result') }}
<table id="race_data_list" class="table table-hover table-sm">
    <thead class="thead-dark sticky-top">
        <tr>
            <th>日付</th>
            <th>Place</th>
            <th>含水率芝</th>
            <th>含水率ダ</th>
            <th>クッション値</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($csv_dir_list as $dir_data)
        <tr>
            <form action="/cushion_regist" method="POST">
            @csrf
            <td>{{ $dir_data["race_date"] }}</td>
            <input type="hidden" name="race_date" value="{{ $dir_data["race_date"] }}">
            <td>
            <select name="place_id" class="form-select">
                @foreach ($place_list as $k => $v)
                    <option value="{{ $k }}"
                    @if ($k == $dir_data["place_id"]) selected @endif>
                    {{ $v }}</option>
                @endforeach
            </select>
            </td>

            <td><input class="form-control" type="text" name="tmc" value="{{ $dir_data["turf_moisture_content"] }}"></td>
            <td><input class="form-control" type="text" name="dmc" value="{{ $dir_data["dart_moisture_content"] }}"></td>
            <td><input class="form-control" type="text" name="cushion" value="{{ $dir_data["cushion"] }}"></td>
            <td><button type="submit" class="btn btn-primary">登録</button></td>
            </form>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
