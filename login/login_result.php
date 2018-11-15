<?php
session_start();
$id=htmlspecialchars($_POST["id"]);
$pw=htmlspecialchars($_POST["pass"]);

$pw = md5($pw);
/*if($id="admin" && $pw=1){
    $_SESSION["userid"]="admin";
    $_SESSION["name"]="관리자";
    $_SESSION["nick"]="관리자";
    $_SESSION["level"]=1;

    header("Location:http://54.180.29.9/phpbbs/codingtest/index.php");
    exit;
}*/

require_once ("../lib/myDB.php");
$pdo = db_connect();

try{
    $sql = "SELECT * FROM member WHERE id=?";
    $stmh = $pdo->prepare($sql);
    $stmh ->bindValue(1,$id, PDO::PARAM_STR);
    $stmh->execute();

    $count = $stmh->rowCount();

}catch (PDOException $exception){
    echo "오류: ".$exception->getMessage();
}

$row = $stmh->fetch(PDO::FETCH_ASSOC);

    if($count<1) {

    ?>

    <script>
        alert("아이디가 없습니다!");
        history.back();       //이전 페이지로 이동
    </script>

    <?php
     }else if($pw!=$row["passwd"]) {   //비밀번호가 같지 않을 때
    ?>
    <script>
        alert("비밀번호가 틀립니다!");
        history.back();
    </script>
    <?php
    }else{
        $_SESSION["userid"]=$row["id"];
        $_SESSION["name"]=$row["name"];
        $_SESSION["nick"]=$row["nick"];
        $_SESSION["level"]=$row["level"];

        header("Location:http://54.180.29.9/index.php");
        exit;
    }
    ?>