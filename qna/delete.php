  <?php
   session_start();
  //CSRF취약점 방어 토큰확인 시작
  $tokn = htmlspecialchars($_REQUEST['tokn']);
  if(isset($_SESSION["tokn"]) && $tokn == $_SESSION["tokn"]) {
  } else {
      die("비정상적인 접근입니다.");
  }



   $num=htmlspecialchars($_REQUEST["num"]);
   $page=htmlspecialchars($_REQUEST["page"]);

   require_once("../lib/myDB.php");
   $pdo = db_connect();

   $upload_dir = '../data/';   //물리적 저장위치
    //서버에 저장된 파일삭제 하기 위해
   try{
     $sql = "select * from qna where num = ? ";
     $stmh = $pdo->prepare($sql);
     $stmh->bindValue(1,$num,PDO::PARAM_STR);
     $stmh->execute();
     $count = $stmh->rowCount();

     $row = $stmh->fetch(PDO::FETCH_ASSOC);
     $copied_name[0] = $row[file_copied_0];
     $copied_name[1] = $row[file_copied_1];
     $copied_name[2] = $row[file_copied_2];

     for ($i=0; $i<3; $i++)

        if ($copied_name[$i])
         {
            $file_name = $upload_dir.$copied_name[$i];
	        unlink($file_name);  //서버에서 저장된 파일 삭제
	     }

   }catch (PDOException $Exception) {
            print "오류: ".$Exception->getMessage();
   }
    //테이블에 있는 레코드 삭제
   try{
     $pdo->beginTransaction();
     $sql = "delete from qna where num = ?";
     $stmh = $pdo->prepare($sql);
     $stmh->bindValue(1,$num,PDO::PARAM_STR);
     $stmh->execute();
     $pdo->commit();


	//알림삭제 시작
	$pdo->beginTransaction();
	$sql = "delete from notify where tgt_board = 'qna' AND tgt_num = ?";
	$stmh = $pdo->prepare($sql);
	$stmh->bindValue(1, $num, PDO::PARAM_STR);
	$stmh->execute();
	$pdo->commit();
	

     header("Location:http://54.180.29.9/qna/list.php?page=$page");

     } catch (Exception $ex) {
            $pdo->rollBack();
        print "오류: ".$Exception->getMessage();
   }
?>