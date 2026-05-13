<?php
require_once 'db_config.php';
// デバッグ用（開発が終わったら消す）
ini_set('display_errors', 1);
error_reporting(E_ALL);

// SQL実行：newsテーブルから全てのデータを取ってくる
$stmt = $pdo->query('SELECT * FROM news');

echo "<h2>最新ニュース一覧</h2>";
echo "<table border='1'>";
echo "<tr><th>No</th><th>真偽</th><th>影響度</th><th>内容</th><th>解説</th><th>ツイート主</th></tr>";

// 1行ずつ取り出して表示
while ($row = $stmt->fetch()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['no']) . "</td>";
    echo "<td>" . htmlspecialchars($row['singi']) . "</td>";
    echo "<td>" . htmlspecialchars($row['score']) . "</td>";
    echo "<td>" . htmlspecialchars($row['question_text']) . "</td>"; // contentというカラムがあると仮定
    echo "<td>" . htmlspecialchars($row['kaisetu_text']) . "</td>"; // contentというカラムがあると仮定
    echo "<td>" . htmlspecialchars($row['tuinusi']) . "</td>"; // contentというカラムがあると仮定
    echo "</tr>";
}

echo "</table>";