<?php
use Database\MySQLWrapper;

$mysqli = new MySQLWrapper();
$directory = __DIR__ . '/Examples';

// SQLファイルが存在するディレクトリ内のすべてのファイルをループ
foreach (new DirectoryIterator($directory) as $file) {
    if ($file->isFile() && $file->getExtension() === 'sql') {
        $filePath = $file->getPathname();
        $sql = file_get_contents($filePath);
        
        // SQLファイルの内容を実行
        $result = $mysqli->query($sql);
        if ($result === false) {
            throw new Exception("Could not execute query in file: $filePath");
        } else {
            print("Successfully ran SQL setup query from file: $filePath" . PHP_EOL);
        }
    }
}
