<?php
    session_start();
    //CSRF취약점 방어 토큰확인 시작
    if(isset($_SESSION["tokn"]) && $_REQUEST["tokn"] == $_SESSION["tokn"]) {
    } else {
        die('비정상적인 접근입니다.');
    }
   $num=htmlspecialchars($_REQUEST["num"]);

   require_once("../lib/myDB.php");
   $pdo = db_connect();

    try{
       $pdo->beginTransaction();
       $sql = "delete from memo where num = ?";
       $stmh = $pdo->prepare($sql);
       $stmh->bindValue(1,$num,PDO::PARAM_STR);
       $stmh->execute();
       $pdo->commit();

       header("Location:http://54.180.29.9/memo/memo.php");
    } catch (Exception $ex) {
                    $pdo->rollBack();
                print "오류: ".$Exception->getMessage();
    }
?>