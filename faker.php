<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'db_config.php';
require_once 'game_manager.php';

if (isset($_GET['question_limit'])) {
    init_game($_GET['question_limit']);
}

$rule = get_game_rule();
// ★現在のフォロワー数を取得（なければ1万人で保護）
$followers = isset($_SESSION['followers']) ? $_SESSION['followers'] : 10000;

$sql = "SELECT * FROM news ORDER BY RAND() LIMIT 6";
$stmt = $pdo->query($sql);
$news_list = $stmt->fetchAll();

$_SESSION['current_hand'] = $news_list;
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fake News Poker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #e9f1f7; position: relative; }
        .news-card {
            background-color: #000 !important;
            color: #fff;
            border-width: 3px !important;
            cursor: pointer;
            transition: transform 0.2s;
            text-decoration: none;
        }
        .selected-card {
            border-color: #ff0000 !important;
            box-shadow: 0 0 15px rgba(255, 0, 0, 0.5) !important;
            transform: scale(1.05) !important;
        }
        .news-card:hover {
            transform: scale(1.03);
            filter: brightness(1.2);
        }
        .user-icon { width: 45px; height: 45px; font-weight: bold; font-size: 1.2rem; }
        .score-text { font-size: 1.2rem; font-weight: bold; }
        /* ★右上フォロワー数表示用スタイル */
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
    </style>
</head>
<body>

<div class="follower-counter text-dark">
    👥 フォロワー数: <span class="text-primary fs-5"><?php echo number_format($followers); ?></span> 人
</div>

<form action="kotae.php" method="POST" class="container py-5">
    <h1 class="text-center mb-2 fw-bold">正しいニュースを選べ！</h1>
    
    
    <p class="text-center text-secondary mb-5 fs-5">
        設定: <span class="text-dark fw-bold"><?php echo htmlspecialchars($rule['limit'] === 'endless' ? 'エンドレス' : $rule['limit'] . '問'); ?></span> 
        （現在 <span class="badge bg-primary fs-6"><?php echo htmlspecialchars($rule['current_round']); ?></span> 問目）
    </p>
    
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php foreach ($news_list as $news): ?>
            <?php $display_name = !empty($news['tuinusi']) ? $news['tuinusi'] : "風吹けば名無し"; ?>
            <div class="col">
                <div class="card h-100 news-card shadow-sm" data-no="<?php echo $news['no']; ?>">
                    
                    <input type="checkbox" name="selected_news[]" value="<?php echo $news['no']; ?>" class="d-none news-checkbox">

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

    <div class="text-center mt-5">
        <button type="submit" class="btn btn-primary btn-lg px-5 shadow">すべての答え合わせ</button>
    </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('.news-card');
    cards.forEach(card => {
        card.addEventListener('click', () => {
            card.classList.toggle('selected-card');
            const checkbox = card.querySelector('.news-checkbox');
            checkbox.checked = !checkbox.checked;
        });
    });
});
</script>
</body>
</html>