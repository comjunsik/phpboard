# CODE

[beginTreansaction()](#beginTreansaction())<br>
[INSERT_DB](#INSERT_DB)<br>
[likeify()](#likeify())<br>
[move_uploaded_file()](#move_uploaded_file())<br>
[preg_match](#preg_match)<br>
# beginTreansaction()
실제로 db에 변동사항이 있을때만 적용해 준다.

insert, update, delete..

# INSERT_DB

```php
    try{
    $pdo->beginTransaction();
    $sql="INSERT INTO member(id, passwd, name, reg_date)
        VALUES(?,?,?,now())";
    $stmh=$pdo->prepare($sql);
    $stmh->bindValue(1, $id, PDO::PARAM_STR);
    $stmh->bindValue(2, $password, PDO::PARAM_STR);
    $stmh->bindValue(3, $name, PDO::PARAM_STR);

    $stmh->execute();
    $pdo->commit();
    echo "데이터가 추가되었습니다.";
}catch (PDOException $Exception){
    $pdo->rollBack();
    echo "오류 :".$Exception->getMessage();
}
?>
```

# likeify()
![image](https://user-images.githubusercontent.com/41488792/48034120-65e08f80-e1a1-11e8-99fe-30620f8aec44.png)

# getimagesieze() 함수
![image](https://user-images.githubusercontent.com/41488792/48263376-38c60280-e469-11e8-8dc2-fcd15c8a9a37.png)

# $_FILES
![image](https://user-images.githubusercontent.com/41488792/48268730-218f1100-e479-11e8-844d-a375c66a13f3.png)

# file_upload 문제
$upload_dir = '/home/ubuntu/var/www/html/phpbbs/codintest/data/';   //물리적 저장위치
절대 경로로 하면 안된다... 왜 안되는지 이해를 잘 못하겠다.. 알아봐야한다..

그래서
$upload_dir = '../data/';   //물리적 저장위치
상대경로로 바꿔줬더니 이번엔 permission denied 문제가 ㅠㅠ

서버 들어가서 확인해 봤떠니 data 디렉터리 others 에 w 권한이 없는것을 확인 권환 줬더니 해결...

# move_uploaded_file()
![image](https://user-images.githubusercontent.com/41488792/48273526-7edc8f80-e484-11e8-885e-a51ebb976ed7.png)

# unlink()
![image](https://user-images.githubusercontent.com/41488792/48273588-9e73b800-e484-11e8-8889-3cf7e67e47b3.png)

# preg_match
![image](https://user-images.githubusercontent.com/41488792/48290552-a2b8c900-e4b6-11e8-810c-741ee229c08e.png)

사용법 :http://php.net/manual/kr/function.preg-match.php