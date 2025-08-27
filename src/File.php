<?php

/**
 * Part of the Joomla Framework Filesystem Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Filesystem;

use Joomla\Filesystem\Exception\FilesystemException;

/**
 * A File handling class
 *
 * @since  1.0
 */
class File
{
    /**
     * @var    boolean  true if OPCache enabled, and we have permission to invalidate files
     * @since  3.2.0
     */
    protected static $canFlushFileCache;

    /**
     * Gets the extension of a file name
     *
     * @param   string  $file  The file name
     *
     * @return  string  The file extension
     *
     * @since   3.0.0
     */
    public static function getExt($file)
    {
        // String manipulation should be faster than pathinfo() on newer PHP versions.
        $dot = strrpos($file, '.');

        if ($dot === false) {
            return '';
        }

        $ext = substr($file, $dot + 1);

        // Extension cannot contain slashes.
        if (strpos($ext, '/') !== false || (DIRECTORY_SEPARATOR === '\\' && strpos($ext, '\\') !== false)) {
            return '';
        }

        return $ext;
    }

    /**
     * Strips the last extension off of a file name
     *
     * @param   string  $file  The file name
     *
     * @return  string  The file name without the extension
     *
     * @since   1.0
     */
    public static function stripExt($file)
    {
        return preg_replace('#\.[^.]*$#', '', $file);
    }

    /**
     * Makes the file name safe to use
     *
     * @param   string  $file        The name of the file [not full path]
     * @param   array   $stripChars  Array of regex (by default will remove any leading periods)
     *
     * @return  string  The sanitised string
     *
     * @since   1.0
     */
    public static function makeSafe($file, array $stripChars = ['#^\.#'])
    {
        // Try transliterating the file name using the native php function
        if (function_exists('transliterator_transliterate') && function_exists('iconv')) {
            // Using iconv to ignore characters that can't be transliterated
            $file = iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", transliterator_transliterate('Any-Latin; Latin-ASCII', $file));
        }

        $regex = array_merge(['#(\.){2,}#', '#[^A-Za-z0-9\.\_\- ]#'], $stripChars);
        $file  = preg_replace($regex, '', $file);

        // Remove any trailing dots, as those aren't ever valid file names.
        $file = rtrim($file, '.');

        return trim($file);
    }

    /**
     * Copies a file
     *
     * @param   string   $src         The path to the source file
     * @param   string   $dest        The path to the destination file
     * @param   string   $path        An optional base path to prefix to the file names
     * @param   boolean  $useStreams  True to use streams
     *
     * @return  boolean  True on success
     *
     * @since   1.0
     * @throws  FilesystemException
     * @throws  \UnexpectedValueException
     */
    public static function copy($src, $dest, $path = null, $useStreams = false)
    {
        // Prepend a base path if it exists
        if ($path) {
            $src  = Path::clean($path . '/' . $src);
            $dest = Path::clean($path . '/' . $dest);
        }

        // Check src path
        if (!is_readable($src)) {
            throw new \UnexpectedValueException(
                sprintf(
                    "%s: Cannot find or read file: %s",
                    __METHOD__,
                    Path::removeRoot($src)
                )
            );
        }

        if ($useStreams) {
            $stream = Stream::getStream();

            if (!$stream->copy($src, $dest, null, false)) {
                throw new FilesystemException(sprintf('%1$s(%2$s, %3$s): %4$s', __METHOD__, $src, $dest, $stream->getError()));
            }

            self::invalidateFileCache($dest);

            return true;
        }

        if (!@ copy($src, $dest)) {
            throw new FilesystemException(sprintf('%1$s(%2$s, %3$s): %4$s', __METHOD__, $src, $dest, 'Copy failed'));
        }

        self::invalidateFileCache($dest);

        return true;
    }

    /**
     * Delete a file or array of files
     *
     * @param   mixed  $file  The file name or an array of file names
     *
     * @return  boolean  True on success
     *
     * @since   1.0
     * @throws  FilesystemException
     */
    public static function delete($file)
    {
        $files = (array) $file;

        foreach ($files as $file) {
            $file     = Path::clean($file);
            $filename = basename($file);

            /**
             * Try making the file writable first. If it's read-only, it can't be deleted
             * on Windows, even if the parent folder is writable
             */
            @chmod($file, 0777);

            /**
             * Invalidate the OPCache for the file before actually deleting it
             * @link https://www.php.net/manual/en/function.opcache-invalidate.php#116372
             */
            self::invalidateFileCache($file);

            /**
             * In case of restricted permissions we zap it one way or the other
             * as long as the owner is either the webserver or the ftp
             */
            if (!@ unlink($file)) {
                throw new FilesystemException(__METHOD__ . ': Failed deleting ' . $filename);
            }
        }

        return true;
    }

    /**
     * Moves a file
     *
     * @param   string   $src         The path to the source file
     * @param   string   $dest        The path to the destination file
     * @param   string   $path        An optional base path to prefix to the file names
     * @param   boolean  $useStreams  True to use streams
     *
     * @return  boolean  True on success
     *
     * @since   1.0
     * @throws  FilesystemException
     */
    public static function move($src, $dest, $path = '', $useStreams = false)
    {
        if ($path) {
            $src  = Path::clean($path . '/' . $src);
            $dest = Path::clean($path . '/' . $dest);
        }

        // Check src path
        if (!is_readable($src)) {
            return 'Cannot find source file.';
        }

        self::invalidateFileCache($src);

        if ($useStreams) {
            $stream = Stream::getStream();

            if (!$stream->move($src, $dest, null, false)) {
                throw new FilesystemException(__METHOD__ . ': ' . $stream->getError());
            }

            self::invalidateFileCache($dest);

            return true;
        }

        if (!@ rename($src, $dest)) {
            throw new FilesystemException(__METHOD__ . ': Rename failed.');
        }

        self::invalidateFileCache($dest);

        return true;
    }

    /**
     * Write contents to a file
     *
     * @param   string   $file          The full file path
     * @param   string   $buffer        The buffer to write
     * @param   boolean  $useStreams    Use streams
     * @param   boolean  $appendToFile  Append to the file and not overwrite it.
     *
     * @return  boolean  True on success
     *
     * @since   1.0
     */
    public static function write($file, $buffer, $useStreams = false, $appendToFile = false)
    {
        if (\function_exists('set_time_limit')) {
            set_time_limit(ini_get('max_execution_time'));
        }

        // If the destination directory doesn't exist we need to create it
        if (!file_exists(\dirname($file))) {
            Folder::create(\dirname($file));
        }

        if ($useStreams) {
            $stream = Stream::getStream();

            // Beef up the chunk size to a meg
            $stream->set('chunksize', (1024 * 1024));
            $stream->writeFile($file, $buffer, $appendToFile);

            self::invalidateFileCache($file);

            return true;
        }

        $file = Path::clean($file);

        // Set the required flag to only append to the file and not overwrite it
        if ($appendToFile === true) {
            $res = \is_int(file_put_contents($file, $buffer, \FILE_APPEND));
        } else {
            $res = \is_int(file_put_contents($file, $buffer));
        }

        self::invalidateFileCache($file);

        return $res;
    }

    /**
     * Moves an uploaded file to a destination folder
     *
     * @param   string   $src         The name of the php (temporary) uploaded file
     * @param   string   $dest        The path (including filename) to move the uploaded file to
     * @param   boolean  $useStreams  True to use streams
     *
     * @return  boolean  True on success
     *
     * @since   1.0
     * @throws  FilesystemException
     */
    public static function upload($src, $dest, $useStreams = false)
    {
        // Ensure that the path is valid and clean
        $dest = Path::clean($dest);

        // Create the destination directory if it does not exist
        $baseDir = \dirname($dest);

        if (!is_dir($baseDir)) {
            Folder::create($baseDir);
        }

        self::invalidateFileCache($src);

        if ($useStreams) {
            $stream = Stream::getStream();

            if (!$stream->upload($src, $dest, null, false)) {
                throw new FilesystemException(sprintf('%1$s(%2$s, %3$s): %4$s', __METHOD__, $src, $dest, $stream->getError()));
            }

            self::invalidateFileCache($dest);

            return true;
        }

        if (is_writable($baseDir) && move_uploaded_file($src, $dest)) {
            // Short circuit to prevent file permission errors
            if (Path::setPermissions($dest)) {
                self::invalidateFileCache($dest);

                return true;
            }

            throw new FilesystemException(__METHOD__ . ': Failed to change file permissions.');
        }

        throw new FilesystemException(__METHOD__ . ': Failed to move file.');
    }

    /**
     * Invalidate any opcache immediately for a file if opcache* functions are enabled and the file is a PHP file.
     *
     * @param   string  $file  The path to the file just written to, to flush from opcache
     *
     * @return void
     */
    public static function invalidateFileCache($file)
    {
        /**
         * Check if we can invalidate the opcache. This is the case if
         * - opcache is enabled, and
         * - the opcache_invalidate function is available, and
         * - calling opcache_invalidate is not restricted by opcache.restrict_api
         *   or it is restricted to allow the currently executing script
         */
        if (!isset(static::$canFlushFileCache)) {
            static::$canFlushFileCache
                = ini_get('opcache.enable')
                && \function_exists('opcache_invalidate')
                && (!ini_get('opcache.restrict_api') || str_starts_with(realpath($_SERVER['SCRIPT_FILENAME']), ini_get('opcache.restrict_api')));
        }

        if (!static::$canFlushFileCache) {
            return;
        }

        $info = pathinfo($file);

        if (isset($info['extension']) && $info['extension'] === 'php') {
            // Force invalidation to be absolutely sure the opcache is cleared for this file.
            opcache_invalidate($file, true);
        }
    }

    /**
     * Wrapper for the standard file_exists function
     *
     * @param   string  $file  File path
     *
     * @return  boolean  True if path is a file
     *
     */
    public static function exists(string $file): bool
    {
        return is_file(Path::clean($file));
    }
}
