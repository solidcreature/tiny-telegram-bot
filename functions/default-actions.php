<?php
add_action('tinybot_data_recieved_hook', 'tinybot_qualify_data', 10, 1);
add_action('tinybot_data_recieved_hook', 'tinybot_save_last_data', 20, 1);
add_action('tinybot_last_response_hook', 'tinybot_save_last_response', 10, 1);
add_action('tinybot_greetings_hook','tinybot_send_greetings', 10, 2);	
add_action('tinybot_greetings_hook','tinybot_do_person_reset', 20, 2);	
add_action('tinybot_nofound_hook','tinybot_send_nofound_message', 10, 3);	

//Сохраняем в Options последнее пришедшее в бот сообщение
//Можно посмотреть здесь /wp-admin/admin.php?page=tinybot-lastmessage
function tinybot_save_last_data($data) {
	update_option('tinybot_lastmessage', print_r($data, true));
}

//Сохраняем в Options ответ от Telegram API на последнее действие бота
//Можно посмотреть здесь /wp-admin/admin.php?page=tinybot-lastmessage
function tinybot_save_last_response($out) {
	update_option('tinybot_lastresponse', print_r($out, true));
}

//Проверяем является ли сообщение картинкой или видео-файлом. Если так, то сохраняем file_id как отдельный тип записи
//Настройка что сохранять и посмотреть список можно здесь /wp-admin/admin.php?page=tinybot-media
function tinybot_qualify_data($data) {
	//Проверяем тип присланных данных
	
	//Задаем переменную, чтобы в принципе понимать надо нам сохранять данные медиа или не надо
	$save_tg_media = true;

	//Если отмечено от всех пользователей, то сохраняем все медиа 0 - все пользователи / 1 - только от админа
	//Если отмечено от администратора, но его ID не задан, то ничего не сохраняем
	//Из групповых чатов ничего не сохраняем по умолчанию
	$tinybot_adminonly = get_option('tinybot_adminonly');
	$tinybot_admintg = get_option('tinybot_admintg'); 		
	if ($tinybot_adminonly AND $data['message']['from']['id'] != $tinybot_admintg) { $save_tg_media = false; }	

	//Делаем проверку, если сообщение пришло из группового чата, то дальше не идем
	if ( $data['message']['from']['id'] != $data['message']['chat']['id']) { $save_tg_media = false; }		
	
	//Сохраняем фото
	if ( isset($data['message']['photo']) ) {
		
		if ($save_tg_media) {
			tinybot_save_tgphoto($data);	
		}		
	}
	
	//Сохраняем видео
	if ( isset($data['message']['video']) ) {
		if ($save_tg_media) {
			tinybot_save_tgvideo($data);		
		}				
	}	
	
	return;
}



//Отправляем простое текстовое приветствие или приветствие с картинкой на старте
//Настройка приветствия /wp-admin/admin.php?page=tinybot-greetings
function tinybot_send_greetings($chat_id, $person_id) {	
	//Получим параметры плагина со страницы настроек Приветствие
	$greetings_photo = get_option('tinybot_greetingsphoto');
	
	//Если текст приветствия не задан -- выводим заполняющий текст
	$greetings_text = get_option('tinybot_greetingstext'); 
	if (!$greetings_text) $greetings_text = 'Вас приветсвует Tiny Bot 2.1' . PHP_EOL .'<em>Вы можете поменять текст в настройках бота</em>';
	
	$has_keyboard = get_option('tinybot_greetingskeyboard');
	
	//Для начала настроим клавиатуру, если параметр не задан, то удаляем уже имеющуюся клавитауру
	if ($has_keyboard) {
		$keyboard = tinybot_default_keyboard();
	}
	else {
		$keyboard = tinybot_remove_keyboard();
	}
	
	//Развилка, если есть фото, отправляем фото и подпись, если нет, то только текст
	if ($greetings_photo) {
		tinybot_send_photo($chat_id, $greetings_photo, $greetings_text, $keyboard);
	}
	else {
		tinybot_send($chat_id, $greetings_text, $keyboard);
	}	
}


//Обнуляем счетчик и статус пользователя
//Ставим начальные значения для мета-данных телеграм-пользователя
function tinybot_do_person_reset($chat_id, $person_id) {
	update_post_meta( $person_id, 'tg_status', '');
	update_post_meta( $person_id, 'tg_count', 0);
}


//Шлём сообщение, что не поняли команду
function tinybot_send_nofound_message($chat_id, $message, $person_id) {
	$text = 'Бот не распознал команду <i>' . $message . '</i>';
	tinybot_send($chat_id, $text);
}