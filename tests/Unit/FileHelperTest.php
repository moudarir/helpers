<?php

declare(strict_types=1);

namespace Moudarir\Helpers\Tests\Unit;

use ErrorException;
use Moudarir\Helpers\FileHelper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FileHelperTest extends TestCase
{

    #[Test]
    public function newFilename_returnsOriginalPathWhenFileDoesNotExist(): void
    {
        $filepath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'example.txt';

        $result = FileHelper::newFilename($filepath);

        self::assertSame('example.txt', $result['file_name']);
        self::assertSame('example', $result['raw_name']);
        self::assertSame('.txt', $result['file_ext']);
        self::assertSame(sys_get_temp_dir(), $result['file_path']);
        self::assertSame($filepath, $result['full_path']);
    }

    #[Test]
    public function newFilename_preservesExtension(): void
    {
        $filepath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'example.tar.gz';

        $result = FileHelper::newFilename($filepath);

        self::assertSame('.gz', $result['file_ext']);
        self::assertSame('gz', pathinfo($result['file_name'], PATHINFO_EXTENSION));
    }

    #[Test]
    public function newFilename_generatesRandomFilenameWhenFileExists(): void
    {
        $filepath = tempnam(sys_get_temp_dir(), 'helpers-').'.txt';

        touch($filepath);

        try {
            $result = FileHelper::newFilename($filepath);

            self::assertNotSame(basename($filepath), $result['file_name']);
            self::assertSame('.txt', $result['file_ext']);
            self::assertSame(realpath(sys_get_temp_dir()), realpath($result['file_path']));
            self::assertFileDoesNotExist($result['full_path']);
        } finally {
            @unlink($filepath);
        }
    }

    #[Test]
    public function newFilename_addsNumericSuffixWhenEncryptionIsDisabled(): void
    {
        $filepath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'example.txt';

        file_put_contents($filepath, 'test');

        try {
            $result = FileHelper::newFilename($filepath, encrypt: false);

            self::assertSame('example-2.txt', $result['file_name']);
            self::assertSame('example-2', $result['raw_name']);
            self::assertSame('.txt', $result['file_ext']);
            self::assertFileDoesNotExist($result['full_path']);
        } finally {
            @unlink($filepath);
        }
    }

    #[Test]
    public function newFilename_incrementsSuffixUntilAvailableFilenameIsFound(): void
    {
        $directory = sys_get_temp_dir();
        $filepath = $directory.DIRECTORY_SEPARATOR.'example.txt';
        $second = $directory.DIRECTORY_SEPARATOR.'example-2.txt';

        file_put_contents($filepath, 'test');
        file_put_contents($second, 'test');

        try {
            $result = FileHelper::newFilename($filepath, encrypt: false);

            self::assertSame('example-3.txt', $result['file_name']);
            self::assertSame('example-3', $result['raw_name']);
            self::assertFileDoesNotExist($result['full_path']);
        } finally {
            @unlink($filepath);
            @unlink($second);
        }
    }

    #[Test]
    public function newFilename_handlesFilenameWithoutExtension(): void
    {
        $filepath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'example';

        file_put_contents($filepath, 'test');

        try {
            $result = FileHelper::newFilename($filepath, encrypt: false);

            self::assertSame('example-2', $result['file_name']);
            self::assertSame('example-2', $result['raw_name']);
            self::assertSame('', $result['file_ext']);
            self::assertFileDoesNotExist($result['full_path']);
        } finally {
            @unlink($filepath);
        }
    }

    #[Test]
    public function info_returnsEmptyArrayForMissingFile(): void
    {
        $filepath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'missing-file.txt';

        self::assertSame([], FileHelper::info($filepath));
    }

    #[Test]
    public function info_returnsBasicFileInformation(): void
    {
        $filepath = tempnam(sys_get_temp_dir(), 'helpers-');
        $content = 'Hello World';

        file_put_contents($filepath, $content);

        try {
            $info = FileHelper::info($filepath);

            self::assertSame(basename($filepath), $info['name']);
            self::assertSame($filepath, $info['server_path']);
            self::assertSame(strlen($content), $info['size']);
            self::assertIsInt($info['date']);
            self::assertGreaterThan(0, $info['date']);
        } finally {
            unlink($filepath);
        }
    }

    #[Test]
    public function info_returnsOptionalFileInformation(): void
    {
        $filepath = tempnam(sys_get_temp_dir(), 'helpers-');

        file_put_contents($filepath, 'Hello World');

        try {
            $info = FileHelper::info($filepath, true);

            self::assertArrayHasKey('readable', $info);
            self::assertArrayHasKey('executable', $info);
            self::assertArrayHasKey('fileperms', $info);

            self::assertIsBool($info['readable']);
            self::assertIsBool($info['executable']);
            self::assertIsInt($info['fileperms']);
        } finally {
            unlink($filepath);
        }
    }

    #[Test]
    public function info_doesNotReturnOptionalInformationByDefault(): void
    {
        $filepath = tempnam(sys_get_temp_dir(), 'helpers-');

        file_put_contents($filepath, 'Hello World');

        try {
            $info = FileHelper::info($filepath);

            self::assertArrayNotHasKey('readable', $info);
            self::assertArrayNotHasKey('executable', $info);
            self::assertArrayNotHasKey('fileperms', $info);
        } finally {
            unlink($filepath);
        }
    }

    #[Test]
    public function write_createsFileAndWritesData(): void
    {
        $filepath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'helpers-write.txt';
        $data = 'Hello World';

        try {
            self::assertTrue(FileHelper::write($filepath, $data));
            self::assertSame($data, file_get_contents($filepath));
        } finally {
            @unlink($filepath);
        }
    }

    #[Test]
    public function write_overwritesExistingFileByDefault(): void
    {
        $filepath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'helpers-write.txt';

        file_put_contents($filepath, 'old');

        try {
            self::assertTrue(FileHelper::write($filepath, 'new'));
            self::assertSame('new', file_get_contents($filepath));
        } finally {
            @unlink($filepath);
        }
    }

    #[Test]
    public function write_appendsDataWhenAppendModeIsUsed(): void
    {
        $filepath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'helpers-write.txt';

        file_put_contents($filepath, 'Hello');

        try {
            self::assertTrue(FileHelper::write($filepath, ' World', 'ab'));
            self::assertSame('Hello World', file_get_contents($filepath));
        } finally {
            @unlink($filepath);
        }
    }

    #[Test]
    public function write_handlesEmptyData(): void
    {
        $filepath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'helpers-write.txt';

        try {
            self::assertTrue(FileHelper::write($filepath, ''));
            self::assertSame('', file_get_contents($filepath));
        } finally {
            @unlink($filepath);
        }
    }

    #[Test]
    public function write_returnsFalseWhenFileCannotBeOpened(): void
    {
        $filepath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'missing'.DIRECTORY_SEPARATOR.'file.txt';

        self::assertFalse(FileHelper::write($filepath, 'data'));
    }

    #[Test]
    public function getContent_returnsFileContent(): void
    {
        $filepath = tempnam(sys_get_temp_dir(), 'helpers-');
        $content = 'Hello World';

        file_put_contents($filepath, $content);

        try {
            self::assertSame($content, FileHelper::getContent($filepath));
        } finally {
            unlink($filepath);
        }
    }

    #[Test]
    public function getContent_throwsExceptionWhenFileDoesNotExist(): void
    {
        $filepath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'missing-file.txt';

        $this->expectException(ErrorException::class);
        $this->expectExceptionMessageIsOrContains("The file `$filepath` was not found.");

        FileHelper::getContent($filepath);
    }

    #[Test]
    public function getContent_acceptsUrlWhenUrlModeIsEnabled(): void
    {
        $filepath = tempnam(sys_get_temp_dir(), 'helpers-');
        $content = 'Hello World';

        file_put_contents($filepath, $content);

        try {
            self::assertSame($content, FileHelper::getContent($filepath, true));
        } finally {
            unlink($filepath);
        }
    }

    #[Test]
    public function getContent_throwsExceptionWhenUrlCannotBeRead(): void
    {
        $url = 'file:///this/path/does/not/exist';

        $this->expectException(ErrorException::class);
        $this->expectExceptionMessageIsOrContains("Unable to read the contents of `$url`.");

        FileHelper::getContent($url, true);
    }

    #[Test]
    public function saveContent_writesFileContent(): void
    {
        $filepath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'helpers-save.txt';
        $content = 'Hello World';

        try {
            self::assertTrue(FileHelper::saveContent($filepath, $content));
            self::assertSame($content, file_get_contents($filepath));
        } finally {
            @unlink($filepath);
        }
    }

    #[Test]
    public function saveContent_replacesExistingContent(): void
    {
        $filepath = tempnam(sys_get_temp_dir(), 'helpers-');

        file_put_contents($filepath, 'old');

        try {
            self::assertTrue(FileHelper::saveContent($filepath, 'new'));
            self::assertSame('new', file_get_contents($filepath));
        } finally {
            unlink($filepath);
        }
    }

    #[Test]
    public function saveContent_handlesEmptyContent(): void
    {
        $filepath = tempnam(sys_get_temp_dir(), 'helpers-');

        file_put_contents($filepath, 'old');

        try {
            self::assertTrue(FileHelper::saveContent($filepath, ''));
            self::assertSame('', file_get_contents($filepath));
        } finally {
            unlink($filepath);
        }
    }

    #[Test]
    public function saveContent_throwsExceptionWhenFileCannotBeSaved(): void
    {
        $filepath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'missing'.DIRECTORY_SEPARATOR.'file.txt';

        $this->expectException(ErrorException::class);
        $this->expectExceptionMessageIsOrContains("Unable to save the contents of `$filepath`.");

        FileHelper::saveContent($filepath, 'data');
    }
}
