<?php
/**
 *
 * Notify on new error log entries. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2022, MarkDHamill, https://www.phpbbservices.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbservices\notifyonerror\notification\type;

/**
 * Notify on new error log entries Notification class.
 */
class errorlog extends \phpbb\notification\type\base
{

	/* The following objects are defined in the parent class and can be used if needed:
		$auth
		$db
		$language
		$user
		$phpbb_root_path
		$php_ext
		$user_notifications_table
	*/

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\user_loader */
	protected $user_loader;

	/**
	 * Set controller helper.
	 *
	 * @param \phpbb\controller\helper  $helper  Controller helper object
	 * @return void
	 */
	public function set_helper(\phpbb\controller\helper $helper)
	{
		$this->helper = $helper;
	}

	/**
	 * Set user loader.
	 *
	 * @param \phpbb\user_loader  $user_loader  User loader object
	 * @return void
	 */
	public function set_user_loader(\phpbb\user_loader $user_loader)
	{
		$this->user_loader = $user_loader;
	}

	public function set_config(\phpbb\config\config $config)
	{
		$this->config = $config;
	}

	/**
	 * Get notification type name
	 *
	 * @return string
	 */
	public function get_type()
	{
		return 'phpbbservices.notifyonerror.notification.type.errorlog';
	}

	/**
	 * Notification option data (for outputting to the user)
	 *
	 * @var bool|array False if the service should use it's default data
	 * 					Array of data (including keys 'id', 'lang', and 'group')
	 */
	public static $notification_option = [
		'lang'	=> 'PHPBBSERVICES_NOTIFYONERROR_NOTIFICATION_NOTIFYONERROR',
		'group' => 'PHPBBSERVICES_NOTIFYONERROR_NOTIFICATIONS',
	];

	/**
	 * Is this type available to the current user (defines whether or not it will be shown in the UCP Edit notification options)
	 *
	 * @return bool True/False whether or not this is available to the user
	 */
	public function is_available()
	{
		// If a user can view the admin logs, they should have a control on the UCP Edit notifications options
		return $this->auth->acl_get('a_viewlogs');
	}

	/**
	 * Get the id of the notification
	 *
	 * @param array $data The type specific data
	 *
	 * @return int Id of the notification
	 */
	public static function get_item_id($data)
	{
		// Return the next notification ID for this extension.
		return $data['item_id'];
	}

	/**
	 * Get the id of the parent
	 *
	 * @param array $data The type specific data
	 *
	 * @return int Id of the parent
	 */
	public static function get_item_parent_id($data)
	{
		// No parent
		return 0;
	}

	/**
	 * Find the users who want to receive notifications
	 *
	 * @param array $data The type specific data
	 * @param array $options Options for finding users for notification
	 * 		ignore_users => array of users and user types that should not receive notifications from this type because they've already been notified
	 * 						e.g.: [2 => [''], 3 => ['', 'email'], ...]
	 *
	 * @return array
	 */
	public function find_users_for_notification($notifications_data, $options = [])
	{
		// Get a list of users with the a_viewlogs permission
		$acl_get_ary = $this->auth->acl_get_list(false, 'a_viewlogs');
		$response = $this->check_user_notification_options($acl_get_ary[0]['a_viewlogs']);

		// If this admin triggered the error, they should see an error message, so don't send them a notification
		unset($response[$this->user->data['user_id']]);
		return $response;
	}


	/**
	 * Users needed to query before this notification can be displayed
	 *
	 * @return array Array of user_ids
	 */
	public function users_to_query()
	{
		// All notifications are at the administrator level so no usernames need appear in the user notification message.
		return [];
	}

	/**
	 * Get the HTML formatted title of this notification
	 *
	 * @return string
	 */
	public function get_title()
	{
		return $this->language->lang('PHPBBSERVICES_NOTIFYONERROR_NOTIFYONERROR_TITLE');
	}

	/**
	 * Get the url to this item
	 *
	 * @return string URL
	 */
	public function get_url()
	{
		return append_sid("{$this->phpbb_root_path}adm/index.{$this->php_ext}?i=acp_logs&amp;mode=critical");
	}

	/**
	 * Get email template
	 *
	 * @return string|bool
	 */
	public function get_email_template()
	{
		return '@phpbbservices_notifyonerror/errorlog';
	}

	/**
	 * Get email template variables
	 *
	 * @return array
	 */
	public function get_email_template_variables()
	{
		// For emailing, need to remove the relative path from the ACP error log URL and make it absolute
		return [
			'ERROR_LOG_URL'	=> str_replace($this->phpbb_root_path, '/', $this->get_url()),
		];
	}

}
