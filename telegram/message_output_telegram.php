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
 * Contains the definiton of the Telegram message processor (sends messages to users via Telegram)
 *
 * @package message_telegram
 * @author  Daria Rudchenko
 * @copyright 2022 Daria Rudchenko
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/message/output/lib.php');

/**
 * The Telegram message processor class
 *
 * @package message_telegram
 * @author  Daria Rudchenko
 * @copyright 2022 Daria Rudchenko
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class message_output_telegram extends message_output
{

	private $manager;
	private $nobotdata;
	private $chatidisset;

	/**
	 * Конструктор.
	 * Инициализируем все необходимые данные для быстрого доступа к ним во время работы.
	 */
	public function __construct()
	{
		global $CFG, $USER;

		$this->manager = new message_telegram_manager();

		//true: администратор не заполнил поле telegramboturl или telegrambottoken
		$this->nobotdata = ($CFG->telegramboturl == '') || (trim($CFG->telegrambottoken) == '');

		//true: chat_id с ботом Телеграма уже сохранен для текущего пользователя.
		//preference 'message_processor_telegram_chatid' задается в $this->manager->get_chatid(),
		//удаляется в $this->process_form() при снятии чекбокса пользователем.
		$this->chatidisset = get_user_preferences('message_processor_telegram_chatid', '', $USER->id) != '';
	}

	/**
	 * Обрабатывает сообщение и отправляет уведомление через Telegram
	 *
	 * @param object $eventdata : данные, переданные отправителем сообщения, и, дополнительно, - $eventdata->savedmessageid
	 * @return true if ok, false if error
	 */
	function send_message($eventdata)
	{
		global $CFG;

		//Не отправляем сообщения, адресованные приостановленным или удаленным пользователям.
		if (
			$eventdata->userto->auth === 'nologin' ||
			$eventdata->userto->suspended ||
			$eventdata->userto->deleted
		) {
			return true;
		}

		//Не отправляем сообщение, если в config.php установлен флаг $CFG->noemailever,
		//используемый для отладки, и означающий запрет посылки сообщений реальным пользователям.
		if (!empty($CFG->noemailever)) {
			//debugging('$CFG->noemailever is active, no Telegram message sent.', DEBUG_MINIMAL);
			return true;
		}

		//Если администратор заполнил не все поля в форме настройки модуля отправки сообщений через Telegram.
		if ($this->nobotdata) {
			return true;
		}

		$recipient_userid = $eventdata->userto->id;
		$recipient_tg_chatid = get_user_preferences('message_processor_telegram_chatid', '', $recipient_userid);

		//Если получатель не настроил у себя модуль отправки сообщений через Telegram.
		//Такая настройка заключается в создании связи его акканута в Moodle с его акканутом в Telegram
		//(получение и сохранение chat_id чата этого пользователя с Telegram-ботом).
		if (empty($recipient_tg_chatid)) {
			return true;
		}

		//Посылаем сообщение получателю путем отправки команды 'sendMessage' нашему Telegram-боту (Telegram Bot API).
		//Бот, получив такую команду, отправляет это сообщение в приватный чат с получателем.
		//Идентификатор такого чата (chat_id) мы получили в $this->manager->get_chatid(),
		//и сохранили в preference получателя 'message_processor_telegram_chatid'.
		//Используем HTML-стиль сообщений.
		return $this->manager->send_message(
			'<b>' .
				(
					//1: это - уведомление Moodle; 0: это - сообщение от пользователя
					($eventdata->notification == 1) ?
					//"Уведомление"
					(get_user_preferences('message_processor_telegram_localized_notification', '', $recipient_userid))
					:
					//"Сообщение от <fullname>"
					(get_user_preferences('message_processor_telegram_localized_messagefrom', '', $recipient_userid) . fullname($eventdata->userfrom))
				) .
				':</b>' . "\n" .
				$eventdata->smallmessage, //формат сообщения, используемый для SMS, Twitter, и т.п.
			$recipient_tg_chatid
		);
	}

	/**
	 * Создаем необходимые поля в форме конфигурации модуля обмена сообщениями для текущего пользователя.
	 *
	 * @param array $preferences : массив с настройками текущего пользователя
	 */
	function config_form($preferences)
	{
		global $CFG;

		//Если администратор заполнил не все поля в форме настройки модуля отправки сообщений через Telegram.
		if ($this->nobotdata) {
			//Просто отображаем собщение об этом.
			return get_string('modulenotconfigured', 'message_telegram');
		} else if ($this->chatidisset) {
			//chat_id с ботом Телеграма уже сохранен для текущего пользователя.

			//Отображаем чекбокс, указывающий, что аккаунт пользователя в Telegram связан с его акканутом в Moodle.
			//Если пользователь снимет этот чекбокс и нажмет кнопку "Сохранить",
			//то preference 'message_processor_telegram_chatid' удалится (тут: $this->process_form()),
			//тем самым для текущего пользователя удалится связь его аккаунта в Telegram с его аккаунтом в Moodle,
			//и, в этом случае, отправка сообщений текущему пользователю в Telegram будет невозможна.
			return	get_string('unlinkaccountsinfo', 'message_telegram') . '<br><br><input type="checkbox" name="chk_link_accounts" id="chk_link_accounts_id" checked="checked"><label for="chk_link_accounts_id">&nbsp;' . get_string('accountislinked', 'message_telegram') . '</label>';
		}
		//chat_id с ботом Телеграма еще НЕ сохранен для текущего пользователя.

		//Отображаем пользователю инструкцию по связи его аккаунта в Telegram с его аккаyнтом в Moodle.

		return str_replace(
			'[connect_url]',
			'<a href="' . $CFG->telegramboturl . '?start=' . sesskey() . '" target="_blank">' . get_string('linkaccountslinktext', 'message_telegram') . '</a>',
			get_string('linkaccountsinfo', 'message_telegram')
		);
	}

	/**
	 * Обрабатывает данные формы настройки модуля после нажатия кнопки "Сохранить".
	 *
	 * @param object $form : preferences form class
	 * @param array $preferences : массив с настройками текущего пользователя
	 */
	function process_form($form, &$preferences)
	{
		global $USER;

		//Если администратор заполнил не все поля в форме настройки модуля отправки сообщений через Telegram.
		//Пользователь прочитал сообщение об этом.
		if ($this->nobotdata) {
			//Ничего не делаем.
			return false;
		} else if ($this->chatidisset) {
			//chat_id с ботом Телеграма уже сохранен для текущего пользователя.
			//Пользователю отображалось сообщение с чекбоксом.
			//Проверяем - включен ли чекбокс.
			if (isset($form->chk_link_accounts)) {
				//Чекбокс включен (т.е., его не выключили). Ничего не делаем.
				return true;
			}
			//Чекбокс выключили.
			//Это означет, что пользователь захотел удалить связь его аккаунта в Telegram
			//с его аккаунтом в Moodle.
			//Для этого просто удаляем сохраненный в 'message_processor_telegram_chatid'
			//chat_id чата пользователя с ботом Телеграма.
			unset_user_preference('message_processor_telegram_chatid', $USER->id);
			return true;
		}
		//Администратор заполнил все поля в форме настройки модуля отправки сообщений через Telegram,
		//и chat_id чата пользователя с ботом Телеграма еще НЕ сохранен или был удален.

		//Посылаем боту Телеграма команду getUpdates с отрицательным offset,
		//и ищем в результатах команду "/start <текущая сессия пользователя в Moodle>"
		return $this->manager->get_chatid();
	}

	/**
	 * Загружает данные конфигурации, чтобы поместить их на форму при первоначальном отображении формы
	 *
	 * @param array $preferences preferences array
	 * @param int $userid the user id
	 */
	function load_data(&$preferences, $userid)
	{
	}
}
