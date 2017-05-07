<?php
$pdo = new PDO('sqlite:kubox.sqlite3');

// SQL実行時にもエラーの代わりに例外を投げるように設定
// (毎回if文を書く必要がなくなる)
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// デフォルトのフェッチモードを連想配列形式に設定 
// (毎回PDO::FETCH_ASSOCを指定する必要が無くなる)
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

// 挿入（プリペアドステートメント）
$stmt = $pdo->prepare("INSERT INTO items(body, created_at, updated_at) VALUES (?, ?, ?)");
$stmt->execute(['hgo','2','3']);
$stmt = $pdo->prepare("SELECT * FROM items");
$stmt->execute();
$results = $stmt->fetchAll();
print_r($results);
//$stmt = $pdo->prepare("DELETE FROM items");
//$flg = $stmt->execute();
//echo $flg;
