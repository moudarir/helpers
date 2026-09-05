<?php

declare(strict_types=1);

namespace Moudarir\Helpers\Tests\Unit;

use Moudarir\Helpers\DirectoryHelper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DirectoryHelperTest extends TestCase
{

    #[Test]
    public function recursively_returnsEmptyArrayForMissingDirectory(): void
    {
        $directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'missing-directory';

        self::assertSame([], DirectoryHelper::recursively($directory));
    }

    #[Test]
    public function recursively_returnsEmptyArrayForEmptyDirectory(): void
    {
        $directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'helpers-empty';

        mkdir($directory);

        try {
            self::assertSame([], DirectoryHelper::recursively($directory));
        } finally {
            rmdir($directory);
        }
    }

    #[Test]
    public function recursively_returnsDirectoriesOnly(): void
    {
        $directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'helpers-recursive';
        $subDirectory = $directory.DIRECTORY_SEPARATOR.'sub';
        $file = $directory.DIRECTORY_SEPARATOR.'file.txt';

        mkdir($directory);
        mkdir($subDirectory);
        file_put_contents($file, 'test');

        try {
            self::assertSame([$subDirectory], DirectoryHelper::recursively($directory));
        } finally {
            unlink($file);
            rmdir($subDirectory);
            rmdir($directory);
        }
    }

    #[Test]
    public function recursively_returnsNestedDirectories(): void
    {
        $directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'helpers-recursive';
        $first = $directory.DIRECTORY_SEPARATOR.'first';
        $second = $first.DIRECTORY_SEPARATOR.'second';
        $third = $second.DIRECTORY_SEPARATOR.'third';

        mkdir($third, 0777, true);

        try {
            self::assertSame(
                [$first, $second, $third],
                DirectoryHelper::recursively($directory)
            );
        } finally {
            rmdir($third);
            rmdir($second);
            rmdir($first);
            rmdir($directory);
        }
    }

    #[Test]
    public function recursively_returnsDirectoriesFromAllBranches(): void
    {
        $directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'helpers-recursive';
        $first = $directory.DIRECTORY_SEPARATOR.'first';
        $second = $directory.DIRECTORY_SEPARATOR.'second';
        $nested = $first.DIRECTORY_SEPARATOR.'nested';

        mkdir($nested, 0777, true);
        mkdir($second);

        try {
            self::assertSame(
                [$first, $nested, $second],
                DirectoryHelper::recursively($directory)
            );
        } finally {
            rmdir($nested);
            rmdir($first);
            rmdir($second);
            rmdir($directory);
        }
    }

    #[Test]
    public function map_returnsEmptyArrayForMissingDirectory(): void
    {
        $directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'missing-directory';

        self::assertSame([], DirectoryHelper::map($directory));
    }

    #[Test]
    public function map_returnsFilesAndDirectories(): void
    {
        $directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'helpers-map';
        $subDirectory = $directory.DIRECTORY_SEPARATOR.'sub';
        $file = $directory.DIRECTORY_SEPARATOR.'file.txt';

        mkdir($subDirectory, 0777, true);
        file_put_contents($file, 'test');

        try {
            self::assertSame(
                [
                    'file.txt',
                    'sub'.DIRECTORY_SEPARATOR => [],
                ],
                DirectoryHelper::map($directory)
            );
        } finally {
            unlink($file);
            rmdir($subDirectory);
            rmdir($directory);
        }
    }

    #[Test]
    public function map_returnsFullPathsWhenFilepathOptionIsEnabled(): void
    {
        $directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'helpers-map';
        $file = $directory.DIRECTORY_SEPARATOR.'file.txt';

        mkdir($directory);
        file_put_contents($file, 'test');

        try {
            self::assertSame(
                [$file],
                DirectoryHelper::map($directory, ['filepath' => true])
            );
        } finally {
            unlink($file);
            rmdir($directory);
        }
    }

    #[Test]
    public function map_excludesHiddenFilesByDefault(): void
    {
        $directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'helpers-map';
        $visible = $directory.DIRECTORY_SEPARATOR.'visible.txt';
        $hidden = $directory.DIRECTORY_SEPARATOR.'.hidden.txt';

        mkdir($directory);
        file_put_contents($visible, 'visible');
        file_put_contents($hidden, 'hidden');

        try {
            self::assertSame(['visible.txt'], DirectoryHelper::map($directory));
        } finally {
            unlink($visible);
            unlink($hidden);
            rmdir($directory);
        }
    }

    #[Test]
    public function map_includesHiddenFilesWhenHiddenOptionIsEnabled(): void
    {
        $directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'helpers-map';
        $visible = $directory.DIRECTORY_SEPARATOR.'visible.txt';
        $hidden = $directory.DIRECTORY_SEPARATOR.'.hidden.txt';

        mkdir($directory);
        file_put_contents($visible, 'visible');
        file_put_contents($hidden, 'hidden');

        try {
            $result = DirectoryHelper::map($directory, ['hidden' => true]);

            self::assertCount(2, $result);
            self::assertContains('visible.txt', $result);
            self::assertContains('.hidden.txt', $result);
        } finally {
            unlink($visible);
            unlink($hidden);
            rmdir($directory);
        }
    }

    #[Test]
    public function map_limitsRecursionDepth(): void
    {
        $directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'helpers-map';
        $first = $directory.DIRECTORY_SEPARATOR.'first';
        $second = $first.DIRECTORY_SEPARATOR.'second';

        mkdir($second, 0777, true);

        try {
            self::assertSame(
                ['first'.DIRECTORY_SEPARATOR],
                DirectoryHelper::map($directory, ['depth' => 1])
            );
        } finally {
            rmdir($second);
            rmdir($first);
            rmdir($directory);
        }
    }

    #[Test]
    public function map_recursesAccordingToDepth(): void
    {
        $directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'helpers-map';
        $first = $directory.DIRECTORY_SEPARATOR.'first';
        $second = $first.DIRECTORY_SEPARATOR.'second';
        $file = $second.DIRECTORY_SEPARATOR.'file.txt';

        mkdir($second, 0777, true);
        file_put_contents($file, 'test');

        try {
            self::assertSame(
                [
                    'first'.DIRECTORY_SEPARATOR => [
                        'second'.DIRECTORY_SEPARATOR,
                    ],
                ],
                DirectoryHelper::map($directory, ['depth' => 2])
            );
        } finally {
            unlink($file);
            rmdir($second);
            rmdir($first);
            rmdir($directory);
        }
    }

    #[Test]
    public function map_recursesWithoutDepthLimitByDefault(): void
    {
        $directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'helpers-map';
        $first = $directory.DIRECTORY_SEPARATOR.'first';
        $second = $first.DIRECTORY_SEPARATOR.'second';
        $file = $second.DIRECTORY_SEPARATOR.'file.txt';

        mkdir($second, 0777, true);
        file_put_contents($file, 'test');

        try {
            self::assertSame(
                [
                    'first'.DIRECTORY_SEPARATOR => [
                        'second'.DIRECTORY_SEPARATOR => [
                            'file.txt',
                        ],
                    ],
                ],
                DirectoryHelper::map($directory)
            );
        } finally {
            unlink($file);
            rmdir($second);
            rmdir($first);
            rmdir($directory);
        }
    }

    #[Test]
    public function map_returnsFullPathsRecursivelyWhenFilepathOptionIsEnabled(): void
    {
        $directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'helpers-map';
        $subDirectory = $directory.DIRECTORY_SEPARATOR.'sub';
        $file = $subDirectory.DIRECTORY_SEPARATOR.'file.txt';

        mkdir($subDirectory, 0777, true);
        file_put_contents($file, 'test');

        try {
            self::assertSame(
                [
                    'sub'.DIRECTORY_SEPARATOR => [
                        $file,
                    ],
                ],
                DirectoryHelper::map($directory, ['filepath' => true])
            );
        } finally {
            unlink($file);
            rmdir($subDirectory);
            rmdir($directory);
        }
    }

    #[Test]
    public function create_createsNestedDirectories(): void
    {
        $directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'helpers'
            .DIRECTORY_SEPARATOR.'first'
            .DIRECTORY_SEPARATOR.'second';

        try {
            self::assertTrue(DirectoryHelper::create($directory));
            self::assertDirectoryExists($directory);
        } finally {
            @rmdir($directory);
            @rmdir(dirname($directory));
            @rmdir(dirname(dirname($directory)));
        }
    }

    #[Test]
    public function create_returnsTrueForExistingDirectory(): void
    {
        $directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'helpers-existing';

        mkdir($directory);

        try {
            self::assertTrue(DirectoryHelper::create($directory));
        } finally {
            rmdir($directory);
        }
    }

    #[Test]
    public function create_returnsFalseWhenPathIsAFile(): void
    {
        $filepath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'helpers-file';

        file_put_contents($filepath, 'test');

        try {
            self::assertFalse(DirectoryHelper::create($filepath));
        } finally {
            unlink($filepath);
        }
    }

    #[Test]
    public function create_handlesTrailingSlash(): void
    {
        $directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'helpers-trailing';

        try {
            self::assertTrue(DirectoryHelper::create($directory.DIRECTORY_SEPARATOR));
            self::assertDirectoryExists($directory);
        } finally {
            @rmdir($directory);
        }
    }

    #[Test]
    public function create_handlesRepeatedSlashes(): void
    {
        $base = sys_get_temp_dir();
        $directory = $base.DIRECTORY_SEPARATOR.'helpers-slashes';
        $path = $base.'//helpers-slashes//nested';

        try {
            self::assertTrue(DirectoryHelper::create($path));
            self::assertDirectoryExists($directory.DIRECTORY_SEPARATOR.'nested');
        } finally {
            @rmdir($directory.DIRECTORY_SEPARATOR.'nested');
            @rmdir($directory);
        }
    }

    #[Test]
    public function getFilesInfo_returnsEmptyArrayForMissingDirectory(): void
    {
        $directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'missing-directory';

        self::assertSame([], DirectoryHelper::getFilesInfo($directory));
    }

    #[Test]
    public function getFilesInfo_returnsTopLevelFiles(): void
    {
        $directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'helpers-info';
        $file = $directory.DIRECTORY_SEPARATOR.'file.txt';

        mkdir($directory);
        file_put_contents($file, 'Hello World');

        try {
            $result = DirectoryHelper::getFilesInfo($directory);

            self::assertArrayHasKey('file.txt', $result);
            self::assertSame('file.txt', $result['file.txt']['name']);
            self::assertSame(11, $result['file.txt']['size']);
            self::assertSame(realpath($file), $result['file.txt']['server_path']);
            self::assertSame($directory, $result['file.txt']['relative_path']);
        } finally {
            unlink($file);
            rmdir($directory);
        }
    }

    #[Test]
    public function getFilesInfo_returnsOnlyTopLevelFilesByDefault(): void
    {
        $directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'helpers-info';
        $subDirectory = $directory.DIRECTORY_SEPARATOR.'sub';
        $file = $subDirectory.DIRECTORY_SEPARATOR.'file.txt';

        mkdir($subDirectory, 0777, true);
        file_put_contents($file, 'test');

        try {
            $result = DirectoryHelper::getFilesInfo($directory);

            self::assertArrayHasKey('sub', $result);
            self::assertArrayNotHasKey('file.txt', $result);
        } finally {
            unlink($file);
            rmdir($subDirectory);
            rmdir($directory);
        }
    }

    #[Test]
    public function getFilesInfo_returnsNestedFilesWhenRecursionIsEnabled(): void
    {
        $directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'helpers-info';
        $subDirectory = $directory.DIRECTORY_SEPARATOR.'sub';
        $file = $subDirectory.DIRECTORY_SEPARATOR.'file.txt';

        mkdir($subDirectory, 0777, true);
        file_put_contents($file, 'test');

        try {
            $result = DirectoryHelper::getFilesInfo($directory, false);

            self::assertArrayHasKey('file.txt', $result);
            self::assertSame(realpath($file), $result['file.txt']['server_path']);
            self::assertSame(
                realpath($subDirectory).DIRECTORY_SEPARATOR,
                $result['file.txt']['relative_path']
            );
        } finally {
            unlink($file);
            rmdir($subDirectory);
            rmdir($directory);
        }
    }

    #[Test]
    public function getFilesInfo_ignoresHiddenEntries(): void
    {
        $directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'helpers-info';
        $visible = $directory.DIRECTORY_SEPARATOR.'visible.txt';
        $hidden = $directory.DIRECTORY_SEPARATOR.'.hidden.txt';

        mkdir($directory);
        file_put_contents($visible, 'visible');
        file_put_contents($hidden, 'hidden');

        try {
            $result = DirectoryHelper::getFilesInfo($directory);

            self::assertArrayHasKey('visible.txt', $result);
            self::assertArrayNotHasKey('.hidden.txt', $result);
        } finally {
            unlink($visible);
            unlink($hidden);
            rmdir($directory);
        }
    }

    #[Test]
    public function deleteFiles_returnsFalseWhenDirectoryCannotBeOpened(): void
    {
        $directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'missing-directory';

        self::assertFalse(DirectoryHelper::deleteFiles($directory));
    }

    #[Test]
    public function deleteFiles_deletesFilesFromDirectory(): void
    {
        $directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'helpers-delete';
        $file = $directory.DIRECTORY_SEPARATOR.'file.txt';

        mkdir($directory);
        file_put_contents($file, 'test');

        try {
            self::assertTrue(DirectoryHelper::deleteFiles($directory));
            self::assertFileDoesNotExist($file);
            self::assertDirectoryExists($directory);
        } finally {
            @unlink($file);
            @rmdir($directory);
        }
    }

    #[Test]
    public function deleteFiles_deletesFilesRecursively(): void
    {
        $directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'helpers-delete';
        $subDirectory = $directory.DIRECTORY_SEPARATOR.'sub';
        $file = $subDirectory.DIRECTORY_SEPARATOR.'file.txt';

        mkdir($subDirectory, 0777, true);
        file_put_contents($file, 'test');

        try {
            self::assertTrue(DirectoryHelper::deleteFiles($directory));
            self::assertFileDoesNotExist($file);
            self::assertDirectoryExists($subDirectory);
            self::assertDirectoryExists($directory);
        } finally {
            @unlink($file);
            @rmdir($subDirectory);
            @rmdir($directory);
        }
    }

    #[Test]
    public function deleteFiles_deletesDirectoriesWhenRequested(): void
    {
        $directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'helpers-delete';
        $subDirectory = $directory.DIRECTORY_SEPARATOR.'sub';
        $file = $subDirectory.DIRECTORY_SEPARATOR.'file.txt';

        mkdir($subDirectory, 0777, true);
        file_put_contents($file, 'test');

        try {
            self::assertTrue(DirectoryHelper::deleteFiles($directory, true));

            self::assertFileDoesNotExist($file);
            self::assertDirectoryDoesNotExist($subDirectory);
            self::assertDirectoryExists($directory);
        } finally {
            @unlink($file);
            @rmdir($subDirectory);
            @rmdir($directory);
        }
    }

    #[Test]
    public function deleteFiles_preservesHtdocsFilesWhenHtdocsModeIsEnabled(): void
    {
        $directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'helpers-delete';

        mkdir($directory);
        file_put_contents($directory.DIRECTORY_SEPARATOR.'.htaccess', 'test');
        file_put_contents($directory.DIRECTORY_SEPARATOR.'index.php', 'test');
        file_put_contents($directory.DIRECTORY_SEPARATOR.'file.txt', 'test');

        try {
            self::assertTrue(
                DirectoryHelper::deleteFiles($directory, false, true)
            );

            self::assertFileExists($directory.DIRECTORY_SEPARATOR.'.htaccess');
            self::assertFileExists($directory.DIRECTORY_SEPARATOR.'index.php');
            self::assertFileDoesNotExist($directory.DIRECTORY_SEPARATOR.'file.txt');
        } finally {
            @unlink($directory.DIRECTORY_SEPARATOR.'.htaccess');
            @unlink($directory.DIRECTORY_SEPARATOR.'index.php');
            @unlink($directory.DIRECTORY_SEPARATOR.'file.txt');
            @rmdir($directory);
        }
    }

    #[Test]
    public function deleteFiles_deletesHtdocsFilesWhenHtdocsModeIsDisabled(): void
    {
        $directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'helpers-delete';

        mkdir($directory);
        file_put_contents($directory.DIRECTORY_SEPARATOR.'.htaccess', 'test');
        file_put_contents($directory.DIRECTORY_SEPARATOR.'index.php', 'test');

        try {
            self::assertTrue(DirectoryHelper::deleteFiles($directory));

            self::assertFileDoesNotExist($directory.DIRECTORY_SEPARATOR.'.htaccess');
            self::assertFileDoesNotExist($directory.DIRECTORY_SEPARATOR.'index.php');
        } finally {
            @unlink($directory.DIRECTORY_SEPARATOR.'.htaccess');
            @unlink($directory.DIRECTORY_SEPARATOR.'index.php');
            @rmdir($directory);
        }
    }

    #[Test]
    public function deleteFiles_deletesDirectorySymlinkWithoutFollowingIt(): void
    {
        $directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'helpers-delete';
        $target = sys_get_temp_dir().DIRECTORY_SEPARATOR.'helpers-target';
        $link = $directory.DIRECTORY_SEPARATOR.'link';

        mkdir($directory);
        mkdir($target);

        if (symlink($target, $link) === false) {
            rmdir($target);
            rmdir($directory);
            self::markTestSkipped('Symbolic links are not supported.');
        }

        try {
            self::assertTrue(DirectoryHelper::deleteFiles($directory));
            self::assertFileDoesNotExist($link);
            self::assertDirectoryExists($target);
        } finally {
            @unlink($link);
            @rmdir($target);
            @rmdir($directory);
        }
    }
}
