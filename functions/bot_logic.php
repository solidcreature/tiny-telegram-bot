<?php 
add_action('tinybot_commands_hook','tinybot_send_person_chatid', 10, 3);
add_action('tinybot_commands_hook','tinybot_set_person_status_to_busy', 10, 3);
add_action('tinybot_status_commands_hook','tinybot_send_busy_message', 10, 4);

//Отправляем участнику его ID в Телеграме
function tinybot_send_person_chatid($chat_id, $message, $person_id) {
	if ($message != 'Узнать свой ID') return;
	
	$text = 'Ваш ID в Телеграме: <code>' . $chat_id . '</code>';
	tinybot_send($chat_id, $text);
	
	exit('ok');
}

//Устанавливаем статус Busy для участника
function tinybot_set_person_status_to_busy($chat_id, $message, $person_id) {
	if ($message != 'Установить статус Busy') return;	
	
	//Меняем статус участника на busy
	update_post_meta($person_id, 'tg_status', 'busy');
	
	$text = '<em>Ваш статус установлен на Busy</em>';
	tinybot_send($chat_id, $text);
	
	exit('ok');	
	
}	

//Шлем сообщение о текущем статусе
function tinybot_send_busy_message($chat_id, $message, $person_id, $person_status) {
	if ($person_status != 'busy') return;	
	
	$text = 'В режиме busy вы игнорируете любые команды и сообщения кроме /start';
	tinybot_send($chat_id, $text);
	
	exit('ok');	
}