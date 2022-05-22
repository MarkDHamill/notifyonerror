<?php
/**
 *
 * Notify on new error log entries. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2022, MarkDHamill, https://www.phpbbservices.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbservices\notifyonerror\migrations;

class release_1_0_0 extends \phpbb\db\migration\migration
{

	public function effectively_installed()
	{
		return false;
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v330\v330');
	}

	public function update_data()
	{
		return array(
			array('config.add', array('phpbbservices_notifyonerror_notification_id', '0')
			)
		);
	}

}
