<?php

use Helpers\DatabaseHelper;
use Helpers\ValidationHelper;
use Response\HTTPRenderer;
use Response\Render\HTMLRenderer;
use Response\Render\JSONRenderer;

use Database\DataAccess\Implementations\ComputerPartDAOImpl;
use Database\DataAccess\Implementations\PostDAOImpl;
use Helpers\ValueType;
use Models\ComputerPart;
use Models\Post;

return [

    '' => function (): HTTPRenderer {
        // 親threadの表示

        $errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : null;
        unset($_SESSION['errors']);

        try {

            $postDao = new PostDAOImpl();

            //最新スレッドを検索する
            $threads = $postDao->getAllThreads(0, 5); //親スレッドの最新５件を取得

            // 各スレッドのリプライを取得
            $replyMap = [];

            foreach ($threads as $thread) {
                //各スレッドに対するリプライを取得
                $replies = $postDao->getReplies($thread, 0, 5);

                //  親スレッドのidをキーにしてマップを作成
                $replyMap[$thread->getPost_id()] = $replies;
            }



            return new HTMLRenderer("threads", ['threads' => $threads, 'replies' => $replyMap, 'errors' => $errors]);
        } catch (Exception $e) {
            error_log($e->getMessage());

            return new JSONRenderer(['status' => 'error', 'message' => 'An error occurred.']);
        }
    },
    'parent_register' => function () {
        $postDao = new PostDAOImpl();

        // リクエストメソッドがPOSTかどうかをチェックします
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $validationErrors = [];

            $titleRes = ValidationHelper::validateText($_POST['subject'] ?? null, 1, 100);
            $textRes = ValidationHelper::validateText($_POST['text'] ?? null, 1, 500);

            // バリデーションエラーのマージ
            $validationErrors = array_merge($validationErrors, $titleRes["error"], $textRes["error"]);

            // ファイルの存在チェック
            if (!isset($_FILES['image']) || $_FILES['image']['error'] == UPLOAD_ERR_NO_FILE) {
                $validationErrors[] = 'An image should be attached!';
            } else {
                // ファイルサイズのチェック
                $fileSizeRes = ValidationHelper::validateFileSize($_FILES['image']['size']);
                if (count($fileSizeRes["error"]) > 0) {
                    $validationErrors = array_merge($validationErrors, $fileSizeRes["error"]);
                }
            }

            // バリデーションエラーがあった場合
            if (!empty($validationErrors)) {
                $_SESSION['errors'] = $validationErrors;

                header("Location: ..");
                exit;
            }


            // 新しい投稿のデータを準備する


            // post_idを生成
            $post_id = bin2hex(random_bytes(16)); // 32文字のランダムな文字列を生成
            $subject = $_POST['subject'];
            $text = $_POST['text'];


            $newPost = new Post($post_id, null, $subject, $text);
            //スレッドを登録する
            $createResult = $postDao->create($newPost);
            if (!$createResult) {

                throw new Exception('something wrong with registering the thread data!');
            }


            // 画像を保存する


            //public/img/originalに画像を保存する
            $file = $_FILES['image'];
            $fileTmpName = $file['tmp_name'];

            // ファイルの保存処理
            $uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . "/img/original"; // プロジェクトのルートディレクトリに対する相対パス

            //  テーブルに登録された時間を取得
            $create_datetime = $createResult->getCreated_at();

            // ファイル名をハッシュ化する
            $hashedFileName = hash('sha256', $post_id . $create_datetime);

            // ファイルの拡張子を取得
            $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);

            // ハッシュ化されたファイル名と拡張子を組み合わせてアップロードパスを作成
            $uploadPath = $uploadDirectory . "/" . $hashedFileName . '.' . $fileExtension;


            //ファイル名を登録する
            $newPost->setFileName($hashedFileName . '.' . $fileExtension);
            $updateResult = $postDao->update($newPost);
            if (!$updateResult) {

                throw new Exception('something wrong with registering the img to table!');
            }


            // ファイルを保存する
            if (!move_uploaded_file($fileTmpName, $uploadPath)) {
                throw new Exception('something wrong with uploading the img file!');
            }


            // サムネイル画像も作成して保存する
            // ハッシュ化されたファイル名と拡張子を組み合わせてアップロードパスを作成
            $uploadPath = $uploadDirectory . "/" . $hashedFileName . '.' . $fileExtension;


            // 入力ファイル名と出力ファイル名
            $inputFile = $uploadPath;
            $outputFile = $_SERVER['DOCUMENT_ROOT'] . "/img/thumbnail" . "/" . $hashedFileName . '.' . $fileExtension;


            // ファイルの拡張子がGIFの場合、最初のフレームだけを取り出す
            if ($fileExtension === 'gif') {
                $inputFile .= '[0]';
            }
            // Imagemagickのコマンド
            $command = "convert " . escapeshellarg($inputFile) . " -resize 150x150 " . escapeshellarg($outputFile);

            // コマンドを実行
            exec($command, $output, $return_code);

            // 実行結果をチェック
            if ($return_code !== 0) {
                throw new Exception('generating thumbnail img failed');
            }


            header("Location: ..");
            exit;
        }
    },



    'thread' => function (): HTTPRenderer {


        $errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : null;
        unset($_SESSION['errors']);
        try {
            $postDao = new PostDAOImpl();

            $post_id = $_GET['id'] ?? null;

            if (empty($post_id)) {
                throw new Exception('id is necessary');
            }


            // //post_idからスレッドの情報を取得
            $thread = $postDao->getById($post_id);

            if (empty($thread)) {
                throw new Exception('this id does not exist.');
            }

            // //このスレッドに紐づくリプライを取得
            $replies = $postDao->getReplies($thread, 0, PHP_INT_MAX); //全件取得

            return new HTMLRenderer("thread", ['thread' => $thread, 'replies' => $replies, 'errors' => $errors]);
        } catch (Exception $e) {
            error_log($e->getMessage());

            return new JSONRenderer(['status' => 'error', 'message' => 'An error occurred.']);
        }
    },
    'child_register' => function () {
        try {
            $postDao = new PostDAOImpl();
            // リクエストメソッドがPOSTかどうかをチェックします
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                $validationErrors = [];

                $textRes = ValidationHelper::validateText($_POST['text'] ?? null, 1, 500);

                // バリデーションエラーのマージ
                $validationErrors = array_merge($validationErrors,  $textRes["error"]);

                // ファイルの存在チェック
                // if (isset($_FILES['image'])) {
                if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {

                    // ファイルサイズのチェック
                    $fileSizeRes = ValidationHelper::validateFileSize($_FILES['image']['size']);
                    if (count($fileSizeRes["error"]) > 0) {
                        $validationErrors = array_merge($validationErrors, $fileSizeRes["error"]);
                    }
                }
                // バリデーションエラーがあった場合
                if (!empty($validationErrors)) {

                    $_SESSION['errors'] = $validationErrors;

                    header("Location: ../thread?id=" . $_POST['reply_id']);
                    exit;
                }


                // 新しい投稿のデータを準備する


                // post_idを生成
                $post_id = bin2hex(random_bytes(16)); // 32文字のランダムな文字列を生成
                $text = $_POST['text'];


                $reply_id = $_POST['reply_id'] ?? null;


                $newPost = new Post($post_id, $reply_id, null, $text);
                //スレッドを登録する
                $createResult = $postDao->create($newPost);
                if (!$createResult) {

                    throw new Exception('something wrong with registering the thread data!');
                }


                // 画像を保存する　添付画像がある場合のみ
                // if (isset($_FILES['image'])) {
                if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                    //public/img/originalに画像を保存する
                    $file = $_FILES['image'];
                    $fileTmpName = $file['tmp_name'];

                    // ファイルの保存処理
                    $uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . "/img/original"; // プロジェクトのルートディレクトリに対する相対パス

                    //  テーブルに登録された時間を取得
                    $create_datetime = $createResult->getCreated_at();

                    // ファイル名をハッシュ化する
                    $hashedFileName = hash('sha256', $post_id . $create_datetime);

                    // ファイルの拡張子を取得
                    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);

                    // ハッシュ化されたファイル名と拡張子を組み合わせてアップロードパスを作成
                    $uploadPath = $uploadDirectory . "/" . $hashedFileName . '.' . $fileExtension;


                    //ファイル名を登録する
                    $newPost->setFileName($hashedFileName . '.' . $fileExtension);
                    $updateResult = $postDao->update($newPost);
                    if (!$updateResult) {

                        throw new Exception('something wrong with registering the img to table!');
                    }


                    // ファイルを保存する
                    if (!move_uploaded_file($fileTmpName, $uploadPath)) {
                        throw new Exception('something wrong with uploading the img file!');
                    }

                    // サムネイル画像も作成して保存する
                    // ハッシュ化されたファイル名と拡張子を組み合わせてアップロードパスを作成
                    $uploadPath = $uploadDirectory . "/" . $hashedFileName . '.' . $fileExtension;


                    // 入力ファイル名と出力ファイル名
                    $inputFile = $uploadPath;
                    $outputFile = $_SERVER['DOCUMENT_ROOT'] . "/img/thumbnail" . "/" . $hashedFileName . '.' . $fileExtension;

                    // ファイルの拡張子がGIFの場合、最初のフレームだけを取り出す
                    if ($fileExtension === 'gif') {
                        $inputFile .= '[0]';
                    }

                    // Imagemagickのコマンド
                    $command = "convert " . escapeshellarg($inputFile) . " -resize 150x150 " . escapeshellarg($outputFile);

                    // コマンドを実行
                    exec($command, $output, $return_code);

                    // 実行結果をチェック
                    if ($return_code !== 0) {
                        throw new Exception('generating thumbnail img failed');
                    }
                } else {
                    print_r("No img");
                }



                header("Location: ../thread?id=" . $reply_id);
                exit;
            } else {
                throw new Exception("the way the data sent was invalid.");
            }
        } catch (Exception $e) {
            error_log($e->getMessage());

            return new JSONRenderer(['status' => 'error', 'message' => 'An error occurred.']);
        }
    },
];
