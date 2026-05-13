<?php
//接続情報
$host = '10.18.79.54';
$db   = 'faker';    // 作成したデータベース名
$user = 'team_user';   // 作成したユーザー名
$pass = '1234';        // 設定したパスワード
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";//データソースネーム
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // エラー投げます
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // 連想配列で取得
    PDO::ATTR_EMULATE_PREPARES   => false,                  // SQLインジェクション対策
];
$pdo = new PDO($dsn, $user, $pass, $options);//ようやく接続