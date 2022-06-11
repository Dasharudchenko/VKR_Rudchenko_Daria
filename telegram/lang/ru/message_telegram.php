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
 * Strings for component 'message_telegram', language 'ru'
 *
 * @package message_telegram
 * @author  Daria Rudchenko
 * @copyright 2022 Daria Rudchenko
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Telegram';

$string['linkaccountsinfo'] = '
Для получения сообщений из Moodle в вашем Telegram, вам необходимо связать ваш аккаунт в Moodle с вашим аккаунтом в Telegram.
Для этого выполните следующие шаги:
<ol>
<li>Кликните на линк:<br>[connect_url]</li>
<li>В открывшемся клиенте Telegram нажмите "Начать".</li>
<li>Вернитесь в это окно и нажмите кнопку "Сохранить".</li>
</ol>
При необходимости, после этого вы сможете отвязать свой аккаунт в Moodle от своего аккаунта в Telegram.
';

$string['linkaccountslinktext'] = 'Связать мои аккаунты Moodle и Telegram';
$string['accountislinked'] = 'Связать мои аккаунты Moodle и Telegram';

$string['unlinkaccountsinfo'] = '
<b>Ваши аккаунты в Moodle и Telegram связаны. Сообщения пересылаются в ваш Telegram.</b><br>
Для того, чтобы отвязать ваш аккаунт в Moodle от вашего аккаунта в Telegram, снимите чекбокс и нажмите кнопку "Сохранить".
';

$string['messagefrom'] = 'Сообщение от ';
$string['notification'] = 'Уведомление';

$string['telegrambottoken'] = 'Токен Telegram-бота';
$string['configtelegrambottoken'] = '';

$string['telegramboturl'] = 'URL Telegram-бота';
$string['configtelegramboturl'] = '';

$string['modulenotconfigured'] = 'Модуль отправки сообщений в Телеграм не настроен администратором.';

$string['adminmodulesetupinfo'] = '
Для настройки модуля отправки сообщений в Telegram, вам необходимо указать <b>URL</b> и <b>token</b> Telegram-бота, который будет заниматься такой отправкой.
<br>
Если такой бот еще не создан, то, для его создания, выполните следующие шаги:
<ol>
<li>В клиенте Telegram найдите бота с именем <b>"BotFather"</b>, и нажмите у него кнопку <b>"Начать"</b>.</li>
<li>Отправьте этому боту команду: <b>/newbot</b></li>
<li>Введите и отправьте боту любое имя вашего нового бота.</li>
<li>Введите и отправьте боту username вашего нового бота. Необходимо придумать такой username, который свободен для регистрации.</li>
<li>После того, как BotFather отобразит сообщение об успешном создании вашего бота,
скопируйте линк на вашего бота (он указан после слов <b>"You will find it at"</b>),
и вставьте его в поле <b>"URL Telegram-бота"</b> ниже,
затем скопируйте токен вашего бота (он указан после слов <b>"Use this token to access the HTTP API:"</b>),
и вставьте его в поле <b>"Токен Telegram-бота"</b> ниже.</li>
</ol>
Настройка завершена.<br>Теперь, для сохранения настроек модуля, нажмите кнопку "Сохранить изменения".
<hr>
';
