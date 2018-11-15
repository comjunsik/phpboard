
<?php
    $id = htmlspecialchars($_POST["userID"]);
    //echo $id;
    //alert($id);
    $empty=3;
    $true=1;
    $false=2;

    if(!$id)
    {
       echo $empty;
    }
    else
    {
        require_once("../lib/myDB.php");
        $pdo = db_connect();

        try{
            $sql = "select * from member where id = ? ";
            $stmh = $pdo->prepare($sql);
            $stmh->bindValue(1,$id,PDO::PARAM_STR);
            $stmh->execute();
            $count = $stmh->rowCount();
        } catch (PDOException $Exception) {
            echo "오류: ".$Exception->getMessage();
        }

        if($count<1){
            echo $true;
        }else{
            echo $false;
        }
    }




?>