<?php

namespace Helpers;


class ValidationHelper
{

    const  AVAILABLE_FILE = [
        'image/jpeg' => 'image/jpeg',
        'image/png' => 'image/png',
        'image/gif' => 'image/gif',
    ];

    public static function integer($value, float $min = -INF, float $max = INF): int
    {
        // PHPには、データを検証する組み込み関数があります。詳細は https://www.php.net/manual/en/filter.filters.validate.php を参照ください。
        $value = filter_var($value, FILTER_VALIDATE_INT, ["min_range" => (int) $min, "max_range"=>(int) $max]);

        // 結果がfalseの場合、フィルターは失敗したことになります。
        if ($value === false) throw new \InvalidArgumentException("The provided value is not a valid integer.");

        // 値がすべてのチェックをパスしたら、そのまま返します。
        return $value;
    }

    public static function validateDate(string $date, string $format = 'Y-m-d'): string
    {
        $d = \DateTime::createFromFormat($format, $date);
        if ($d && $d->format($format) === $date) {
            return $date;
        }

        throw new \InvalidArgumentException(sprintf("Invalid date format for %s. Required format: %s", $date, $format));
    }

    public static function validateFields(array $fields, array $data): array
    {
        $validatedData = [];

        foreach ($fields as $field => $type) {
            if (!isset($data[$field]) || ($data)[$field] === '') {
                throw new \InvalidArgumentException("Missing field: $field");
            }

            $value = $data[$field];

            $validatedValue = match ($type) {
                ValueType::STRING => is_string($value) ? $value : throw new \InvalidArgumentException("The provided value is not a valid string."),
                ValueType::INT => self::integer($value), // You can further customize this method if needed
                ValueType::FLOAT => filter_var($value, FILTER_VALIDATE_FLOAT),
                ValueType::DATE => self::validateDate($value),
                default => throw new \InvalidArgumentException(sprintf("Invalid type for field: %s, with type %s", $field, $type)),
            };

            if ($validatedValue === false) {
                throw new \InvalidArgumentException(sprintf("Invalid value for field: %s", $field));
            }

            $validatedData[$field] = $validatedValue;
        }

        return $validatedData;
    }
    public static function validateText($text, int $min = 1, int $max = PHP_INT_MAX)
    {

        $error = [];
        $response = [
            "error" => $error,
            "value" => $text
        ];

        // $text が string 型であることを確認
        if (!is_string($text)) {
            $response["error"][] = "Subject / Text: The provided value is not a valid string.";
            return $response;
        }

        // 文字列の長さを取得
        $length = strlen($text);

        // 文字列の長さが指定された範囲内であることを確認
        if ($length < $min || $length > $max) {
            // throw new \InvalidArgumentException(sprintf("The provided string must be between %d and %d characters in length.", $min, $max));

            $response["error"][] = sprintf("Title / Text: The provided string must be between %d and %d characters in length.", $min, $max);
            return $response;
        }

        // 値がすべてのチェックをパスしたら、そのまま返します。
        return $response;
    }


    public static function validateFileType($fileType)


    {

        $error = [];
        $response = [
            "error" => $error,
            "value" => $fileType
        ];

        // $syntax が string 型であることを確認
        if (!is_string($fileType)) {
            $response["error"][] = "FileType: The provided value is not a valid string.";
            return $response;
        }
        // $syntax がリスト内の値と一致するか確認
        if (!array_key_exists($fileType, self::AVAILABLE_FILE)) {

            $response["error"][] = sprintf("FileType: The provided syntax '%s' is not allowed. Allowed filetypes are: %s", $fileType, implode(', ', self::AVAILABLE_FILE));
            return $response;
        }

        // 値がすべてのチェックをパスしたら、そのまま返します。

        return $response;
    }



    public static function validateFileSize($fileSize)
{
    $maxSize = 2 * 1024 * 1024; // 2MBをバイト単位で指定

    $error = [];
    $response = [
        "error" => $error,
        "value" => $fileSize
    ];

    if ($fileSize > $maxSize) {
        $response["error"][] = "File size: File is too large (maximum 2MB)";
    }

    return $response;
}
}