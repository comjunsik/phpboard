 <?php
 session_start();

 //CSRF취약점 방어 토큰생성 시작
 $tokn = md5(uniqid(rand(), true));
 $_SESSION["tokn"] = $tokn;

 $file_dir = '../data/';       //업로드 파일 경로
 $num=htmlspecialchars($_REQUEST["num"]);
 $page=htmlspecialchars($_REQUEST["page"]);   //페이지번호
 require_once("../lib/myDB.php");
 $pdo = db_connect();

$mode="";

 if(isset($_REQUEST["ripple_page"])) { // $_REQUEST["page"]값이 없을 때에는 1로 지정
     $ripple_page = htmlspecialchars($_REQUEST["ripple_page"]);  // 페이지 번호
 }else {
     $ripple_page = 1;
 }
 $ripple_scale=5;         //한 페이지에 보여질 덧글 수
 $ripple_page_scale=3;    //한 페이지당 표시될 페이지 수
 $ripple_first_num = ($ripple_page-1)*$ripple_scale;     //덧글 리스트에 표시되는 덧글의 첫 순번

 try{
     $sql = "select * from qna where num=?";
     $stmh = $pdo->prepare($sql);
     $stmh->bindValue(1, $num, PDO::PARAM_STR);
     $stmh->execute();

     $row = $stmh->fetch(PDO::FETCH_ASSOC);
     $item_num     = $row["num"];
     $item_id      = $row["id"];
     $item_name    = $row["name"];
     $item_nick    = $row["nick"];
     $item_subject = str_replace(" ", "&nbsp;", $row["subject"]);
     $item_content = $row["content"];
     $item_date    = $row["reg_date"];
     $item_date    = substr($item_date, 0, 10);
     $item_hit     = $row["hit"];
     $item_depth   = $row["depth"];



     // 파일 업로드 부분
     $file_name[0]   = $row["file_name_0"];
     $file_name[1]   = $row["file_name_1"];
     $file_name[2]   = $row["file_name_2"];

     $file_type[0]   = $row["file_type_0"];
     $file_type[1]   = $row["file_type_1"];
     $file_type[2]   = $row["file_type_2"];

     $file_copied[0] = $row["file_copied_0"];
     $file_copied[1] = $row["file_copied_1"];
     $file_copied[2] = $row["file_copied_2"];

     //덧글
     $item_date    = $row["reg_date"];
     $item_date    = substr($item_date,0,10);
     $item_subject = str_replace(" ", "&nbsp;", $row["subject"]);
     $item_content = $row["content"];



     //조회수 증가
     $new_hit = $item_hit + 1;
     try{
         $pdo->beginTransaction();
         $sql = "update qna set hit=? where num=?";   // 글 조회수 증가
         $stmh = $pdo->prepare($sql);
         $stmh->bindValue(1, $new_hit, PDO::PARAM_STR);
         $stmh->bindValue(2, $num, PDO::PARAM_STR);
         $stmh->execute();
         $pdo->commit();

		//확인된 알림 끄기 시작
		$tgt_board = "qna";
		$pdo->beginTransaction();
		$sql = "delete from notify where id=? AND tgt_board=? AND tgt_num=?";
		$stmh = $pdo->prepare($sql);
		$stmh->bindValue(1, htmlspecialchars($_SESSION["userid"]), PDO::PARAM_STR);
		$stmh->bindValue(2, $tgt_board, PDO::PARAM_STR);
		$stmh->bindValue(3, $num, PDO::PARAM_STR);
		$stmh->execute();
		$pdo->commit();


        } catch (PDOException $Exception) {
             $pdo->rollBack();
             print "오류: ".$Exception->getMessage();
        }

?>
 <!DOCTYPE HTML>
 <html>
 <head>
     <meta charset="utf-8">
     <link  rel="stylesheet" type="text/css" href="../css2/common.css">
     <link  rel="stylesheet" type="text/css" href="../css2/board1.css">
     <link  rel="stylesheet" type="text/css" href="../css2/board4.css">
     <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
     <script>
            function del(href)
            {
                    if(confirm("한번 삭제한 자료는 복구할 방법이 없습니다.\n\n정말 삭제하시겠습니까?")) {
                           document.location.href = href;
                        }
                }
         </script>
     </head>
 <body>
  <div id="wrap">
      <div id="header"><?php include "../lib/top_login2.php"; ?></div>
      <div id="menu"><?php include "../lib/top_menu2.php"; ?></div>
      <div id="content">
          <div id="col1">
              <div id="left_menu"><?php include "../lib/left_menu.php"; ?></div>
          </div>
          <div id="col2">
              <div id="title"><img src="../img/title_qna.gif"></div>
              <div id="view_comment"> &nbsp;</div>
              <div id="view_title">
                  <div id="view_title1"><?= $item_subject ?></div>
                  <div id="view_title2"><?= $item_nick ?> | 조회 : <?= $item_hit ?> | <?= $item_date ?> </div>
              </div>
              <div id="view_content">

              <!-- 다운로드 링크 & 이미지 파일 뿌려주기 -->
              <?php       //다운로드 링크 걸어주기
              for ($i=0; $i<3; $i++) {
                  if ($file_copied[$i]) {
                      $show_name = $file_name[$i];
                      $real_name = $file_copied[$i];
                      $real_type = $file_type[$i];
                      $file_path = $file_dir . $real_name;
                      $file_size = filesize($file_path);
                      //만약 gif, png , jpg 타입이면 화면에 뿌려주기


                      print "▷ 첨부파일 : $show_name ($file_size Byte) &nbsp;&nbsp;
                               <a href='download2.php?real_name=$real_name&show_name=$show_name'>[다운로드]</a><br>";
                  }
                  ///////////////이미지 게시글에 뿌려주기 /////////////////
                  if(($real_type[$i] != "image/gif") && ($real_type[$i] != "image/jpeg" && ($real_type[$i] != "image/png"))) {

                      if ($file_copied[$i])    //여기서 image 등록해놨는지 검사해서 1개만 들록했으면 1번만 반복됨.
                      {
                          $imageinfo = getimagesize($file_dir . $file_copied[$i]);  //getimagesize()함수
                          $image_width[$i] = $imageinfo[0];  //파일 너비
                          $image_height[$i] = $imageinfo[1]; //파일 높이
                          $image_type[$i] = $imageinfo[2];  //파일 형식
                          $img_name = $file_copied[$i];     //이미지 이름
                          $img_name = "../data/" . $img_name;

                          //이미지 너비 맞추기
                          if ($image_width[$i] > 785)
                              $image_width[$i] = 785;

                          // image 타입 1은 gif 2는 jpg 3은 png
                          if ($image_type[$i] == 1 || $image_type[$i] == 2 || $image_type[$i] == 3) {
                              print "<img src='$img_name' width='$image_width[$i]'><br><br>";
                          }
                      }
                  }
              }
              ?>
              <br>
              <!-- 게시글 내용, 사용자가 타이핑 친 내용-->
              <?= $item_content ?>
          </div>

          <!-- 댓글 내용 시작 ------------------->
          <div id="ripple">
              <?php
              try{
                  $sql = "select * from qna_ripple where parent='$item_num' and depth='$item_depth' order by num asc limit $ripple_first_num, $ripple_scale";
                  $stmh1 = $pdo->query($sql);   // ripple PDOStatement 변수명을 다르게

                  $sql2 = "select * from qna_ripple where depth='$item_depth' and parent='$num'";          // 현재 게시글 부모에 맞춰 전체 덧글 수 파악하기 위해
                  $stmh2 = $pdo->query($sql2);
                  $ripple_total_row = $stmh2->rowCount();        //전체 덧글 수

                  $ripple_total_page = ceil($ripple_total_row / $ripple_scale);  //전체 덧글 페이지 블록 수 올림 처리
                  $ripple_current_page = ceil($ripple_page/$ripple_page_scale);  //현재 덧글 페이지 블록 위치계산


                  if($ripple_page==1)
                      $ripple_start_num = $ripple_total_row;
                  else
                      $ripple_start_num = $ripple_total_row - ($ripple_page -1)*$ripple_scale;

              } catch (PDOException $Exception) {
                  print "오류: ".$Exception->getMessage();
              }

              while ($row_ripple = $stmh1->fetch(PDO::FETCH_ASSOC)) {
                  $ripple_num     = $row_ripple["num"];
                  $ripple_id      = $row_ripple["id"];
                  $ripple_nick    = $row_ripple["nick"];
                  $ripple_content = str_replace("\n", "<br>", $row_ripple["content"]);
                  $ripple_content = str_replace(" ", "&nbsp;", $ripple_content);
                  $ripple_date    = $row_ripple["reg_date"];
                  $ripple_parent  = $row_ripple["parent"];
                  ?>
                <table>
                      <thead>
                          <tr>
                              <th id="writer_title1"><?= $ripple_nick ?></th>
                                <th id="writer_title2"><?= $ripple_date ?></th>

                          &nbsp; &nbsp;
                          <?php
                          if (isset($_SESSION["userid"])) {
                              if ($_SESSION["userid"] == "admin" || $_SESSION["userid"] == $ripple_id)
                                 print "<th><a href='#' onclick='ripple_modify($ripple_num);' type='button'>&nbsp;[수정]&nbsp;</a></th>";
                                  print "<th><a href=delete_ripple.php?num=$item_num&ripple_num=$ripple_num&page=$page&ripple_page=$ripple_page&tokn=$tokn>[삭제]</a></th></tr>";
                          }
                          ?>

                      </thead>
                        <tbody>
                            <tr>
                                <td colspan="4" id="ripple_content"><?=$ripple_content?></td>
                            </tr>
                        </tbody>
                </table>


                  <div class="hor_line_ripple"></div>
                  <?php

              } // 댓글 while문의 끝

              // 덧글 페이지 구분 블럭의 첫 페이지 수 계산
              $ripple_start_page = ($ripple_current_page -1) * $ripple_page_scale + 1;
              // 덧글 페이지 구분 블럭의 마지막 페이지 수 계산
              $ripple_end_page = $ripple_start_page + $ripple_page_scale -1;

              ?>


              <div id="ripple_page_button" align="center">
                  <div id="ripple_page_num">
                      <?php
                            if($ripple_page!=1 && $ripple_page_scale){
                                $ripple_prev_page = $ripple_page - $ripple_page_scale;
                                if($ripple_prev_page<=0)
                                    $ripple_prev_page =1;
                                print "<a href=view.php?num=$num&page=$page&ripple_page=$ripple_prev_page>◀ </a>";
                            }

                            for($i=$ripple_start_page; $i<=$ripple_end_page && $i<=$ripple_total_page; $i++){
                                if($ripple_page==$i && $num==$ripple_parent)
                                    print "<span style='color: red;' ><b>[$i]</b></span>";
                                else
                                    print "<a href=view.php?num=$num&page=$page&ripple_page=$i>[$i]</a>";
                            }

                            if($ripple_page<$ripple_total_page){
                                $ripple_next_page = $ripple_page + $ripple_page_scale;
                                if($ripple_next_page > $ripple_total_page)
                                    $ripple_next_page = $ripple_total_page;
                                print "<a href=view.php?num=$num&page=$page&ripple_page=$ripple_next_page> ▶</a><p>";
                                }
                        ?>
                  </div>



              </div>

<br>
              <script>
                  function ripple_modify(ripple_number) {       //[수정] 버튼 클릭시 해당 덧글 num으로 content 불러오기

                      var ripple_num = ripple_number;

                      $.ajax({
                          type: 'POST',
                          url : './ripple_num.php',
                          data: {ripple_num: ripple_num},
                          success: function(result){
                              document.getElementById('ripple_content2').value=result;
                              document.getElementById('ripple_num2').value=ripple_num;
                              document.getElementById('mode2').value="modify";
                              document.getElementById('ripple_content2').focus();
                          },
                          error:function () {
                              alert("실패");
                          }
                      });
                  }

              </script>


              <!--댓글 폼------------------ -->
              <?php
              if(isset($_SESSION['userid'])){
              ?>
              <form name="ripple_form" method="post" action="insert_ripple.php">
                  <input type="hidden" name="num" value="<?=$item_num?>">
                  <input type="hidden" name="page" value="<?=$page?>">
                  <input type="hidden" name="depth" value="<?=$item_depth?>">
                  <input type="hidden" name="ripple_page" value="<?=$ripple_page?>">
                  <input type="hidden" name="ripple_num2" id="ripple_num2">
                  <input type="hidden" name="mode2" id="mode2">
                  <input type="hidden" name="tokn" value="<?=$tokn?>"> <!-- csrf tokn-->
                  <div id="ripple_box">
                      <div id="ripple_box1"><img src="../img/title_comment.gif"></div>

                      <div id="ripple_box2"><textarea rows="5" cols="65" id="ripple_content2" name="ripple_content" style="resize: none;" required></textarea></div>

                      <div id="ripple_box3"><input type=image src="../img/ok_ripple.gif"></div>
                  </div>
              </form>
              <?php } ?>
          </div> <!-- end of ripple  댓글 끝------------>





              <div id="view_button">  <!-- 목록 버튼-->
                  <a href="list.php?page=<?=$page?>"><img src="../img/list.png"></a>&nbsp;
                     <?php
                        if(isset($_SESSION["userid"])) {
                        if($_SESSION["userid"]==$item_id || $_SESSION["userid"]=="admin"){
                        ?>
                        	<a href="write_form.php?mode=modify&num=<?=$num?>&page=<?=$page?>"><img src="../img/modify.png"></a>&nbsp;
                            <a href="javascript:del('delete.php?num=<?=$num?>&page=<?=$page?>&tokn=<?=$tokn?>')"><img src="../img/delete.png"></a>&nbsp;
                         <?php  	}
                            ?>
                        <!-- 답변 쓰기-->
                            <a href="write_form.php?mode=response&num=<?=$num?>&page=<?=$page?>"><img src="../img/response.png"></a>&nbsp;
                            <a href="write_form.php"><img src="../img/write.png"></a>
                     <?php
	                       }
                        } catch (PDOException $Exception) {
                            print "오류: ".$Exception->getMessage();

                        }
                    ?>
              </div>
            	<div class="clear"></div>
                 </div> <!-- end of col2 -->
           </div> <!-- end of content -->
     </div> <!-- end of wrap -->
 </body>
 </html>
