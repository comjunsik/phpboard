<?php session_start(); ?>
<meta charset="utf-8">
<?php
//CSRF취약점 방어 토큰확인 시작
if(isset($_SESSION["tokn"]) && $_POST["tokn"] == $_SESSION["tokn"]) {
} else {
    die('비정상적인 접근입니다.');
}

if(!isset($_SESSION["userid"])) {
    ?>
    <script>
        alert('로그인 후 이용해 주세요.');
        history.back();
    </script>
    <?php
}


$content=htmlspecialchars($_POST["content"]);

require_once("../lib/myDB.php");
$pdo = db_connect();
try{
    $pdo->beginTransaction();
    $sql = "insert into phptest.memo(id, name, nick, content, reg_date)";
    $sql.= "values(?, ?, ?, ?,now())";
    $stmh = $pdo->prepare($sql);
    $stmh->bindValue(1, htmlspecialchars($_SESSION["userid"]), PDO::PARAM_STR);
    $stmh->bindValue(2, htmlspecialchars($_SESSION["name"]), PDO::PARAM_STR);
    $stmh->bindValue(3, htmlspecialchars($_SESSION["nick"]), PDO::PARAM_STR);
    $stmh->bindValue(4, $content, PDO::PARAM_STR);
    $stmh->execute();
    $pdo->commit();

    header("Location:http://54.180.29.9/memo/memo.php");
} catch (PDOException $Exception) {
    $pdo->rollBack();
    print "오류: ".$Exception->getMessage();
}
?>

