<?php
//Демо-файл позволяет сразу	
add_action('tinybot_greetings_hook','tinybot_demo_start', 40, 2);	
add_action('tinybot_actions_hook','tinybot_demo_send_chatid', 10, 3);
add_action('tinybot_actions_hook','tinybot_demo_set_status_busy', 10, 3);
add_action('tinybot_status_actions_hook','tinybot_demo_send_busy_text', 10, 4);


//ДЕМО - Отправляем сообщение о том, что это демо-функционал и добавляем клавиатуру
function tinybot_demo_start($chat_id, $person_id) {
	$text = 'Бот работает в демо-режиме, доступен только базовый функционал. Нажмите на одну из кнопок ниже';
	
	//Задаем массив строк, состоящий из массива кнопок
	$rows = [['Узнать свой ID'],['Установить статус Busy']];
	
	//Получаем клавиатуру на основе заданных строк
	$keyboard = tinybot_get_keyboard($rows);
	
	//Отправляем сообщение и завершаем работу бота
	tinybot_send($chat_id, $text, $keyboard);
}	

//ДЕМО - Отправляем пользователю его ID в Телеграме, если он нажал кнопку "Узнать свой ID"
function tinybot_demo_send_chatid($chat_id, $message, $person_id) {
	if ($message != 'Узнать свой ID') return;
	
	$text = 'Ваш ID в Телеграме: <code>' . $chat_id . '</code>';
	tinybot_send($chat_id, $text);
	
	exit('ok');
}

//ДЕМО - Меняем Статус участника на busy при нажатии на кнопку "Установить статус Busy"
function tinybot_demo_set_status_busy($chat_id, $message, $person_id) {
	if ($message != 'Установить статус Busy') return;	
	
	//Меняем статус участника на busy
	update_post_meta($person_id, 'tg_status', 'busy');
	
	$text = '<em>Ваш статус установлен на Busy</em>';
	tinybot_send($chat_id, $text);
	
	exit('ok');	
	
}	

//ДЕМО - Шлем пользователю сообщение, о том что он в статусе Busy, игнорируем другие команды
function tinybot_demo_send_busy_text($chat_id, $message, $person_id, $person_status) {
	if ($person_status != 'busy') return;	
	
	$text = 'В режиме busy вы игнорируете любые команды и сообщения кроме /start';
	tinybot_send($chat_id, $text);
	
	exit('ok');	
}