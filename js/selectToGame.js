const q3 = document.getElementById("q3");
const q5 = document.getElementById("q5");
const q7 = document.getElementById("q7");
const endless = document.getElementById("q8");

q3.addEventListener("change",function(){
    if(q3.checked){
        location.href = "haya.php?count=" + q3.value;
    }
});

q5.addEventListener("change",function(){
    if(q5.checked){
        location.href = "haya.php?count=" +q5.value;
    }
});

q7.addEventListener("change",function(){
    if(q7.checked){
        location.href = "haya.php?count=" +q7.value;
    }
});

endless.addEventListener("change",function(){
    if(endless.checked){
        location.href = "haya.php?count=" +endless.value;
    }
});


