<?php session_start(); ?>
<?php
if(!isset($_SESSION["userid"])) {
    print("
	    <script>
	     window.alert('로그인 후 이용해 주세요.');
	     history.go(-1);
	    </script>
		");
    exit;
}
$filename = $_GET["real_name"];
$show_name= urldecode($_GET["show_name"]);
$path = "../data/$filename";
$real_filename = urldecode("$filename");
header("Content-type: application/x-octetstream");
header("Content-Disposition: attachment; filename=$show_name");
header("Content-Length: ".filesize($path));
header("Content-Transfer-Encoding: binary");
header("Pragma: no-cache");
header("Expires: 0");
flush();

$fp = fopen($path, "r");

fpassthru($fp);
fclose($fp);
?>

