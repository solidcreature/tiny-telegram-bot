<?php

//Отправляем простое текстовое приветствие на старте
function tinybot_send_person_greetings($chat_id, $person_id) {	
	$text = 'Tiny Telegram Bot v 1.0.0';
	$keyboard = tinybot_default_keyboard();
	
	tinybot_send($chat_id, $text, $keyboard);
}

//Обнуляем счетчик и статус пользователя
function tinybot_do_person_reset($chat_id, $person_id) {
	update_post_meta( $person_id, 'tg_status', '');
	update_post_meta( $person_id, 'tg_count', '');
}

//Шлем сообщение, что не поняли команду
function tinybot_send_person_nofound_message($chat_id, $message, $person_id) {
	$text = 'Бот не распознал команду <i>' . $message . '</i>';
	tinybot_send($chat_id, $text);
}