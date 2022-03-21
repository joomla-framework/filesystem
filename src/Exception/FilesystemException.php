<?php
/**
 * Part of the Joomla Framework Filesystem Package
 *
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Filesystem\Exception;

use Joomla\Filesystem\Path;

/**
 * Exception class for handling errors in the Filesystem package
 *
 * @since  1.2.0
 */
class FilesystemException extends \RuntimeException
{
	public function __construct($message = "", $code = 0, \Throwable $previous = null)
	{
		parent::__construct(
			Path::removeRoot($message),
			$code,
			$previous
		);
	}
}
