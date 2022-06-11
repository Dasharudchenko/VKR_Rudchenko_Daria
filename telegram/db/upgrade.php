<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Upgrade code for the Telegram message processor
 *
 * @package message_telegram
 * @author  Daria Rudchenko
 * @copyright 2022 Daria Rudchenko
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade code for the Telegram message processor
 *
 * @param int $oldversion The version that we are upgrading from
 */
function xmldb_message_telegram_upgrade($oldversion)
{
	global $DB;

	if ($oldversion < 2022051600.00) {
		$processor = new stdClass();
		$processor->name  = 'telegram';
		if (!$DB->record_exists('message_processors', array('name' => $processor->name))) {
			$DB->insert_record('message_processors', $processor);
		}
		upgrade_plugin_savepoint(true, 2022051600.00, 'message', 'telegram');
	}

	return true;
}
