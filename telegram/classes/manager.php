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
 * Telegram message plugin version information.
 *
 * @package message_telegram
 * @author  Daria Rudchenko
 * @copyright 2022 Daria Rudchenko
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir . '/filelib.php');

/**
 * Telegram helper manager class
 *
 * @author  Daria Rudchenko
 * @copyright 2022 Daria Rudchenko
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class message_telegram_manager
{

	//Base URL для Telegram Bot API
	const TELEGRAM_BOT_API_BASE_URL = 'https://api.telegram.org/bot';

	// Сколько последних сообщений брать при запросе getUpdates к боту.
	//
	// Процесс связи аккаунтов в Moodle и Telegram состоит из трех шагов:
	//
	//	1.	Пользователь нажал на линк:
	//		https://t.me/bot_username?start=<moodle_session_id>
	//
	//	2.	Мы выполнили запрос к боту:
	//		https://api.telegram.org/bot<bot_token>/getUpdates?offset=-<TELEGRAM_BOT_GET_LAST_UPDATES>
	//
	//	3.	И нашли в присланном куске истории сообщений текст, переданный боту в п.1:
	//		/start <moodle_session_id>
	//
	// Фактически, число TELEGRAM_BOT_GET_LAST_UPDATES - это максимальное количество кликов на линк
	// "Связать мои аккаунты Moodle и Telegram" разными пользователями за время,
	// прошедшее между таким кликом определенного пользователя, и нажатием им же на кнопку "Сохранить",
	// для выполнения успешной связи аккаунтов этого пользователя в Moodle и Telegram
	// (т.е., для успеха поиска текста "/start <moodle_session_id>" в полученном "куске" истории сообщений бота).
	//
	const TELEGRAM_BOT_GET_LAST_UPDATES = 50;

	private $curl;

	/**
	 * Конструктор.
	 * Инициализируем все необходимые данные для быстрого доступа к ним во время работы.
	 */
	public function __construct()
	{
		$this->curl = new curl();
	}

	/**
	 * Посылаем боту команду getUpdates?offset=-N,
	 * затем ищем в полученных сообщениях текст "/start <текущая сессия пользователя в Moodle>"
	 *
	 * @return boolean Success
	 */
	public function get_chatid()
	{
		global $USER;

		//Посылаем боту через Telgram Bot API команду getUpdates с отрицательным offset.
		//Т.е. запрашиваем у бота TELEGRAM_BOT_GET_LAST_UPDATES последних сообщений.
		//При этом все остальные сообщения, присланные боту,
		//автоматически удаляются на стороне Telegram (потому, что мы используем параметр ?offset=).
		$response = $this->send_to_bot_api('getUpdates?offset=-' . self::TELEGRAM_BOT_GET_LAST_UPDATES);
		if (!$response->ok) {
			//Если Telegram возвращает ошибку
			return false;
		}

		$userid = $USER->id;

		//'/start <текущая сессия пользователя в Moodle>'
		$search_text = '/start ' . sesskey();

		$results = $response->result;

		//Перебираем все полученные сообщения (TELEGRAM_BOT_GET_LAST_UPDATES последних сообщений, отправленных боту).
		foreach ($results as $result) {
			//Если нашли текст '/start <текущая сессия пользователя в Moodle>'
			if (isset($result->message) && isset($result->message->text) && ($result->message->text == $search_text)) {

				//Сохраняем chat_id чата пользователя, от которого это сообщение пришло (это - текущий пользователь Moodle).
				set_user_preference('message_processor_telegram_chatid', $result->message->chat->id, $userid);

				//Также сохраняем текст "Сообщение от " (отображаемое в Telegram) в его локализации.
				set_user_preference('message_processor_telegram_localized_messagefrom', get_string('messagefrom', 'message_telegram'), $userid);

				//Также сохраняем текст "Уведомление" (отображаемое в Telegram) в его локализации.
				set_user_preference('message_processor_telegram_localized_notification', get_string('notification', 'message_telegram'), $userid);

				return true;
			}
		}
		return false;
	}

	/**
	 * Посылаем боту команду sendMessage с текстом сообщения.
	 *
	 * @param string $message : текст, который необходимо передать.
	 * @param string $recipient_tg_chatid : chat_id чата получателя сообщения с Telegram-ботом.
	 */
	public function send_message($message, $recipient_tg_chatid)
	{
		$response = $this->send_to_bot_api(
			'sendMessage',
			[
				'chat_id' => $recipient_tg_chatid,
				'text' => $message,
				'parse_mode' => 'HTML'
			]
		);
		return (!empty($response) && isset($response->ok) && ($response->ok == true));
	}

	/**
	 * Отправляет команду через Telegram Bot API, и возвращает JSON-decoded результат.
	 *
	 * @param string $command : команда Telegram Bot API
	 * @param array $params : параметры команды
	 * @return object The JSON-decoded object.
	 */
	private function send_to_bot_api($command, $params = null)
	{
		global $CFG;
		return json_decode($this->curl->get(self::TELEGRAM_BOT_API_BASE_URL . trim($CFG->telegrambottoken) . '/' . $command, $params));
	}
}
