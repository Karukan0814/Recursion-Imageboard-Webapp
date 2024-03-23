<?php

namespace Commands\Programs;


use Commands\AbstractCommand;
use Commands\Argument;
use Database\MySQLWrapper;
use Exception;
use Helpers\Settings;


class DBWipe extends AbstractCommand
{
    // 使用するコマンド名を設定
    protected static ?string $alias = 'db-wipe';


    // 引数を割り当て
    public static function getArguments(): array
    {
        return [];
    }

    public function execute(): int
    {
        $this->log("Wiping DB...");
        $mysqli = new MySQLWrapper();

        //データベースダンプを取っておく

        $backupPath = __DIR__ . '/../../Database/backups/backup.sql';


        $mysqli->dbDump($backupPath);


        // データベースの名前を取得
        $dbName = Settings::env('DATABASE_NAME');

        // データベース全体を削除してデータベース全体をクリア
        $dropResult = $mysqli->query("DROP DATABASE IF EXISTS $dbName");
        if ($dropResult === false) {
            throw new Exception("Could not execute query to clear DB: $dbName");
        } else {
            print("Successfully ran SQL to clear DB: $dbName" . PHP_EOL);
        }
        $createResult = $mysqli->query("CREATE DATABASE $dbName");
        if ($createResult === false) {
            throw new Exception("Could not execute query to recreate DB: $dbName");
        } else {
            print("Successfully ran SQL to recreate DB: $dbName" . PHP_EOL);
        }
        return 0;
    }
    private function migrate(): void
    {
        $this->log("Running migrations...");
        $this->log("Migration ended...\n");
    }

    private function rollback(): void
    {
        $this->log("Rolling back migration...\n");
    }
}
