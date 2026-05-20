// script.js

const overlay = document.getElementById("scoreOverlay");
const scoreValue = document.getElementById("scoreValue");
const diffText = document.getElementById("diffText");
const miniScoreBox = document.getElementById("miniScoreBox");
const miniScoreValue = document.getElementById("miniScoreValue");
const skipLayer = document.getElementById("skipLayer");

skipLayer.addEventListener("click", () => {

    // 演出中だけ
    if(animationRunning){
        skipRequested = true;
    }

});

/*
    仮データ
*/
let currentScore = 1000;
let changeValue = 100;

let skipRequested = false; // trueならスキップする
let animationRunning = false; // アニメーションが実行中かどうか

// true = プラス
// false = マイナス
// ここで増減どっちかの処理してるからなんかfalseゲームの時はここで引数の受け渡しをしそう
const isPlus = false;


/*
    演出開始
*/
 /*下のこれが処理の開始のやつ,これをif文とかで囲めば動かない；
                    現在のスコア,増減する値,その値がプラスかマイナスか*/ 
startScoreAnimation(currentScore, changeValue, isPlus);

async function startScoreAnimation(score, diff, plus){

    // スキップレイヤー有効化
    skipLayer.style.pointerEvents = "auto";

    if(animationRunning) return;

    animationRunning = true;
    skipRequested = false; // スキップフラグをリセット

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

    // スキップされたら即終了
    if(skipRequested){

        const target = plus
            ? score + diff
            : score - diff;

        await finishAnimation(target);
        return;
    }

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

    // スキップされたら即終了
    if(skipRequested){
        await finishAnimation(target);
        return;
    }

    // 通常終了
    await finishAnimation(target);

    /*AIがやれっていうから一旦消した

    // 停止後2秒待機だけど長いから要らない
   // await wait(2000);

    // 最終スコアを右上へ反映
    // miniScoreValue.textContent = target;

    // フェードアウト
    //overlay.classList.remove("show");

    // 完全終了待ち
    //await wait(600);

    // クリック判定削除
    //overlay.style.display = "none";
    //overlay.classList.add("hidden");AIはこっち派

    // 右上スコアを戻す
//miniScoreBox.classList.remove("hide");

*/
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

        const start = performance.now();

        function check(now){

            // スキップ要求
            if(skipRequested){
                resolve();
                return;
            }

            // 通常終了
            if(now - start >= ms){
                resolve();
                return;
            }

            requestAnimationFrame(check);
        }

        requestAnimationFrame(check);
    });
}


/*
    演出終了処理
*/
async function finishAnimation(finalScore){

    // 最終スコアを確定
    scoreValue.textContent = finalScore;
    miniScoreValue.textContent = finalScore;

    // diffTextアニメ停止
    diffText.classList.remove("diff-up", "diff-down");

    // フェードアウト開始
    overlay.classList.remove("show");

    // CSSのtransition待ち
    await new Promise(resolve => setTimeout(resolve, 500));

    // 完全非表示
    overlay.classList.add("hidden");

    // 右上スコアを戻す
    miniScoreBox.classList.remove("hide");

    // クリック無効化
    skipLayer.style.pointerEvents = "none";

    // 状態リセット
    animationRunning = false;
    skipRequested = false;
}