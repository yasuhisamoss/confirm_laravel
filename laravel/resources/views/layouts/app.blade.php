<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style type="text/css">
        body {
        padding-top: 60px;
        }
        td.first{background-color: #FFFF00;}
        td.second{background-color: #32CD32;}
        td.third{background-color: #00FFFF;}
        td.fourth{background-color: #DDA0DD;}
        td.fifth{background-color: #DCDCDC;}

        td.waku_1{background-color: #FFFFFF;}
        td.waku_2{background-color: #000000; color:  #FFFFFF;}
        td.waku_3{background-color: #FF0000; color:  #FFFFFF;}
        td.waku_4{background-color: #0000FF; color:  #FFFFFF;}
        td.waku_5{background-color: #FFFF00; color:}
        td.waku_6{background-color: #008000; color:  #FFFFFF;}
        td.waku_7{background-color: #ffa500; color:  #FFFFFF;}
        td.waku_8{background-color: #ff69b4; color:  #FFFFFF;}

        td.pif_1{background-color: #FFFF00;}
        td.pif_2{background-color: #00ff00;}
        td.pif_3{background-color: #008000;}
        td.pif_4{background-color: #0000ff;}
        td.pif_5{background-color: #00bfff;}
    </style>
</head>
<body>
<!-- 共通のヘッダー内容はここに記述 -->
@include('layouts.header')

<!-- メインコンテンツはここに表示されます -->
@yield('content')

<!-- 共通のフッター内容はここに記述 -->
@include('layouts.footer')
</body>
</html>
