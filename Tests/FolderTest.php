<?php

/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Filesystem\Tests;

use Joomla\Filesystem\Exception\FilesystemException;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Joomla\Filesystem\Path;

/**
 * Test class for Joomla\Filesystem\Folder.
 */
class FolderTest extends FilesystemTestCase
{
    /**
     * Tests the Folder::copy method.
     */
    public function testCopyWithPathArgPassed()
    {
        $name             = 'tempFolder';
        $copiedFolderName = 'tempCopiedFolderName';

        if (!Folder::create($this->testPath . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        $this->assertTrue(
            Folder::copy($name, $copiedFolderName, $this->testPath),
            'The test directory was not copied.'
        );
    }

    /**
     * Tests the Folder::copy method.
     */
    public function testCopyWithoutPathArgPassed()
    {
        $name             = 'tempFolder';
        $copiedFolderName = 'tempCopiedFolderName';

        if (!Folder::create($this->testPath . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        $this->assertTrue(
            Folder::copy($this->testPath . '/' . $name, $this->testPath . '/' . $copiedFolderName),
            'The test directory was not copied.'
        );
    }
    /**
     * Tests the Folder::copy method.
     */
    public function testCopyWithStreams()
    {
        $name             = 'tempFolder';
        $copiedFolderName = 'tempCopiedFolderName';

        if (!Folder::create($this->testPath . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        $this->assertTrue(
            Folder::copy($name, $copiedFolderName, $this->testPath, false, true),
            'The test directory was not copied.'
        );
    }


    /**
     * Test the Folder::copy method where source folder doesn't exist.
     */
    public function testCopySrcDontExist()
    {
        $this->expectException(FilesystemException::class);

        $name             = 'tempFolder';
        $copiedFolderName = 'tempCopiedFolderName';

        Folder::copy($name, $copiedFolderName, $this->testPath);
    }

    /**
     * Test the Folder::copy method where destination folder exists and the copy is forced.
     */
    public function testCopyDestExistAndForced()
    {
        $name             = 'tempFolder';
        $copiedFolderName = 'tempCopiedFolderName';

        if (!Folder::create($this->testPath . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!Folder::create($this->testPath . '/' . $copiedFolderName)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        $this->assertTrue(
            Folder::copy($name, $copiedFolderName, $this->testPath, true),
            'The test directory was not forcibly copied.'
        );
    }

    /**
     * Test the Folder::copy method where destination folder exists and the copy is not forced.
     */
    public function testCopyDestExistAndNotForced()
    {
        $this->expectException(FilesystemException::class);

        $name             = 'tempFolder';
        $copiedFolderName = 'tempCopiedFolderName';

        if (!Folder::create($this->testPath . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!Folder::create($this->testPath . '/' . $copiedFolderName)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        Folder::copy($name, $copiedFolderName, $this->testPath, false);
    }

    /**
     * Tests the Folder::create method for a nested directory.
     */
    public function testCreateNested()
    {
        $this->assertTrue(
            Folder::create($this->testPath . '/tempFolder/subTempFolder'),
            'The nested directory was not created.'
        );
    }

    /**
     * Tests the Folder::create method for a potential infinite loop.
     */
    public function testCreateInfiniteLoopException()
    {
        $this->expectException(FilesystemException::class);

        Folder::create($this->testPath . '/a/b/c/d/e/f/g/h/i/j/k/l/m/n/o/p/q/r/s/t/u/v/w/x/y/z');
    }

    /**
     * Tests the Folder::delete method for a nested directory.
     */
    public function testDeleteRecursive()
    {
        $name = 'tempFolder';
        $data = 'Lorem ipsum dolor sit amet';

        if (!Folder::create($this->testPath . '/' . $name . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!File::write($this->testPath . '/' . $name . '/' . $name . '/' . $name . '.txt', $data)) {
            $this->markTestSkipped('The test file could not be created.');
        }

        $this->assertTrue(
            Folder::delete($this->testPath . '/' . $name),
            'The test directory and its children were not deleted.'
        );
    }

    /**
     * Tests the Folder::delete method blocks removal of the root directory.
     */
    public function testDeleteBaseDir()
    {
        $this->expectException(FilesystemException::class);

        Folder::delete('');
    }

    /**
     * Tests the Folder::move method.
     */
    public function testMoveWithPathArgPassed()
    {
        $name            = 'tempFolder';
        $movedFolderName = 'tempMovedFolderName';

        if (!Folder::create($this->testPath . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        $this->assertTrue(
            Folder::move($name, $movedFolderName, $this->testPath),
            'The test directory was not moved.'
        );
    }

    /**
     * Tests the Folder::move method.
     */
    public function testMoveWithoutPathArgPassed()
    {
        $name            = 'tempFolder';
        $movedFolderName = 'tempMovedFolderName';

        if (!Folder::create($this->testPath . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        $this->assertTrue(
            Folder::move($this->testPath . '/' . $name, $this->testPath . '/' . $movedFolderName),
            'The test directory was not moved.'
        );
    }

    /**
     * Tests the Folder::move method.
     */
    public function testMoveWithStreams()
    {
        $this->markTestSkipped('Need to debug internals');

        $name            = 'tempFolder';
        $movedFolderName = 'tempMovedFolderName';

        if (!Folder::create($this->testPath . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        $this->assertTrue(
            Folder::move($name, $movedFolderName, $this->testPath, true),
            'The test directory was not moved.'
        );
    }

    /**
     * Test the Folder::move method where source folder doesn't exist.
     *
     * @return void
     *
     * @since   1.0
     */
    public function testMoveSrcDontExist()
    {
        $name            = 'tempFolder';
        $movedFolderName = 'tempMovedFolderName';

        $this->assertSame(
            'Cannot find source folder',
            Folder::move($name, $movedFolderName, $this->testPath)
        );
    }

    /**
     * Test the Folder::move method where destination folder exists and the move is not forced.
     *
     * @return void
     *
     * @since   1.0
     */
    public function testMoveDestExist()
    {
        $name            = 'tempFolder';
        $movedFolderName = 'tempMovedFolderName';

        if (!Folder::create($this->testPath . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!Folder::create($this->testPath . '/' . $movedFolderName)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        $this->assertSame(
            'Folder already exists',
            Folder::move($name, $movedFolderName, $this->testPath)
        );
    }

    /**
     * Tests the Folder::files method.
     */
    public function testFiles()
    {
        $name = 'tempFolder';
        $data = 'Lorem ipsum dolor sit amet';

        if (!Folder::create($this->testPath . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!File::write($this->testPath . '/' . $name . '/index.txt', $data)) {
            $this->markTestSkipped('The test file could not be created.');
        }

        if (!File::write($this->testPath . '/' . $name . '/index.html', $data)) {
            $this->markTestSkipped('The test file could not be created.');
        }

        $this->assertSame(
            [
                'index.html',
                'index.txt',
            ],
            Folder::files($this->testPath . '/' . $name),
            'The files within a directory should be listed'
        );
    }

    /**
     * Tests the Folder::files method.
     */
    public function testFilesWithExcludeList()
    {
        $name = 'tempFolder';
        $data = 'Lorem ipsum dolor sit amet';

        if (!Folder::create($this->testPath . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!File::write($this->testPath . '/' . $name . '/index.txt', $data)) {
            $this->markTestSkipped('The test file could not be created.');
        }

        if (!File::write($this->testPath . '/' . $name . '/index.html', $data)) {
            $this->markTestSkipped('The test file could not be created.');
        }

        $this->assertSame(
            [
                'index.txt',
            ],
            Folder::files($this->testPath . '/' . $name, '.', false, false, ['index.html']),
            'The files within a directory should be listed unless excluded'
        );
    }

    /**
     * Tests the Folder::files method.
     */
    public function testFilesWithFullPath()
    {
        $name = 'tempFolder';
        $data = 'Lorem ipsum dolor sit amet';

        if (!Folder::create($this->testPath . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!File::write($this->testPath . '/' . $name . '/index.txt', $data)) {
            $this->markTestSkipped('The test file could not be created.');
        }

        if (!File::write($this->testPath . '/' . $name . '/index.html', $data)) {
            $this->markTestSkipped('The test file could not be created.');
        }

        $this->assertSame(
            [
                Path::clean($this->testPath . '/' . $name . '/index.html'),
                Path::clean($this->testPath . '/' . $name . '/index.txt'),
            ],
            Folder::files($this->testPath . '/' . $name, '.', false, true),
            'Files should be listed with their full paths'
        );
    }

    /**
     * Tests the Folder::files method.
     */
    public function testFilesWithFilter()
    {
        $name = 'tempFolder';
        $data = 'Lorem ipsum dolor sit amet';

        if (!Folder::create($this->testPath . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!File::write($this->testPath . '/' . $name . '/index.txt', $data)) {
            $this->markTestSkipped('The test file could not be created.');
        }

        if (!File::write($this->testPath . '/' . $name . '/index.html', $data)) {
            $this->markTestSkipped('The test file could not be created.');
        }

        $this->assertSame(
            [
                'index.html',
            ],
            Folder::files($this->testPath . '/' . $name, 'index.html'),
            'Only files matching the filter should be listed'
        );
    }

    /**
     * Tests the Folder::files method.
     */
    public function testFilesWithRecursiveFilter()
    {
        $name = 'tempFolder';
        $data = 'Lorem ipsum dolor sit amet';

        if (!Folder::create($this->testPath . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!Folder::create($this->testPath . '/' . $name . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!File::write($this->testPath . '/' . $name . '/index.txt', $data)) {
            $this->markTestSkipped('The test file could not be created.');
        }

        if (!File::write($this->testPath . '/' . $name . '/index.html', $data)) {
            $this->markTestSkipped('The test file could not be created.');
        }

        if (!File::write($this->testPath . '/' . $name . '/' . $name . '/index.txt', $data)) {
            $this->markTestSkipped('The test file could not be created.');
        }

        if (!File::write($this->testPath . '/' . $name . '/' . $name . '/index.html', $data)) {
            $this->markTestSkipped('The test file could not be created.');
        }

        $this->assertSame(
            [
                'index.html',
                'index.html',
            ],
            Folder::files($this->testPath . '/' . $name, 'index.html', true),
            'Only files matching the filter in nested directories should be listed'
        );
    }

    /**
     * Tests the Folder::files method.
     */
    public function testFilesWithRecursiveFullPath()
    {
        $name = 'tempFolder';
        $data = 'Lorem ipsum dolor sit amet';

        if (!Folder::create($this->testPath . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!Folder::create($this->testPath . '/' . $name . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!File::write($this->testPath . '/' . $name . '/index.txt', $data)) {
            $this->markTestSkipped('The test file could not be created.');
        }

        if (!File::write($this->testPath . '/' . $name . '/index.html', $data)) {
            $this->markTestSkipped('The test file could not be created.');
        }

        if (!File::write($this->testPath . '/' . $name . '/' . $name . '/index.txt', $data)) {
            $this->markTestSkipped('The test file could not be created.');
        }

        if (!File::write($this->testPath . '/' . $name . '/' . $name . '/index.html', $data)) {
            $this->markTestSkipped('The test file could not be created.');
        }

        $this->assertSame(
            [
                Path::clean($this->testPath . '/' . $name . '/index.html'),
                Path::clean($this->testPath . '/' . $name . '/index.txt'),
                Path::clean($this->testPath . '/' . $name . '/' . $name . '/index.html'),
                Path::clean($this->testPath . '/' . $name . '/' . $name . '/index.txt'),
            ],
            Folder::files($this->testPath . '/' . $name, '.', true, true),
            'Files in all nested directories should be listed with their full paths'
        );
    }

    /**
     * Tests the Folder::files method.
     */
    public function testFilesWithNonexistingDirectory()
    {
        $this->expectException(\UnexpectedValueException::class);

        $name = 'tempFolder';

        Folder::files($this->testPath . '/' . $name);
    }

    /**
     * Tests the Folder::folders method.
     */
    public function testFolders()
    {
        $name = 'tempFolder';

        if (!Folder::create($this->testPath . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        $this->assertSame(
            [
                $name,
            ],
            Folder::folders($this->testPath),
            'The folders within a directory should be listed'
        );
    }

    /**
     * Tests the Folder::folders method.
     */
    public function testFoldersWithExcludeList()
    {
        $name        = 'tempFolder';
        $excludeName = 'otherFolder';

        if (!Folder::create($this->testPath . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!Folder::create($this->testPath . '/' . $excludeName)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        $this->assertSame(
            [
                $name,
            ],
            Folder::folders($this->testPath, '.', false, false, [$excludeName]),
            'The folders within a directory should be listed unless excluded'
        );
    }

    /**
     * Tests the Folder::folders method.
     */
    public function testFoldersWithFullPath()
    {
        $name = 'tempFolder';

        if (!Folder::create($this->testPath . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        $this->assertSame(
            [
                Path::clean($this->testPath . '/' . $name),
            ],
            Folder::folders($this->testPath, '.', false, true),
            'Folders should be listed with their full paths'
        );
    }

    /**
     * Tests the Folder::folders method.
     */
    public function testFoldersWithFilter()
    {
        $name        = 'tempFolder';
        $excludeName = 'otherFolder';

        if (!Folder::create($this->testPath . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!Folder::create($this->testPath . '/' . $excludeName)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        $this->assertSame(
            [
                $name,
            ],
            Folder::folders($this->testPath, $name),
            'Only folders matching the filter should be listed'
        );
    }

    /**
     * Tests the Folder::folders method.
     */
    public function testFoldersWithRecursiveFilter()
    {
        $name        = 'tempFolder';
        $excludeName = 'otherFolder';

        if (!Folder::create($this->testPath . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!Folder::create($this->testPath . '/' . $excludeName)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!Folder::create($this->testPath . '/' . $name . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!Folder::create($this->testPath . '/' . $name . '/' . $excludeName)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!Folder::create($this->testPath . '/' . $excludeName . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!Folder::create($this->testPath . '/' . $excludeName . '/' . $excludeName)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        $this->assertSame(
            [
                $name,
                $name,
                $name,
            ],
            Folder::folders($this->testPath, $name, true),
            'Only folders matching the filter in nested directories should be listed'
        );
    }

    /**
     * Tests the Folder::folders method.
     */
    public function testFoldersWithRecursiveFullPath()
    {
        $name        = 'tempFolder';
        $excludeName = 'otherFolder';

        if (!Folder::create($this->testPath . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!Folder::create($this->testPath . '/' . $excludeName)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!Folder::create($this->testPath . '/' . $name . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!Folder::create($this->testPath . '/' . $name . '/' . $excludeName)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!Folder::create($this->testPath . '/' . $excludeName . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!Folder::create($this->testPath . '/' . $excludeName . '/' . $excludeName)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        $this->assertSame(
            [
                Path::clean($this->testPath . '/' . $excludeName),
                Path::clean($this->testPath . '/' . $excludeName . '/' . $excludeName),
                Path::clean($this->testPath . '/' . $excludeName . '/' . $name),
                Path::clean($this->testPath . '/' . $name),
                Path::clean($this->testPath . '/' . $name . '/' . $excludeName),
                Path::clean($this->testPath . '/' . $name . '/' . $name),
            ],
            Folder::folders($this->testPath, '.', true, true),
            'Folders in all nested directories should be listed with their full paths'
        );
    }

    /**
     * Tests the Folder::folders method.
     */
    public function testFoldersWithNonexistingDirectory()
    {
        $this->expectException(\UnexpectedValueException::class);

        $name = 'tempFolder';

        Folder::folders($this->testPath . '/' . $name);
    }

    /**
     * Tests that concurrent Folder::create calls for the same path do not fail.
     */
    public function testCreateConcurrently()
    {
        $script = tempnam(sys_get_temp_dir(), 'jfs');
        file_put_contents(
            $script,
            '<?php
            define("JPATH_ROOT", ' . var_export(JPATH_ROOT, true) . ');
            require ' . var_export(dirname(__DIR__) . '/vendor/autoload.php', true) . ';
            $failures = 0;
            for ($i = 0; $i < 100; $i++) {
                try {
                    Joomla\Filesystem\Folder::create(' . var_export($this->testPath, true) . ' . "/r$i/a/b/c");
                } catch (Joomla\Filesystem\Exception\FilesystemException $e) {
                    $failures++;
                }
            }
            echo $failures;'
        );

        $processes = [];
        $pipes     = [];

        for ($i = 0; $i < 4; $i++) {
            $processes[$i] = proc_open([PHP_BINARY, $script], [1 => ['pipe', 'w']], $pipes[$i]);
        }

        $failures = 0;

        for ($i = 0; $i < 4; $i++) {
            $failures += (int) stream_get_contents($pipes[$i][1]);
            fclose($pipes[$i][1]);
            proc_close($processes[$i]);
        }

        unlink($script);

        $this->assertSame(0, $failures, 'Folder::create failed because another process created the folder first.');
    }

    /**
     * Tests that a recursive listing does not follow a link back to a parent folder.
     */
    public function testRecursiveListingStopsAtLinkLoops()
    {
        mkdir($this->testPath . '/a/b', 0777, true);
        $link = $this->testPath . '/a/b/loop';

        if (!@symlink($this->testPath . '/a', $link)) {
            if (DIRECTORY_SEPARATOR !== '\\') {
                $this->markTestSkipped('Symbolic links cannot be created.');
            }

            // A junction does not need special privileges on Windows
            exec('mklink /J ' . escapeshellarg(Path::clean($link)) . ' ' . escapeshellarg(Path::clean($this->testPath . '/a')), $output, $result);

            if ($result !== 0) {
                $this->markTestSkipped('Neither a symbolic link nor a junction can be created.');
            }
        }

        try {
            $folders = Folder::folders($this->testPath, '.', true, false);
            sort($folders);

            $this->assertSame(['a', 'b', 'loop'], $folders);
        } finally {
            // Remove the link itself, never what it points to; Windows removes links to folders with rmdir()
            if (!@unlink($link)) {
                rmdir($link);
            }
        }
    }

    /**
     * Tests the Folder::listFolderTree method.
     */
    public function testListFolderTreeWithEmptyDirectory()
    {
        $name = 'tempFolder';

        if (!Folder::create($this->testPath . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        $this->assertSame(
            [],
            Folder::listFolderTree($this->testPath . '/' . $name, '.'),
            'There should not be a folder tree for an empty directory.'
        );
    }

    /**
     * Tests the Folder::listFolderTree method.
     */
    public function testListFolderTreeWithASubdirectory()
    {
        $name      = 'tempFolder';
        $childName = 'subTempFolder';

        if (!Folder::create($this->testPath . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!Folder::create($this->testPath . '/' . $name . '/' . $childName)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        $this->assertSame(
            [
                [
                    'id'       => 1,
                    'parent'   => 0,
                    'name'     => $childName,
                    'fullname' => Path::clean($this->testPath . '/' . $name . '/' . $childName),
                    'relname'  => Path::clean($this->testPath . '/' . $name . '/' . $childName),
                ],
            ],
            Folder::listFolderTree($this->testPath . '/' . $name, '.'),
            'The folder tree was not listed as expected.'
        );
    }

    /**
     * Tests the Folder::listFolderTree method.
     */
    public function testListFolderTreeWithMultipleSubirectories()
    {
        $name       = 'tempFolder';
        $childName1 = 'subTempFolder1';
        $childName2 = 'subTempFolder2';

        if (!Folder::create($this->testPath . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!Folder::create($this->testPath . '/' . $name . '/' . $childName1)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!Folder::create($this->testPath . '/' . $name . '/' . $childName2)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        $this->assertSame(
            [
                [
                    'id'       => 1,
                    'parent'   => 0,
                    'name'     => $childName1,
                    'fullname' => Path::clean($this->testPath . '/' . $name . '/' . $childName1),
                    'relname'  => Path::clean($this->testPath . '/' . $name . '/' . $childName1),
                ],
                [
                    'id'       => 2,
                    'parent'   => 0,
                    'name'     => $childName2,
                    'fullname' => Path::clean($this->testPath . '/' . $name . '/' . $childName2),
                    'relname'  => Path::clean($this->testPath . '/' . $name . '/' . $childName2),
                ],
            ],
            Folder::listFolderTree($this->testPath . '/' . $name, '.'),
            'The folder tree was not listed as expected.'
        );
    }

    /**
     * Tests the Folder::listFolderTree method.
     */
    public function testListFolderTreeWithANestedSubdirectory()
    {
        $name         = 'tempFolder';
        $childName    = 'subTempFolder';
        $subChildName = 'subSubTempFolder';

        if (!Folder::create($this->testPath . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!Folder::create($this->testPath . '/' . $name . '/' . $childName)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!Folder::create($this->testPath . '/' . $name . '/' . $childName . '/' . $subChildName)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        $this->assertSame(
            [
                [
                    'id'       => 1,
                    'parent'   => 0,
                    'name'     => $childName,
                    'fullname' => Path::clean($this->testPath . '/' . $name . '/' . $childName),
                    'relname'  => Path::clean($this->testPath . '/' . $name . '/' . $childName),
                ],
                [
                    'id'       => 2,
                    'parent'   => 1,
                    'name'     => $subChildName,
                    'fullname' => Path::clean($this->testPath . '/' . $name . '/' . $childName . '/' . $subChildName),
                    'relname'  => Path::clean($this->testPath . '/' . $name . '/' . $childName . '/' . $subChildName),
                ],
            ],
            Folder::listFolderTree($this->testPath . '/' . $name, '.'),
            'The folder tree was not listed as expected.'
        );
    }

    /**
     * Tests the Folder::listFolderTree method.
     */
    public function testListFolderTreeWithMultipleNestedSubirectories()
    {
        $name          = 'tempFolder';
        $childName1    = 'subTempFolder1';
        $childName2    = 'subTempFolder2';
        $subChildName1 = 'subSubTempFolder1';
        $subChildName2 = 'subSubTempFolder2';

        if (!Folder::create($this->testPath . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!Folder::create($this->testPath . '/' . $name . '/' . $childName1)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!Folder::create($this->testPath . '/' . $name . '/' . $childName1 . '/' . $subChildName1)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!Folder::create($this->testPath . '/' . $name . '/' . $childName1 . '/' . $subChildName2)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!Folder::create($this->testPath . '/' . $name . '/' . $childName2)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!Folder::create($this->testPath . '/' . $name . '/' . $childName2 . '/' . $subChildName1)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        if (!Folder::create($this->testPath . '/' . $name . '/' . $childName2 . '/' . $subChildName2)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        $this->assertSame(
            [
                [
                    'id'       => 1,
                    'parent'   => 0,
                    'name'     => $childName1,
                    'fullname' => Path::clean($this->testPath . '/' . $name . '/' . $childName1),
                    'relname'  => Path::clean($this->testPath . '/' . $name . '/' . $childName1),
                ],
                [
                    'id'       => 2,
                    'parent'   => 1,
                    'name'     => $subChildName1,
                    'fullname' => Path::clean($this->testPath . '/' . $name . '/' . $childName1 . '/' . $subChildName1),
                    'relname'  => Path::clean($this->testPath . '/' . $name . '/' . $childName1 . '/' . $subChildName1),
                ],
                [
                    'id'       => 3,
                    'parent'   => 1,
                    'name'     => $subChildName2,
                    'fullname' => Path::clean($this->testPath . '/' . $name . '/' . $childName1 . '/' . $subChildName2),
                    'relname'  => Path::clean($this->testPath . '/' . $name . '/' . $childName1 . '/' . $subChildName2),
                ],
                [
                    'id'       => 4,
                    'parent'   => 0,
                    'name'     => $childName2,
                    'fullname' => Path::clean($this->testPath . '/' . $name . '/' . $childName2),
                    'relname'  => Path::clean($this->testPath . '/' . $name . '/' . $childName2),
                ],
                [
                    'id'       => 5,
                    'parent'   => 4,
                    'name'     => $subChildName1,
                    'fullname' => Path::clean($this->testPath . '/' . $name . '/' . $childName2 . '/' . $subChildName1),
                    'relname'  => Path::clean($this->testPath . '/' . $name . '/' . $childName2 . '/' . $subChildName1),
                ],
                [
                    'id'       => 6,
                    'parent'   => 4,
                    'name'     => $subChildName2,
                    'fullname' => Path::clean($this->testPath . '/' . $name . '/' . $childName2 . '/' . $subChildName2),
                    'relname'  => Path::clean($this->testPath . '/' . $name . '/' . $childName2 . '/' . $subChildName2),
                ],
            ],
            Folder::listFolderTree($this->testPath . '/' . $name, '.'),
            'The folder tree was not listed as expected.'
        );
    }

    /**
     * Tests the Folder::makeSafe method.
     */
    public function testMakeSafe()
    {
        $this->assertSame(
            'test1/testdirectory',
            Folder::makeSafe('test1/testdirectory')
        );
    }


    /**
     * Test exists method.
     */
    public function testExistsForExistingFolder()
    {
        $name = 'tempFolder';

        if (!Folder::create($this->testPath . '/' . $name)) {
            $this->markTestSkipped('The test directory could not be created.');
        }

        $this->assertTrue(
            Folder::exists($this->testPath . '/' . $name),
            'The folder exists.'
        );
    }

    /**
     * Test exists method.
     */
    public function testExistsForNonexistingFolder()
    {
        $name = 'nonExistingTempFolder';

        $this->assertFalse(
            Folder::exists($this->testPath . '/' . $name),
            'The folder does not exists.'
        );
    }
}
