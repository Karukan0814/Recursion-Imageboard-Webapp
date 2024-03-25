<?php


ob_start(); // 出力バッファリングを開始
header("Access-Control-Allow-Origin: *");

spl_autoload_extensions(".php");
spl_autoload_register();

$DEBUG = true;


// セッションクッキーの有効期限を24時間に設定
$lifetime = 86400; // 秒単位
session_set_cookie_params($lifetime);

// セッションを開始
session_start();

$DEBUG = true;

if (preg_match('/\.(?:png|jpg|jpeg|gif|js|css|html)$/', $_SERVER["REQUEST_URI"])) {
    return false;
}


// ルートを読み込みます。
$routes = include('Routing/routes.php');

// リクエストURIを解析してパスだけを取得します。
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = ltrim($path, '/');


// パスが img フォルダを指している場合、画像ファイルを出力して終了
if (preg_match('#^img/([^/]+)$#', $path, $matches)) {
    if (file_exists($path)) {
        $contentType = mime_content_type($path);
        header("Content-Type: $contentType");
        readfile($path);
    } else {
        http_response_code(404);
        echo "404 Not Found: The requested image was not found on this server.";
    }
    exit;
}

// ルートにパスが存在するかチェックする
if (isset($routes[$path])) {
    // コールバックを呼び出してrendererを作成します。
    $renderer = $routes[$path]();

    try {
        // ヘッダーを設定します。
        foreach ($renderer->getFields() as $name => $value) {
            // ヘッダーに対する単純な検証を実行します。
            $sanitized_value = filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

            if ($sanitized_value && $sanitized_value === $value) {
                header("{$name}: {$sanitized_value}");
                header("Access-Control-Allow-Origin: *");
            } else {
                // ヘッダー設定に失敗した場合、ログに記録するか処理します。
                // エラー処理によっては、例外をスローするか、デフォルトのまま続行することもできます。
                http_response_code(500);
                if ($DEBUG) print("Failed setting header - original: '$value', sanitized: '$sanitized_value'");
                exit;
            }

            print($renderer->getContent());
        }
    } catch (Exception $e) {
        http_response_code(500);
        print("Internal error, please contact the admin.<br>");
        if ($DEBUG) print($e->getMessage());
    }
} else {
    // マッチするルートがない場合、404エラーを表示します。
    http_response_code(404);
    echo "404 Not Found: The requested route was not found on this server.";
}
