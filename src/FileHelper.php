<?php

declare(strict_types=1);

namespace Moudarir\Helpers;

use ErrorException;
use Random\RandomException;

final class FileHelper
{

    /**
     * @return array{
     *     file_name: string,
     *     raw_name: string,
     *     file_ext: string,
     *     full_path: string,
     *     file_path: string
     * }
     * @throws RandomException
     */
    public static function newFilename(string $filepath, int $length = 40, bool $encrypt = true): array
    {
        $next = 2;
        $pathInfo = pathinfo($filepath);
        $directory = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];
        $extension = isset($pathInfo['extension']) ? '.'.$pathInfo['extension'] : '';

        while (is_file($filepath)) {
            $basename = $encrypt
                ? EncryptionHelper::generateToken($length).$extension
                : $filename.'-'.$next.$extension;

            $filepath = $directory.DIRECTORY_SEPARATOR.$basename;
            ++$next;
        }

        $pathInfo = pathinfo($filepath);

        return [
            'file_name' => $pathInfo['basename'],
            'raw_name' => $pathInfo['filename'],
            'file_ext'  => $extension,
            'file_path' => $pathInfo['dirname'],
            'full_path' => $filepath,
        ];
    }

    /**
     * @param string $filepath
     * @param string|null $key
     * @return array{
     *     dirname: string,
     *     basename: string,
     *     filename: string,
     *     extension: string
     * }|string|null
     */
    public static function pathInfo(string $filepath, ?string $key = null): array|string|null
    {
        $info = pathinfo($filepath);

        if (array_key_exists('filename', $info) === false) {
            $info['filename'] = '';
        }

        if (array_key_exists('extension', $info) === false) {
            $info['extension'] = '';
        }

        if ($key !== null) {
            return $info[$key] ?? null;
        }

        return $info;
    }

    public static function info(string $filepath, bool $optionals = false): array
    {
        if (is_file($filepath) === false) {
            return [];
        }

        $size = @filesize($filepath);
        $date = @filemtime($filepath);
        $info = [
            'name' => basename($filepath),
            'server_path' => $filepath,
            'size' => $size === false ? 0 : $size,
            'date' => $date === false ? 0 : $date,
        ];

        if ($optionals === true) {
            $info['readable'] = is_readable($filepath);
            $info['executable'] = is_executable($filepath);
            $info['fileperms'] = fileperms($filepath);
        }

        return $info;
    }

    public static function write(string $filepath, string $data, string $mode = 'wb'): bool
    {
        if (($fp = @fopen($filepath, $mode)) === false) {
            return false;
        }

        flock($fp, LOCK_EX);

        try {
            for ($result = $written = 0, $length = strlen($data); $written < $length; $written += $result) {
                $result = fwrite($fp, substr($data, $written));
                if (is_int($result) === false || $result === 0) {
                    break;
                }
            }

            return is_int($result);
        } finally {
            flock($fp, LOCK_UN);
            fclose($fp);
        }
    }

    /**
     * @throws ErrorException
     */
    public static function getContent(string $filepath, bool $isUrl = false): string
    {
        set_error_handler(static fn (): bool => true, E_WARNING);

        try {
            if ($isUrl === false && is_file($filepath) === false) {
                throw new ErrorException(
                    sprintf("The file `%s` was not found.", $filepath)
                );
            }

            if (($content = file_get_contents($filepath)) === false) {
                throw new ErrorException(
                    sprintf("Unable to read the contents of `%s`.", $filepath)
                );
            }

            return $content;
        } finally {
            restore_error_handler();
        }
    }

    /**
     * @throws ErrorException
     */
    public static function saveContent(string $filepath, string $data): true
    {
        set_error_handler(static fn (): bool => true, E_WARNING);

        try {
            if (file_put_contents($filepath, $data) === false) {
                throw new ErrorException(
                    sprintf("Unable to save the contents of `%s`.", $filepath)
                );
            }

            return true;
        } finally {
            restore_error_handler();
        }
    }
}
