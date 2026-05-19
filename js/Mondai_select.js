
window.addEventListener("DOMContentLoaded", function () {

    // 保存した値を取得
    const skipExplain = localStorage.getItem("skipExplain");

    // モーダル取得
    const overlay = document.getElementById("overlay");

    // overlayが無ければ終了
    if (!overlay) return;

    // skipExplain が true なら非表示
    if (skipExplain === "true") {

        overlay.style.display = "none";//見せません～

    } else {

        overlay.style.display = "flex";//見せるよ！！
    }
});