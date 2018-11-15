 <?php
  session_start();
 //CSRF취약점 방어 토큰생성 시작
 $tokn = md5(uniqid(rand(), true));
 $_SESSION["tokn"] = $tokn;

  if(isset($_REQUEST["page"]))
      $page=htmlspecialchars($_REQUEST["page"]);
  else
      $page=1;

  $scale = 5;
  $page_scale =3;
  $first_num = ($page-1)*$scale;
  require_once("../lib/myDB.php");
  $pdo = db_connect();

  try{
        $sql = "SELECT * FROM memo ORDER BY num DESC";        //최근에 작성한 걸로 정렬
        $stmh = $pdo->query($sql);
        $total_row = $stmh->rowCount();
        $total_page = ceil($total_row/$scale);
        $current_page= ceil($page/$page_scale);
  } catch (PDOException $Exception) {
        echo "오류: ".$Exception->getMessage();
  }
  ?>
  <!DOCTYPE HTML>
  <html>
  <head>
      <meta charset="utf-8">
      <link rel="stylesheet" type="text/css" href="../css2/common.css" >
      <link rel="stylesheet" type="text/css" href="../css2/memo.css">
      </head>
  <body>
  <div id="wrap">
       <div id="header">
            <?php include "../lib/top_login2.php";?>
       </div>

       <div id="menu">
            <?php include "../lib/top_menu2.php";?>
       </div>

       <div id="content">
        	<div id="col1">
            		<div id="left_menu">
                        <?php include "../lib/left_menu.php";?>
                    </div>
            </div>
        	<div id="col2">
            		<div id="title">
                			<img src="../img/title_memo1.gif">
                    </div>
              <?php
              if(isset($_SESSION["userid"])){      //로그인 했을 때 글 쓸수 있는 권한 부여
                  ?>
                     <div id="memo_row1">
                          <form  name="memo_form" method="post" action="insert.php">
                              <input type="hidden" name="tokn" value="<?=$tokn?>">
                        	  <div id="memo_writer"><span>▷ <?=$_SESSION["nick"]?></span></div>
                        	  <div id="memo1"><textarea rows="6" cols="86" name="content" style="resize: none;" required></textarea></div>
                          	  <div id="memo2"><input type="image" src="../img/memo_button.gif"></div>
                        	</form>
                    	</div>
                  <?php
                  }
                  if ($page == 1)
                      $start_num = $total_row;    //1페이지 ,페이지당 표시되는 첫번째 글순번, 전체 글수 담아서, 게시글 수 4,3,2,1 이렇게 알맞게 표시되게
                  else
                      $start_num = $total_row - ($page - 1) * $scale; //페이지 별로 다름. ex) 2페이지 라면, 전체글 수가 19일때, 19-(2-1)*5 하면 2페이 첫 시작은 14번


                    //화면에 방명록 보이게 반복문 시작
                  while($row = $stmh->fetch(PDO::FETCH_ASSOC)){
                        $memo_id      = htmlspecialchars($row["id"]);
	                    $memo_num     = htmlspecialchars($row["num"]);
	                    $memo_date    = htmlspecialchars($row["reg_date"]);
	                    $memo_nick    = htmlspecialchars($row["nick"]);
	                    $memo_content = str_replace("\n", "<br>", $row["content"]);
	                    $memo_content = $ripple_content = str_replace(" ", "&nbsp;", $memo_content);

                  ?>
            	<div id="memo_writer_title" style="margin-top: 50px">
                	  <ul>
                           <li id="writer_title2"><?=$start_num ?></li>
                    	   <li id="writer_title2"><?=$memo_nick ?></li>
                    	   <li id="writer_title3"><?=$memo_date ?></li>
                    	   <li id="writer_title4">
                          <?php
                             if(isset($_SESSION["userid"])){
                            	  if($_SESSION["userid"]=="admin" || $_SESSION["userid"]==$memo_id)  //관리자 거나, 본인이 작성한 경우 삭제 가능
                                	  print "<a href='delete.php?num=$memo_num&tokn=$tokn'>[삭제]</a>";
                             }
                          ?>
                        	   </li>
                    	  </ul>
                	</div>
            	<div id="memo_content"><?= $memo_content ?>
             </div>


                      <?php
                      // 페이지 구분 블럭의 첫 페이지 수 계산 ($start_page)
                      $start_page = ($current_page - 1) * $page_scale + 1;   //page_scale은 한번에 보여줄 페이지 개수, start_page는 화면에 보여줄 페이지 시작번호, 즉 [이전]4,5,6[다음] 여기서 4
                      // 페이지 구분 블럭의 마지막 페이지 수 계산 ($end_page)
                      $end_page = $start_page + $page_scale - 1;
                      $start_num--;
                        }
                      ?>
                    <div id="page_button" style="margin-top: 40px">
                        <div id="page_num">
                        <?php

                              if($page!=1 && $page>$page_scale )
                              {
                                $prev_page = $page - $page_scale;
                                // 이전 페이지값은 해당 페이지 수에서 리스트에 표시될 페이지수 만큼 감소
                                if($prev_page <= 0)
                                    $prev_page = 1;  // 만약 감소한 값이 0보다 작거나 같으면 1로 고정
                                print "<a href=memo.php?page=$prev_page>◀ </a>";
                              }

                              for($i=$start_page; $i<=$end_page && $i<= $total_page; $i++)
                              {        // [1][2][3] 페이지 번호 목록 출력
                                if($page==$i) // 현재 위치한 페이지는 링크 출력을 하지 않도록 설정.
                                   print "<span style='color: red;' ><b>[$i]</b></span>";
                                else
                                   print "<a href=memo.php?page=$i>[$i]</a>";
                              }

                              if($page<$total_page)
                              {
                                $next_page = $page + $page_scale;
                                if($next_page > $total_page)
                                    $next_page = $total_page;
                                // netx_page 값이 전체 페이지수 보다 크면 맨 뒤 페이지로 이동시킴
                                print "<a href=memo.php?page=$next_page> ▶</a><p>";
                              }
                              ?>
                                </div>
                        </div>
            	 </div> <!-- end of ripple -->
              </div> <!-- end of col2 -->
        </div> <!-- end of content -->
  </div> <!-- end of wrap -->

  <p>&nbsp;</p><p>&nbsp;</p>

 </body>
 </html>
