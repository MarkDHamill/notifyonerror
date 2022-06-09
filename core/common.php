<?php
/**
 *
 * Notify on new error log entries. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2022, Mark D. Hamill, https://www.phpbbservices.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbservices\notifyonerror\core;

class common
{

	protected $config;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config $config
	 */

	public function __construct(\phpbb\config\config $config)
	{
		$this->config = $config;
	}

	public function get_next_notification_id()
	{
		// Increment and return the next notification ID for this extension.
		$this->config->increment('phpbbservices_notifyonerror_notification_id', 1);
		return $this->config->offsetGet('phpbbservices_notifyonerror_notification_id');
	}

}

