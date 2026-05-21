<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'game_manager.php';

// ==========================================
// ★ 炎上ルーレットの設定（後から調整可能）
// ==========================================
$enjo_penalties = [
    'chinka'    => 0,  // 鎮火: 固定でマイナスするフォロワー数
    'enjo'      => 0.2,  // 炎上: 固定でマイナスするフォロワー数
    'dai_enjo'  => 0.5  // 大炎上: 固定でマイナスするフォロワー数
];

$news_list = isset($_SESSION['six_news']) ? $_SESSION['six_news'] : [];
$selected_news = isset($_POST['selected_news']) ? $_POST['selected_news'] : [];
$rule = get_game_rule();

// ★【フォロワー数計算ロジック】
if (!isset($_SESSION['followers'])) {
    $_SESSION['followers'] = 10000; // 初期フォロワー数（ゲーム開始時に設定されているはずですが、念のため）
}

$has_dropped = false; // 今回のラウンドでフォロワーが減少したかどうかのフラグ
$hendou_followers = 0;

foreach ($news_list as $news) {
    if (in_array($news['no'], $selected_news)) {
        if ($news['singi']) {
            // 真（REAL）を選んでいたらフォロワー数上昇
            // $_SESSION['followers'] += (int)$news['score'];
            $hendou_followers += (int)$news['score'];
        } else {
            // 偽（FAKE）を選んでいたらフォロワー数下降
            // $_SESSION['followers'] -= (int)$news['score'] * 2; // ペナルティを倍にしてよりシビアに
            $hendou_followers -= (int)$news['score'] * 1.5; // ペナルティを倍にしてよりシビアに
            $has_dropped = true; // フォロワー減少を検知
        }
    }
}

// ★【炎上ルーレットロジック】
// スコアがマイナス（フォロワー減少）が発生した場合のみルーレットが回る
$roulette_result = null;
$roulette_penalty = 0;

// 連続炎上カウントの初期化（セッションになければ0にする）
if (!isset($_SESSION['enjo_streak'])) {
    $_SESSION['enjo_streak'] = 0;
}

$is_dobon = false; // ドボン（連続炎上による強制ゲームオーバー）フラグ

if ($has_dropped) {
    // 1〜100のランダムな数字を生成（確率のパーセンテージ用）
    $dice = rand(1, 100);
    if ($dice <= 50) {
        $roulette_result = 'chinka';     // 1〜60（60%の確率）
        $_SESSION['enjo_streak'] = 0;    // 鎮火したら連続カウントはリセット！
    } elseif ($dice <= 40) {
        $roulette_result = 'enjo';       // 61〜90（30%の確率）
        $_SESSION['enjo_streak']++;      // 炎上カウント+1
    } else {
        $roulette_result = 'dai_enjo';   // 91〜100（10%の確率）
        $_SESSION['enjo_streak']++;      // 大炎上カウント+1
    }
    
    // 設定された数値を適用
    $roulette_penalty = $_SESSION['followers'] * $enjo_penalties[$roulette_result];
    $_SESSION['followers'] -= $roulette_penalty;

    // ★2回連続で炎上または大炎上を引いた場合のドボン判定
    if ($_SESSION['enjo_streak'] >= 2) {
        $is_dobon = true;
        $_SESSION['followers'] = 0; // フォロワーを0にして強制終了へ
    }
} else {
    // このラウンドでフェイクを選んでいない（フォロワーが減っていない）なら
    // 安全に切り抜けたということなので、連続炎上カウントはリセット
    $_SESSION['enjo_streak'] = 0;

}

$_SESSION['followers'] += $hendou_followers; // フォロワー数の変動を適用
// 計算完了後のフォロワー数
$followers = $_SESSION['followers'];

// 今回のラウンド終了処理（カウントアップ）
advance_round();

// ゲームオーバー判定
$is_game_over = check_game_over();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>答え合わせ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #e9f1f7; position: relative; }
        .news-card {
            background-color: #000 !important;
            color: #fff;
            border-width: 4px !important;
            position: relative;
        }
        .border-fake {
            border-color: #dc3545 !important;
            box-shadow: 0 0 15px rgba(220, 53, 69, 0.4) !important;
        }
        .border-real {
            border-color: #198754 !important;
            box-shadow: 0 0 15px rgba(25, 135, 84, 0.4) !important;
        }
        .user-selected {
            transform: scale(1.02);
            z-index: 2;
        }

        .judgment-stamp {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-100%, -100%) rotate(-15deg) scale(2);
            font-size: 2.5rem;
            font-weight: 900;
            padding: 0.2em 0.5em;
            border: 4px solid;
            border-radius: 10px;
            text-transform: uppercase;
            opacity: 0;
            z-index: 10;
            pointer-events: none;
            animation: stamp-animation 0.4s ease-out forwards;
        }
        @keyframes stamp-animation {
            to {
                transform: translate(-50%, -50%) rotate(-15deg) scale(1);
                opacity: 0.85;
            }
        }
        .user-icon { width: 45px; height: 45px; font-weight: bold; font-size: 1.2rem; }
        .score-text { font-size: 1.2rem; font-weight: bold; }
        .badge-singi { font-size: 1rem; padding: 0.5em 0.8em; }
        .follower-counter {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            font-weight: bold;
            z-index: 1000;
        }

        /* ★炎上通知カード用の追加CSS */
        .enjo-card {
            border: 3px solid;
            border-radius: 12px;
            overflow: hidden;
            background-color: #fff;
        }
        .enjo-card-chinka { border-color: #0dcaf0; box-shadow: 0 0 15px rgba(13, 202, 240, 0.3); }
        .enjo-card-enjo { border-color: #ffc107; box-shadow: 0 0 20px rgba(255, 193, 7, 0.4); }
        .enjo-card-dai_enjo { border-color: #dc3545; box-shadow: 0 0 25px rgba(220, 53, 69, 0.6); animation: pulse 1.5s infinite; }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.01); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>

<div class="follower-counter text-dark">
    👥 フォロワー数: <span class="text-primary fs-5"><?php echo number_format($followers); ?></span> 人
</div>

<div class="container py-5">
    <h1 class="text-center mb-5 fw-bold">ファクトチェック</h1>
    
    <!-- ★ 炎上ルーレットの結果UIセクション -->
    <?php if ($roulette_result !== null): ?>
        <div class="row justify-content-center mb-5">
            <div class="col-md-8 col-lg-6">
                
                <?php if ($roulette_result === 'chinka'): ?>
                    <!-- 【鎮火】のUI -->
                    <div class="enjo-card enjo-card-chinka p-4 text-center">
                        <div class="display-4 mb-2">🧯</div>
                        <h3 class="fw-bold text-info mb-2">炎上ルーレット：鎮火</h3>
                        <p class="text-secondary mb-3">デマの拡散に気づき、ボヤのうちに即座に消し止めた！<br>ネットの批判を最小限に抑え込みました。</p>
                        <div class="badge bg-info fs-6 px-3 py-2 rounded-pill">
                            フォロワー減少: <?php echo number_format($roulette_penalty); ?> 人
                        </div>
                    </div>
                    
                <?php elseif ($roulette_result === 'enjo'): ?>
                    <!-- 【炎上】のUI -->
                    <div class="enjo-card enjo-card-enjo p-4 text-center">
                        <div class="display-4 mb-2">🔥</div>
                        <h3 class="fw-bold text-warning mb-2" style="color: #d39e00 !important;">炎上ルーレット：通常炎上</h3>
                        <p class="text-secondary mb-3">リプライ欄に批判が殺到中！<br>「ファクトチェックしろ」と叩かれ、アカウントの信用が削られています。</p>
                        <div class="badge bg-warning text-dark fs-6 px-3 py-2 rounded-pill">
                            フォロワー減少: <?php echo number_format($roulette_penalty); ?> 人
                        </div>
                    </div>
                    
                <?php elseif ($roulette_result === 'dai_enjo'): ?>
                    <!-- 【大炎上】のUI -->
                    <div class="enjo-card enjo-card-dai_enjo p-4 text-center bg-danger-subtle">
                        <div class="display-4 mb-2">🌋</div>
                        <h3 class="fw-bold text-danger mb-2">炎上ルーレット：大炎上</h3>
                        <p class="text-danger-emphasis mb-3 fw-bold">トレンド1位にランクイン！まとめサイトの餌食にされました。<br>世界中に醜態が拡散され、フォロー解除の嵐が止まりません！</p>
                        <div class="badge bg-danger fs-5 px-4 py-2 rounded-pill shadow-sm">
                            フォロワー大激減: <?php echo number_format($roulette_penalty); ?> 人
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    <?php endif; ?>
    
    <?php if (empty($news_list)): ?>
        <div class="alert alert-warning text-center shadow-sm py-4">
            <p class="mb-3 fw-bold">手札のデータが見つかりません。</p>
            <a href="Mondai_select.php" class="btn btn-primary">最初から遊ぶ</a>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($news_list as $news): ?>
                <?php 
                    $display_name = !empty($news['tuinusi']) ? $news['tuinusi'] : "風吹けば名無し";
                    $result_border = $news['singi'] ? 'border-real' : 'border-fake';
                    $did_user_select = in_array($news['no'], $selected_news);
                    $select_class = $did_user_select ? 'user-selected' : 'user-not-selected';

                    $game_result = '';
                    if ($did_user_select) {
                        $game_result = $news['singi'] ? 'WIN' : 'LOSE';
                    }
                ?>
                <div class="col">
                    <div class="card h-100 news-card <?php echo $result_border; ?> <?php echo $select_class; ?> shadow-sm">
                        
                        <?php if ($game_result === 'WIN'): ?>
                            <div class="judgment-stamp text-success border-success">正解</div>
                        <?php elseif ($game_result === 'LOSE'): ?>
                            <div class="judgment-stamp text-danger border-danger">不正解</div>
                        <?php endif; ?>

                        <div class="card-body d-flex flex-column">
                            
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-info d-flex justify-content-center align-items-center user-icon me-3 shadow">
                                    <?php echo mb_substr($display_name, 0, 1); ?>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold"><?php echo htmlspecialchars($display_name); ?></div>
                                    <small class="text-secondary">@news_faker</small>
                                </div>
                                
                                <div>
                                    <?php if ($news['singi']): ?>
                                        <span class="badge bg-success badge-singi">リアル</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger badge-singi">フェイク</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="card-text flex-grow-1" style="font-size: 1.1rem; line-height: 1.6;">
                                <div class="fw-bold mb-2  rounded small">
                                    問題文：<?php echo htmlspecialchars($news['mondai']); ?>
                                </div>
                                <div class="text-light p-2 bg-dark " style="border: 1px solid #333;">
                                    <strong>解説:</strong><br>
                                    <?php echo nl2br(htmlspecialchars($news['kaisetu'])); ?>
                                </div>
                            </div>

                            <div class="text-end mt-3 border-top pt-2">
                                <span class="score-text">
                                    影響度: ❤️ <?php echo number_format($news['score']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="text-center mt-5">
        <?php if ($is_game_over): ?>
            <div class="alert alert-danger d-inline-block p-4 shadow-sm">
                <?php if ($is_dobon): ?>
                    <!-- ★連続炎上ドボンのメッセージ -->
                    <h4 class="fw-bold mb-2">🚨 2回連続で炎上を発生させてしまいました！</h4>
                    <p class="mb-3 text-danger-emphasis fw-bold">ネット上で弁明の余地のない致命的なレッテルを貼られ、アカウントが凍結しました...</p>
                <?php elseif ($followers <= 0): ?>
                    <h4 class="fw-bold mb-3">💀 フォロワーが0人になりました。ゲームオーバーです...</h4>
                <?php else: ?>
                    <h4 class="fw-bold mb-3">🎉 全<?php echo htmlspecialchars($rule['limit']); ?>問、すべて終了しました！</h4>
                    <h5>最終フォロワー数: <span class="text-primary fw-bold"><?php echo number_format($followers); ?></span> 人</h5>
                <?php endif; ?>
                
                <?php
                // ゲームオーバーなので、次のプレイのために連続カウントを綺麗にリセットしておく
                $_SESSION['enjo_streak'] = 0;
                ?>
                <a href="Mondai_select.php" class="btn btn-danger btn-lg px-5 fw-bold mt-3">もう一度最初から遊ぶ</a>
            </div>
        <?php else: ?>
            <a href="faker.php" class="btn btn-primary btn-lg px-5 fw-bold shadow">次の問題へ進む</a>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>