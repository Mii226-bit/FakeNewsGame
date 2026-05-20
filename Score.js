// script.js

const overlay = document.getElementById("scoreOverlay");
const scoreValue = document.getElementById("scoreValue");
const diffText = document.getElementById("diffText");
const miniScoreBox = document.getElementById("miniScoreBox");
const miniScoreValue = document.getElementById("miniScoreValue");

/*
    ここの値はゲームの方の値を受け取るやつ
*/
let currentScore = 100;
let changeValue = 10;

// true = プラス
// false = マイナス
// ここで増減どっちかの処理してるからなんかfalseゲームの時はここで引数の受け渡しをしそう
const isPlus = false;


/*
    演出開始
*/
startScoreAnimation(currentScore, changeValue, isPlus);

async function startScoreAnimation(score, diff, plus){
// 右上スコアを消す
miniScoreBox.classList.add("hide");
    // 初期値セット
    scoreValue.textContent = score;

    // 増減表示設定
    diffText.textContent = plus ? `+${diff}` : `-${diff}`;

    diffText.classList.remove("plus", "minus");
    diffText.classList.add(plus ? "plus" : "minus");

    // フェードイン
    overlay.classList.remove("hidden");

    requestAnimationFrame(() => {
        overlay.classList.add("show");
    });

    // 少し待つ
    await wait(600);

    // 増減アニメーション開始
    diffText.classList.remove("diff-up", "diff-down");

    void diffText.offsetWidth;

    diffText.classList.add(
        plus ? "diff-up" : "diff-down"
    );

    // スコア増減アニメーション
    const target = plus
        ? score + diff
        : score - diff;

    animateNumber(score, target, 1200);

    // 数字スクロール待機
    await wait(1400);

    // 停止後2秒待機
    await wait(2000);

    // 最終スコアを右上へ反映
miniScoreValue.textContent = target;

    // フェードアウト
    overlay.classList.remove("show");

    // 完全終了待ち
    await wait(600);

    // クリック判定削除
    overlay.style.display = "none";

    // 右上スコアを戻す
miniScoreBox.classList.remove("hide");
}


/*
    数値スクロール
*/
function animateNumber(start, end, duration){

    const startTime = performance.now();

    function update(now){

        const elapsed = now - startTime;

        const progress = Math.min(elapsed / duration, 1);

        // イージング
        const eased = easeOutCubic(progress);

        const current =
            Math.floor(
                start + (end - start) * eased
            );

        scoreValue.textContent = current;

        if(progress < 1){
            requestAnimationFrame(update);
        }else{
            scoreValue.textContent = end;
        }
    }

    requestAnimationFrame(update);
}


/*
    イージング
*/
function easeOutCubic(t){
    return 1 - Math.pow(1 - t, 3);
}


/*
    待機
*/
function wait(ms){
    return new Promise(resolve => {
        setTimeout(resolve, ms);
    });
}