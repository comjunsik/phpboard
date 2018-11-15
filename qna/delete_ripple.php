<?php
    session_start();
    //CSRF취약점 방어 토큰확인 시작
    $tokn = htmlspecialchars($_REQUEST['tokn']);
    if(isset($_SESSION["tokn"]) && $_REQUEST["tokn"] == $_SESSION["tokn"]) {
    } else {
        die("비정상적인 접근입니다.");
    }
    $num=htmlspecialchars($_REQUEST["num"]);  //게시글 번호
    $ripple_num=htmlspecialchars($_REQUEST["ripple_num"]);  //댓글 목록번호
    $page =$_REQUEST['page'];
    $ripple_page=$_REQUEST['ripple_page'];
    require_once("../lib/myDB.php");
    $pdo = db_connect();

    try {
        $pdo->beginTransaction();
        $sql = "delete from qna_ripple where num = ?";  //리플 삭제
        $stmh = $pdo->prepare($sql);
        $stmh->bindValue(1, $ripple_num, PDO::PARAM_STR);
        $stmh->execute();
        $pdo->commit();

		//알림삭제 시작
		$pdo->beginTransaction();
		$sql = "delete from notify where tgt_board = 'qna' AND tgt_num = ?";
		$stmh = $pdo->prepare($sql);
		$stmh->bindValue(1, $num, PDO::PARAM_STR);
		$stmh->execute();
		$pdo->commit();
		

        header("Location:http://54.180.29.9/qna/view.php?num=$num&page=$page&ripple_page=$ripple_page");
    }catch (PDOException $Exception) {
    $pdo->rollBack();
    print "오류: ".$Exception->getMessage();
    }
?>
