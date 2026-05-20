<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'game_manager.php';

$news_list = isset($_SESSION['current_hand']) ? $_SESSION['current_hand'] : [];
$selected_news = isset($_POST['selected_news']) ? $_POST['selected_news'] : [];
$rule = get_game_rule();

// ★【フォロワー数計算ロジック】
// 答え合わせ画面が開いた段階で、今回選ばれたカードに基づくスコアの変動を計算する
if (!isset($_SESSION['followers'])) {
    $_SESSION['followers'] = 10000;
}

// フォロワー数の変動前の値を保存
$followers_before = $_SESSION['followers'];

foreach ($news_list as $news) {
    // ユーザーがこのカードを選択していた場合のみフォロワー数が変動する
    if (in_array($news['no'], $selected_news)) {
        if ($news['singi']) {
            // 真（REAL）を選んでいたらフォロワー数上昇
            $_SESSION['followers'] += (int)$news['score'];
        } else {
            // 偽（FAKE）を選んでいたらフォロワー数下降
            $_SESSION['followers'] -= (int)$news['score'] * 2; // ★ペナルティを倍にしてよりシビアに
        }
    }
}

// 計算完了後のフォロワー数
$followers = $_SESSION['followers'];

// フォロワー数の変動値を計算
$follower_change = $followers - $followers_before;
$is_positive_change = $follower_change >= 0;

// 今回のラウンド終了処理（カウントアップ）
advance_round();

// 進めた結果、上限に達したか、またはフォロワーが0以下になったかでゲームオーバー判定
$is_game_over = check_game_over();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>答え合わせ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="Score.css">
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
        /* ★右上フォロワー数表示用スタイル */
        /* .follower-counter {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            font-weight: bold;
            z-index: 1000;
        } */
    </style>
</head>
<body>

<!-- <div class="follower-counter text-dark">
    👥 フォロワー数: <span class="text-primary fs-5"><?php echo number_format($followers); ?></span> 人
</div> -->
<div id="miniScoreBox">
    <div class="miniLabel">👥フォロワー数</div>
    <div id="miniScoreValue"><?php echo number_format($followers_before); ?>人</div>
</div>
<!-- オーバーレイ -->
<div id="scoreOverlay" class="hidden" data-follower-before="<?php echo $followers_before; ?>" data-follower-after="<?php echo $followers; ?>" data-follower-change="<?php echo abs($follower_change); ?>" data-is-positive="<?php echo $is_positive_change ? 'true' : 'false'; ?>">

    <!-- 増減の計算結果をここに入れる -->
    <div id="diffText" class="<?php echo $is_positive_change ? 'plus' : 'minus'; ?>"><?php echo ($is_positive_change ? '+' : '-') . abs($follower_change); ?></div>

    <!-- 大きく画面に出る方のスコア -->
    <div class="scoreBox">
        <div class="label">フォロワー数</div>
        <div id="scoreValue"><?php echo number_format($followers_before); ?>人</div>
    </div>

</div>


<div class="container py-5">
    <h1 class="text-center mb-5 fw-bold">答え合わせ</h1>
    
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

                    // ★「真を選べ！」ルールに基づいた勝敗判定に修正
                    $game_result = '';
                    if ($did_user_select) {
                        // 真（リアル）を選んでいたらWIN、偽（フェイク）を選んでいたらLOSE
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
                <?php if ($followers <= 0): ?>
                    <h4 class="fw-bold mb-3">💀 フォロワーが0人になりました。ゲームオーバーです...</h4>
                <?php else: ?>
                    <h4 class="fw-bold mb-3">🎉 全<?php echo htmlspecialchars($rule['limit']); ?>問、すべて終了しました！</h4>
                    <h5>最終フォロワー数: <span class="text-primary fw-bold"><?php echo number_format($followers); ?></span> 人</h5>
                <?php endif; ?>
                <a href="Mondai_select.php" class="btn btn-danger btn-lg px-5 fw-bold mt-3">もう一度最初から遊ぶ</a>
            </div>
        <?php else: ?>
            <a href="faker.php" class="btn btn-primary btn-lg px-5 fw-bold shadow">次の問題へ進む</a>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="Score.js"></script>
</body>
</html>