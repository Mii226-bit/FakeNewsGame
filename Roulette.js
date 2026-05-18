const bgImage = document.getElementById("bgImage");
const lightImage = document.getElementById("lightImage");
const centerLight = document.getElementById("centerLight");
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

/* ======================
   演出フェードイン
====================== */

setTimeout(()=>{

    bgImage.style.opacity = 1;

},250);

setTimeout(()=>{

    introImage.classList.add("show");

},750);

setTimeout(()=>{

    introVideo.style.opacity = 1;

    /*
       動画再生,今はまだないけど動画入れたらここで再生する
    */

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

},2000);

/* ======================
   ルーレット
====================== */

let spinning = false;
let slowing = false;

let currentRotation = 0;

/* 現在速度 */
let speed = 0;

/* 停止角度 */
let resultAngle = 0;

/* 最終停止地点 */
let targetRotation = 0;

let animationId;

let phase = "idle";

/* ======================
   結果抽選
====================== */

function decideResult(){

    const r = Math.random() * 100;

    /* 50% */

    if(r < 50){

        return {
            name:"炎上",
            angle:90
        };
    }

    /* 40% */

    if(r < 90){

        return {
            name:"何もなし",
            angle:250
        };
    }

    /* 10% */

    return {
        name:"大炎上",
        angle:340
    };
}

/* ======================
   回転処理
====================== */

function rotate(){

    /* ======================
       加速フェーズ1
    ====================== */

    if(phase === "accelerate1"){

        speed += 0.4;

        if(speed >= 15){

            phase = "accelerate2";
        }
    }

    /* ======================
       加速フェーズ2
    ====================== */

    else if(phase === "accelerate2"){

        speed += 0.6;

        if(speed >= 35){

            phase = "accelerate3";
        }
    }

    /* ======================
       加速フェーズ3
    ====================== */

    else if(phase === "accelerate3"){

        speed += 0.8;

        if(speed >= 65){

            phase = "maxspeed";
        }
    }

    /* ======================
       最高速維持
    ====================== */

    else if(phase === "maxspeed"){

        speed = 65;
    }

    /* ======================
       減速
    ====================== */

    else if(phase === "decelerate"){

        const remaining =
        targetRotation - currentRotation;

        /*
           イージング減速
        */

        speed =
        Math.max(
            remaining * 0.015,
            0.5
        );

        /*
           最後だけさらに減速
        */

        if(remaining < 720){

            speed *= 0.97;
        }

        currentRotation += speed;

        /*
           到達判定
        */

        if(currentRotation >= targetRotation){

            currentRotation =
            targetRotation;

            roulette.style.transform =
            `rotate(${currentRotation}deg)`;

            spinning = false;

            slowing = false;

            phase = "idle";

            cancelAnimationFrame(animationId);

            console.log("停止");

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

    /* ======================
       通常回転
    ====================== */

    currentRotation += speed;

    roulette.style.transform =
    `rotate(${currentRotation}deg)`;

    animationId =
    requestAnimationFrame(rotate);
}

/* ======================
   ボタン
====================== */

spinButton.addEventListener("click",()=>{

    /* ======================
       開始
    ====================== */
if(!spinning){

    spinning = true;

    /*
       結果だけ先に決定
    */

    const result =
    decideResult();

    console.log(result.name);

    resultAngle =
    result.angle;

    /*
       加速フェーズ開始
    */

    speed = 2;

    phase = "accelerate1";

    rotate();

    spinButton.textContent =
    "STOP";

    return;
}

    /* ======================
       停止開始
    ====================== */

    if(!slowing){

        slowing = true;

        /*
        現在地点から
        十分先を停止位置にする
        */

        targetRotation=
        currentRotation
        + 360 *6
        + resultAngle;

        phase="decelerate";

        spinButton.disabled = true;
    }
});

/* ======================
   フェードアウト
====================== */

function fadeOutAll(){

    overlay.style.transition =
    "opacity 2s";

    overlay.style.opacity = 0;

    setTimeout(()=>{

        overlay.style.display =
        "none";

    },2000);
}