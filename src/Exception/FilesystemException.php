<?php
/**
 * Part of the Joomla Framework Filesystem Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Filesystem\Exception;

/**
 * Exception class for handling errors in the Filesystem package
 *
 * @since   1.2.0
 * @change  __DEPLOY_VERSION__  If the message containes a full path, the root path (JPATH_ROOT) is removed from it
 *          to avoid any full path disclosure. Before __DEPLOY_VERSION__, the path was propagated as provided.
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
