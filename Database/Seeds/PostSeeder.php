<?php


namespace Database\Seeds;

require_once 'vendor/autoload.php';

use Database\AbstractSeeder;
use Faker\Factory;

class PostSeeder extends AbstractSeeder
{
    protected ?string $tableName = 'posts';

    protected array $tableColumns = [
        
        [
            'data_type' => 'string',
            'column_name' => 'post_id'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'reply_to_id'
        ],

        [
            'data_type' => 'string',
            'column_name' => 'subject'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'text'
        ]
        ,
        [
            'data_type' => 'string',
            'column_name' => 'file_name'
        ]
    ];

    public function createRowData(): array
    {
        $faker = Factory::create();
        $insertDatas = [];

//post_idのリストを作成する
        for ($i = 1; $i <= 3; $i++) {
            // $carId =   $carIds[$i % 100];
            $data = [
                strval($i),
            null
                ,
                $faker->text(20),
                $faker->text(200),
                "test.png"

            ];

            $insertDatas[] = $data;
        };

        // // リプライのデータを追加
        // for ($i = 4; $i <= 10; $i++) {
        //     // $carId =   $carIds[$i % 100];
        //     $data = [
        //         strval($i),
        //         rand(1,3),
        //         $faker->text(20),
        //         $faker->text(200),

        //     ];

        //     $insertDatas[] = $data;
        // };


        // $posts = [
        //     new Post(
        //         post_id: '1',
        //         reply_to_id: null,
        //         subject: 'Test Subject 1',
        //         text: 'Test text for post 1',
        //     ),
        //     new Post(
        //         post_id: '2',
        //         reply_to_id: '1',
        //         subject: 'Reply to Test Subject 1',
        //         text: 'Test reply text for post 1',
        //     ),
        //     new Post(
        //         post_id: '3',
        //         reply_to_id: null,
        //         subject: 'Test Subject 2',
        //         text: 'Test text for post 2',
        //     ),
        //     new Post(
        //         post_id: '4',
        //         reply_to_id: '3',
        //         subject: 'Reply to Test Subject 2',
        //         text: 'Test reply text for post 2',
        //     ),
        //     new Post(
        //         post_id: '5',
        //         reply_to_id: '3',
        //         subject: 'Another Reply to Test Subject 2',
        //         text: 'Another test reply text for post 2',
        //     ),
        // ];
        return $insertDatas;
    }
}
