<?php
require('define.php');
$dsn = DSN;
$user = USER;
$password = PASSWORD;

if(isset($_POST['submit'])){
    try{
        $pdo = new PDO($dsn,$user,$password);
// productsテーブルの各カラムに値を入れる
        $sql_insert = '
        INSERT INTO products (product_code,product_name,price,stock_quantity,vendor_code)
        VALUES (:product_code, :product_name, :price, :stock_quantity,:vendor_code)
        ';
        // 準備完了、入力内容待ち
        $stmt_insert = $pdo->prepare($sql_insert);
// 各値受け取り、バインド先設定
        $stmt_insert->bindValue(':product_code',$_POST['product_code'],PDO::PARAM_INT);
        $stmt_insert->bindValue(':product_name',$_POST['product_name'],PDO::PARAM_STR);
        $stmt_insert->bindValue(':price',$_POST['price'],PDO::PARAM_INT);
        $stmt_insert->bindValue(':stock_quantity',$_POST['stock_quantity'],PDO::PARAM_INT);
        $stmt_insert->bindValue(':vendor_code',$_POST['vendor_code'],PDO::PARAM_INT);

    // 指摘３修正箇所です
        
        $bool_product_code=is_numeric($_POST['product_code']);
        $bool_product_name=is_string($_POST['product_name']);
        $bool_price=is_numeric($_POST['price']);
        $bool_stock_quantity=is_numeric($_POST['stock_quantity']);
        $bool_vendor_code=is_numeric($_POST['vendor_code']);

       if($bool_product_code=== TRUE && $bool_product_name=== TRUE && $bool_price=== TRUE && $bool_stock_quantity=== TRUE && $bool_vendor_code=== TRUE){
           $stmt_insert->execute();
       }else{
           header('Location:error.php');
           exit;
       }
       
       
        
// INSERT命令で受けたレコード数を$countへ返す
        $count = $stmt_insert->rowCount();

        $message = "商品を{$count}件登録しました。";
// ヘッダー情報の利用。LocationはクライアントにURL移動をさせる。
        // 同時に変数$messageも渡す
        header("Location:read.php?message={$message}");
    } catch(PDOException $e){
        exit($e->getMessage());
    }
}

try{
    $pdo = new PDO($dsn,$user,$password);

    $sql_select = 'SELECT vendor_code FROM vendors';

    $stmt_select = $pdo->query($sql_select);

    $vendor_codes = $stmt_select->fetchAll(PDO::FETCH_COLUMN);
}catch(PDOException $e){
   exit($e->getMessage()); 
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品登録</title>
    <link rel= "stylesheet" href="css\style.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
     <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
     <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <nav>
            <a href="index.php">商品管理アプリ</a>
        </nav>
    </header>
    <main>
        <article class="registration">
            <h1>商品登録</h1>
            <div class="back">
                <a href="read.php" class="btn">&lt;戻る</a>
            <div>
                <form action="create.php" method="post" class="registration-form">
                    <div>
                        <label for="product_code">商品コード</label>
                        <input type="number" name= "product_code" min="0" max="100000000" required>

                        <label for="product_name">商品名</label>
                        <input type="text" name="product_name" maxlength="50" required>

                        <label for="price">単価</label>
                        <input type="number" name="price" min="0" max="100000000" required>

                        <label for="stock_quantity">在庫数</label>
                        <input type="number" name="stock_quantity" min="0" max="100000000" required>

                        <label for="vendor_code">仕入れ先コード</label>
                        <select name="vendor_code" required>
                            <option disabled selected value>選択してください</option>
                            <?php
                            foreach ($vendor_codes as $vendor_code){
                                echo "<option value='{$vendor_code}'>{$vendor_code}</option>";
                            }
                            ?>
                            </select>
                    </div>
                    <button type="submit" class="submit-btn" name="submit" value="create">登録</button>
                </form>
        </article>
    </main>
    <footer>
        <p class='copyright'>&copy; 商品管理アプリ　All rights reserved.</p>
    </footer>
</body>
</html>