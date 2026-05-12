<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <!--Bootstarap CDN -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-64UC4BEhTGwk3eGpak4nO2jqtl7liTS+juXkSJ2gPAQPmlClQO7s5UgCeR6US48g" crossorigin="anonymous">
        <title></title>
    </head>
    <body>
        <?php include("navbar.php")?>
        <h1>ゲーム名</h1>
        <div class="d-flex justify-content-center mt-4">
            
            <div class="btn-group" role="group">
                <input type="radio" class ="btn-check" name="playMode" id="multi" >
                <label class="btn btn-outline-primary btn-lg px-3 py-5" for="multi">
                マルチ
                </label>
             

                <input type="radio" class="btn-check" name="playMode" id="solo">
                <label class="btn btn-outline-primary btn-lg px-3 py-5" for="solo">
                    ソロ
                </label>
            </div>
        </div>
        <!--Bootstorap css (js)-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-jdSIJTK9l6XwXj3RixpVDXtMcA2bFd9O81RlLAwhpr2oXRqvQP88rr16IeFXTgFE" crossorigin="anonymous"></script>
    </body>
</html>