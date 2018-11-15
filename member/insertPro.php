<?php
    session_start();
    //CSRF취약점 방어 토큰확인 시작
    $tokn = htmlspecialchars($_POST['tokn']);
    if(isset($_SESSION["tokn"]) && $tokn == $_SESSION["tokn"]) {
    } else {
        die("비정상적인 접근입니다.");
    }
  $id = htmlspecialchars($_POST["userID"]);
  $pass = htmlspecialchars($_POST["userPassword1"]);
  $name = htmlspecialchars($_POST["userName"]);
  $nick = htmlspecialchars($_POST["userNick"]);

  $pass = md5($pass);

  require_once("../lib/myDB.php");
  $pdo = db_connect();

  try{
      $pdo->beginTransaction();
      $sql = "insert into member VALUES(?, ?, ?, ?, now(),9)";
      $stmh = $pdo->prepare($sql);
      $stmh->bindValue(1, $id, PDO::PARAM_STR);
      $stmh->bindValue(2, $pass, PDO::PARAM_STR);
      $stmh->bindValue(3, $name, PDO::PARAM_STR);
      $stmh->bindValue(4, $nick, PDO::PARAM_STR);


      $stmh->execute();
      $pdo->commit();

      header("Location:http://54.180.29.9/login/login_form.php");
   } catch (PDOException $Exception) {
      $pdo->rollBack();
      print "오류: ".$Exception->getMessage();
  }
?>
