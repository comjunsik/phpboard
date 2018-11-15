<?php session_start();
//CSRF취약점 방어 토큰확인 시작
$tokn = htmlspecialchars($_POST['tokn']);
if(isset($_SESSION["tokn"]) && $tokn == $_SESSION["tokn"]) {
} else {
    die("비정상적인 접근입니다.");
}
?>
      <meta charset="utf-8">
      <?php
     if(!isset($_SESSION["userid"])) {
      ?>
        <script>
                 alert('로그인 후 이용해 주세요.');
        	 history.back();
            </script>
      <?php
      }

  $num=htmlspecialchars($_POST['num']);    //게시글 번호
  if(isset($_REQUEST["page"]))
      $page=htmlspecialchars($_REQUEST["page"]);
  else
      $page=1;

  //덧글
  if(isset($_REQUEST['ripple_page']))
      $ripple_page=$_REQUEST['ripple_page'];
  else
      $ripple_page=1;

  $ripple_depth=htmlspecialchars($_REQUEST['depth']);

  $ripple_content=htmlspecialchars($_POST["ripple_content"]);

  //덧글 수정인지, 신규 덧글인지 구별
  if(isset($_REQUEST['mode2']))
      $mode=$_REQUEST['mode2'];
  else
      $mode="";

  if(isset($_REQUEST['ripple_num2']))
      $ripple_num=$_REQUEST['ripple_num2'];
  else
      $ripple_num="";

  require_once("../lib/myDB.php");
  $pdo = db_connect();

  if($mode=="modify"){
      try{
          $pdo->beginTransaction();
          $sql="update qna_ripple set content=? where num=? ";
          $stmh= $pdo->prepare($sql);
          $stmh->bindValue(1,$ripple_content, PDO::PARAM_STR);
          $stmh->bindValue(2,$ripple_num,PDO::PARAM_STR);
          $stmh->execute();
          $pdo->commit();

          header("Location:http://54.180.29.9/qna/view.php?num=$num&page=$page&ripple_page=$ripple_page");
      }catch (PDOException $Exception) {
          $pdo->rollBack();
          print "오류: " . $Exception->getMessage();
      }

  }else {
      try {
          $pdo->beginTransaction();
          $sql = "insert into qna_ripple(parent, id, name, nick, content, reg_date, depth)";
          $sql .= "values(?, ?, ?, ?, ?, now(), ?)";
          $stmh = $pdo->prepare($sql);
          $stmh->bindValue(1, $num, PDO::PARAM_STR);
          $stmh->bindValue(2, htmlspecialchars($_SESSION["userid"]), PDO::PARAM_STR);
          $stmh->bindValue(3, htmlspecialchars($_SESSION["name"]), PDO::PARAM_STR);
          $stmh->bindValue(4, htmlspecialchars($_SESSION["nick"]), PDO::PARAM_STR);
          $stmh->bindValue(5, $ripple_content, PDO::PARAM_STR);
          $stmh->bindValue(6, $ripple_depth, PDO::PARAM_STR);
          $stmh->execute();
          $pdo->commit();

          //알림 메시지 등록 시작
          $sql = "select * from qna where num=?";
          $stmh = $pdo->prepare($sql);
          $stmh->bindValue(1, $num, PDO::PARAM_STR);
          $stmh->execute();
          $row = $stmh->fetch(PDO::FETCH_ASSOC);
          $tgt_id = $row["id"];
          $message = "게시물에 덧글이 달렸습니다.";
          $tgt_board = "qna";
          $tgt_num = $num;
          $tgt_page= $page;
          $pdo->beginTransaction();
          $sql = "insert into notify(id, message, tgt_board, tgt_num, reg_date, page)";
          $sql .= "values(?, ?, ?, ?,now(), ?)";
          $stmh = $pdo->prepare($sql);
          $stmh->bindValue(1, $tgt_id, PDO::PARAM_STR);
          $stmh->bindValue(2, $message, PDO::PARAM_STR);
          $stmh->bindValue(3, $tgt_board, PDO::PARAM_STR);
          $stmh->bindValue(4, $tgt_num, PDO::PARAM_STR);
          $stmh->bindValue(5, $tgt_page, PDO::PARAM_STR);
          $stmh->execute();
          $pdo->commit();

          header("Location:http://54.180.29.9/qna/view.php?num=$num&page=$page&ripple_page=$ripple_page");
      } catch (PDOException $Exception) {
          $pdo->rollBack();
          print "오류: " . $Exception->getMessage();
      }
  }
   ?>

