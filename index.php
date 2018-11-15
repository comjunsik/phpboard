<?php
session_start();

?>
<!DOCTYPE html>
    <html>
        <head>
          <meta charset="UTF-8">
          <link rel="stylesheet" type="text/css" href="http://54.180.29.9/css2/common.css">
            <link rel="stylesheet" href="./css/bootstrap.min.css">
            <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>

        </head>

    <body>
        <div id="wrap">
            <div id="header">
            <?php include "./lib/top_login1.php"; ?>
            </div> <!-- end of header -->
            <div id="menu">

            </div> <!-- end of menu -->
            <?php include "./lib/top_menu1.php"; ?>

             <div id="content"><br>
            <div id="main_img"><img src="./img/cafe24_image01.jpg"></div>
            </div> <!-- end of content -->
        </div> <!-- end of wrap -->
        <footer class="bg-dark mt-4 p-3 text-center fixed-bottom" style="color: #FFFFFF;">
            <!-- 저작권 표시 -->
            Copyright &copy; 2018 CodingTest 원준식 All Rights Reserved.
        </footer>
    </body>
</html>
