//ボタン押下→設定を保存(ソロ/マルチ)→画面遷移
//要素の取得
const multi = document.getElementById("multi");//id名
const solo = document.getElementById("solo");
const skip = document.getElementById("skip");

//マルチ
multi.addEventListener("change",function(){//changeが起きたら実行
    localStorage.setItem("skipExplain", skip.checked);
    //skipExplain true or false  skip.checked・・・チェックされてる？
    //setItem("名前", 値);で名前を付けて保存
    localStorage.setItem("playMode","multi");//localstrageにplayModeを保存する　どのモードか保存
    location.href = "Mondai_select.php";//画面遷移 問題選択画面に
});

//ソロ
solo.addEventListener("change",function(){
    localStorage.setItem("skipExplain", skip.checked);
    localStorage.setItem("playMode","solo");
    location.href = "Mondai_select.php";
});