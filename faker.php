<?php
require_once 'db_config.php';
// 2. ランダムに6件のニュースを取得
// ORDER BY RAND() でランダム抽出し、LIMIT 6 で件数を絞ります
$sql = "SELECT * FROM news ORDER BY RAND() LIMIT 6";
$stmt = $pdo->query($sql);
$news_list = $stmt->fetchAll();

// カードの枠線色をランダムに割り当てるための配列
$border_colors = ['border-danger', 'border-warning', 'border-success', 'border-info', 'border-primary'];
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fake News Poker</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e9f1f7; /* 背景色を少し青っぽく */
        }
        .news-card {
            /* 白黒選択したい */
            background-color: #000 !important;
            color: #fff;
            /* background-color: #fff !important;
            color: #000; */
            border-width: 3px !important;
            cursor: pointer;
            transition: transform 0.2s;/*ふわっとするやつ*/
            text-decoration: none;
        }
        .news-card:hover {
            transform: scale(1.03); /* ホバー時に少し大きく */
            filter: brightness(1.4);
        }
        .user-icon {
            width: 45px;
            height: 45px;
            font-weight: bold;
            font-size: 1.2rem;
        }
        .score-text {
            font-size: 1.2rem;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <h1 class="text-center mb-5 fw-bold">Fake News Poker</h1>
    
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php foreach ($news_list as $news): ?>
            <?php 
                // ランダムに枠線の色を選択
                $random_border = $border_colors[array_rand($border_colors)]; 
            ?>
            <div class="col">
                <!-- クリックした時に詳細や判定へ飛ばす想定（例：judge.php?no=1） -->
                <a href="judge.php?no=<?php echo $news['no']; ?>" class="card h-100 news-card <?php echo $random_border; ?> shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <!-- ツイ主（ユーザー）情報 -->
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-info d-flex justify-content-center align-items-center user-icon me-3 shadow">
                                <?php 
                                    // ツイ主の最初の1文字をアイコンとして表示
                                    echo mb_substr($news['tuinusi'], 0, 1); 
                                ?>
                            </div>
                            <div>
                                <div class="fw-bold"><?php echo htmlspecialchars($news['tuinusi']); ?></div>
                                <small class="text-secondary">@news_faker</small>
                            </div>
                        </div>

                        <!-- ニュース内容 -->
                        <p class="card-text flex-grow-1" style="font-size: 1.1rem; line-height: 1.6;">
                            <?php echo htmlspecialchars($news['question_text']); ?>
                        </p>

                        <!-- スコア表示 -->
                        <div class="text-end mt-3 border-top pt-2">
                            <span class="score-text">
                                <?php 
                                    // 感情アイコンを適当に変えてみる例
                                    $icons = ['❤️', '💚', '🧡'];
                                    echo $icons[array_rand($icons)];
                                ?> 
                                <?php echo number_format($news['score']); ?>
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="text-center mt-5">
        <button class="btn btn-outline-primary btn-lg" onclick="location.reload();">更新して配り直す</button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>