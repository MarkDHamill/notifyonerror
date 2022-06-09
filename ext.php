<?php
/**
 *
 * Notify on new error log entries. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2022, Mark D. Hamill, https://www.phpbbservices.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbservices\notifyonerror;

/**
 * Notify on new error log entries Extension base
 *
 * It is recommended to remove this file from
 * an extension if it is not going to be used.
 */
class ext extends \phpbb\extension\base
{

	public function is_enableable()
	{

		// phpBB 3.3 only test
		$config = $this->container->get('config');

		if (
			phpbb_version_compare($config['version'], '3.3.0', '<') ||
			phpbb_version_compare($config['version'], '4.0', '>='))
		{

			// Import my extension's language file
			$language = $this->container->get('language');
			$language->add_lang('common', 'phpbbservices/notifyonerror');

			// Return generic message indicating not all install requirements were met.
			return [$language->lang('PHPBBSERVICES_NOTIFYONERROR_INSTALL_REQUIREMENTS')];

		}
		return true;

	}

	/**
	 * Enable notifications for the extension
	 *
	 * @param	mixed	$old_state	The return value of the previous call
	 *								of this method, or false on the first call
	 * @return	mixed				Returns false after last step, otherwise
	 *								temporary state which is passed as an
	 *								argument to the next step
	 */
	public function enable_step($old_state)
	{
		if ($old_state === false)
		{
			$this->container->get('notification_manager')
				->enable_notifications('phpbbservices.notifyonerror.notification.type.errorlog');

			return 'notification';
		}

		return parent::enable_step($old_state);
	}

	/**
	 * Disable notifications for the extension
	 *
	 * @param	mixed	$old_state	The return value of the previous call
	 *								of this method, or false on the first call
	 * @return	mixed				Returns false after last step, otherwise
	 *								temporary state which is passed as an
	 *								argument to the next step
	 */
	public function disable_step($old_state)
	{
		if ($old_state === false)
		{
			$this->container->get('notification_manager')
				->disable_notifications('phpbbservices.notifyonerror.notification.type.errorlog');

			return 'notification';
		}

		return parent::disable_step($old_state);
	}

	/**
	 * Purge notifications for the extension
	 *
	 * @param	mixed	$old_state	The return value of the previous call
	 *								of this method, or false on the first call
	 * @return	mixed				Returns false after last step, otherwise
	 *								temporary state which is passed as an
	 *								argument to the next step
	 */
	public function purge_step($old_state)
	{
		if ($old_state === false)
		{
			$this->container->get('notification_manager')
				->purge_notifications('phpbbservices.notifyonerror.notification.type.errorlog');

			return 'notification';
		}

		return parent::purge_step($old_state);
	}
}
