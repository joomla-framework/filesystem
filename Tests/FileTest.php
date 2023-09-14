<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Filesystem\Tests;

use Joomla\Filesystem\File;

/**
 * Test class for Joomla\Filesystem\File.
 */
class FileTest extends FilesystemTestCase
{
	/**
	 * Provides the data to test the stripExt method.
	 *
	 * @return  \Generator
	 */
	public function dataTestStripExt(): \Generator
	{
		yield [
			'foobar.php',
			'foobar',
		];

		yield [
			'foobar..php',
			'foobar.',
		];

		yield [
			'foobar.php.',
			'foobar.php',
		];
	}

	/**
	 * Test stripExt method
	 *
	 * @param   string  $fileName        The name of the file with extension
	 * @param   string  $nameWithoutExt  Name without extension
	 *
	 * @dataProvider  dataTestStripExt
	 */
	public function testStripExt(string $fileName, string $nameWithoutExt): void
	{
		$this->assertEquals(
			File::stripExt($fileName),
			$nameWithoutExt,
			'File extension should be stripped'
		);
	}

	/**
	 * Provides the data to test the makeSafe method.
	 *
	 * @return  \Generator
	 */
	public function dataTestMakeSafe(): \Generator
	{
		yield [
			'joomla.',
			['#^\.#'],
			'joomla',
			'There should be no fullstop on the end of a filename',
		];

		yield [
			'Test j00mla_5-1.html',
			['#^\.#'],
			'Test j00mla_5-1.html',
			'Alphanumeric symbols, dots, dashes, spaces and underscores should not be filtered',
		];

		yield [
			'Test j00mla_5-1.html',
			['#^\.#', '/\s+/'],
			'Testj00mla_5-1.html',
			'Using strip chars parameter here to strip all spaces',
		];

		yield [
			'joomla.php!.',
			['#^\.#'],
			'joomla.php',
			'Non-alphanumeric symbols should be filtered to avoid disguising file extensions',
		];

		yield [
			'joomla.php.!',
			['#^\.#'],
			'joomla.php',
			'Non-alphanumeric symbols should be filtered to avoid disguising file extensions',
		];

		yield [
			'.gitignore',
			[],
			'.gitignore',
			'Files starting with a fullstop should be allowed when strip chars parameter is empty',
		];

		$longName = str_repeat('0123456789abcdef', 16);
		yield [
			$longName . '.png',
			[],
			substr($longName, 0, 236) . '.png',
			'Filenames with more than 240 characters should be shortened to 240 characters preserving the extension',
		];
	}

	/**
	 * Test makeSafe method.
	 *
	 * @param   string  $name        The name of the file to test filtering of
	 * @param   array   $stripChars  Whether to filter spaces out the name or not
	 * @param   string  $expected    The expected safe file name
	 * @param   string  $message     The message to show on failure of test
	 *
	 * @covers        \Joomla\Filesystem\File::makeSafe
	 * @dataProvider  dataTestMakeSafe
	 */
	public function testMakeSafe(string $name, array $stripChars, string $expected, string $message): void
	{
		$this->assertEquals($expected, File::makeSafe($name, $stripChars), $message);
	}

	/**
	 * Test copy method.
	 */
	public function testCopyWithPathArgPassed(): void
	{
		$name       = 'tempFile';
		$copiedName = 'tempCopiedFileName';
		$data       = 'Lorem ipsum dolor sit amet';

		if (!File::write($this->testPath . '/' . $name, $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$this->assertTrue(
			File::copy($name, $copiedName, $this->testPath),
			'The file was not copied.'
		);

		$this->assertFileEquals(
			$this->testPath . '/' . $name,
			$this->testPath . '/' . $copiedName,
			'Content should remain intact after copy.'
		);
	}

	/**
	 * Test copy method.
	 */
	public function testCopyWithoutPathArgPassed(): void
	{
		$name       = 'tempFile';
		$copiedName = 'tempCopiedFileName';
		$data       = 'Lorem ipsum dolor sit amet';

		if (!File::write($this->testPath . '/' . $name, $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$this->assertTrue(
			File::copy($this->testPath . '/' . $name, $this->testPath . '/' . $copiedName),
			'The file was not copied.'
		);

		$this->assertFileEquals(
			$this->testPath . '/' . $name,
			$this->testPath . '/' . $copiedName,
			'Content should remain intact after copy.'
		);
	}

	/**
	 * Test copy method using streams.
	 */
	public function testCopyWithStreams(): void
	{
		$name       = 'tempFile';
		$copiedName = 'tempCopiedFileName';
		$data       = 'Lorem ipsum dolor sit amet';

		if (!File::write($this->testPath . '/' . $name, $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$this->assertTrue(
			File::copy($name, $copiedName, $this->testPath, true),
			'The file was not copied.'
		);

		$this->assertFileEquals(
			$this->testPath . '/' . $name,
			$this->testPath . '/' . $copiedName,
			'Content should remain intact after copy.'
		);
	}

	/**
	 * Test makeCopy method for an exception
	 */
	public function testCopySrcDontExist(): void
	{
		$this->expectException(\UnexpectedValueException::class);

		$name       = 'tempFile';
		$copiedName = 'tempCopiedFileName';

		File::copy($name, $copiedName, $this->testPath);
	}

	/**
	 * Test delete method.
	 */
	public function testDeleteForSingleFile(): void
	{
		$name = 'tempFile';
		$data = 'Lorem ipsum dolor sit amet';

		if (!File::write($this->testPath . '/' . $name, $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$this->assertTrue(
			File::delete($this->testPath . '/' . $name),
			'The file was not deleted.'
		);
	}

	/**
	 * Test delete method.
	 */
	public function testDeleteForArrayOfFiles(): void
	{
		$name1 = 'tempFile1';
		$name2 = 'tempFile2';
		$data  = 'Lorem ipsum dolor sit amet';

		if (!File::write($this->testPath . '/' . $name1, $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		if (!File::write($this->testPath . '/' . $name2, $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$this->assertTrue(
			File::delete([$this->testPath . '/' . $name1, $this->testPath . '/' . $name2]),
			'The files were not deleted.'
		);
	}

	/**
	 * Tests the File::move method.
	 */
	public function testMoveWithPathArgPassed(): void
	{
		$name      = 'tempFile';
		$movedName = 'tempCopiedFileName';
		$data      = 'Lorem ipsum dolor sit amet';

		if (!File::write($this->testPath . '/' . $name, $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$this->assertTrue(
			File::move($name, $movedName, $this->testPath),
			'The test file was not moved.'
		);
	}

	/**
	 * Tests the File::move method.
	 */
	public function testMoveWithoutPathArgPassed(): void
	{
		$name      = 'tempFile';
		$movedName = 'tempCopiedFileName';
		$data      = 'Lorem ipsum dolor sit amet';

		if (!File::write($this->testPath . '/' . $name, $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$this->assertTrue(
			File::move($this->testPath . '/' . $name, $this->testPath . '/' . $movedName),
			'The test file was not moved.'
		);
	}

	/**
	 * Tests the File::move method.
	 */
	public function testMoveWithStreams(): void
	{
		$name      = 'tempFile';
		$movedName = 'tempCopiedFileName';
		$data      = 'Lorem ipsum dolor sit amet';

		if (!File::write($this->testPath . '/' . $name, $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$this->assertTrue(
			File::move($name, $movedName, $this->testPath, true),
			'The test directory was not moved.'
		);
	}

	/**
	 * Test the File::move method where source file doesn't exist.
	 */
	public function testMoveSrcDontExist(): void
	{
		$name      = 'tempFile';
		$movedName = 'tempCopiedFileName';

		$this->assertSame(
			'Cannot find source file.',
			File::move($name, $movedName, $this->testPath)
		);
	}

	/**
	 * Test write method.
	 */
	public function testWrite(): void
	{
		$name = 'tempFile';
		$data = 'Lorem ipsum dolor sit amet';

		$this->assertTrue(
			File::write($this->testPath . '/' . $name, $data),
			'The file was not written.'
		);

		$this->assertStringEqualsFile(
			$this->testPath . '/' . $name,
			$data,
			'The written file should match the given content.'
		);
	}

	/**
	 * Test write method when appending to a file.
	 */
	public function testWriteWithAppend(): void
	{
		$name       = 'tempFile.txt';
		$data       = 'Lorem ipsum dolor sit amet';
		$appendData = PHP_EOL . $data;

		$this->assertTrue(
			File::write($this->testPath . '/' . $name, $data),
			'The file was not written.'
		);

		$this->assertTrue(
			File::write($this->testPath . '/' . $name, $appendData, false, true),
			'The file was not appended.'
		);

		$this->assertStringEqualsFile(
			$this->testPath . '/' . $name,
			$data . $appendData,
			'The written file should match the given content.'
		);
	}

	/**
	 * Test write method.
	 */
	public function testWriteCreatesMissingDirectory(): void
	{
		$name = 'tempFile';
		$data = 'Lorem ipsum dolor sit amet';

		$this->assertTrue(
			File::write($this->testPath . '/' . $name . '/' . $name, $data),
			'The file was not written.'
		);

		$this->assertStringEqualsFile(
			$this->testPath . '/' . $name . '/' . $name,
			$data,
			'The written file should match the given content.'
		);
	}

	/**
	 * Test write method.
	 */
	public function testWriteWithStreams(): void
	{
		$name = 'tempFile';
		$data = 'Lorem ipsum dolor sit amet';

		$this->assertTrue(
			File::write($this->testPath . '/' . $name, $data, true),
			'The file was not written.'
		);

		$this->assertStringEqualsFile(
			$this->testPath . '/' . $name,
			$data,
			'The written file should match the given content.'
		);
	}

	/**
	 * Test upload method.
	 *
	 * @backupGlobals enabled
	 */
	public function testUpload(): void
	{
		include_once __DIR__ . '/Stubs/PHPUploadStub.php';

		$name = 'tempFile';
		$data = 'Lorem ipsum dolor sit amet';
		$uploadedFileName = 'uploadedFileName';

		if (!File::write($this->testPath . '/' . $name, $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$_FILES = [
			'test' => [
				'name'     => 'test.jpg',
				'tmp_name' => $this->testPath . '/' . $name,
			],
		];

		$this->assertTrue(
			File::upload($this->testPath . '/' . $name, $this->testPath . '/' . $uploadedFileName)
		);
	}

	/**
	 * Test upload method.
	 *
	 * @backupGlobals enabled
	 */
	public function testUploadWithStreams(): void
	{
		include_once __DIR__ . '/Stubs/PHPUploadStub.php';

		$name = 'tempFile';
		$data = 'Lorem ipsum dolor sit amet';
		$uploadedFileName = 'uploadedFileName';

		if (!File::write($this->testPath . '/' . $name, $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$_FILES = [
			'test' => [
				'name'     => 'test.jpg',
				'tmp_name' => $this->testPath . '/' . $name,
			],
		];

		$this->assertTrue(
			File::upload($this->testPath . '/' . $name, $this->testPath . '/' . $uploadedFileName, true)
		);
	}

	/**
	 * Test upload method.
	 *
	 * @backupGlobals enabled
	 */
	public function testUploadToNestedDirectory(): void
	{
		include_once __DIR__ . '/Stubs/PHPUploadStub.php';

		$name             = 'tempFile';
		$data             = 'Lorem ipsum dolor sit amet';
		$uploadedFileName = 'uploadedFileName';

		if (!File::write($this->testPath . '/' . $name . '.txt', $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$_FILES = [
			'test' => [
				'name'     => 'test.jpg',
				'tmp_name' => $this->testPath . '/' . $name . '.txt',
			],
		];

		$this->assertTrue(
			File::upload(
				$this->testPath . '/' . $name . '.txt',
				$this->testPath . '/' . $name . '/' . $uploadedFileName
			)
		);
	}
}
