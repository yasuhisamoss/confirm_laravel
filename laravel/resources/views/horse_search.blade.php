@extends('layouts.app')

@section('title', '馬情報')

@section('content')
<h1>Horse!</h1>
<table class="table table-hover table-sm" id="race_card_list">
<thead>
    <tr class="table-secondary">
        <th scope="col">name</th>
        <th scope="col">コメント</th>
        <th scope="col">調教師</th>
        <th scope="col">父</th>
        <th scope="col">母父</th>
    </tr>
  </thead>
  <tbody class="table-group-divider">
    @foreach($horse_search_list as $h)
    <tr>
        <td><a href="/race_history/{{$h->horse_id}}">{{$h->horse_name}}</a></td>
        <td>{{$h->horse_comment}}</td>
        <td>{{$h->trainer_name}}</td>
        <td>{{$h->father_name}}</td>
        <td>{{$h->grandfather_name}}</td>
    </tr>
    @endforeach
  </tbody>
</table>
@endsection
