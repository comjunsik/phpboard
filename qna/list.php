 <?php
 session_start();
 ?>
 <!DOCTYPE HTML>
 <html>
 <head>
     <meta charset="UTF-8">
     <link rel="stylesheet" type="text/css" href="../css2/common.css">
     <link rel="stylesheet" type="text/css" href="../css2/board2.css">
     <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>


 </head>

 <?php
  require_once("../lib/myDB.php");
  $pdo = db_connect();

  if(isset($_REQUEST["page"])) // $_REQUEST["page"]값이 없을 때에는 1로 지정
        $page=htmlspecialchars($_REQUEST["page"]);  // 페이지 번호
  else
    $page=1;

  $scale = 10;       // 한 페이지에 보여질 게시글 수
  $page_scale = 3;  // 한 페이지당 표시될 페이지 수
  $first_num = ($page-1) * $scale; // 리스트에 표시되는 게시글의 첫 순번., jsp 할때 sql 문 limit에 들어갈 것

 if(isset($_REQUEST["mode"]))
        $mode=htmlspecialchars($_REQUEST["mode"]);
 else
    $mode="";

 if(isset($_REQUEST["search"]))
        $search=htmlspecialchars($_REQUEST["search"]);
 else
    $search="";

 if(isset($_REQUEST["find"]))
        $find=htmlspecialchars($_REQUEST["find"]);
 else
    $find="";

 if($mode=="search"){
  if(!$search){
 ?>
   <script>
         alert('검색할 단어를 입력해 주세요!');
         history.back();
       </script>
 <?php
     }
        //sql 인젝션 방지
        $sqls=["SELECT * FROM qna WHERE subject LIKE ? ORDER BY num DESC",
            "SELECT * FROM qna WHERE content LIKE ? ORDER BY num DESC",
            "SELECT * FROM qna WHERE nick LIKE ? ORDER BY num DESC",
            "SELECT * FROM qna WHERE name LIKE ? ORDER BY num DESC"];
        $sql=$sqls[$find];
        $stmh = $pdo->prepare($sql);
        $stmh->bindValue(1, '%'.$search.'%', PDO::PARAM_STR);
        $stmh->execute();
        $search_total_row=$stmh->rowCount();  //검색결과 게시글 개수
        $search2="t";
  } else {
     $sql="select * from qna order by group_num desc, ord asc limit $first_num, $scale";  //페이징 처리, ord는 최신 글부터 보려고
        //limit를 사용해 레코드 개수를 한 페이지당 출력하는 수로 제한
     $stmh=$pdo->prepare($sql);
     $stmh->execute();
  }
 try{
 $sql = "select * from qna";  //전체 레코드수를 파악하기 위함.
 $stmh1 = $pdo->query($sql);

 $total_row = $stmh1->rowCount();     //전체 글수
 $total_page = ceil($total_row / $scale); // 전체 페이지 블록 수, ceil 올림처리 함수
 $current_page = ceil($page / $page_scale); //현재 페이지 블록 위치계산

 
 ?>
 <body>
 <div id="wrap">
     <div id="header">
             <?php include "../lib/top_login2.php"; ?>
          </div> <!-- end of header -->
     <div id="menu">
             <?php include "../lib/top_menu2.php"; ?>
          </div> <!-- end of menu -->
     <div id="content" style="width: 1200px;">
         <div id="col1">
             <div id="left_menu">
                     <?php include "../lib/left_menu.php"; ?>
                       </div> <!-- end of left_menu -->
         </div> <!-- end of col1 -->

         <div id="col2" style="width: 870px;">
             <div id="title"><img src="../img/title_free.gif"></div>
             <form name="board_form" method="get" action="list.php">
                 <input type="hidden" name="mode" value="search">
                 <div id="list_search">
                     <div id="list_search1">
                    <?php
                    if($search2!="t"){ ?>
                        ▷ 총 <?= $total_row ?>개의 게시물이 있습니다.
                    <?php }else{ ?>
                        ▷ 검색결과 총 <?= $search_total_row ?>개의 게시물이 있습니다.
                     <?php } ?>
                     </div>
                     <div id="list_search2"><img src="../img/select_search.gif"></div>
                     <div id="list_search3">
                         <select name="find">
                             <option value="0">제목</option>
                             <option value="1">내용</option>
                             <option value="2">닉네임</option>
                             <option value="3">이름</option>
                         </select></div> <!-- end of list_search3 -->
                     <div id="list_search4"><input type="text" name="search"></div>
                     <div id="list_search5"><input type="image" src="../img/list_search_button.gif"></div>
                 </div> <!-- end of list_search -->
             </form>
             <div class="clear"></div>
             <div id="list_top_title">
                 <ul>
                     <li id="list_title1"><img src="../img/list_title1.gif"></li>
                     <li id="list_title2"><img src="../img/list_title2.gif"></li>
                     <li id="list_title3"><img src="../img/list_title3.gif"></li>
                     <li id="list_title4"><img src="../img/list_title4.gif"></li>
                     <li id="list_title5"><img src="../img/list_title5.gif"></li>
                 </ul>
             </div> <!-- end of list_top_title -->
             <div id="list_content" style="overflow:auto; height: 500px">
                 <?php // 글 목록 출력
                 if ($page == 1)
                     $start_num = $total_row;    //1페이지 ,페이지당 표시되는 첫번째 글순번, 전체 글수 담아서, 게시글 수 4,3,2,1 이렇게 알맞게 표시되게
                 else
                     $start_num = $total_row - ($page - 1) * $scale; //페이지 별로 다름. ex) 2페이지 라면, 전체글 수가 19일때, 19-(2-1)*5 하면 2페이 첫 시작은 14번

                 while ($row = $stmh->fetch(PDO::FETCH_ASSOC)) {

                     $item_num = $row["num"];
                     $item_id = $row["id"];
                     $item_name = $row["name"];
                     $item_nick = $row["nick"];
                     $item_hit = $row["hit"];
                     $item_date = $row["reg_date"];
                     $item_date = substr($item_date, 0, 10);   //작성 일자 년-월-일 만 보여주기 위해
                     $item_subject = str_replace(" ", "&nbsp;", $row["subject"]);
                     $item_depth = $row["depth"];                     //답변이 몇 단계 인지, 답변의 답변인지, 답변의 답변의 답변인지..
                     $space = "";         //들여 쓰기
                     for ($j = 0; $j < $item_depth; $j++)
                         $space = "&nbsp;&nbsp;" . $space;


                     $sql = "SELECT * FROM qna_ripple WHERE parent=$item_num";    //댓글수, parent 부모 게시글(댓글단 게시글) 번호를 가지고 있는 레코드들을 가져와라
                     $stmh2= $pdo->query($sql);
                     $num_ripple=$stmh2->rowCount();

                     ?>
                     <div id="list_item">
                         <div id="list_item1"><?= $start_num ?></div>    <!-- 글 목록 번호, $start_num은 db에 있는 레코드 개수, -->
                         <div id="list_item2"><?= $space ?><a href="view.php?num=<?= $item_num ?>&page=<?= $page ?>"><?= $item_subject ?></a>
                             <?php
                             //댓글 수 표시하기 위해
                             if($num_ripple){
                                print "[<span style='color:red;'><b>$num_ripple</b></span>]";
                             }
                             ?>
                         </div>
                         <div id="list_item3"><?= $item_nick ?></div>
                         <div id="list_item4"><?= $item_date ?></div>
                         <div id="list_item5"><?= $item_hit ?></div>
                     </div>  <!-- end of list_item-->

                     <?php
                     $start_num--;              //글목록 갯수대로 표시하기 위해(글 목록 번호 1씩 감소), 글 3개면 글 목록 번호 3,2,1 이렇게 표시해주기 위해
                 }
                 } catch (PDOException $Exception){
                    print "오류 :".$Exception->getMessage();
                 }

                 // 페이지 구분 블럭의 첫 페이지 수 계산 ($start_page)
                 $start_page = ($current_page - 1) * $page_scale + 1;   //page_scale은 한번에 보여줄 페이지 개수, start_page는 화면에 보여줄 페이지 시작번호, 즉 [이전]4,5,6[다음] 여기서 4
                 // 페이지 구분 블럭의 마지막 페이지 수 계산 ($end_page)
                 $end_page = $start_page + $page_scale - 1;

                 ?>
                 <div id="page_button">
                     <div id="page_num">
                <?php
                if($search2!="t"){
                      if($page!=1 && $page>$page_scale )
                      {
                        $prev_page = $page - $page_scale;
                        // 이전 페이지값은 해당 페이지 수에서 리스트에 표시될 페이지수 만큼 감소
                        if($prev_page <= 0)
                            $prev_page = 1;  // 만약 감소한 값이 0보다 작거나 같으면 1로 고정
                        print "<a href=list.php?page=$prev_page>◀ </a>";
                      }

                      for($i=$start_page; $i<=$end_page && $i<= $total_page; $i++)
                      {        // [1][2][3] 페이지 번호 목록 출력
                        if($page==$i) // 현재 위치한 페이지는 링크 출력을 하지 않도록 설정.
                           print "<span style='color: red;' ><b>[$i]</b></span>";
                        else
                           print "<a href=list.php?page=$i>[$i]</a>";
                      }

                      if($page<$total_page)
                      {
                        $next_page = $page + $page_scale;
                        if($next_page > $total_page)
                            $next_page = $total_page;
                        // netx_page 값이 전체 페이지수 보다 크면 맨 뒤 페이지로 이동시킴
                        print "<a href=list.php?page=$next_page> ▶</a><p>";
                      }
                }
                 ?>
                    </div>
                 </div>
                
                  <div id="write_button">
                    <a href="list.php"><img src="../img/list.png"></a>&nbsp;
                    <?php
                    if(isset($_SESSION["userid"]))
                    {
                        ?>
                        <a href="write_form.php"><img src="../img/write.png"></a>
                        <?php
                    }
                    ?>
                </div>
                    </div>
               </div> <!-- end of col2 -->
          </div> <!-- end of content -->
     </div> <!-- end of wrap -->
 </body>
 </html>
