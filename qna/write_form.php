 <?php
  session_start();
	//CSRF취약점 방어 토큰생성 시작
	$tokn = md5(uniqid(rand(), true));
	$_SESSION["tokn"] = $tokn;
	//CSRF취약점 방어 토큰생성 끝

  if(isset($_REQUEST["page"]))  // 페이지 번호
       $page=htmlspecialchars($_REQUEST["page"]);
  else
   $page=1;
  if(isset($_REQUEST["mode"]))  // 새로 쓰기, 수정, 답변 구분
       $mode=htmlspecialchars($_REQUEST["mode"]);
  else
   $mode="";

  if(isset($_REQUEST["num"]))
       $num=htmlspecialchars($_REQUEST["num"]);
  else
   $num="";

  if ($mode=="modify" || $mode=="response"){
      require_once("../lib/myDB.php");
      $pdo = db_connect();

      try{
        $sql = "select * from qna where num = ? ";
        $stmh = $pdo->prepare($sql);
        $stmh->bindValue(1,$num,PDO::PARAM_STR);
        $stmh->execute();
        $count = $stmh->rowCount();


        if($count<1){
            print "검색결과가 없습니다.<br>";
        }else{
            $row = $stmh->fetch(PDO::FETCH_ASSOC);
            $item_subject = $row["subject"];
            $item_content = $row["content"];
            $item_file_0 = $row["file_name_0"];
            $item_file_1 = $row["file_name_1"];
            $item_file_2 = $row["file_name_2"];
            $copied_file_0 = $row["file_copied_0"];
            $copied_file_1 = $row["file_copied_1"];
            $copied_file_2 = $row["file_copied_2"];
        }
            //답글인 경우
            if ($mode=="response")
            {

                $item_subject = "[답변]".$item_subject;
                $item_content = $item_content."\n"."--------------------------------------------------------------------------"."\n";
                $item_file_0 = $row["file_name_0"];
                $item_file_1 = $row["file_name_1"];
                $item_file_2 = $row["file_name_2"];
                $copied_file_0 = $row["file_copied_0"];
                $copied_file_1 = $row["file_copied_1"];
                $copied_file_2 = $row["file_copied_2"];

            }
        }catch (PDOException $Exception) {
            print "오류: ".$Exception->getMessage();
        }
  }
 ?>
 <!DOCTYPE HTML>
 <html>
 <head>
     <meta charset="utf-8">
     <link  rel="stylesheet" type="text/css" href="../css2/common.css">
     <link  rel="stylesheet" type="text/css" href="../css2/board2.css">
     <link  rel="stylesheet" type="text/css" href="../css2/concert.css">
     <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
     </head>
 <body>
 <div id="wrap">
      <div id="header">
             <?php include "../lib/top_login2.php"; ?>
          </div>
      <div id="menu">
        <?php include "../lib/top_menu2.php"; ?>
          </div>
      <div id="content">
            <div id="col1">
                    	  <div id="left_menu">
                         <?php include "../lib/left_menu.php";?>
                    	</div>
                    </div>
              <div id="col2">
              	<div id="title">
                  	<img src="../img/title_qna.gif">
                  	</div>
              	<div class="clear"></div>
              	<div id="write_form_title">
                  	<img src="../img/write_form_title.gif">
                  	</div>
              	<div class="clear"></div>
               <?php
                 if($mode=="modify") {
                ?>
                 <form  name="board_form" method="post" action="insert.php?mode=modify&num=<?=$num?>&page=<?=$page?>" enctype="multipart/form-data">
                   <?php
                      } else if ($mode=="response") {
                    ?>
                       <form  name="board_form" method="post" action="insert.php?mode=response&num=<?=$num?>&page=<?=$page?>" enctype="multipart/form-data">
                          <?php
                             } else {
                            ?>
                            <form  name="board_form" method="post" action="insert.php" enctype="multipart/form-data">
                              <?php
                                 }
                                ?>
                             	<div id="write_form">
                                 	 <div class="write_line"></div>
                                 	 <div id="write_row1">
                                     	   <div class="col1"> 닉네임 </div>
                                                <div class="col2"><?=$_SESSION["nick"]?></div>
                                     </div>
                                 	 <div class="write_line"></div>
                                        <div id="write_row2"><div class="col1"> 제목   </div>
                                     		<div class="col2"><input type="text" name="subject"
                                                 <?php if ($mode=="modify" || $mode=="response") {?>
                                                   value="<?=$item_subject?>" <?php } ?> required></div>
                                        </div>
                                    <div class="write_line"></div>
                                        <div id="write_row3">
                                            <div class="col1"> 내용   </div>
                                            <div class="col2"><textarea rows="15" cols="79" name="content" style="resize: none;" required><?php if ($mode=="modify" || $mode=="response") {?><?=$item_content?> <?php } ?></textarea></div>
                                        </div>
                                    <div class="write_line"></div>

                                    <!-- 이미지 파일 첨부-->
                                    <div id="write_row4">
                                        <div class="col1"> 파일첨부1   </div>
                                        <div class="col2"><input type="file" name="upfile[]"></div> <!--input type file 하면 됨. 여러파일 저장하기 위해 upfile[] 배열로 저장 -->
                                    </div>
                                    <div class="clear"></div>
                                    <?php if ($mode=="modify" && $item_file_0)  //수정 상황이고 && 이미지 파일 첫번째를 등로했을 경우
                                    {
                                        ?>
                                        <div class="delete_ok">
                                            <?=$item_file_0?> 파일이 등록되어 있습니다.
                                            <input type="checkbox" name="del_file[]" value="0"> 삭제</div>
                                        <div class="clear"></div>
                                    <?php  } ?>
                                    <div class="write_line"></div>
                                    <div id="write_row5"><div class="col1"> 파일첨부2  </div>
                                        <div class="col2"><input type="file" name="upfile[]"></div>
                                    </div>
                                    <?php 	if ($mode=="modify" && $item_file_1)
                                    {
                                        ?>
                                        <div class="delete_ok"><?=$item_file_1?> 파일이 등록되어 있습니다.
                                            <input type="checkbox" name="del_file[]" value="1"> 삭제</div>
                                        <div class="clear"></div>
                                    <?php  } ?>
                                    <div class="write_line"></div>
                                    <div class="clear"></div>
                                    <div id="write_row6"><div class="col1"> 파일첨부3  </div>
                                        <div class="col2"><input type="file" name="upfile[]"></div>
                                    </div>
                                    <?php 	if ($mode=="modify" && $item_file_2)
                                    {
                                        ?>
                                        <div class="delete_ok"><?=$item_file_2?> 파일이 등록되어 있습니다.
                                            <input type="checkbox" name="del_file[]" value="2"> 삭제</div>
                                        <div class="clear"></div>
                                    <?php  	} ?>
                                    <div class="write_line"></div>
                                    <div class="clear"></div>

                                </div>
                                	<div id="write_button"><input type="image" src="../img/ok.png">&nbsp;
                                    	   <a href="list.php?page=<?=$page?>"><img src="../img/list.png"></a>
                                    </div>
                        <!-- csrf tokn 입력-->
						<input type="hidden" name="tokn" value="<?=$tokn?>">

                       </form>
                </div>
            </div>
      </div> <!-- end of wrap -->
  </body>
  </html>
