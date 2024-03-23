<?php

use Helpers\DatabaseHelper;
use Helpers\ValidationHelper;
use Response\HTTPRenderer;
use Response\Render\HTMLRenderer;
use Response\Render\JSONRenderer;

use Database\DataAccess\Implementations\ComputerPartDAOImpl;
use Database\DataAccess\Implementations\PostDAOImpl;
use Models\ComputerPart;
use Types\ValueType;

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
    '' => function (): HTTPRenderer {
        // Home画面の描画
        try {


            $postDao = new PostDAOImpl();

            //スレッドの最新５件を取得
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
        } catch (InvalidArgumentException $e) {
            error_log($e->getMessage()); // エラーログはPHPのログやstdoutから見ることができます。
            return new JSONRenderer(['status' => 'error', 'message' => 'Invalid data.']);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => 'An error occurred.']);
        }
    },
    'register' => function (): HTTPRenderer {
        // 親threadの登録
        try {

            // リクエストメソッドがPOSTかどうかをチェックします
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method!');
            }

            $required_fields = [
                'subject' => ValueType::STRING,
                'text' => ValueType::STRING,

            ];
            $validatedData = ValidationHelper::validateFields($required_fields, $_POST);

            // 画像の添付がある場合は保存する
            if (isset($_FILES['image'])) {

                
            }


            $postDao = new PostDAOImpl();

            //スレッドを登録する
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
        } catch (InvalidArgumentException $e) {
            error_log($e->getMessage()); // エラーログはPHPのログやstdoutから見ることができます。
            return new JSONRenderer(['status' => 'error', 'message' => 'Invalid data.']);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => 'An error occurred.']);
        }
    },
];
