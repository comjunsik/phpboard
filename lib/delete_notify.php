<?php
require_once("./myDB.php");
$pdo = db_connect();
$num = $_POST['num'];
//echo $num;
try{
    $sql = "DELETE FROM notify WHERE num=?";
    $stmh = $pdo->prepare($sql);
    $stmh->bindValue(1,$num, PDO::PARAM_STR);
    $stmh->execute();


} catch (PDOException $Exception) {
    $pdo->rollBack();
    print "오류: ".$Exception->getMessage();
}
?>