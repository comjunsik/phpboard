<?php
//header("Content-Type:application/json");
session_start(); ?>

 <?php
   if(!isset($_SESSION["userid"])) {
       ?>
       <script>
           alert('로그인 후 이용해 주세요.');
           history.back();
       </script>
   <?php }

   $ripple_num=htmlspecialchars($_POST['ripple_num']);
   //echo $ripple_num;
require_once("../lib/myDB.php");
$pdo = db_connect();

try{
    $sql = "select * from qna_ripple where num=?";  // get target record
    $stmh = $pdo->prepare($sql);
    $stmh->bindValue(1,$ripple_num,PDO::PARAM_STR);
    $stmh->execute();
    $row = $stmh->fetch(PDO::FETCH_ASSOC);

    $data = $row['content'];
    echo $data;
    //echo json_encode($o);
    //echo '{"item":'. '[ { "title":"TEST","urla":"url" },{ "title":"test99999","urla":"url1" } ]' .'}';

}catch (PDOException $Exception){
    $pdo->rollBack();
    print "오류: ".$Exception->getMessage();
}

?>