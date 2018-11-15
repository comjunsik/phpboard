<?php
session_start();
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 2018-11-06
 * Time: 오전 8:23
 */
//CSRF취약점 방어 토큰확인 시작
$tokn = htmlspecialchars($_REQUEST['tokn']);
if(isset($_SESSION["tokn"]) && $tokn == $_SESSION["tokn"]) {
} else {
    die("비정상적인 접근입니다.");
}

$id=htmlspecialchars($_REQUEST["id"]);  //삭제 버튼에서 받아오기

//db connection
require_once ("../lib/myDB.php");
$pdo=db_connect();

try{
    $pdo->beginTransaction();
    $sql="DELETE FROM member WHERE id=?";
    $stmh=$pdo->prepare($sql);
    $stmh->bindValue(1, $id, PDO::PARAM_STR);

    $stmh->execute();
    $pdo->commit();
    //echo "데이터가 삭제되었습니다.";
    header("Location:http://54.180.29.9/admin/list.php");
}catch (PDOException $Exception){
    $pdo->rollBack();
    echo "오류 :".$Exception->getMessage();
}
?>