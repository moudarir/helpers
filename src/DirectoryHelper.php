<?php

declare(strict_types=1);

namespace Moudarir\Helpers;

final class DirectoryHelper
{

    public static function recursively(string $directory): array
    {
        if (($filenames = @scandir($directory)) === false) {
            return [];
        }

        $directories = [];

        foreach ($filenames as $filename) {
            if ($filename === '.' || $filename === '..') {
                continue;
            }

            $dir = rtrim($directory, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$filename;

            if (is_dir($dir)) {
                $directories[] = $dir;

                foreach (self::recursively($dir) as $subDirectory) {
                    $directories[] = $subDirectory;
                }
            }
        }

        return $directories;
    }

    /**
     * Reads the specified directory and builds an array representation of it.
     * Sub-folders contained with the directory will be mapped as well.
     *
     * @param string $directory Path to source
     * @param array{depth?: int, hidden?: bool, filepath?: bool} $options
     * @return array
     */
    public static function map(string $directory, array $options = []): array
    {
        $filesData = [];
        $params = array_merge(['depth' => 0, 'hidden' => false, 'filepath' => false], $options);
        $newDepth = (int)$params['depth'] - 1;
        $directory = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (($fp = @opendir($directory)) !== false) {
            while (($file = readdir($fp)) !== false) {
                // Remove '.', '..', and hidden files [optional]
                if ($file === '.' || $file === '..' || ($params['hidden'] === false && $file[0] === '.')) {
                    continue;
                }

                $fullpath = $directory . $file;
                $isDirectory = is_dir($fullpath);

                if ($isDirectory) {
                    $file .= DIRECTORY_SEPARATOR;
                }

                if ($isDirectory && ($params['depth'] < 1 || $newDepth > 0)) {
                    $filesData[$file] = self::map($fullpath, [
                        'depth' => $newDepth,
                        'hidden' => (bool)$params['hidden'],
                        'filepath' => (bool)$params['filepath']
                    ]);
                } else {
                    $filesData[] = $params['filepath'] === true ? $fullpath : $file;
                }
            }

            closedir($fp);
        }

        return $filesData;
    }

    public static function create(string $directory): bool
    {
        $wrapper = null;

        if (self::isStreamUrl($directory)) {
            [$wrapper, $directory] = explode('://', $directory, 2);
        }

        $directory = str_replace('//', '/', $directory);

        if ($wrapper !== null) {
            $directory = $wrapper.'://'.$directory;
        }

        $directory = rtrim($directory, '/');
        if (empty($directory)) {
            $directory = '/';
        }

        if (file_exists($directory)) {
            return @is_dir($directory);
        }

        $parentPath = dirname($directory);
        while ('.' != $parentPath && !is_dir($parentPath)) {
            $parentPath = dirname($parentPath);
        }

        if ($stat = @stat($parentPath)) {
            $dirPerms = $stat['mode'] & 0007777;
        } else {
            $dirPerms = 0750;
        }

        if (@mkdir($directory, $dirPerms, true)) {
            if ($dirPerms != ($dirPerms & ~umask())) {
                $folderParts = explode('/', substr($directory, strlen($parentPath) + 1));
                for ($i = 1, $c = count($folderParts); $i <= $c; $i++) {
                    @chmod($parentPath.'/'.implode('/', array_slice($folderParts, 0, $i)), $dirPerms);
                }
            }

            return true;
        }

        return false;
    }

    public static function getFilesInfo(string $directory, bool $onlyTopLevel = true, bool $recursion = false): array
    {
        static $filesData = [];
        $relativePath = $directory;

        if (($fp = @opendir($directory)) !== false) {
            // reset the array and make sure $source_dir has a trailing slash on the initial call
            if ($recursion === false) {
                $filesData = [];
                $directory = rtrim(realpath($directory), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            }

            // Used to be foreach (scandir($source_dir, 1) as $file), but scandir() is simply not as fast
            while (($file = readdir($fp)) !== false) {
                if (is_dir($directory . $file) && $file[0] !== '.' && $onlyTopLevel === false) {
                    self::getFilesInfo($directory . $file . DIRECTORY_SEPARATOR, false, true);
                } elseif ($file[0] !== '.') {
                    $filesData[$file] = FileHelper::info($directory . $file);
                    $filesData[$file]['relative_path'] = $relativePath;
                }
            }

            closedir($fp);
        }

        return $filesData;
    }

    public static function deleteFiles(
        string $directory,
        bool   $alsoDeleteDirectory = false,
        bool   $htdocs = false,
        int    $level = 0
    ): bool
    {
        // Trim the trailing slash
        $directory = rtrim($directory, '/\\');

        if (($currentDir = @opendir($directory)) === false) {
            return false;
        }

        while (($filename = @readdir($currentDir)) !== false) {
            if ($filename !== '.' && $filename !== '..') {
                $filepath = $directory . DIRECTORY_SEPARATOR . $filename;

                if (is_dir($filepath) && $filename[0] !== '.' && !is_link($filepath)) {
                    self::deleteFiles(
                        $filepath,
                        $alsoDeleteDirectory,
                        $htdocs,
                        $level + 1
                    );
                } elseif (
                    $htdocs === false ||
                    !preg_match('/^(\.htaccess|index\.(html|htm|php)|web\.config)$/i', $filename)
                ) {
                    @unlink($filepath);
                }
            }
        }

        closedir($currentDir);

        return !($alsoDeleteDirectory === true && $level > 0) || @rmdir($directory);
    }

    private static function isStreamUrl(string $path): bool
    {
        static $pattern = null;

        if ($pattern === null) {
            $pattern = sprintf(
                '!^(%s)://!',
                implode('|', array_map('preg_quote', stream_get_wrappers()))
            );
        }

        return preg_match($pattern, $path) === 1;
    }
}
