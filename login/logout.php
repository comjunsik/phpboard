<?php
    session_start();
    /*unset($_SESSION["userid"]);
    unset($_SESSION["name"]);
    unset($_SESSION["nick"]);
    unset($_SESSION["level"]);*/
    session_destroy();
    echo '<script>alert("로그아웃 되었습니다.");
            location.href="http://54.180.29.9/index.php";
            </script>'
    //header("Location:http://54.180.29.9/phpbbs/codingtest/index.php");
?>

<?php
//
?>