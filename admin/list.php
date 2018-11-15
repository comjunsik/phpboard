<?php
session_start();
//CSRF취약점 방어 토큰생성 시작
$tokn = md5(uniqid(rand(), true));
$_SESSION["tokn"] = $tokn;
?>

<!DOCTYPE html>
<?php
    //db connection
    require_once ("../lib/myDB.php");
    $pdo = db_connect();

    if(isset($_REQUEST["page"])) // $_REQUEST["page"]값이 없을 때에는 1로 지정
        $page=htmlspecialchars($_REQUEST["page"]);  // 페이지 번호
    else
        $page=1;

    $scale = 5;       // 한 페이지에 보여질 게시글 수
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
    $sqls=["SELECT * FROM member WHERE id LIKE ? ORDER BY id DESC",
        "SELECT * FROM member WHERE name LIKE ? ORDER BY name DESC",
        "SELECT * FROM member WHERE nick LIKE ? ORDER BY nick DESC"];
    $sql=$sqls[$find];
    $stmh = $pdo->prepare($sql);
    $stmh->bindValue(1, '%'.$search.'%', PDO::PARAM_STR);
    $stmh->execute();
    } else {
        $sql="select * from member order by nick desc limit $first_num, $scale";  //페이징 처리, ord는 최신 글부터 보려고
        //limit를 사용해 레코드 개수를 한 페이지당 출력하는 수로 제한
        $stmh=$pdo->prepare($sql);
        $stmh->execute();
    }
    /*try{
    $sql = "select * from member";  //전체 레코드수를 파악하기 위함.
    $stmh1 = $pdo->query($sql);

    $total_row = $stmh1->rowCount();     //전체 글수
    $total_page = ceil($total_row / $scale); // 전체 페이지 블록 수, ceil 올림처리 함수
    $current_page = ceil($page / $page_scale); //현재 페이지 블록 위치계산*/
?>
<html>
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="../css/custom.css">
    <link rel="stylesheet" type="text/css" href="../css2/common.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css2/board2.css">
    <title>codingtest 회원관리</title>

</head>
<body>
<div id="wrap">
    <div id="header">
             <?php include "../lib/top_login2.php"; ?>
    </div> <!-- end of header -->
    <div id="menu">
             <?php include "../lib/top_menu2.php"; ?>
    </div> <!-- end of menu -->
    <div id="content">
        <div id="col1">
            <div id="left_menu">
                     <?php include "../lib/left_menu.php"; ?>
            </div> <!-- end of left_menu -->
        </div> <!-- end of col1 -->
<?php
try{
    $sql = "select * from member";  //전체 레코드수를 파악하기 위함.
    $stmh1 = $pdo->query($sql);

    $total_row = $stmh1->rowCount();     //전체 글수
    $total_page = ceil($total_row / $scale); // 전체 페이지 블록 수, ceil 올림처리 함수
    $current_page = ceil($page / $page_scale); //현재 페이지 블록 위치계산



}catch (PDOException $Exception){
    $pdo->rollBack();
    echo "오류 :".$Exception->getMessage();
}

if($total_row<1){
    echo "가입자가 없습니다.";
}else{

?>
        <div id="col2">
            <div id="title">회원관리</div>

            <form name="board_form" method="get" action="list.php">
                <input type="hidden" name="mode" value="search">
                <div id="list_search">


                    <div id="list_search3">
                        <select name="find">
                            <option value="0">아이디</option>
                            <option value="1">이름</option>
                            <option value="2">닉네임</option>
                        </select></div> <!-- end of list_search3 -->
                    <div id="list_search4"><input type="text" name="search"></div>
                    <div id="list_search5"><input type="image" src="../img/list_search_button.gif"></div>
                </div> <!-- end of list_search -->
            </form>

            <table class="table  table-hover" style="text-align: center; border: 1px solid #b3d7ff">
                <thead>
                <tr>
                    <th style="background-color:#eeeeee; text-align: center;">아이디</th>
                    <th style="background-color:#eeeeee; text-align: center;">이름</th>
                    <th style="background-color:#eeeeee; text-align: center;">닉네임</th>
                    <th style="background-color:#eeeeee; text-align: center;">가입시간</th>
                    <th style="background-color:#eeeeee; text-align: center;">수정</th>
                    <th style="background-color:#eeeeee; text-align: center;">삭제</th>
                </tr>
                <?php
                if($page==1)
                    $start_num = $total_row;    //1페이지 ,페이지당 표시되는 첫번째 글순번, 전체 글수 담아서, 게시글 수 4,3,2,1 이렇게 알맞게 표시되게
                else
                    $start_num = $total_row - ($page - 1) * $scale; //페이지 별로 다름. ex) 2페이지 라면, 전체글 수가 19일때, 19-(2-1)*5 하면 2페이 첫 시작은 14번

                while ($row=$stmh->fetch(PDO::FETCH_ASSOC)){
                $item_date = $row["reg_date"];
                $item_date = substr($item_date, 0, 10);   //작성 일자 년-월-일 만 보여주기 위해
                ?>
                </thead>
                <tbody>
                <tr>
                    <?php
                    if ($row['id'] != "admin"){


                    ?>
                    <td name="id"><?= $row['id'] ?></td>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['nick'] ?></td>
                    <td><?= $item_date ?></td>
                    <td><a href="updateForm.php?id=<?= $row['id']?>" class="btn btn-outline-success my-2 my-sm-0"
                           role="button">수정</a></td>
                    <td><a href="delete.php?id=<?= $row['id']?>&tokn=<?=$tokn?>" class="btn btn-outline-danger my-2 my-sm-0"
                           role="button">삭제</a></td>
                </tr>
                <?php }
                    }
                } ?>
                </tbody>
            </table>
        <div align="center">
            <?php
            // 페이지 구분 블럭의 첫 페이지 수 계산 ($start_page)
            $start_page = ($current_page - 1) * $page_scale + 1;   //page_scale은 한번에 보여줄 페이지 개수, start_page는 화면에 보여줄 페이지 시작번호, 즉 [이전]4,5,6[다음] 여기서 4
            // 페이지 구분 블럭의 마지막 페이지 수 계산 ($end_page)
            $end_page = $start_page + $page_scale - 1;
            if($page!=1 && $page>$page_scale)
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

            ?>
         </div>

        </div> <!-- end of col2 -->

    </div> <!-- end of content -->


    </div><!-- end of wrap-->
    <!-- 가장 아래쪽에 들어가는 내용 -->
    <footer class="bg-dark mt-4 p-3 text-center fixed-bottom" style="color: #FFFFFF;">
        <!-- 저작권 표시 -->
        Copyright &copy; 2018 CodingTest 원준식 All Rights Reserved.
    </footer>
</body>
</html>