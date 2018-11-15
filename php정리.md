# PHP

[ob_start()](#ob_start())
<br>
[Prepare Statement SELECT](#Prepare_Statement_SELECT_Query)<br>
[md5()](#md5())<br>
[쿠키설정](#쿠키설정)<br>
[PDO](#PDO)<br>
[require_once()](#require_once())


# ob_start()
출력 버퍼링은 출력물이 php에 파싱되어 echo나 print에 의해 브라우저로 출력되는 방법이 일반적인데, 필요에 의해 출력 결과물을 바로 브라우저로 보내지 않고, 내용물을 잠깐 동안 버퍼에 보관해 두었다가 출력이 필요할 곳에 이를 사용할 수 있습니다.

이를 사용하기 위애서는 ob_start()함수를 호출하고, ob_end_flush로 버퍼링을 비워주면 되는데, 여기서 **비운다는 의미**는 <u>버퍼에 저장되어 있는 내용을 브라우저로 출력하고, 버퍼를 비운다는 뜻</u>입니다.

예제들 : http://blog.habonyphp.com/entry/php-%EC%B6%9C%EB%A0%A5-%EB%B2%84%ED%8D%BC-flush-%ED%95%A8%EC%88%98#.W94hRJMzaUk

# Prepare_Statement_SELECT_Query
```php
$stmt=$connect->prepare("SELECT userID, userPw FROM user WHERE userID=?");
//$stmt->set_charset("utf8");
$stmt->bind_param('s', $userID);
$stmt->execute();
//$stmt->bind_result($ruserID, $ruserPw);
$result=$stmt->get_result();
//$result=mysqli_query($query, $connect);
//$member=mysqli_fetch_array($result);

while ($date=$result->fetch_assoc()){
    echo $date[userID];
}
```
Prepare Statement를 사용하면 대부분의 SQL 인젝션 공격을 막을 수 있다.

```php
    $result->fetch_assoc()
```
![image](https://user-images.githubusercontent.com/41488792/47989765-270ef300-e129-11e8-9a4c-682535159528.png)

사용법: https://m.blog.naver.com/PostView.nhn?blogId=diceworld&logNo=220295777271&proxyReferer=https%3A%2F%2Fwww.google.co.kr%2F

# md5()
이 함수는 문자열에서 md5 해시값을 생성해 준다.

**문자열 암호화** 에 사용한다.

구조 | _
---- | ----
string md5 ( string $str [, bool $raw_output ] ) | 

string $str : 입력 문자열

bool $raw_output
:이 값이 true 일 경우 해시의 길이 16바이너리 형식으로 반환, 이값을 생략하면 **기본값은 false** 를 갖는다.

# 쿠키설정

**setcookie()**<br>
```php
if($member[userID] and $member[userPw]==$pw){
    $tmp=$member[userID]."//".$member[userPw];
    setcookie("COOKIES",$tmp,time()+60*60*24,"/" );  //24시간동안 유효
}
```
$tmp=$member[userID]."//".$member[userPw];

//로 아이디와 비밀번호를 구분하여 쿠키에 저장.

![image](https://user-images.githubusercontent.com/41488792/47991068-6f7be000-e12c-11e8-9704-440bfed5962a.png)
![image](https://user-images.githubusercontent.com/41488792/47991103-891d2780-e12c-11e8-957d-91856315c038.png)

**$_COOKIE['쿠키명']**<br>
쿠키값을 새롭게 업데이트하거나 읽어올 경우

**쿠키값 삭제 또는 만료하기**<br>
1.  setcookie() 함수를 사용하되 해당 쿠키명의 시간에 마이너스값을 입력
2. unset() 함수를 사용

쿠키를 삭제 및 중단 되도록 바꿉니다.

```php
setcookie("COOKIES","",0,"/"); //쿠키 지우기
```
쿠키이름, 쿠키값 비워주고, 쿠키 유지시간 0으로, 장경로 / ('/' 만 넣어주면 알아서 client에 저장해줌.)

출처: https://webisfree.com/2015-03-02/[php]-%EC%BF%A0%ED%82%A4-%EC%84%A4%EC%A0%95%ED%95%98%EA%B8%B0-setcookie()

# PDO
**PDO 란?**
1) 문제점 : PHP에서 사용되는 데이터베이스 extension(oci, mysql, postgresql, mssql 등)간의 일관성이 심각하게
   결여된 상태
2) 문제점의 결과 : 이러한 문제로 유지보수가 어렵고 핵심 PHP 개발자 인력이 극히 제한된 현실. 
3) 문제 해결 : 따라서 2003년 독일에서 열린 LinuxTag 컨퍼런스 행사에서 PHP 데이터베이스 extension 관리 담당자들이
   한자리에 모여 PHP의 데이터베이스 액세스에 관련한 몇 가지 목표를 설정함
   – 명확하게 정의되고 사용이 편리한 lightweight API 제공(가벼운 API 제공)
   – 여러 RDMBS 라이버르러들이 공통적으로 제공하는 기능들을 통합, but 각 라이브러리가 제공하는 고급기능은 제외시키지 않음
   – 추상화/호환성에 관련된 무거운 기능들을 PHP 스크립트를 통해 옵션으로 제공
4) PDO : 이러한 개념을 PHP Data Objects(PDO)라 부르기로 함

**PDO가 필요한 이유**
1) 성능 : 기존 database extension의 성공/실폐 사례를 활용. PDO의 모든 코드는 새롭게 작성되고 PHP5 환경을 기반으로
   성능 개선 효과를 극대화
2) 기능 : PDO는 공통 데이터베이스 기능을 기반 환경으로 제공, but 각 RDBMS 제품의 독특한 기능을 편리하게 접근할 수 있는
   환경 제공
3) 편의성 : API에 구애 받지 않고 독립적인 코드를 작성하는 한편 각 함수 호출의 역할을 명확하게 정의
4) 런타임 확장 지원 : PDO extension은 모듈러 형태로 구현되며, PHP 배포본을 다시 컴파일하거나 재설치하지 않고도 런타임
   환경에서 데이타베이스 드라이버를 로드할 수 있다. 예를 들어, PDO_OCI extension은 PDO extension을 위한 오라클
   데이타베이스 API를 구현가능. 그 밖에도 MySQL, PostgreSQL, ODBC, Firebird 등을 위한 드라이버가 현재 개발 중

   ![image](https://user-images.githubusercontent.com/41488792/48028922-37a68400-e190-11e8-82bb-b83e1da41575.png)
    ![image](https://user-images.githubusercontent.com/41488792/48028955-5442bc00-e190-11e8-9995-972c79b6f6d9.png)

    ![image](https://user-images.githubusercontent.com/41488792/48028980-67558c00-e190-11e8-9caa-ff7da82cea71.png)

출처: https://idchowto.com/?p=20119

<h4>
이제 PHP는 mysql, mysqli 관련 함수는 더 이상 제공하려고 하지 않고, 자동으로 SQL 인젝션을 방어해주니 이걸 사용하자!
</h4>
거진 9시간 동안 mysqli를 사용한 것을 PDO로 고쳤다 ㅠㅠ 밤 완전 꼴딱 새버렷다...

# require_once()
require_once 구문은 PHP가 파일을 이미 포함하였는지 확인하여 다시 include(require)하지 않는 점을 제외하면, require와 동일합니다.

메뉴얼:http://php.net/manual/kr/function.require-once.php