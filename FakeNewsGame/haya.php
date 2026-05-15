            <?php
            require_once 'db_config.php';
            // 2. ランダムに6件のニュースを取得
            $sql = "SELECT * FROM news ORDER BY RAND() LIMIT 6";
            $stmt = $pdo->query($sql);
            $news_list = $stmt->fetchAll();
            ?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fake News Poker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e9f1f7;
        }
        .news-card {
            /* 白黒選択したい */
            background-color: #000 !important;
            color: #fff;
            /* background-color: #fff !important;
            color: #000; */
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .news-card:hover {
            filter: brightness(1.2);
        }

        /* ★選択された時の赤いボーダー */
        .selected-card {
            border: 10px solid #e05c23 !important;
            /* box-shadow: 0 0 15px rgba(255, 0, 0, 0.6) !important; */
        }
                .news-card:hover {
            transform: scale(1.03); /* ホバー時に少し大きく */
            filter: brightness(1.2);
        }

                    .user-icon {
                        width: 45px;
                        height: 45px;
                        font-weight: bold;
                        font-size: 1.2rem;
                    }
                    
        .score-text {
            font-size: 1.5rem;
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
                $display_name = !empty($news['tuinusi']) ? $news['tuinusi'] : "風吹けば名無し";
            ?>
            <div class="col">
                <!-- data-no属性にIDを保持させ、JSで取得できるようにする -->
                <div class="card h-100 news-card shadow-sm" 
                     data-no="<?php echo $news['no']; ?>" 
                     onclick="toggleSelect(this)">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-info d-flex justify-content-center align-items-center user-icon me-3 shadow">
                                <?php echo mb_substr($display_name, 0, 1); ?>
                            </div>
                            <div>
                                <div class="fw-bold"><?php echo htmlspecialchars($display_name); ?></div>
                                <small class="text-secondary">@news_faker</small>
                            </div>
                        </div>

                        <p class="card-text flex-grow-1" style="font-size: 1.1rem; line-height: 1.6;">
                            <?php echo htmlspecialchars($news['mondai']); ?>
                        </p>

                        <!-- スコア表示 -->
                        <div class="text-end mt-3 border-top pt-5">
                            <span class="score-text">
                                <?php 
                                    // 感情アイコンを適当に変えてみる例
                                    $icons = ['❤️'];
                                    echo $icons[array_rand($icons)];
                                ?> 
                                <?php echo number_format($news['score']); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- 判定用の操作エリア -->
    <div class="text-center mt-5">
        <button class="btn btn-danger btn-lg px-5 shadow" onclick="submitSelection()">選択したニュースを判別する</button>
        <div class="mt-3">
            <button class="btn btn-outline-secondary" onclick="location.reload();">カードを配り直す</button>
        </div>
    </div>
</div>

<script>
// 選択されたカードのID（no）を管理するセット
let selectedNos = new Set();

/**
 * カードの選択状態を切り替える
 */
function toggleSelect(element) {
    const no = element.getAttribute('data-no');

    if (element.classList.contains('selected-card')) {
        // 解除
        element.classList.remove('selected-card');
        selectedNos.delete(no);
    } else {
        // 選択（枚数制限をかけるならここで判定）
        element.classList.add('selected-card');
        selectedNos.add(no);
    }
}

/**
 * 判定処理へ進む
 */
function submitSelection() {
    if (selectedNos.size === 0) {
        alert("判別するカードを1枚以上選んでください！");
        return;
    }

    // 選んだIDをカンマ区切りで送る（例: judge.php?nos=1,4,5）
    const nosParam = Array.from(selectedNos).join(',');
    window.location.href = `judge.php?nos=${nosParam}`;
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>