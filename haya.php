<?php
// 接続情報（あなたの環境に合わせて書き換えてください）
$host = '10.18.79.54'; // 先ほど確認したWindowsのIP
$db   = 'faker_db';    // 作成したデータベース名
$user = 'team_user';   // 作成したユーザー名
$pass = '1234';        // 設定したパスワード
$charset = 'utf8mb4';

// 接続設定（おまじないだと思ってOKです）
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // エラー投げます
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // 連想配列で取得
    PDO::ATTR_EMULATE_PREPARES   => false,                  // SQLインジェクション対策
];

try {
    // ここで接続実行！
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "データベース接続成功！チーム開発の準備完了です。";
} catch (\PDOException $e) {
    // 失敗したらエラーを表示
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}