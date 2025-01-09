<?php

$host = 'mysql'; // MySQLコンテナのサービス名
$dbname = $_ENV['MYSQL_DATABASE'];
$username = 'root';
$password = $_ENV['MYSQL_ROOT_PASSWORD'];

# 新しいPDOオブジェクトを作成し、MySQLデータベースに接続
$db = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $username, $password);

# SQL　文を実行
//$stmt = $db->prepare("INSERT INTO mytable (id, name) values (1, 'ああああ')");
$stmt = $db->prepare("select * from mytable");
$stmt->execute();

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($results as $result) {
    echo $result['id'] . '. ' . $result['name'] . PHP_EOL;
}
