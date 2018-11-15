    <div id="logo"><a href="http://54.180.29.9/index.php"><img src="http://54.180.29.9/img/logo2.gif" border="0"></a></div>   <!--메인 로고 이미지-->
	<div id="moto"><img src="http://54.180.29.9/img/moto2.gif"></div>   <!--메인 글자 이미지-->
	<div id="top_login">
<?php
    if(!isset($_SESSION["userid"]))
	{
?>
          <a href="http://54.180.29.9/login/login_form.php">로그인</a> | <a href="http://54.180.29.9/member/insertForm.php">회원가입</a>
<?php
	}
	else
	{
?>
		<?=$_SESSION["nick"]?> (level:<?=$_SESSION["level"]?>) | 
		<a href="http://54.180.29.9/login/logout.php">로그아웃</a> | <a href="http://54.180.29.9/member/updateForm.php?id=<?=$_SESSION["userid"]?>">정보수정</a>
		<?php
        if(isset($_SESSION["userid"])&& $_SESSION["userid"]=="admin") { ?>
            | <a href="http://54.180.29.9/admin/list.php" style="color:gray;">회원정보 관리</a><?php } ?>
<?php
	}
?>
	 </div>
