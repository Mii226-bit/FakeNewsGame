<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/navbar.css" rel="stylesheet">

    <style>
        /* カード風デザイン */
        .mode-card{
            display: flex;
            justify-content: center;
            align-items: center;
            width: 300px;
            height: 400px;
            background-color: #cfe3f5;
            border: 3px solid #1e4f75;
            border-radius: 30px;
            cursor: pointer;
            transition: 0.2s;
            font-size: 1.3rem;
            font-weight: bold;
        }

        .mode-card:hover{
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .btn-check:checked + .mode-card{
            background-color: #0d6efd;
            color: white;
            transform: scale(1.08);
        }

        .skip-button{
            position: fixed;
            bottom: 30px;
            left: 30px;

            width: 140px;
            height: 80px;

            font-size: 1rem;
        }
    </style>
</head>

<body>

<?php include("navbar.php")?>
<?php include("header.php")?>
<h1 class="text-center mt-4">ゲーム名</h1>

<div class="d-flex justify-content-center align-items-center mt-5 gap-4">

    <!-- マルチ -->
    <input type="radio" class="btn-check" name="playMode" id="multi">
    <label class="mode-card" for="multi">
        マルチ
    </label>

    <!-- ソロ -->
    <input type="radio" class="btn-check" name="playMode" id="solo">
    <label class="mode-card" for="solo">
        ソロ
    </label>

    <input type="checkbox" class="btn-check" id="skip">
    <label class="mode-card skip-button" for="skip">
        説明を省く
    </label>
</div>

<script src="js/home.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>