<?php session_start(); ?>
<?php
	//CSRF취약점 방어 토큰확인 시작
    $tokn = htmlspecialchars($_POST['tokn']);
    if(isset($_SESSION["tokn"]) && $tokn == $_SESSION["tokn"]) {
    } else {
        die("비정상적인 접근입니다.");
    }
	//CSRF취약점 방어 토큰확인 끝
?>
 <meta charset="utf-8">
 <?php
   if(!isset($_SESSION["userid"])) {
 ?>
    <script>
         alert('로그인 후 이용해 주세요.');
	 history.back();
     </script>
 <?php }
  if(isset($_REQUEST["page"]))
    $page=htmlspecialchars($_REQUEST["page"]);
  else
    $page=1;
 if(isset($_REQUEST["mode"]))  // 입력방식 구분
    $mode=htmlspecialchars($_REQUEST["mode"]);
 else
    $mode="";

 if(isset($_REQUEST["num"]))
    $num=htmlspecialchars($_REQUEST["num"]);
 else
    $num="";




 $subject=htmlspecialchars($_REQUEST["subject"]);   //게시글 제목
 $content=htmlspecialchars($_REQUEST["content"]);   //게시글 내용

 $files = $_FILES["upfile"];    //첨부파일 $_FILESE super global 변수 첨부된 파일자체를 받음
 $count = count($files["name"]);   //업로드 되어 지는 실제 파일명 의 개수
 $upload_dir = '../data/';   //물리적 저장위치

 for ($i=0; $i<$count; $i++)
 {
     $upfile_name[$i]     = basename($files["name"][$i]);
     $upfile_tmp_name[$i] = $files["tmp_name"][$i];  //서버에 저장되는 임시 파일명
     $upfile_type[$i]     = $files["type"][$i];  //업로드 파일 형식
     $upfile_size[$i]     = $files["size"][$i];  //업로드 파일 크기
     $upfile_error[$i]    = $files["error"][$i]; //에러 발생 확인
     $file = explode(".", $upfile_name[$i]);   //파일 확장자 gif, jpg 때기
     $file_name = $file[0];
     $file_ext  = $file[1];


     //확장자 체크하여 업로드 못하게할 파일 정하기
     $pattern = '/php|inc|html|exe|sh|bat/';
     if(@preg_match($pattern, $file_ext)){
         echo "<script>
        alert('업로드할 수 없는 형식의 파일입니다.');
        history.back();
        </script>";
         exit();
     }

     if (!$upfile_error[$i])
     {
         $new_file_name = date("Y_m_d_H_i_s");  //년-월-일-시-분-초 로 서버에 저장되는 임시 파일명을 바꿔준다 , 이름이 같게 된다면 덮어 쓰게 되서.
         $new_file_name = $new_file_name."_".$i;         //,$i 총 3개 까지 첨부 가능한데 몇번째 첨부 파일인지
         $copied_file_name[$i] = $new_file_name.".".$file_ext;   //파일 확장자 붙여주기
         $uploaded_file[$i] = $upload_dir.$copied_file_name[$i];  //..data/파일이름.확장자

         if( $upfile_size[$i]  > 110000000 ) {         //업로드 파일 용량 110mb 로 제한

             print("
                    <script>
                    alert('업로드 파일 크기가 지정된 용량(5MB)을 초과합니다!<br>파일 크기를 체크해주세요! ');
                    history.back();
                    </script>"
             );
             exit;
         }
         if (!move_uploaded_file($upfile_tmp_name[$i], $uploaded_file[$i]) )
         {
             print("<script>
                    alert('파일을 지정한 디렉토리에 복사하는데 실패했습니다.');
                    history.back();
                    </script>");
             exit;
         }
     }
 }

 require_once("../lib/myDB.php");
 $pdo = db_connect();

 if ($mode=="modify"){    //수정 시
     $num_checked = count($_REQUEST['del_file']);  //수정 시 삭제할 파일 개수
     $position =$_REQUEST['del_file'];

     for($i=0; $i<$num_checked; $i++)      // delete checked item
     {
         $index = $position[$i];
         $del_ok[$index] = "y";
     }
     try{
         $sql = "select * from qna where num=?";  // get target record
         $stmh = $pdo->prepare($sql);
         $stmh->bindValue(1,$num,PDO::PARAM_STR);
         $stmh->execute();
         $row = $stmh->fetch(PDO::FETCH_ASSOC);
     } catch (PDOException $Exception) {
         $pdo->rollBack();
         print "오류: ".$Exception->getMessage();
     }

     //업로드 파일 수정 or 삭제 체크 했을 경우
     for ($i=0; $i<$count; $i++)
     {
         $field_org_name = "file_name_".$i;
         $field_real_name = "file_copied_".$i;
         $org_name_value = $upfile_name[$i];
         $org_real_value = $copied_file_name[$i];
         if ($del_ok[$i] == "y")                            //첨부 파일 삭제 박스 클릭했다면
         {
             $delete_field = "file_copied_".$i;
             $delete_name = $row[$delete_field];

             $delete_path = $upload_dir.$delete_name;
             unlink($delete_path);               //파일 삭제

             try{
                 $pdo->beginTransaction();
                 $sql = "update qna set $field_org_name = ?, $field_real_name = ?  where num=?";
                 $stmh = $pdo->prepare($sql);
                 $stmh->bindValue(1, $org_name_value, PDO::PARAM_STR);
                 $stmh->bindValue(2, $org_real_value, PDO::PARAM_STR);
                 $stmh->bindValue(3, $num, PDO::PARAM_STR);
                 $stmh->execute();
                 $pdo->commit();
             } catch (PDOException $Exception) {
                 $pdo->rollBack();
                 print "오류: ".$Exception->getMessage();
             }
         }  else  {                    //첨부 파일 삭제 클릭 안했을 때
             if (!$upfile_error[$i])
             {
                 try{
                     $pdo->beginTransaction();
                     $sql = "update qna set $field_org_name = ?, $field_real_name = ?  where num=?";
                     $stmh = $pdo->prepare($sql);
                     $stmh->bindValue(1, $org_name_value, PDO::PARAM_STR);
                     $stmh->bindValue(2, $org_real_value, PDO::PARAM_STR);
                     $stmh->bindValue(3, $num, PDO::PARAM_STR);
                     $stmh->execute();
                     $pdo->commit();
                 } catch (PDOException $Exception) {
                     $pdo->rollBack();
                     print "오류: ".$Exception->getMessage();
                 }
             }
         }
     }


     try{
        $pdo->beginTransaction();
        $sql = "update qna set subject=?, content=? where num=?";
        $stmh = $pdo->prepare($sql);
        $stmh->bindValue(1, $subject, PDO::PARAM_STR);
        $stmh->bindValue(2, $content, PDO::PARAM_STR);
        //$stmh->bindValue(3, $html_ok, PDO::PARAM_STR);
        $stmh->bindValue(3, $num, PDO::PARAM_STR);
        $stmh->execute();
        $pdo->commit();

        header("Location:http://54.180.29.9/qna/list.php?page=$page");
        } catch (PDOException $Exception) {
            $pdo->rollBack();
            print "오류: ".$Exception->getMessage();
        }

 } else	{               // mode가 modify가 아닌 신규or 답변 작성시

     $content = htmlspecialchars($content);

        if ($mode=="response")         //답변 작성시
        {
            try{
            $sql = "select * from qna where num = $num"; // 부모 글 가져오기
            $stmh = $pdo->prepare($sql);
            $stmh->bindValue(1, $num, PDO::PARAM_STR);
            $stmh->execute();

            $row = $stmh->fetch(PDO::FETCH_ASSOC);
            $group_num = $row["group_num"];      // group_num, depth, ord 설정
            $depth = $row["depth"] + 1;
            $ord = $row["ord"] + 1;
            // ord 가 부모글의 ord($row[ord]) 보다 큰 경우엔 ord 값 1 증가 시킴
            $pdo->beginTransaction();
            $sql = "update qna set ord = ord + 1 where group_num = ? and ord > ?";
            $stmh = $pdo->prepare($sql);
            $stmh->bindValue(1, $row["group_num"], PDO::PARAM_STR);
            $stmh->bindValue(2, $row["ord"], PDO::PARAM_STR);
            $stmh->execute();
            $pdo->commit();

            $pdo->beginTransaction();
            $sql = "insert into qna(group_num, depth, ord, id, name, nick, subject,";
            $sql .= "content, reg_date, hit,";
            $sql .= "file_name_0, file_name_1, file_name_2, file_type_0,  file_type_1, file_type_2, ";
            $sql .=  "file_copied_0, file_copied_1, file_copied_2, parent) ";
            $sql .= "values(?, ?, ?, ?, ?, ?, ?, ?, now(), 0, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmh = $pdo->prepare($sql);
            $stmh->bindValue(1, $group_num, PDO::PARAM_STR);
            $stmh->bindValue(2, $depth, PDO::PARAM_STR);
            $stmh->bindValue(3, $ord, PDO::PARAM_STR);
            $stmh->bindValue(4, htmlspecialchars($_SESSION["userid"]), PDO::PARAM_STR);
            $stmh->bindValue(5, htmlspecialchars($_SESSION["name"]), PDO::PARAM_STR);
            $stmh->bindValue(6, htmlspecialchars($_SESSION["nick"]), PDO::PARAM_STR);
            $stmh->bindValue(7, $subject, PDO::PARAM_STR);
            $stmh->bindValue(8, $content, PDO::PARAM_STR);
            $stmh->bindValue(9, $upfile_name[0], PDO::PARAM_STR);
            $stmh->bindValue(10, $upfile_name[1], PDO::PARAM_STR);
            $stmh->bindValue(11, $upfile_name[2], PDO::PARAM_STR);
            $stmh->bindValue(12, $upfile_type[0], PDO::PARAM_STR);
            $stmh->bindValue(13, $upfile_type[1], PDO::PARAM_STR);
            $stmh->bindValue(14, $upfile_type[2], PDO::PARAM_STR);
            $stmh->bindValue(15, $copied_file_name[0], PDO::PARAM_STR);
            $stmh->bindValue(16, $copied_file_name[1], PDO::PARAM_STR);
            $stmh->bindValue(17, $copied_file_name[2], PDO::PARAM_STR);
            $stmh->bindValue(18, $num, PDO::PARAM_STR);
            $stmh->execute();


            //알림 메시지 등록 시작
            $tgt_id = $row["id"];
            $message = "게시물에 답글이 달렸습니다.";
            $tgt_board = "qna";
            $tgt_page = $page;
            $tgt_num = $pdo->lastInsertId();
                    $pdo->commit();
            $pdo->beginTransaction();
            $sql = "insert into notify(id, message, tgt_board, tgt_num, reg_date, page)";
            $sql.= "values(?, ?, ?, ?,now() ,?)";
            $stmh = $pdo->prepare($sql);
            $stmh->bindValue(1, $tgt_id, PDO::PARAM_STR);
            $stmh->bindValue(2, $message, PDO::PARAM_STR);
            $stmh->bindValue(3, $tgt_board, PDO::PARAM_STR);
            $stmh->bindValue(4, $tgt_num, PDO::PARAM_STR);
            $stmh->bindValue(5, $tgt_page, PDO::PARAM_STR);
            $stmh->execute();
            $pdo->commit();

            header("Location:http://54.180.29.9/qna/list.php?page=$page");
            } catch (PDOException $Exception) {
                $pdo->rollBack();
                print "오류: ".$Exception->getMessage();
            }

        }else {          //mode 가 신규 작성 시
            $depth = 0;   // depth, ord 를 0으로 초기화
            $ord = 0;

            try{
                $pdo->beginTransaction();    // 레코드 삽입(group_num 제외)
                $sql = "insert into qna(depth,ord,id,name,nick,subject,content,reg_date,hit, ";
                $sql .= "file_name_0, file_name_1, file_name_2, file_type_0,  file_type_1, file_type_2, ";
                $sql .=  "file_copied_0, file_copied_1, file_copied_2) ";
                $sql .= "values(?, ?, ?, ?, ?, ?, ?,now(), 0, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmh = $pdo->prepare($sql);
                $stmh->bindValue(1, $depth, PDO::PARAM_STR);
                $stmh->bindValue(2, $ord, PDO::PARAM_STR);
                $stmh->bindValue(3, $_SESSION["userid"], PDO::PARAM_STR);
                $stmh->bindValue(4, $_SESSION["name"], PDO::PARAM_STR);
                $stmh->bindValue(5, $_SESSION["nick"], PDO::PARAM_STR);
                $stmh->bindValue(6, $subject, PDO::PARAM_STR);
                $stmh->bindValue(7, $content, PDO::PARAM_STR);
                $stmh->bindValue(8, $upfile_name[0], PDO::PARAM_STR);
                $stmh->bindValue(9, $upfile_name[1], PDO::PARAM_STR);
                $stmh->bindValue(10, $upfile_name[2], PDO::PARAM_STR);
                $stmh->bindValue(11, $upfile_type[0], PDO::PARAM_STR);
                $stmh->bindValue(12, $upfile_type[1], PDO::PARAM_STR);
                $stmh->bindValue(13, $upfile_type[2], PDO::PARAM_STR);
                $stmh->bindValue(14, $copied_file_name[0], PDO::PARAM_STR);
                $stmh->bindValue(15, $copied_file_name[1], PDO::PARAM_STR);
                $stmh->bindValue(16, $copied_file_name[2], PDO::PARAM_STR);
                $stmh->execute();
                $lastId = $pdo->lastInsertId();   //마지막에 추가된 레코드의 id 값 받아오기. Auto_Increment num필드값
                $pdo->commit();

                //group이랑 num값 수정 부분  여기서 group_num 값을 집어 넣어줌
                $pdo->beginTransaction();
                $sql = "update qna set group_num = ? where num=?";
                $stmh1 = $pdo->prepare($sql);
                $stmh1->bindValue(1, $lastId, PDO::PARAM_STR);
                $stmh1->bindValue(2, $lastId, PDO::PARAM_STR);
                $stmh1->execute();
                $pdo->commit();

                header("Location:http://54.180.29.9/qna/list.php?page=$page");
                } catch (PDOException $Exception) {
                    $pdo->rollBack();
                    print "오류: ".$Exception->getMessage();
                }
            }
       }
  ?>



