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
 * Strings for component 'message_telegram', language 'en'
 *
 * @package message_telegram
 * @author  Daria Rudchenko
 * @copyright 2022 Daria Rudchenko
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Telegram';

$string['linkaccountsinfo'] = '
To receive messages from Moodle in your Telegram, you need to link your Moodle account to your Telegram account.
To do this, follow these steps:
<ol>
<li>Click on the link:<br>[connect_url]</li>
<li>In the Telegram client that opens, click "Start".</li>
<li>Go back to this window and click "Save changes".</li>
</ol>
After that, you can unlink your Moodle account from your Telegram account, if necessary.
';

$string['linkaccountslinktext'] = 'Link my Moodle and Telegram accounts';
$string['accountislinked'] = 'Link my Moodle and Telegram accounts';

$string['unlinkaccountsinfo'] = '
<b>Your Moodle and Telegram accounts are linked. Messages are forwarded to your Telegram.</b><br>
In order to unlink your Moodle account from your Telegram account, uncheck the box and click "Save changes".
';

$string['messagefrom'] = 'Message from ';
$string['notification'] = 'Notification';

$string['telegrambottoken'] = 'Telegram bot token';
$string['configtelegrambottoken'] = '';

$string['telegramboturl'] = 'Telegram bot URL';
$string['configtelegramboturl'] = '';

$string['modulenotconfigured'] = 'The Telegram messaging module is not configured by the administrator.';

$string['adminmodulesetupinfo'] = '
To configure the Telegram messaging module, you need to specify the <b>URL</b> and <b>token</b> of the Telegram bot that will do the messaging.
<br>
If such a bot has not yet been created, follow these steps to create one:
<ol>
<li>In the Telegram client, find the bot named <b>"BotFather"</b> and click its <b>"Start"</b> button.</li>
<li>Send this bot a command: <b>/newbot</b></li>
<li>Enter and send the bot any name of your new bot.</li>
<li>Enter and send the username of your new bot to the bot. You must come up with a username that is free to register.</li>
<li>After BotFather displays a message saying your bot was successfully created,
copy the link to your bot (it\'s after <b>"You will find it at"</b>), and paste it into the <b>"Telegram bot URL"</b> field below,
then copy your bot\'s token (it\'s after <b>"Use this token to access the HTTP API:"</b>),
and paste it into the <b>"Telegram bot token"</b> field below.</li>
</ol>
The settings are complete.<br>Now, to save the module settings, click the "Save changes" button.
<hr>
';
