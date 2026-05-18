<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <!--Bootstarap CDN -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-64UC4BEhTGwk3eGpak4nO2jqtl7liTS+juXkSJ2gPAQPmlClQO7s5UgCeR6US48g" crossorigin="anonymous">
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
        <title></title>
    </head>
    <body>
        <div>
            <?php include"navbar.php";?>
            <?php include"Rule.php";?>
            <h1 class="title">問題数を選択</h1>
            <div class="container-box">
                <input type="radio" class="btn-check" name="select" id="q3" value="3">
                <label class="btn btn-outline-primary" for="q3">3問</label>

                <input type="radio" class="btn-check" name="select" id="q5" value="5">
                <label class="btn btn-outline-primary" for="q5">5問</label>

                <input type="radio" class="btn-check" name="select" id="q7" value="7">
                <label class="btn btn-outline-primary" for="q7">7問</label>

                <input type="radio" class="btn-check" name="select" id="endless" value="endless">
                <label class="btn btn-outline-primary" for="q8">エンドレス</label>
            </div>
        </div>
        <!--Bootstorap css (js)-->
        <script src="js/Mondai_select.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-jdSIJTK9l6XwXj3RixpVDXtMcA2bFd9O81RlLAwhpr2oXRqvQP88rr16IeFXTgFE" crossorigin="anonymous"></script>
    </body>
</html>