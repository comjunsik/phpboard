<?php
session_start();
//CSRF취약점 방어 토큰확인 시작
$tokn = htmlspecialchars($_POST['tokn']);
if(isset($_SESSION["tokn"]) && $tokn == $_SESSION["tokn"]) {
} else {
    die("비정상적인 접근입니다.");
}
$id=htmlspecialchars($_POST["userID"]);
$password=htmlspecialchars($_POST["userPassword1"]);
$nick =htmlspecialchars($_POST["userNick"]);
$name=htmlspecialchars($_POST["userName"]);


require_once ("../lib/myDB.php");
$pdo=db_connect();

try{
    $pdo->beginTransaction();
    $sql="UPDATE member SET passwd=?, name=?, nick=? WHERE id=?";
    $stmh=$pdo->prepare($sql);

    $stmh->bindValue(1, $password, PDO::PARAM_STR);
    $stmh->bindValue(2, $name, PDO::PARAM_STR);
    $stmh->bindValue(3, $nick, PDO::PARAM_STR);
    $stmh->bindValue(4, $id, PDO::PARAM_STR);

    $stmh->execute();
    $pdo->commit();
    header("Location:http://54.180.29.9/admin/list.php");
}catch (PDOException $Exception){
    $pdo->rollBack();
    echo "오류 :".$Exception->getMessage();
}
?>