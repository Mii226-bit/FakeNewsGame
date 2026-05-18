window.addEventListener("DOMContentLoaded", function () {

    const overlay = document.getElementById("overlay");
    const skipExplain = localStorage.getItem("skipExplain");

    console.log("skip:", skipExplain);
    console.log("overlay:", overlay);

    if (!overlay) {
        console.log("overlayなし");
        return;
    }

    if (skipExplain === "true") {
        overlay.style.display = "none";
    } else {
        overlay.style.display = "flex";
    }
});