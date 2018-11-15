
<?php
session_start();
//CSRF취약점 방어 토큰생성 시작
$tokn = md5(uniqid(rand(), true));
$_SESSION["tokn"] = $tokn;
?>
<!DOCTYPE html>
<html>
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="http://54.180.29.9/css2/common.css">
    <link rel="stylesheet" href="http://54.180.29.9/css2/member.css">
    <link rel="stylesheet" href="http://54.180.29.9/css/bootstrap.min.css">
    <link rel="stylesheet" href="http://54.180.29.9/css/custom.css">
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="http://54.180.29.9/phpbbs/codingtest/js/bootstrap.min.js"></script>


    <title>codingtest 회원가입 폼</title>
    <script type="text/javascript">

        //id 체크하는 부분
        function check_id()
        {

            var userID = $('#userID').val();
            $.ajax({
                type: 'POST',
                url : './check_id.php',
                data: {userID: userID},
                success: function(result){
                    if(result ==2){
                        $('#checkMessage').html('사용할 수 없는 아이디입니다.');
                        $('#checkType').attr('class', 'modal-content panel-success');
                        //alert(result);
                    }
                    else if(result==1){
                        $('#checkMessage').html('사용 가능한 아이디입니다.');
                        $('#checkType').attr('class', 'modal-content panel-warning');
                        //alert(result);
                    }else if(result==3){
                        $('#checkMessage').html('아이디를 입력해 주세요.');
                        $('#checkType').attr('class', 'modal-content panel-warning');

                    }
                    $('#checkModal').modal("show");

                }
            });


        }

        //닉네임 체크
        function check_nick()
        {
            var userNick = $('#userNick').val();
            $.ajax({
                type: 'POST',
                url : './check_nick.php',
                data: {userNick: userNick},
                success: function(result){
                    if(result ==2){
                        $('#checkMessage').html('사용할 수 없는 닉네임입니다.');
                        $('#checkType').attr('class', 'modal-content panel-success');
                        //alert(result);
                    }
                    else if(result==1){
                        $('#checkMessage').html('사용 가능한 닉네임입니다.');
                        $('#checkType').attr('class', 'modal-content panel-warning');
                        //alert(result);
                    }else if(result==3){
                        $('#checkMessage').html('닉네임을 입력해 주세요.');
                        $('#checkType').attr('class', 'modal-content panel-warning');

                    }
                    $('#checkModal').modal("show");
                }
            });

        }


        //비밀번호 체크
        function passwordCheckFunction(){
            console.log(';;;');
            var userPassword1 = $('#userPassword1').val();
            var userPassword2 = $('#userPassword2').val();
            if(userPassword1 != userPassword2){
                $('#passwordCheckMessage').html('비밀번호가 서로 일치하지 않습니다.');
            }else{
                $('#passwordCheckMessage').html('');
            }
        }


    </script>
</head>
<?php
    $id = htmlspecialchars($_REQUEST["id"]);

    require_once ("../lib/myDB.php");
    $pdo = db_connect();

    try{
        $sql = "SELECT * FROM member WHERE id =?";
        $stmh = $pdo->prepare($sql);
        $stmh->bindValue(1, $id, PDO::PARAM_STR);
        $stmh->execute();
        $count = $stmh->rowCount();
        }catch (PDOException $exception){
            echo "오류: ".$exception->getMessage();
        }
     if($count<1){
         echo "검색결과과 없습니다.<br>";
     }else{
         $row=$stmh->fetch(PDO::FETCH_ASSOC);
     }
?>
<body>
<div id="wrap">
    <div id="header">
        <?php include "../lib/top_login2.php"?>
    </div> <!-- end of header -->

    <div id="menu">
        <?php include "../lib/top_menu2.php"?>
    </div> <!-- end of menu -->

    <div id="content">
        <div id="col1">
            <div id="left_menu">
            <?php include "../lib/left_menu.php"?>
            </div>
        </div> <!-- end of col1 -->
        <div id="col2">
            <br><br>
            <div class="container">
                <form method="post" action="./updatePro.php">
                    <input type="hidden" name="tokn" value="<?=$tokn?>">
                    <table class="table table-bordered table-hover" style="text-align: center; border: 1px solid #dddddd">
                        <thead>
                        <tr>
                            <th colspan="3"><h4>회원 정보 수정</h4></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td style="width: 110px;"><h5>아이디</h5></td>
                            <td colspan="2"><input class="form-control" type="text" id="userID" name="userID" maxLength="20" placeholder="아이디를 입력해주세요." value="<?=$row['id']?>" required readonly="readonly"></td>
                            <input type="hidden" name="userID" id="userID" value="<?=$row['id']?>">

                        </tr>
                        <tr>
                            <td style="width: 110px;"><h5>비밀번호</h5></td>
                            <td colspan="2"><input class="form-control" type="password" onkeyup="passwordCheckFunction();" id="userPassword1" name="userPassword1" maxLength="20" placeholder="비밀번호를 입력해주세요." value="<?=$row['passwd']?>" required></td>
                        </tr>
                        <tr>
                            <td style="width: 110px;"><h5>비밀번호 확인</h5></td>
                            <td colspan="2"><input class="form-control" type="password" onkeyup="passwordCheckFunction();" id="userPassword2" name="userPassword2" maxLength="20" placeholder="비밀번호 확인을 입력해주세요." value="<?=$row['passwd']?>" required></td>
                        </tr>
                        <tr>
                            <td style="width: 110px;"><h5>이름</h5></td>
                            <td colspan="2"><input class="form-control" type="text" id="userName" name="userName" maxLength="20" placeholder="이름을 입력해주세요." value="<?=$row['name']?>" required></td>
                        </tr>
                        <tr>
                            <td style="width: 110px;"><h5>닉네임</h5></td>
                            <td><input class="form-control" type="text" id="userNick" name="userNick" maxLength="20" placeholder="닉네임을 입력해주세요." value="<?=$row['nick']?>" required></td>
                            <td style="width: 110px;"><button class="btn btn-primary" onclick="check_nick();" type="button">중복체크</button></td>
                        </tr>

                        <tr>
                            <td style="text-align: left" colspan="3"><h5 style="color: red;" id="passwordCheckMessage"></h5><input class="btn btn-primary float-right" type="submit" value="수정하기"></td>
                        </tr>

                        </tbody>
                    </table>
                </form>
            </div>
        </div> <!-- end of col2 -->
    </div> <!-- end of content -->
</div> <!-- end of wrap -->



<!-- 가장 아래쪽에 들어가는 내용 -->
<footer class="bg-dark mt-4 p-3 text-center fixed-bottom" style="color: #FFFFFF;">
    <!-- 저작권 표시 -->
    Copyright &copy; 2018 CodingTest 원준식 All Rights Reserved.
</footer>
<div class="modal fade" id="checkModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog vertical-align-center">
            <div id="checkType" class="modal-content panel-info">
                <div class="modal-header panel-heading">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true"></span>
                        <span class="sr-only">Close</span>
                    </button>
                    <h4 class="modal-title">
                        확인 메시지
                    </h4>
                </div>
                <div class="modal-body" id="checkMessage">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">확인</button>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>