<div class="menus"><a href="http://54.180.29.9/memo/memo.php"><img src="http://54.180.29.9/img/menu01.gif" border="0"></a></div>
<div class="menus"><a href="http://54.180.29.9/qna/list.php"><img src="http://54.180.29.9/img/menu05.gif" border="0"></a></div>
<div class="menus"><a href="http://54.180.29.9/memoir/"><img src="http://54.180.29.9/img/menu03.gif" border="0"></a></div>
<?php
	require_once("../lib/myDB.php");
	$pdo = db_connect();
	if(isset($_SESSION["userid"])) {
		$sql_notify = "SELECT * FROM notify WHERE checked = 'N' AND id=? ORDER BY num DESC";
		$stmh_notify = $pdo->prepare($sql_notify);
		$stmh_notify->bindValue(1, htmlspecialchars($_SESSION["userid"]), PDO::PARAM_STR);
		$stmh_notify->execute();
		$notify_cnt = $stmh_notify->rowCount();
		if($notify_cnt > 0) {
?>
<script>
function notify_view() {
	var f = document.getElementsByClassName("notify_list");
	for(var i=0;i<f.length;i+=1) {
		if(f[i].style.display=="block") {
			f[i].style.display = "none";
		} else {
			f[i].style.display = "block";
		}
	}
}
</script>
<style>
.menus_notify { width: 100px; text-align: center; padding: 10px 0 0 0; }
.menus_notify a { color: #d9534f !important; }
.notify_div { position: absolute; width: 200px; padding: 10px 0 0 0; }
.notify_div .notify_list { display: none; position: relative; width: 100%; text-align: center; padding: 10px 5px; background: #333300; color: #ffffff; border-bottom: 1px solid #e0e0e0; }
.notify_div .notify_list:last-child { border-bottom: none; }
.notify_div .notify_list a { color: #ffffff !important; }
</style>
<div class="menus menus_notify" ><a href="javascript://" onclick="notify_view()" >알림 <?=$notify_cnt?>개</a>
	<div class="notify_div">
<?php
			while ($row_notify = $stmh_notify->fetch(PDO::FETCH_ASSOC)) {
?>
                <div class="notify_list" onclick="notify_delete(<?=$row_notify['num']?>);" ><a href="http://54.180.29.9/<?=$row_notify['tgt_board']?>/view.php?num=<?=$row_notify['tgt_num']?>&page=<?=$row_notify['page']?>"><?=$row_notify['message']?></a></div>
                <?php
            }
}

if($notify_cnt > 0) {
?>
    </div>
</div>
        <script>
            function notify_delete(notify_num) {

                var num = notify_num;

                $.ajax({
                    type: 'POST',
                    url : '../lib/delete_notify.php',
                    data: {num: num},
                    success: function(){
                    },
                    error: function () {
                    }

                });
            }

        </script>
        <?php
    }
    }
?>