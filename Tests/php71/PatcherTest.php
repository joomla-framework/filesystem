<?php
/**
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Filesystem\Tests\php71;

use Joomla\Filesystem\Patcher;
use Joomla\Filesystem\Path;
use Joomla\Test\TestHelper;
use PHPUnit\Framework\TestCase;

/**
 * A unit test class for Patcher
 *
 * @since  1.0
 */
class PatcherTest extends TestCase
{
	const TMP_DIR = __DIR__ . '/tmp/patcher';

	/**
	 * This method is called before the first test of this test class is run.
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 */
	public static function setUpBeforeClass(): void
	{
		if (!\defined('JPATH_ROOT'))
		{
			self::markTestSkipped('Constant `JPATH_ROOT` is not defined.');
		}
	}

	/**
	 * Sets up the fixture.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function setUp(): void
	{
		parent::setUp();

		// Make sure previous test files are cleaned up
		$this->_cleanupTestFiles();

		// Make some test files and folders
		if (!mkdir(self::TMP_DIR, 0777, true)) {
			throw new \RuntimeException('Unable to create tmp directory');
		}
	}

	/**
	 * Remove created files
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function tearDown(): void
	{
		$this->_cleanupTestFiles();
	}

	/**
	 * Convenience method to clean up before and after test
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	private function _cleanupTestFiles(): void
	{
		$this->_cleanupFile(Path::clean(__DIR__ . '/tmp/patcher/lao2tzu.diff'));
		$this->_cleanupFile(Path::clean(__DIR__ . '/tmp/patcher/lao'));
		$this->_cleanupFile(Path::clean(__DIR__ . '/tmp/patcher/tzu'));
		$this->_cleanupFile(Path::clean(self::TMP_DIR));
	}

	/**
	 * Convenience method to clean up for files test
	 *
	 * @param   string  $path  The path to clean
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	private function _cleanupFile(string $path): void
	{
		if (file_exists($path))
		{
			if (is_file($path))
			{
				unlink($path);
			}
			elseif (is_dir($path))
			{
				rmdir($path);
			}
		}
	}

	/**
	 * Data provider for testAdd
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function addData(): array
	{
		$udiff = 'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200
@@ -1,7 +1,6 @@
-The Way that can be told of is not the eternal Way;
-The name that can be named is not the eternal name.
 The Nameless is the origin of Heaven and Earth;
-The Named is the mother of all things.
+The named is the mother of all things.
+
 Therefore let there always be non-being,
   so we may see their subtlety,
 And let there always be being,
@@ -9,4 +8,7 @@
 The two are the same,
 But after they are produced,
   they have different names.
+They both may be called deep and profound.
+Deeper and more profound,
+The door of all subtleties!
';

		return array(
			array(
				$udiff,
				self::TMP_DIR,
				0,
				array(
					array(
						'udiff' => $udiff,
						'root' => self::TMP_DIR . '/',
						'strip' => 0
					)
				)
			),
			array(
				$udiff,
				self::TMP_DIR . '/',
				0,
				array(
					array(
						'udiff' => $udiff,
						'root' => self::TMP_DIR . '/',
						'strip' => 0
					)
				)
			),
			array(
				$udiff,
				null,
				0,
				array(
					array(
						'udiff' => $udiff,
						'root' => '',
						'strip' => 0
					)
				)
			),
			array(
				$udiff,
				'',
				0,
				array(
					array(
						'udiff' => $udiff,
						'root' => '/',
						'strip' => 0
					)
				)
			),
		);
	}

	/**
	 * Test add a unified diff string to the patcher
	 *
	 * @param   string       $udiff     Unified diff input string
	 * @param   string|null  $root      The files root path
	 * @param   integer      $strip     The number of '/' to strip
	 * @param   array        $expected  The expected array patches
	 *
	 * @return  void
	 *
	 * @dataProvider addData
	 * @since        1.0
	 * @throws       \ReflectionException
	 */
	public function testAdd(string $udiff, ?string $root, int $strip, array $expected): void
	{
		$patcher = Patcher::getInstance()->reset();
		$patcher->add($udiff, $root, $strip);
		$this->assertEquals(
			$expected,
			TestHelper::getValue($patcher, 'patches'),
			'Line:' . __LINE__ . ' The patcher cannot add the unified diff string.'
		);
	}

	/**
	 * Test Patcher::addFile add a unified diff file to the patcher
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  \ReflectionException
	 */
	public function testAddFile(): void
	{
		$udiff = 'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200
@@ -1,7 +1,6 @@
-The Way that can be told of is not the eternal Way;
-The name that can be named is not the eternal name.
 The Nameless is the origin of Heaven and Earth;
-The Named is the mother of all things.
+The named is the mother of all things.
+
 Therefore let there always be non-being,
   so we may see their subtlety,
 And let there always be being,
@@ -9,4 +8,7 @@
 The two are the same,
 But after they are produced,
   they have different names.
+They both may be called deep and profound.
+Deeper and more profound,
+The door of all subtleties!
';

		// Use of realpath to ensure test works for on all platforms
		file_put_contents(__DIR__ . '/tmp/patcher/lao2tzu.diff', $udiff);
		$patcher = Patcher::getInstance()->reset();
		$patcher->addFile(__DIR__ . '/tmp/patcher/lao2tzu.diff', realpath(self::TMP_DIR));

		$this->assertEquals(
			array(
				array(
					'udiff' => $udiff,
					'root' => realpath(self::TMP_DIR) . '/',
					'strip' => 0
				)
			),
			TestHelper::getValue($patcher, 'patches'),
			'Line:' . __LINE__ . ' The patcher cannot add the unified diff file.'
		);
	}

	/**
	 * Reset the patcher to its initial state
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 * @throws  \ReflectionException
	 */
	public function testReset(): void
	{
		$udiff = 'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200
@@ -1,7 +1,6 @@
-The Way that can be told of is not the eternal Way;
-The name that can be named is not the eternal name.
 The Nameless is the origin of Heaven and Earth;
-The Named is the mother of all things.
+The named is the mother of all things.
+
 Therefore let there always be non-being,
   so we may see their subtlety,
 And let there always be being,
@@ -9,4 +8,7 @@
 The two are the same,
 But after they are produced,
   they have different names.
+They both may be called deep and profound.
+Deeper and more profound,
+The door of all subtleties!
';
		$patcher = Patcher::getInstance()->reset();
		$patcher->add($udiff, __DIR__ . '/patcher/');
		$this->assertEquals(
			$patcher->reset(),
			$patcher,
			'Line:' . __LINE__ . ' The reset method does not return $this for chaining.'
		);
		$this->assertEquals(
			array(),
			TestHelper::getValue($patcher, 'sources'),
			'Line:' . __LINE__ . ' The patcher has not been reset.'
		);
		$this->assertEquals(
			array(),
			TestHelper::getValue($patcher, 'destinations'),
			'Line:' . __LINE__ . ' The patcher has not been reset.'
		);
		$this->assertEquals(
			array(),
			TestHelper::getValue($patcher, 'removals'),
			'Line:' . __LINE__ . ' The patcher has not been reset.'
		);
		$this->assertEquals(
			array(),
			TestHelper::getValue($patcher, 'patches'),
			'Line:' . __LINE__ . ' The patcher has not been reset.'
		);
	}

	/**
	 * Data provider for testApply
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function applyData(): array
	{
		return array(
			// Test classical feature
			'Test classical feature' => array(
				'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200
@@ -1,7 +1,6 @@
-The Way that can be told of is not the eternal Way;
-The name that can be named is not the eternal name.
 The Nameless is the origin of Heaven and Earth;
-The Named is the mother of all things.
+The named is the mother of all things.
+
 Therefore let there always be non-being,
   so we may see their subtlety,
 And let there always be being,
@@ -9,4 +8,7 @@
 The two are the same,
 But after they are produced,
   they have different names.
+They both may be called deep and profound.
+Deeper and more profound,
+The door of all subtleties!
',
				self::TMP_DIR,
				0,
				array(
					__DIR__ . '/tmp/patcher/lao' =>
					'The Way that can be told of is not the eternal Way;
The name that can be named is not the eternal name.
The Nameless is the origin of Heaven and Earth;
The Named is the mother of all things.
Therefore let there always be non-being,
  so we may see their subtlety,
And let there always be being,
  so we may see their outcome.
The two are the same,
But after they are produced,
  they have different names.
'
				),
				array(
					__DIR__ . '/tmp/patcher/tzu' =>
					'The Nameless is the origin of Heaven and Earth;
The named is the mother of all things.

Therefore let there always be non-being,
  so we may see their subtlety,
And let there always be being,
  so we may see their outcome.
The two are the same,
But after they are produced,
  they have different names.
They both may be called deep and profound.
Deeper and more profound,
The door of all subtleties!
'
				),
				1,
				false
			),

			// Test truncated hunk
			'Test truncated hunk' => array(
				'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200
@@ -1 +1 @@
-The Way that can be told of is not the eternal Way;
+The named is the mother of all things.
',
				self::TMP_DIR,
				0,
				array(
					__DIR__ . '/tmp/patcher/lao' =>
					'The Way that can be told of is not the eternal Way;
The name that can be named is not the eternal name.
The Nameless is the origin of Heaven and Earth;
The Named is the mother of all things.
Therefore let there always be non-being,
  so we may see their subtlety,
And let there always be being,
  so we may see their outcome.
The two are the same,
But after they are produced,
  they have different names.
'
				),
				array(
					__DIR__ . '/tmp/patcher/tzu' =>
					'The named is the mother of all things.
The name that can be named is not the eternal name.
The Nameless is the origin of Heaven and Earth;
The Named is the mother of all things.
Therefore let there always be non-being,
  so we may see their subtlety,
And let there always be being,
  so we may see their outcome.
The two are the same,
But after they are produced,
  they have different names.
'
				),
				1,
				false
			),

			// Test strip is null
			'Test strip is null' => array(
				'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200
@@ -1,7 +1,6 @@
-The Way that can be told of is not the eternal Way;
-The name that can be named is not the eternal name.
 The Nameless is the origin of Heaven and Earth;
-The Named is the mother of all things.
+The named is the mother of all things.
+
 Therefore let there always be non-being,
   so we may see their subtlety,
 And let there always be being,
@@ -9,4 +8,7 @@
 The two are the same,
 But after they are produced,
   they have different names.
+They both may be called deep and profound.
+Deeper and more profound,
+The door of all subtleties!
',
				self::TMP_DIR,
				null,
				array(
					__DIR__ . '/tmp/patcher/lao' =>
					'The Way that can be told of is not the eternal Way;
The name that can be named is not the eternal name.
The Nameless is the origin of Heaven and Earth;
The Named is the mother of all things.
Therefore let there always be non-being,
  so we may see their subtlety,
And let there always be being,
  so we may see their outcome.
The two are the same,
But after they are produced,
  they have different names.
'
				),
				array(
					__DIR__ . '/tmp/patcher/tzu' =>
					'The Nameless is the origin of Heaven and Earth;
The named is the mother of all things.

Therefore let there always be non-being,
  so we may see their subtlety,
And let there always be being,
  so we may see their outcome.
The two are the same,
But after they are produced,
  they have different names.
They both may be called deep and profound.
Deeper and more profound,
The door of all subtleties!
'
				),
				1,
				false
			),

			// Test strip is different of 0
			'Test strip is different of 0' => array(
				'Index: lao
===================================================================
--- /path/to/lao	2011-09-21 16:05:45.086909120 +0200
+++ /path/to/tzu	2011-09-21 16:05:41.156878938 +0200
@@ -1,7 +1,6 @@
-The Way that can be told of is not the eternal Way;
-The name that can be named is not the eternal name.
 The Nameless is the origin of Heaven and Earth;
-The Named is the mother of all things.
+The named is the mother of all things.
+
 Therefore let there always be non-being,
   so we may see their subtlety,
 And let there always be being,
@@ -9,4 +8,7 @@
 The two are the same,
 But after they are produced,
   they have different names.
+They both may be called deep and profound.
+Deeper and more profound,
+The door of all subtleties!
',
				self::TMP_DIR,
				3,
				array(
					__DIR__ . '/tmp/patcher/lao' =>
					'The Way that can be told of is not the eternal Way;
The name that can be named is not the eternal name.
The Nameless is the origin of Heaven and Earth;
The Named is the mother of all things.
Therefore let there always be non-being,
  so we may see their subtlety,
And let there always be being,
  so we may see their outcome.
The two are the same,
But after they are produced,
  they have different names.
'
				),
				array(
					__DIR__ . '/tmp/patcher/tzu' =>
					'The Nameless is the origin of Heaven and Earth;
The named is the mother of all things.

Therefore let there always be non-being,
  so we may see their subtlety,
And let there always be being,
  so we may see their outcome.
The two are the same,
But after they are produced,
  they have different names.
They both may be called deep and profound.
Deeper and more profound,
The door of all subtleties!
'
				),
				1,
				false
			),

			// Test create file
			'Test create file' => array(
				'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200
@@ -0,0 +1,14 @@
+The Nameless is the origin of Heaven and Earth;
+The named is the mother of all things.
+
+Therefore let there always be non-being,
+  so we may see their subtlety,
+And let there always be being,
+  so we may see their outcome.
+The two are the same,
+But after they are produced,
+  they have different names.
+They both may be called deep and profound.
+Deeper and more profound,
+The door of all subtleties!
+
',
				self::TMP_DIR,
				0,
				array(),
				array(
					__DIR__ . '/tmp/patcher/tzu' =>
					'The Nameless is the origin of Heaven and Earth;
The named is the mother of all things.

Therefore let there always be non-being,
  so we may see their subtlety,
And let there always be being,
  so we may see their outcome.
The two are the same,
But after they are produced,
  they have different names.
They both may be called deep and profound.
Deeper and more profound,
The door of all subtleties!
'
				),
				1,
				false
			),

			// Test patch itself
			'Test patch itself' => array(
				'Index: lao
===================================================================
--- tzu	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200
@@ -1,7 +1,6 @@
-The Way that can be told of is not the eternal Way;
-The name that can be named is not the eternal name.
 The Nameless is the origin of Heaven and Earth;
-The Named is the mother of all things.
+The named is the mother of all things.
+
 Therefore let there always be non-being,
   so we may see their subtlety,
 And let there always be being,
@@ -9,4 +8,7 @@
 The two are the same,
 But after they are produced,
   they have different names.
+They both may be called deep and profound.
+Deeper and more profound,
+The door of all subtleties!
',
				self::TMP_DIR,
				0,
				array(
					__DIR__ . '/tmp/patcher/tzu' =>
					'The Way that can be told of is not the eternal Way;
The name that can be named is not the eternal name.
The Nameless is the origin of Heaven and Earth;
The Named is the mother of all things.
Therefore let there always be non-being,
  so we may see their subtlety,
And let there always be being,
  so we may see their outcome.
The two are the same,
But after they are produced,
  they have different names.
'
				),
				array(
					__DIR__ . '/tmp/patcher/tzu' =>
					'The Nameless is the origin of Heaven and Earth;
The named is the mother of all things.

Therefore let there always be non-being,
  so we may see their subtlety,
And let there always be being,
  so we may see their outcome.
The two are the same,
But after they are produced,
  they have different names.
They both may be called deep and profound.
Deeper and more profound,
The door of all subtleties!
'
				),
				1,
				false
			),

			// Test delete
			'Test delete' => array(
				'Index: lao
===================================================================
--- tzu	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200
@@ -1,11 +1,0 @@
-The Way that can be told of is not the eternal Way;
-The name that can be named is not the eternal name.
-The Nameless is the origin of Heaven and Earth;
-The Named is the mother of all things.
-Therefore let there always be non-being,
-  so we may see their subtlety,
-And let there always be being,
-  so we may see their outcome.
-The two are the same,
-But after they are produced,
-  they have different names.
',
				self::TMP_DIR,
				0,
				array(
					__DIR__ . '/tmp/patcher/tzu' =>
					'The Way that can be told of is not the eternal Way;
The name that can be named is not the eternal name.
The Nameless is the origin of Heaven and Earth;
The Named is the mother of all things.
Therefore let there always be non-being,
  so we may see their subtlety,
And let there always be being,
  so we may see their outcome.
The two are the same,
But after they are produced,
  they have different names.
'
				),
				array(
					__DIR__ . '/tmp/patcher/tzu' => null
				),
				1,
				false
			),

			// Test unexpected eof after header
			'Test unexpected eof after header 1' => array(
				'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200
',
				self::TMP_DIR,
				0,
				array(),
				array(),
				1,
				\RuntimeException::class
			),

			// Test unexpected eof after header
			'Test unexpected eof after header 2' => array(
				'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200',
				self::TMP_DIR,
				0,
				array(),
				array(),
				1,
				\RuntimeException::class
			),

			// Test unexpected eof in header
			'Test unexpected eof in header' => array(
				'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200',
				self::TMP_DIR,
				0,
				array(),
				array(),
				1,
				\RuntimeException::class
			),

			// Test invalid diff in header
			'Test invalid diff in header' => array(
				'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200
',
				self::TMP_DIR,
				0,
				array(),
				array(),
				1,
				\RuntimeException::class
			),

			// Test unexpected eof after hunk 1
			'Test unexpected eof after hunk 1' => array(
				'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200
@@ -1,11 +1,0 @@',
				self::TMP_DIR,
				0,
				array(),
				array(),
				1,
				\RuntimeException::class
			),

			// Test unexpected eof after hunk 2
			'Test unexpected eof after hunk 2' => array(
				'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200
@@ -1,11 +1,11 @@
+The Way that can be told of is not the eternal Way;
+The name that can be named is not the eternal name.
-The Nameless is the origin of Heaven and Earth;
',
				self::TMP_DIR,
				0,
				array(),
				array(),
				1,
				\RuntimeException::class
			),

			// Test unexpected remove line
			'Test unexpected remove line' => array(
				'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200
@@ -1,1 +1,1 @@
-The Way that can be told of is not the eternal Way;
-The name that can be named is not the eternal name.
+The Nameless is the origin of Heaven and Earth;
',
				self::TMP_DIR,
				0,
				array(),
				array(),
				1,
				\RuntimeException::class
			),

			// Test unexpected add line
			'Test unexpected add line' => array(
				'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200
@@ -1,1 +1,1 @@
+The Way that can be told of is not the eternal Way;
+The name that can be named is not the eternal name.
-The Nameless is the origin of Heaven and Earth;
',
				self::TMP_DIR,
				0,
				array(),
				array(),
				1,
				\RuntimeException::class
			),

			// Test unexisting source
			'Test unexisting source' => array(
				'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200
@@ -1,7 +1,6 @@
-The Way that can be told of is not the eternal Way;
-The name that can be named is not the eternal name.
 The Nameless is the origin of Heaven and Earth;
-The Named is the mother of all things.
+The named is the mother of all things.
+
 Therefore let there always be non-being,
   so we may see their subtlety,
 And let there always be being,
@@ -9,4 +8,7 @@
 The two are the same,
 But after they are produced,
   they have different names.
+They both may be called deep and profound.
+Deeper and more profound,
+The door of all subtleties!
',
				self::TMP_DIR,
				0,
				array(),
				array(),
				1,
				\RuntimeException::class
			),

			// Test failed verify
			'Test failed verify' => array(
				'Index: lao
===================================================================
--- lao	2011-09-21 16:05:45.086909120 +0200
+++ tzu	2011-09-21 16:05:41.156878938 +0200
@@ -1,7 +1,6 @@
-The Way that can be told of is not the eternal Way;
-The name that can be named is not the eternal name.
 The Nameless is the origin of Heaven and Earth;
-The Named is the mother of all things.
+The named is the mother of all things.
+
 Therefore let there always be non-being,
   so we may see their subtlety,
 And let there always be being,
@@ -9,4 +8,7 @@
 The two are the same,
 But after they are produced,
   they have different names.
+They both may be called deep and profound.
+Deeper and more profound,
+The door of all subtleties!
',
				self::TMP_DIR,
				0,
				array(
					__DIR__ . '/tmp/patcher/lao' => ''
				),
				array(),
				1,
				\RuntimeException::class
			),
		);
	}

	/**
	 * Apply the patches
	 *
	 * @param   string        $udiff         Unified diff input string
	 * @param   string        $root          The files root path
	 * @param   integer|null  $strip         The number of '/' to strip
	 * @param   array         $sources       The source files
	 * @param   array         $destinations  The destinations files
	 * @param   integer       $result        The number of files patched
	 * @param   mixed         $throw         The exception throw, false for no exception
	 *
	 * @return  void
	 *
	 * @dataProvider applyData
	 * @since        1.0
	 */
	public function testApply(string $udiff, string $root, ?int $strip, array $sources, array $destinations, int $result, $throw): void
	{
		if ($throw)
		{
			$this->expectException($throw);
		}

		foreach ($sources as $path => $content)
		{
			file_put_contents($path, $content);
		}

		$patcher = Patcher::getInstance()->reset();
		$patcher->add($udiff, $root, $strip);
		$this->assertEquals(
			$result,
			$patcher->apply(),
			'Line:' . __LINE__ . ' The patcher did not patch ' . $result . ' file(s).'
		);

		foreach ($destinations as $path => $content)
		{
			if (\is_null($content))
			{
				$this->assertFalse(
					is_file($path),
					'Line:' . __LINE__ . ' The patcher did not succeed in patching ' . $path
				);
			}
			else
			{
				// Remove all vertical characters to ensure system independent compare
				$content = preg_replace('/\v/', '', $content);
				$data = file_get_contents($path);
				$data = preg_replace('/\v/', '', $data);

				$this->assertEquals(
					$content,
					$data,
					'Line:' . __LINE__ . ' The patcher did not succeed in patching ' . $path
				);
			}
		}
	}
}
