<!DOCTYPE html>
<html>
    <head>
    <meta charset="utf-8">
    </head>
<body>

<?php
//DB接続
$dsn='データベース名';
$user='ユーザー名';
$pass='パスワード';
$pdo=new PDO($dsn,$user,$pass,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));

//テーブル作成
$sql="CREATE TABLE IF NOT EXISTS tbtest_2"//testというテーブルがなければ作成
."("
."id INT AUTO_INCREMENT PRIMARY KEY,"//投稿番号
."name char(32),"
."comment TEXT,"
."date TEXT,"//投稿日時
."password TEXT"
.");";
$stmt=$pdo->query($sql);
?>

<?php

if(isset($_POST["name"])&&isset($_POST["comment"])&&isset($_POST["password"])){
    if(!empty($_POST["name"]&&$_POST["comment"]&&$_POST["password"])){
        $name=$_POST["name"];
        $comment=$_POST["comment"];
        $password=$_POST["password"];
        $date=date("Y年m月d日 H:i:s");
        
        //新規投稿
        if(empty($_POST["hidedit"])){//hideditが空のとき
            $hidedit=$_POST["hidedit"];
            //データ入力
            $sql=$pdo->prepare("INSERT INTO tbtest_2(name,comment,
            password,date) VALUES(:name,:comment,:password,:date)");
            $sql->bindParam(':name',$name,PDO::PARAM_STR);
            $sql->bindParam(':comment',$comment,PDO::PARAM_STR);
            $sql->bindParam(':password',$password,PDO::PARAM_STR);
            $sql->bindParam(':date',$date,PDO::PARAM_STR);
            $sql->execute();//実行
            
        //編集機能
        //hideditがあるとき    
        }else{
            $hidedit=$_POST["hidedit"];
            $sql='SELECT * FROM tbtest_2';//データ取得
            $stmt=$pdo->query($sql);
            $results=$stmt->fetchAll();//該当するもの全て配列で返す
    
            foreach($results as $row){
                if($hidedit==$row['id']){//隠す編集対象番号と投稿番号が一致したら
                    $id=$hidedit;//変更する投稿番号
                    $editname=$_POST["name"];//新しい名前
                    $editcomment=$_POST["comment"];//新しいコメント
                    
                    $sql='UPDATE tbtest_2 SET name=:editname,comment=:editcomment,password=:password,date=:date WHERE id=:id';
                    $stmt=$pdo->prepare($sql);
                    $stmt->bindParam(':editname',$editname,PDO::PARAM_STR);
                    $stmt->bindParam(':editcomment',$editcomment,PDO::PARAM_STR);
                    $stmt->bindParam(':password',$password,PDO::PARAM_STR);
                    $stmt->bindParam(':date',$date,PDO::PARAM_STR);
                    $stmt->bindParam(':id',$id,PDO::PARAM_INT);
                    $stmt->execute();//実行
                    
                }
            }
        }
    }
}
//編集機能
if(isset($_POST["edit"])&&isset($_POST["editpass"])){
    if(!empty($_POST["edit"]&&$_POST["editpass"])){
        $edit=$_POST["edit"];
        $editpass=$_POST["editpass"];
        
        $sql='SELECT * FROM tbtest_2';//データ取得
        $stmt=$pdo->query($sql);
        $results=$stmt->fetchAll();//該当するもの全て配列で返す
        foreach($results as $row){
            if($edit==$row['id']){//編集対象番号と投稿番号が一致して
                if($editpass==$row['password']){//編集用パスワードとパスワードが一致したら
                    //新しい変数に代入
                    $newname=$row['name'];
                    $newcomment=$row['comment'];
                    $newedit=$row['id'];
                }
            }
        }
    }
}
        
        
    

//削除機能
if(isset($_POST["delete"])&&isset($_POST["dltpass"])){
    if(!empty($_POST["delete"]&&$_POST["dltpass"])){
        $delete=$_POST["delete"];
        $dltpass=$_POST["dltpass"];
        
        $sql='SELECT * FROM tbtest_2';//データ取得
        $stmt=$pdo->query($sql);
        $results=$stmt->fetchAll();//該当するもの全て配列で返す
    
        foreach($results as $row){
            if($delete==$row['id']){//隠す編集対象番号と投稿番号が一致して
                if($dltpass==$row['password']){//パスワードが一致したら
                    $id=$delete;//削除する投稿番号
                
                    $sql='delete from tbtest_2 WHERE id=:id';
                    $stmt=$pdo->prepare($sql);
                    $stmt->bindParam(':id',$id,PDO::PARAM_INT);
                    $stmt->execute();//実行
                }
            }
        }
    }
}
?>
<form method="POST" action="">
    <input type="text" name="name" placeholder="名前" value=<?php if(!empty($newname)) echo $newname;?>><br>
    <input type="text" name="comment" placeholder="コメント" value=<?php if(!empty($newcomment)) echo $newcomment;?>><br>
    <input type="password" name="password" placeholder="パスワード">
    <input type="hidden" name="hidedit" value=<?php if(!empty($newedit)) echo $newedit;?>>
    <input type="submit" name="submit"><br><br>
    
    <input type="number" name="delete" placeholder="削除対象番号"><br>
    <input type="password" name="dltpass" placeholder="パスワード">
    <input type="submit" name="submit" value="削除"><br><br>
    
    <input type="number" name="edit" placeholder="編集対象番号"><br>
    <input type="password" name="editpass" placeholder="パスワード">
    <input type="submit" name="submit" value="編集"><br>
</form>


<?php
$sql='SELECT * FROM tbtest_2';//全て選択
$stmt=$pdo->query($sql);
$results=$stmt->fetchAll();//該当するデータ全てを配列として受け取る
foreach($results as $row){
    echo $row['id'].','.$row['name'].','.$row['comment'].','.$row['date'].'<br>';
    echo "<hr>";
}
?>
</body>
</html>