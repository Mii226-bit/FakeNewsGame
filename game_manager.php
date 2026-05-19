<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ゲームの初期化関数
function init_game($limit) {
    $_SESSION['game_rule'] = [
        'limit' => $limit,          // '3', '5', '7', 'endless'
        'current_round' => 1,       // 現在何問目か
        'is_active' => true         // ゲームプレイ中フラグ
    ];
    // ★初期フォロワー数を1万人に設定
    $_SESSION['followers'] = 10000; 
}

// ゲームルールを取得する関数
function get_game_rule() {
    if (!isset($_SESSION['game_rule'])) {
        init_game('3');
    }
    return $_SESSION['game_rule'];
}

// 次のラウンドに進むためのカウントアップ関数
function advance_round() {
    if (isset($_SESSION['game_rule']) && $_SESSION['game_rule']['limit'] !== 'endless') {
        $_SESSION['game_rule']['current_round']++;
    }
}

// ゲームがすべて終了したか判定する関数
function check_game_over() {
    // ★フォロワー数が0以下なら強制的にゲームオーバー
    if (isset($_SESSION['followers']) && $_SESSION['followers'] <= 0) {
        return true;
    }

    $rule = get_game_rule();
    if ($rule['limit'] === 'endless') {
        return false; 
    }
    return ($rule['current_round'] > (int)$rule['limit']);
}
?>