```php
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'game_manager.php';

/* ==========================================
   炎上ルーレット設定
========================================== */

$enjo_penalties = [
    'chinka'    => 0,
    'enjo'      => 0.2,
    'dai_enjo'  => 0.5
];

$news_list = isset($_SESSION['six_news']) ? $_SESSION['six_news'] : [];
$selected_news = isset($_POST['selected_news']) ? $_POST['selected_news'] : [];
$rule = get_game_rule();

/* ==========================================
   フォロワー初期化
========================================== */

if (!isset($_SESSION['followers'])) {
    $_SESSION['followers'] = 10000;
}

$has_dropped = false;
$hendou_followers = 0;

/* ==========================================
   正誤判定
========================================== */

foreach ($news_list as $news) {

    if (in_array($news['no'], $selected_news)) {

        if ($news['singi']) {

            $hendou_followers += (int)$news['score'];

        } else {

            $hendou_followers -= (int)$news['score'] * 1.5;

            $has_dropped = true;
        }
    }
}

/* ==========================================
   ルーレット結果
========================================== */

$roulette_result = null;
$roulette_penalty = 0;

if ($has_dropped) {

    $dice = rand(1, 100);

    if ($dice <= 60) {

        $roulette_result = 'chinka';

    } elseif ($dice <= 90) {

        $roulette_result = 'enjo';

    } else {

        $roulette_result = 'dai_enjo';
    }

    $roulette_penalty =
    $_SESSION['followers']
    * $enjo_penalties[$roulette_result];

    $_SESSION['followers'] -= $roulette_penalty;
}

$_SESSION['followers'] += $hendou_followers;

$followers = $_SESSION['followers'];

advance_round();

$is_game_over = check_game_over();

/* ==========================================
   ルーレット停止角度
========================================== */

$roulette_angle = 250;

if ($roulette_result === 'enjo') {
    $roulette_angle = 90;
}

if ($roulette_result === 'dai_enjo') {
    $roulette_angle = 340;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>答え合わせ</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background-color:#e9f1f7;
    position:relative;
    overflow-x:hidden;
}

/* ==========================================
   元のkotae.php
========================================== */

.news-card{
    background-color:#000 !important;
    color:#fff;
    border-width:4px !important;
    position:relative;
}

.border-fake{
    border-color:#dc3545 !important;
    box-shadow:0 0 15px rgba(220,53,69,0.4) !important;
}

.border-real{
    border-color:#198754 !important;
    box-shadow:0 0 15px rgba(25,135,84,0.4) !important;
}

.user-selected{
    transform:scale(1.02);
    z-index:2;
}

.judgment-stamp{
    position:absolute;

    top:50%;
    left:50%;

    transform:
    translate(-100%, -100%)
    rotate(-15deg)
    scale(2);

    font-size:2.5rem;
    font-weight:900;

    padding:0.2em 0.5em;

    border:4px solid;

    border-radius:10px;

    text-transform:uppercase;

    opacity:0;

    z-index:10;

    pointer-events:none;

    animation:
    stamp-animation 0.4s ease-out forwards;
}

@keyframes stamp-animation{

    to{
        transform:
        translate(-50%, -50%)
        rotate(-15deg)
        scale(1);

        opacity:0.85;
    }
}

.user-icon{
    width:45px;
    height:45px;
    font-weight:bold;
    font-size:1.2rem;
}

.score-text{
    font-size:1.2rem;
    font-weight:bold;
}

.badge-singi{
    font-size:1rem;
    padding:0.5em 0.8em;
}

/* ==========================================
   ルーレット演出
========================================== */

#overlay{

    position:fixed;
    inset:0;

    background:rgba(0,0,0,0.75);

    display:flex;
    justify-content:center;
    align-items:center;
    flex-direction:column;

    z-index:99999;

    opacity:1;
}

/* 背景 */

#bgImage{

    position:absolute;

    width:100%;
    height:100%;

    object-fit:cover;

    filter:brightness(0.7);

    opacity:0;

    transition:opacity 2s;
}

/* スポットライト */

#lightImage{

    position:absolute;

    width:100%;
    height:100%;

    object-fit:cover;

    opacity:0;

    transition:opacity 2s;
}

/* 中央ライト */

#centerLight{

    position:absolute;

    width:100%;
    height:100%;

    background:
    radial-gradient(
        circle at center,
        rgba(255,255,220,0.45) 0%,
        rgba(255,255,220,0.15) 20%,
        rgba(0,0,0,0) 45%
    );

    opacity:0;

    transition:opacity 2s;
}

/* 炎上文字 */

#introImage{

    position:absolute;

    scale:1.5;

    width:500px;

    bottom:-500px;
    left:57%;

    transform:
    translateX(-50%)
    scale(0.2);

    opacity:0;

    z-index:5;

    pointer-events:none;

    filter:brightness(2);
}

#introImage.show{

    animation:
    introFloatIn
    0.9s
    cubic-bezier(0.2,0.9,0.2,1)
    forwards;
}

@keyframes introFloatIn{

    0%{

        bottom:-500px;

        transform:
        translateX(-50%)
        scale(0.2);

        opacity:0;
    }

    60%{
        opacity:1;
    }

    100%{

        bottom:450px;

        transform:
        translateX(-50%)
        scale(1);

        opacity:1;
    }
}

/* 動画 */

#introVideo{

    position:absolute;

    width:900px;

    max-width:90vw;

    opacity:0;

    z-index:6;

    pointer-events:none;

    transition:opacity 0.5s;
}

/* ルーレット */

#rouletteContainer{

    position:relative;

    top:120px;

    width:400px;
    height:400px;

    opacity:0;

    transition:opacity 2s;
}

#roulette{

    position:absolute;

    inset:0;

    border-radius:50%;

    z-index:1;

    background:
    conic-gradient(
        #ff4444 0% 50%,
        #888888 50% 90%,
        #aa0000 90% 100%
    );

    overflow:hidden;

    box-shadow:
    0 0 20px rgba(255,255,255,0.5);
}

#rouletteFrame{

    position:absolute;

    inset:0;

    width:100%;
    height:100%;

    object-fit:contain;

    scale:2.15;

    z-index:2;

    pointer-events:none;

    filter:
    drop-shadow(
        0 0 30px rgba(255,220,100,0.8)
    );
}

/* ボタン */

#spinButton{

    position:absolute;

    bottom:80px;

    padding:15px 50px;

    font-size:24px;

    border:none;

    border-radius:12px;

    cursor:pointer;

    opacity:0;

    transition:opacity 2s;

    z-index:20;
}

</style>
</head>

<body>

<div class="container py-5">

<h1 class="text-center mb-5 fw-bold">
ファクトチェック
</h1>

<?php if (empty($news_list)): ?>

<div class="alert alert-warning text-center shadow-sm py-4">

<p class="mb-3 fw-bold">
手札のデータが見つかりません。
</p>

<a href="Mondai_select.php" class="btn btn-primary">
最初から遊ぶ
</a>

</div>

<?php else: ?>

<div class="row row-cols-1 row-cols-md-3 g-4">

<?php foreach ($news_list as $news): ?>

<?php

$display_name =
!empty($news['tuinusi'])
? $news['tuinusi']
: "風吹けば名無し";

$result_border =
$news['singi']
? 'border-real'
: 'border-fake';

$did_user_select =
in_array($news['no'], $selected_news);

$select_class =
$did_user_select
? 'user-selected'
: 'user-not-selected';

$game_result = '';

if ($did_user_select) {

    $game_result =
    $news['singi']
    ? 'WIN'
    : 'LOSE';
}
?>

<div class="col">

<div class="card h-100 news-card <?php echo $result_border; ?> <?php echo $select_class; ?> shadow-sm">

<?php if ($game_result === 'WIN'): ?>

<div class="judgment-stamp text-success border-success">
正解
</div>

<?php elseif ($game_result === 'LOSE'): ?>

<div class="judgment-stamp text-danger border-danger">
不正解
</div>

<?php endif; ?>

<div class="card-body d-flex flex-column">

<div class="d-flex align-items-center mb-3">

<div class="rounded-circle bg-info d-flex justify-content-center align-items-center user-icon me-3 shadow">

<?php echo mb_substr($display_name, 0, 1); ?>

</div>

<div class="flex-grow-1">

<div class="fw-bold">
<?php echo htmlspecialchars($display_name); ?>
</div>

<small class="text-secondary">
@news_faker
</small>

</div>

<div>

<?php if ($news['singi']): ?>

<span class="badge bg-success badge-singi">
リアル
</span>

<?php else: ?>

<span class="badge bg-danger badge-singi">
フェイク
</span>

<?php endif; ?>

</div>
</div>

<div class="card-text flex-grow-1"
style="font-size:1.1rem; line-height:1.6;">

<div class="fw-bold mb-2 rounded small">

問題文：
<?php echo htmlspecialchars($news['mondai']); ?>

</div>

<div class="text-light p-2 bg-dark"
style="border:1px solid #333;">

<strong>解説:</strong><br>

<?php echo nl2br(htmlspecialchars($news['kaisetu'])); ?>

</div>
</div>

<div class="text-end mt-3 border-top pt-2">

<span class="score-text">

影響度:
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

<?php if ($is_game_over): ?>

<div class="alert alert-danger d-inline-block p-4 shadow-sm">

<?php if ($followers <= 0): ?>

<h4 class="fw-bold mb-3">
💀 フォロワーが0人になりました。
</h4>

<?php else: ?>

<h4 class="fw-bold mb-3">
🎉 全<?php echo htmlspecialchars($rule['limit']); ?>問終了！
</h4>

<?php endif; ?>

<a href="Mondai_select.php"
class="btn btn-danger btn-lg px-5 fw-bold mt-3">

もう一度最初から遊ぶ

</a>

</div>

<?php else: ?>

<a href="faker.php"
class="btn btn-primary btn-lg px-5 fw-bold shadow">

次の問題へ進む

</a>

<?php endif; ?>

</div>
</div>

<?php if ($has_dropped): ?>

<!-- ==========================================
     ルーレット演出
========================================== -->

<div id="overlay">

    <img id="bgImage" src="images/party4.png">

    <img id="introImage" src="images/enjou.png">

    <video
        id="introVideo"
        src="videos/intro.mp4">
    </video>

    <img id="lightImage" src="images/spot.png">

    <div id="centerLight"></div>

    <div id="rouletteContainer">

        <div id="roulette"></div>

        <img
            id="rouletteFrame"
            src="images/wakugumi2.png">

    </div>

    <button id="spinButton">
        START
    </button>

</div>

<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php if ($has_dropped): ?>

<script>

const bgImage =
document.getElementById("bgImage");

const lightImage =
document.getElementById("lightImage");

const centerLight =
document.getElementById("centerLight");

const introImage =
document.getElementById("introImage");

const introVideo =
document.getElementById("introVideo");

const rouletteContainer =
document.getElementById("rouletteContainer");

const roulette =
document.getElementById("roulette");

const spinButton =
document.getElementById("spinButton");

const overlay =
document.getElementById("overlay");

/* ==========================================
   演出開始
========================================== */

setTimeout(()=>{

    bgImage.style.opacity = 1;

},250);

setTimeout(()=>{

    introImage.classList.add("show");

},750);

setTimeout(()=>{

    introVideo.style.opacity = 1;

    introVideo.play();

},950);

setTimeout(()=>{

    lightImage.style.opacity = 1;

},1250);

setTimeout(()=>{

    centerLight.style.opacity = 1;

},1400);

setTimeout(()=>{

    rouletteContainer.style.opacity = 1;

},2000);

setTimeout(()=>{

    spinButton.style.opacity = 1;

},2200);

/* ==========================================
   ルーレット
========================================== */

let currentRotation = 0;

let speed = 0;

let targetRotation = 0;

let animationId;

let phase = "idle";

/* ==========================================
   自動開始
========================================== */

setTimeout(()=>{

    startRoulette();

},3000);

function startRoulette(){

    speed = 2;

    phase = "accelerate1";

    rotate();

    spinButton.textContent = "STOP";

    setTimeout(()=>{

        stopRoulette();

    },3000);
}

/* ==========================================
   停止
========================================== */

function stopRoulette(){

    targetRotation =
    currentRotation
    + 360 * 6
    + <?php echo $roulette_angle; ?>;

    phase = "decelerate";
}

/* ==========================================
   回転処理
========================================== */

function rotate(){

    if(phase === "accelerate1"){

        speed += 0.4;

        if(speed >= 15){

            phase = "accelerate2";
        }
    }

    else if(phase === "accelerate2"){

        speed += 0.6;

        if(speed >= 35){

            phase = "accelerate3";
        }
    }

    else if(phase === "accelerate3"){

        speed += 0.8;

        if(speed >= 65){

            phase = "maxspeed";
        }
    }

    else if(phase === "maxspeed"){

        speed = 65;
    }

    else if(phase === "decelerate"){

        const remaining =
        targetRotation - currentRotation;

        speed =
        Math.max(
            remaining * 0.015,
            0.5
        );

        if(remaining < 720){

            speed *= 0.97;
        }

        currentRotation += speed;

        if(currentRotation >= targetRotation){

            currentRotation =
            targetRotation;

            roulette.style.transform =
            `rotate(${currentRotation}deg)`;

            cancelAnimationFrame(animationId);

            setTimeout(()=>{

                fadeOutAll();

            },2000);

            return;
        }

        roulette.style.transform =
        `rotate(${currentRotation}deg)`;

        animationId =
        requestAnimationFrame(rotate);

        return;
    }

    currentRotation += speed;

    roulette.style.transform =
    `rotate(${currentRotation}deg)`;

    animationId =
    requestAnimationFrame(rotate);
}

/* ==========================================
   フェードアウト
========================================== */

function fadeOutAll(){

    overlay.style.transition =
    "opacity 2s";

    overlay.style.opacity = 0;

    setTimeout(()=>{

        overlay.style.display =
        "none";

    },2000);
}

</script>

<?php endif; ?>

</body>
</html>
```
