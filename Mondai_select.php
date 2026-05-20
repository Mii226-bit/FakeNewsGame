<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            .title{
                text-align: center;   
            }

            .container-box{
                display: flex;
                flex-direction:column;
                gap: 20px;
                justify-content: center;
                align-items: center;
                margin-top: 50px;
            }

            .container-box label{
                width: 300px;
                padding: 20px;
                text-align: center;
            }
        </style>
        <title>問題数選択</title>
    </head>
    <body>
        <div>
            <?php include "navbar.php";?>
            <?php include "Rule.php";?>

            <h1 class="title mt-5">問題数を選択</h1>

            <form action="faker.php" method="GET" class="container-box">
                
                <input type="radio" class="btn-check" name="question_limit" id="q3" value="3">
                <label class="btn btn-outline-primary fs-5" for="q3">3問</label>

                <input type="radio" class="btn-check" name="question_limit" id="q5" value="5" checked>
                <label class="btn btn-outline-primary fs-5" for="q5">5問</label>

                <input type="radio" class="btn-check" name="question_limit" id="q7" value="7">
                <label class="btn btn-outline-primary fs-5" for="q7">7問</label>

                <input type="radio" class="btn-check" name="question_limit" id="q8" value="endless">
                <label class="btn btn-outline-primary fs-5" for="q8">エンドレス</label>

                <button type="submit" class="btn btn-success btn-lg mt-4 px-5 shadow-sm fw-bold">
                    ゲームスタート！
                </button>
            </form>
        </div>
      
        <script src="js/Mondai_select.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>