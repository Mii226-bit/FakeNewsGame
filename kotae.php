<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// faker.php で配られた 6 枚の手札データをセッションから読み込む
$news_list = isset($_SESSION['current_hand']) ? $_SESSION['current_hand'] : [];

// faker.php から送信されてきた「ユーザーがクリックしたカードの no リスト」
$selected_news = isset($_POST['selected_news']) ? $_POST['selected_news'] : [];
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>答え合わせ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #e9f1f7; }
        .news-card {
            background-color: #000 !important;
            color: #fff;
            border-width: 4px !important;
            position: relative; /* 判定スタンプを絶対配置するため */
        }
        .border-fake {
            border-color: #dc3545 !important;
            box-shadow: 0 0 15px rgba(220, 53, 69, 0.4) !important;
        }
        .border-real {
            border-color: #198754 !important;
            box-shadow: 0 0 15px rgba(25, 135, 84, 0.4) !important;
        }
        


        /* 勝負の成否を表す大文字のスタンプエフェクト */
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
            animation: stamp-animation 0.4s ease-out forwards; /* ガツンとスタンプを押すアニメーション */
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
    </style>
</head>
<body>

<div class="container py-5">
    <h1 class="text-center mb-5 fw-bold">答え合わせ</h1>
    
    <?php if (empty($news_list)): ?>
        <div class="alert alert-warning text-center shadow-sm py-4">
            <p class="mb-3 fw-bold">手札のデータが見つかりません。</p>
            <a href="faker.php" class="btn btn-primary">次のゲームへ
            </a>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($news_list as $news): ?>
                <?php 
                    $display_name = !empty($news['tuinusi']) ? $news['tuinusi'] : "風吹けば名無し";

                    // 1. そのニュース自体の正体（真偽）判定
                    $is_fake = ($news['singi'] == '偽物' || $news['singi'] == '嘘' || $news['singi'] == 1);
                    $result_border = $is_fake ? 'border-fake' : 'border-real';

                    // 2. ユーザーがこのカードを「クリックして選んでいたか」の判定
                    $did_user_select = in_array($news['no'], $selected_news);
                    $select_class = $did_user_select ? 'user-selected' : 'user-not-selected';

                    // 3. ゲームとしての勝敗判定（「フェイクを見抜くゲーム」と仮定した場合のロジック）
                    // 選んだカードがフェイクなら正解(WIN)、選んだカードがリアルならお手付き(LOSE)
                    $game_result = '';
                    if ($did_user_select) {
                        $game_result = $is_fake ? 'WIN' : 'LOSE';
                    }
                ?>
                <div class="col">
                    <div class="card h-100 news-card <?php echo $result_border; ?> <?php echo $select_class; ?> shadow-sm">
                        
                        <?php if ($game_result === 'WIN'): ?>
                            <div class="judgment-stamp text-success border-success">正解 (WIN)</div>
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
                                    <?php if ($is_fake): ?>
                                        <span class="badge bg-danger badge-singi">FAKE</span>
                                    <?php else: ?>
                                        <span class="badge bg-success badge-singi">REAL</span>
                                    <?php endif; ?>
                                </div>
                            </div>


                            <div class="card-text flex-grow-1" style="font-size: 1.1rem; line-height: 1.6;">
                                <div class="fw-bold mb-2 text-warning">
                                    【判定】<?php echo htmlspecialchars($news['singi']); ?>
                                </div>
                                <div class="text-light p-2 bg-dark rounded small" style="border: 1px solid #333;">
                                    <strong>解説:</strong><br>
                                    <?php echo nl2br(htmlspecialchars($news['kaisetu'])); ?>
                                </div>
                            </div>

                            <div class="text-end mt-3 border-top pt-2">
                                <span class="score-text">
                                    ❤️ <?php echo number_format($news['score']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="text-center mt-5">
        <a href="faker.php" class="btn btn-primary btn-lg">もう一度遊ぶ</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>