<?php

/**
 * @copyright  Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Filesystem\Tests\Stubs;

/**
 * A stream wrapper that writes at most three bytes at a time and reports every second write as empty,
 * so that fwrite() returns short writes like a non-blocking socket.
 */
class ShortWriteStreamWrapper
{
    /**
     * The stream context, set by PHP
     *
     * @var  resource|null
     */
    public $context;

    /**
     * The data written to the stream
     *
     * @var  string
     */
    public static $data = '';

    /**
     * Whether the next write writes nothing
     *
     * @var  boolean
     */
    private $stall = false;

    public function stream_open($path, $mode, $options, &$openedPath)
    {
        self::$data = '';

        return true;
    }

    public function stream_write($data)
    {
        if ($this->stall) {
            $this->stall = false;

            return 0;
        }

        $this->stall = true;
        $written     = min(\strlen($data), 3);
        self::$data .= substr($data, 0, $written);

        return $written;
    }

    public function stream_close()
    {
    }

    public function stream_metadata($path, $option, $value)
    {
        return true;
    }
}
