<?php
/**
 *
 * Notify on new error log entries. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2022, Mark D. Hamill, https://www.phpbbservices.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbservices\notifyonerror\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Notify on new error log entries Event listener.
 */
class main_listener implements EventSubscriberInterface
{
	public static function getSubscribedEvents()
	{
		return [
			'core.user_setup'	=> 'load_language_on_setup',
			'core.add_log'		=> 'send_error_notification'
		];
	}

	protected $config;
	protected $helper;
	protected $notification_manager;
	protected $user;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config						$config					Config object
	 * @param \phpbb\notification\manager				$notification_manager	Notification manager object
	 * @param \phpbbservices\notifyonerror\core\common 	$helper					Extension's helper object
	 * @param \phpbb\user 								$user 					The user object
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\notification\manager $notification_manager, \phpbbservices\notifyonerror\core\common $helper, \phpbb\user $user)
	{
		$this->config = $config;
		$this->helper = $helper;
		$this->notification_manager = $notification_manager;
		$this->user = $user;
	}

	/**
	 * Load common language files during user setup
	 *
	 * @param \phpbb\event\data	$event	Event object
	 */
	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = [
			'ext_name' => 'phpbbservices/notifyonerror',
			'lang_set' => 'common',
		];
		$event['lang_set_ext'] = $lang_set_ext;
	}

	/**
	 * Allows to modify log data before we add it to the database
	 *
	 * NOTE: if sql_ary does not contain a log_type value, the entry will
	 * not be stored in the database. So ensure to set it, if needed.
	 *
	 * @event core.add_log
	 * @var	string	mode			Mode of the entry we log
	 * @var	int		user_id			ID of the user who triggered the log
	 * @var	string	log_ip			IP of the user who triggered the log
	 * @var	string	log_operation	Language key of the log operation
	 * @var	int		log_time		Timestamp, when the log was added
	 * @var	array	additional_data	Array with additional log data
	 * @var	array	sql_ary			Array with log data we insert into the
	 *							database. If sql_ary[log_type] is not set,
	 *							we won't add the entry to the database.
	 * @since 3.1.0-a1
	 */
	public function send_error_notification($vars)
	{
		// Notify on critical error log entries only
		if ($vars['mode'] === 'critical')	// Change this to 'admin' to test. Submitting any ACP form will then trigger the error.
		{
			$options = array();

			// Get the next notification ID
			$notification_id = $this->helper->get_next_notification_id();

			// Send each admin who can view logs an email notification
			$this->notification_manager->add_notifications('phpbbservices.notifyonerror.notification.type.errorlog', [
				'item_id'   => $notification_id,
				'user_id'	=> $vars['user_id'],
			],
			$options);
		}
	}

}
