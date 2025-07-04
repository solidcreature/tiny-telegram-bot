<?php
//Список поддерживаемых методов телеграма
//getMe
//setMyCommands
//getMyCommands
//sendMessage
//forwardMessage
//sendPhoto
//sendAudio
//sendDocument
//sendVideo
//sendAnimation
//sendVoice
//sendVideoNote
//sendMediaGroup
//sendLocation
//sendVenue
//sendContact
//sendPoll
//sendDice
//sendChatAction

//Редактирование сообщений
//editMessageText
//editMessageCaption
//editMessageMedia
//editMessageReplyMarkup
//stopPoll
//deleteMessage




//Основной запрос к АПИ телеграма, можно использовать любой метод, даже если для него нет отдельной функции
//Все функции ниже используют в качестве основы tinybot_request()
function tinybot_request($method, $data = array()) {
	$url = 'https://api.telegram.org/bot' . TINY_BOT_TOKEN .  '/' . $method;
	$args = array(
		'body'   => $data
	);

    $raw_out = wp_remote_post( $url, $args );
	$out = json_decode($raw_out['body']);
	
	do_action('tinybot_last_response_hook', $out);
	
    return $out; 
}


//Функция  получает информацию о боте и возвращает ее в текстовом виде
//https://core.telegram.org/bots/api#getme
function tinybot_get_info() {
	$out = tinybot_request('getMe');
	$out = $out -> result;
	
	$text .= '<p>ID: ' . $out -> id . '</p>';
	$text .= '<p>Name: ' . $out -> first_name . '</p>';
	$text .= '<p>Username: <a href="https://t.me/' . $out -> username . '">@' . $out -> username . '</a>' . '</p>';
	
	$text .= '<p>Можно добавлять в группы: '  . $out -> can_join_groups . '</p>';
	$text .= '<p>Имеет доступ ко всем сообщениям: '  . $out -> can_read_all_group_messages . '</p>';
	$text .= '<p>Поддерживает инлайн-режим: '  . $out -> supports_inline_queries . '</p>';
	$text .= '<p>[can_connect_to_business]: '  . $out -> can_connect_to_business . '</p>';
	$text .= '<p>Есть подключенное приложение: '  . $out -> has_main_web_app . '</p>';
	
	return($text);
}


//Функция  получает информацию о настройках вебхука
//https://core.telegram.org/bots/api#getwebhookinfo
function tinybot_get_webhook_info() {
	$out = tinybot_request('getWebhookInfo');
	$out = $out -> result;
	
	return($out);
}


//Отправка простого сообщения по умолчанию без клавиатуры
//https://core.telegram.org/bots/api#sendmessage
function tinybot_send($chat_id, $text, $keyboard='', $parse_mode='HTML', $disable_preview = false, $silent = false, $reply = '' ) { 
	$text = tinybot_clear_tags($text); 
	$data = array(
		'chat_id'      => $chat_id,
		'text'     => $text,
		'parse_mode' => $parse_mode,
		'disable_web_page_preview' => $disable_preview,
		'disable_notification' => $silent,
		'reply_to_message_id' => $reply
	);
	
	if (is_array($keyboard)) { 
		$replyMarkup = json_encode($keyboard);
		$data['reply_markup'] = $replyMarkup; 
	}
	
	
	$out = tinybot_request('sendMessage', $data);
	return $out;
}
     

//Отправка изображения
//https://core.telegram.org/bots/api#sendphoto   
function tinybot_send_photo($chat_id, $photo, $caption='', $keyboard='', $parse_mode='HTML', $disable_preview = false, $silent = false, $reply = '') { 
		
	$data = array(
		'chat_id'      => $chat_id,
		'photo'     => $photo,
		'caption' => $caption,
		'parse_mode' => $parse_mode,
		'disable_web_page_preview' => $disable_preview,
		'disable_notification' => $silent,
		'reply_to_message_id' => $reply					
	);

	if (is_array($keyboard)) { 
		$replyMarkup = json_encode($keyboard);
		$data['reply_markup'] = $replyMarkup; 
	}

	$out = tinybot_request('sendPhoto', $data);
	return $out;	
}	

//Отправка аудио-файла   
//https://core.telegram.org/bots/api#sendaudio
function tinybot_send_audio($chat_id, $audio, $caption='', $keyboard='', $duration='', $performer='', $title='', $thumb='', $parse_mode = 'HTML', $silent = false, $reply = '') { 
		
        $data = array(
            'chat_id'      => $chat_id,
            'audio'     => $audio,
			'caption' => $caption,
			'duration' => $duration,
			'performer' => $performer,
			'title' => $title,
			'thumb' => $thumb,
			'parse_mode' => $parse_mode,
			'disable_notification' => $silent,
			'reply_to_message_id' => $reply					
        );
	
		if (is_array($keyboard)) { 
			$replyMarkup = json_encode($keyboard);
			$data['reply_markup'] = $replyMarkup; 
		}
	
        $out = tinybot_request('sendAudio', $data);
        return $out;	
}	



//Отправка документа   
//https://core.telegram.org/bots/api#senddocument
function tinybot_send_document($chat_id, $document, $caption='', $keyboard='', $thumb='', $parse_mode = 'HTML', $silent = false, $reply = '') { 
	
        $data = array(
            'chat_id' => $chat_id,
            'document' => $document,
			'caption' => $caption,
			'thumb' => $thumb,
			'parse_mode' => $parse_mode,
			'disable_notification' => $silent,
			'reply_to_message_id' => $reply					
        );
	
		if (is_array($keyboard)) { 
			$replyMarkup = json_encode($keyboard);
			$data['reply_markup'] = $replyMarkup; 
		}
	
        $out = tinybot_request('sendDocument', $data);
        return $out;	
}	



//Отправка видео-файла
//https://core.telegram.org/bots/api#sendvideo
function tinybot_send_video($chat_id, $video, $caption='', $keyboard='', $thumb='', $duration='', $width=320, $height=240, $streaming = false, $parse_mode = 'HTML', $silent = false, $reply = '') { 
	
	
        $data = array(
            'chat_id' => $chat_id,
            'video'   => $video,
            'caption' => $caption,
			'thumb' => $thumb,
			'duration' => $duration,
			'width' => $width,
			'height'=> $height,
			'supports_streaming' => $streaming,
			'parse_mode' => $parse_mode,
			'disable_notification' => $silent,
			'reply_to_message_id' => $reply					
        );
	
		if (is_array($keyboard)) { 
			$replyMarkup = json_encode($keyboard);
			$data['reply_markup'] = $replyMarkup; 
		}
	
        $out = tinybot_request('sendVideo', $data);
        return $out;	
}


//Отправка анимации
//https://core.telegram.org/bots/api#sendanimation
function tinybot_send_animation($chat_id, $animation, $caption='', $keyboard='', $thumb='', $duration='', $width=320, $height=240, $parse_mode = 'HTML', $silent = false, $reply = '') { 
	
        $data = array(
            'chat_id'      => $chat_id,
            'animation'     => $animation,
            'caption' => $caption,
			'thumb' => $thumb,
			'duration' => $duration,
			'width' => $width,
			'height' => $height,
			'parse_mode' => $parse_mode,
			'disable_notification' => $silent,
			'reply_to_message_id' => $reply					
        );
	
		if (is_array($keyboard)) { 
			$replyMarkup = json_encode($keyboard);
			$data['reply_markup'] = $replyMarkup; 
		}
	
        $out = tinybot_request('sendAnimation', $data);
        return $out;	
}


//Отправка аудио-сообщения
//https://core.telegram.org/bots/api#sendvoice
function tinybot_send_voice($chat_id, $voice, $caption='', $keyboard='', $duration='', $parse_mode = 'HTML', $silent = false, $reply = '') { 
		
        $data = array(
            'chat_id'      => $chat_id,
            'voice'     => $voice,
			'caption' => $caption,
			'duration' => $duration,
			'parse_mode' => $parse_mode,
			'disable_notification' => $silent,
			'reply_to_message_id' => $reply					
        );
	
		if (is_array($keyboard)) { 
			$replyMarkup = json_encode($keyboard);
			$data['reply_markup'] = $replyMarkup; 
		}
	
        $out = tinybot_request('sendVoice', $data);
        return $out;	
}	


//Отправка видео-сообщения
//https://core.telegram.org/bots/api#sendvideonote
function tinybot_send_videonote($chat_id, $video_note, $keyboard='', $thumb='',$duration='', $length='', $silent = false, $reply = '') { 
	
        $data = array(
            'chat_id'      => $chat_id,
            'video_note'     => $video_note,
			'duration' => $duration,
			'length' => $length,
			'thumb' => $thumb,
			'disable_notification' => $silent,
			'reply_to_message_id' => $reply					
        );
	
		if (is_array($keyboard)) { 
			$replyMarkup = json_encode($keyboard);
			$data['reply_markup'] = $replyMarkup; 
		}
	
        $out = tinybot_request('sendVideoNote', $data);
        return $out;	
}	


//Отправка медиа-группы видео / фото
//https://core.telegram.org/bots/api#sendmediagroup
//$media = array();
//$media[] = array('type'=>'photo', 'media' => 'BAACAgIAAxkBAAIFeV67Jvuu5eG3qOx6FKehrZzydgqfAAIcCAAC3BvYSY8iKIiB5un2GQQ', 'caption' => '', 'parse_mode' => '');
//2-10 items
function tinybot_send_mediagroup($chat_id, $media, $silent = false, $reply = '') {

	$data = array(
		'chat_id'      => $chat_id,
		'media'     =>  json_encode($media),
		'disable_notification' => $silent,
		'reply_to_message_id' => $reply					
	);

	$out = tinybot_request('sendMediaGroup', $data);
	return $out;	
}


//Отправка местоположения
//https://core.telegram.org/bots/api#sendlocation
function tinybot_send_location($chat_id, $latitude, $longitude, $live_period=3600, $keyboard='', $silent = false, $reply = '') { 
        $data = array(
            'chat_id'      => $chat_id,
			'latitude' => $latitude,
			'longitude' => $longitude,
			'live_period' => $live_period,
			'disable_notification' => $silent,
			'reply_to_message_id' => $reply
        );
	
		if (is_array($keyboard)) { 
			$replyMarkup = json_encode($keyboard);
			$data['reply_markup'] = $replyMarkup; 
		}
	
        $out = tinybot_request('sendLocation', $data);
        return $out;	
}


//Отправка места на карте с подписью
//https://core.telegram.org/bots/api#sendvenue
function tinybot_send_venue($chat_id, $latitude, $longitude, $title, $address, $foursquare_id='', $foursquare_type='', $keyboard='', $silent = false, $reply = '') {
        $data = array(
            'chat_id'      => $chat_id,
			'latitude' => $latitude,
			'longitude' => $longitude,
			'title' => $title,
			'address' => $address,
			'foursquare_id' => $foursquare_id,
			'foursquare_type' => $foursquare_type,
			'disable_notification' => $silent,
			'reply_to_message_id' => $reply
        );
	
		if (is_array($keyboard)) { 
			$replyMarkup = json_encode($keyboard);
			$data['reply_markup'] = $replyMarkup; 
		}
	
        $out = tinybot_request('sendVenue', $data);
        return $out;		
}


//Отправка контактов человека
//https://core.telegram.org/bots/api#sendcontact
function tinybot_send_contact($chat_id, $phone, $f_name, $l_name='', $vcard='', $keyboard='', $silent=false, $reply='') {
	$data = array(
		'chat_id'      => $chat_id,
		'phone_number' => $phone,
		'first_name' => $f_name,
		'last_name' => $l_name,
		'vcard' => $vcard,
		'disable_notification' => $silent,
		'reply_to_message_id' => $reply
	);

	if (is_array($keyboard)) { 
		$replyMarkup = json_encode($keyboard);
		$data['reply_markup'] = $replyMarkup; 
	}

	$out = tinybot_request('sendContact', $data);
	return $out;		
}


//Отправка опросника
//https://core.telegram.org/bots/api#sendpoll
function tinybot_send_poll($chat_id, $question, $options, $keyboard='', $anonymous=false, $type='regular', $multiple=false, $correct='', $explanation='', $parse_mode='HTML', $open_period='', $close_date='', $is_closed=false, $silent=false, $reply='') {
	
	$data = array(
		'chat_id'      => $chat_id,
		'question' => $question,
		'options' => json_encode($options),
		'is_anonymous' => $anonymous,
		'type' => $type,
		'allows_multiple_answers' => $multiple,
		'correct_option_id' => $correct,
		'explanation' => $explanation,
		'explanation_parse_mode' => $parse_mode,
		'open_period' => $open_period,
		'close_date' => $close_date,
		'is_closed' => $is_closed,
		'disable_notification' => $silent,
		'reply_to_message_id' => $reply
	);

	if (is_array($keyboard)) { 
		$replyMarkup = json_encode($keyboard);
		$data['reply_markup'] = $replyMarkup; 
	}

	$out = tinybot_request('sendPoll', $data);
	return $out;	
}



//Отправка Dice
//https://core.telegram.org/bots/api#senddice
//“🎲”, “🎯”, or “🏀”
function tinybot_send_dice($chat_id, $emoji, $keyboard='', $silent='', $reply='') { 
        $data = array(
            'chat_id'      => $chat_id,
            'emoji'     => $emoji,
			'disable_notification' => $silent,
			'reply_to_message_id' => $reply
        );
	
		if (is_array($keyboard)) { 
			$replyMarkup = json_encode($keyboard);
			$data['reply_markup'] = $replyMarkup; 
		}
	
        $out = tinybot_request('sendDice', $data);
        return $out;	
}



//Отправка Чат Экшенс
//https://core.telegram.org/bots/api#sendchataction
//Actions: typing, upload_photo, record_video, upload_video, record_audio, upload_audio, upload_document, find_location, record_video_note, upload_video_note
function tinybot_send_action($chat_id, $action) { 
        $data = array(
            'chat_id'      => $chat_id,
            'action'     => $action
        );
        $out = tinybot_request('sendChatAction', $data);
        return $out;	
}




//Назначение специальных команд боту
//https://core.telegram.org/bots/api#setmycommands
//array of arrays ('command'=> , 'description' => )
function tinybot_set_commands($commands) {
        $data = array(
            'commands'      => json_encode($commands)
        );
        $out = tinybot_request('setMyCommands', $data);
        return $out;		
}

//Вывести список актуальных команды бота
//https://core.telegram.org/bots/api#getmycommands
function tinybot_get_commands($chat_id='') {
        $out = tinybot_request('getMyCommands');

		$out = $out['body'];
		$out = json_decode($out);
		$out = $out->result;
	
		$text = '<b>Доступные команды:</b>' . PHP_EOL;
		
		foreach ($out as $command) {
			$text .= '/' . $command -> command . PHP_EOL;
			$text .= '<em>' . $command -> description . '</em>' . PHP_EOL . PHP_EOL;
		}
	
		if ($chat_id) {
			tinybot_send($chat_id, $text);		
		} else {
	        return $text;		
		}	
}



 
 
//Изменить сообщение
//Please note, that it is currently only possible to edit messages without reply_markup or with inline keyboards.
function tinybot_change_message($chat_id, $message_id, $text, $keyboard='', $parse_mode='HTML') {   
    	$text = tinybot_clear_tags($text); 
    	$replyMarkup = json_encode($keyboard);
    	
        $data = array(
            'chat_id'      => $chat_id,
			'message_id' => $message_id,
            'text'     => $text,
            'parse_mode' => $parse_mode,
            'reply_markup' => $replyMarkup
        );

        $out = tinybot_request('editMessageText', $data);
        return $out;
}  

//Изменить подпись сообщения
function tinybot_change_caption($chat_id, $message_id, $caption, $keyboard='', $parse_mode='HTML') {  
    	$caption = tinybot_clear_tags($caption); 
    	$replyMarkup = json_encode($keyboard);
    	
        $data = array(
            'chat_id'      => $chat_id,
			'message_id' => $message_id,
            'caption'     => $caption,
            'parse_mode' => $parse_mode,
            'reply_markup' => $replyMarkup
        );

        $out = tinybot_request('editMessageCaption', $data);
        return $out;
}	

//Изменить медиа-файл сообщения
function tinybot_change_media($chat_id, $message_id, $media_object, $keyboard='') {  
 		$media = json_encode($media_object);
    	$replyMarkup = json_encode($keyboard);
    	
        $data = array(
            'chat_id'      => $chat_id,
			'message_id' => $message_id,
            'media'     => $media,
            'reply_markup' => $replyMarkup
        );

        $out = tinybot_request('editMessageMedia', $data);
        return $out;
}	

//Изменить инлайн-клавиатуру сообщения
function tinybot_change_keyboard($chat_id, $message_id, $keyboard) {  
    	$replyMarkup = json_encode($keyboard);
    	
        $data = array(
            'chat_id'      => $chat_id,
			'message_id' => $message_id,
            'reply_markup' => $replyMarkup
        );

        $out = tinybot_request('editMessageReplyMarkup', $data);
        return $out;
}

//Остановить голосование
function tinybot_stop_poll($chat_id, $message_id, $keyboard='') {
    	$replyMarkup = json_encode($keyboard);
    	
        $data = array(
            'chat_id'      => $chat_id,
			'message_id' => $message_id,
            'reply_markup' => $replyMarkup
        );

        $out = tinybot_request('stopPoll', $data);
        return $out;		
}


//Удалить сообщение
function tinybot_delete_message($chat_id, $message_id) {    	
        $data = array(
            'chat_id'      => $chat_id,
			'message_id' => $message_id
        );

        $out = tinybot_request('deleteMessage', $data);
        return $out;		
}

