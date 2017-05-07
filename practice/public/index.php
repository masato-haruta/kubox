<?php
date_default_timezone_set('Asia/Tokyo');
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

// Define app routes
$app->get('/hello/{name}', function ($request, $response, $args) {
    return $response->write("Hello " . $args['name']);
});

// post test
$app->post('/po', function ($request, $response, $args) {
    print_r($request->getParsedBody());
    return 'hjhhh';        
});

////////////////////////////////
function initDb() {
    $pdo = new PDO('sqlite:' . __DIR__ . '/kubox.sqlite3');
    $pdo->query('SET NAMES utf8');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $sql = "CREATE TABLE IF NOT EXISTS items(id integer PRIMARY KEY AUTOINCREMENT, body text, created_at text, updated_at text);";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $pdo;
}

// 認証用API(GET)
$app->get('/auth/{code}', function ($request, $response, $args) {
    // 認証結果を返す
    if ($args['code'] == '1234') {
        return $response->write(json_encode(array('result' => true)));
    } else {
        return $response->write(json_encode(array('result' => false)));
    }
});

// 認証解除とDB削除のAPI(GET)
$app->delete('/items', function ($request, $response, $args) {
    $pdo = initDb();
    // DB内の全データを削除する        
    $stmt = $pdo->prepare("DELETE FROM items");
    if ($stmt->execute()) {
        // 全データ削除出来たらAutoIncrementもリセットする
        $stmt = $pdo->prepare("DELETE FROM sqlite_sequence WHERE name = 'items'");
        $stmt->execute();
        return $response->write(json_encode(array('result' => true)));
    } else {
        return $response->write(json_encode(array('result' => false)));
    }
});

// 全行データの取得API(GET)
$app->get('/items', function ($request, $response, $args) {
    $pdo = initDb();
    // DB内の全データを取得する        
    $stmt = $pdo->prepare("SELECT * FROM items");
    $stmt->execute();
    return $response->write(json_encode($stmt->fetchAll()));
});

// 行データ追加のAPI(POST)
$app->post('/item', function ($request, $response, $args) {
    $postParams = $request->getParsedBody();
    $pdo = initDb();
    // DBへ新規レコードを追加する        
    $stmt = $pdo->prepare("INSERT INTO items(body, created_at, updated_at) VALUES (?, ?, ?)");
    $createdAt = date("Y-m-d H:i:s");
    $stmt->execute([$postParams['body'], $createdAt,'']);
    $jsonAry = array('id' => $pdo->lastInsertId(), 'body' => $postParams['body'], 'created_at' => $createdAt);
    return $response->write(json_encode($jsonAry));
});

// 行データ更新のAPI(PUT)
$app->put('/item', function ($request, $response, $args) {
    $postParams = $request->getParsedBody();
    $pdo = initDb();
    // DB内の該当レコードの内容を更新する        
    $stmt = $pdo->prepare("UPDATE items set body = ?, updated_at = ? WHERE id = ?");
    $updatedAt = date("Y-m-d H:i:s");
    $stmt->execute([$postParams['body'], $updatedAt, $postParams['id']]);
    $jsonAry = array('id' => $postParams['id'], 'body' => $postParams['body'], 'updated_at' => $updatedAt);
    return $response->write(json_encode($jsonAry));
});

// 行データ削除のAPI(DELETE)
$app->delete('/item/{id}', function ($request, $response, $args) {
    $pdo = initDb();
    // DBから該当レコードを削除する        
    $stmt = $pdo->prepare("DELETE FROM items WHERE id = :id");
    if ($stmt->execute(array(':id' => $args['id']))) {
        return $response->write(json_encode(array('result' => true)));
    } else {
        return $response->write(json_encode(array('result' => false)));
    }
});
////////////////////////////////////

// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Register middleware
require __DIR__ . '/../src/middleware.php';

// Register routes
require __DIR__ . '/../src/routes.php';

// Run app
$app->run();
