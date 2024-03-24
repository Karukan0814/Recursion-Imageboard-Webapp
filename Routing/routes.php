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
    'random/part' => function (): HTTPRenderer {
        $partDao = new ComputerPartDAOImpl();
        $part = $partDao->getRandom();

        if ($part === null) throw new Exception('No parts are available!');

        return new HTMLRenderer('component/computer-part-card', ['part' => $part]);
    },
    'parts' => function (): HTTPRenderer {
        // IDの検証
        $id = ValidationHelper::integer($_GET['id'] ?? null);

        $partDao = new ComputerPartDAOImpl();
        $part = $partDao->getById($id);

        if ($part === null) throw new Exception('Specified part was not found!');

        return new HTMLRenderer('component/computer-part-card', ['part' => $part]);
    },
    'api/random/part' => function (): HTTPRenderer {
        $part = DatabaseHelper::getRandomComputerPart();
        return new JSONRenderer(['part' => $part]);
    },
    'api/parts' => function () {
        $id = ValidationHelper::integer($_GET['id'] ?? null);
        $part = DatabaseHelper::getComputerPartById($id);
        return new JSONRenderer(['part' => $part]);
    },
    'update/part' => function (): HTMLRenderer {
        $part = null;
        $partDao = new ComputerPartDAOImpl();
        if (isset($_GET['id'])) {
            $id = ValidationHelper::integer($_GET['id']);
            $part = $partDao->getById($id);
        }
        return new HTMLRenderer('component/update-computer-part', ['part' => $part]);
    },
    'delete/part' => function (): HTMLRenderer {
        //課題：データベースからコンピュータ部品を削除するエンドポイント
        $part = null;
        $partDao = new ComputerPartDAOImpl();
        $result = false;
        if (isset($_GET['id'])) {
            $id = ValidationHelper::integer($_GET['id']);
            // 削除用のDAOにアクセス
            try {

                $result = $partDao->delete($id);
            } catch (Exception $e) {
                print_r($e);
                return new HTMLRenderer('component/delete-computer-part', ['result' => false]);
            }
        }
        return new HTMLRenderer('component/delete-computer-part', ['result' => $result]);
    },
    'form/update/part' => function (): HTTPRenderer {
        try {
            // リクエストメソッドがPOSTかどうかをチェックします
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method!');
            }

            $required_fields = [
                'name' => ValueType::STRING,
                'type' => ValueType::STRING,
                'brand' => ValueType::STRING,
                'modelNumber' => ValueType::STRING,
                'releaseDate' => ValueType::DATE,
                'description' => ValueType::STRING,
                'performanceScore' => ValueType::INT,
                'marketPrice' => ValueType::FLOAT,
                'rsm' => ValueType::FLOAT,
                'powerConsumptionW' => ValueType::FLOAT,
                'lengthM' => ValueType::FLOAT,
                'widthM' => ValueType::FLOAT,
                'heightM' => ValueType::FLOAT,
                'lifespan' => ValueType::INT,
            ];

            $partDao = new ComputerPartDAOImpl();

            // 入力に対する単純なバリデーション。実際のシナリオでは、要件を満たす完全なバリデーションが必要になることがあります。
            $validatedData = ValidationHelper::validateFields($required_fields, $_POST);

            if (isset($_POST['id'])) $validatedData['id'] = ValidationHelper::integer($_POST['id']);

            // 名前付き引数を持つ新しいComputerPartオブジェクトの作成＋アンパッキング
            $part = new ComputerPart(...$validatedData);

            error_log(json_encode($part->toArray(), JSON_PRETTY_PRINT));

            // 新しい部品情報でデータベースの更新を試みます。
            // 別の方法として、createOrUpdateを実行することもできます。
            if (isset($validatedData['id'])) $success = $partDao->update($part);
            else $success = $partDao->create($part);

            if (!$success) {
                throw new Exception('Database update failed!');
            }

            return new JSONRenderer(['status' => 'success', 'message' => 'Part updated successfully']);
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage()); // エラーログはPHPのログやstdoutから見ることができます。
            return new JSONRenderer(['status' => 'error', 'message' => 'Invalid data.']);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => 'An error occurred.']);
        }
    },
    // '' => function (): HTTPRenderer {
    //     // Home画面の描画
    //     try {


    //         $postDao = new PostDAOImpl();

    //         //スレッドの最新５件を取得
    //         $threads = $postDao->getAllThreads(0, 5); //親スレッドの最新５件を取得

    //         // 各スレッドのリプライを取得
    //         $replyMap = [];

    //         foreach ($threads as $thread) {
    //             //各スレッドに対するリプライを取得
    //             $replies = $postDao->getReplies($thread, 0, 5);

    //             //  親スレッドのidをキーにしてマップを作成
    //             $replyMap[$thread->getPost_id()] = $replies;
    //         }



    //         return new HTMLRenderer("threads", ['threads' => $threads, 'replies' => $replyMap]);
    //     } catch (InvalidArgumentException $e) {
    //         error_log($e->getMessage()); // エラーログはPHPのログやstdoutから見ることができます。
    //         return new JSONRenderer(['status' => 'error', 'message' => 'Invalid data.']);
    //     } catch (Exception $e) {
    //         error_log($e->getMessage());
    //         return new JSONRenderer(['status' => 'error', 'message' => 'An error occurred.']);
    //     }
    // },
    '' => function (): HTTPRenderer {
        // 親threadの登録
        try {

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
                    return new HTMLRenderer('threads', ['threads' => $threads, 'errors' => $validationErrors]);
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
                $file=$_FILES['image'];
                $fileTmpName=$file['tmp_name'];

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

                // Imagemagickのコマンド
                $command = "convert " . escapeshellarg($inputFile) . " -resize 150x150 " . escapeshellarg($outputFile);

                // コマンドを実行
                exec($command, $output, $return_code);

                // 実行結果をチェック
                if ($return_code !== 0) {
                    //全てのエラーを初期ページに引き渡す
                    throw new Exception('generating thumbnail img failed');
                }
            }


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



            return new HTMLRenderer("threads", ['threads' => $threads, 'replies' => $replyMap]);
        } catch (Exception $e) {
            error_log($e->getMessage());

            return new JSONRenderer(['status' => 'error', 'message' => 'An error occurred.']);
        }
    },
];
