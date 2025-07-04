<?php

//Выдаем ID участника по идентификатору тг-чата
function tinybot_get_person_id($chat_id) {
	$args = array(
		'post_type' => 'tg_person',
		'meta_key' => 'chat_id',
		'meta_value' => $chat_id
	);
			
	$query = new WP_Query($args);
	
	if ( $query->have_posts() )	{
		while ( $query->have_posts() ) : $query->the_post();
			$person_id = get_the_ID();
		endwhile; 
		
		return $person_id;
		
	} else {
		//Возвращаем 0, если не нашли пользователя с таким chat_id
		return 0;
		
	}
	
	wp_reset_postdata();
}

//Получим из данных запроса Имя и Фамилию Участника
function tinybot_gather_person_name($data) {

	//Данные запроса обычного сообщения в чате с ботом
	if ( isset($data['message']['from']) ) {
		$name = $data['message']['from']['first_name'] . ' ' . $data['message']['from']['last_name'];	
	}
	
	//Данные запроса при нажатии кнопки с колбеком
	elseif ( isset($data['callback_query']['data']) ) {
		$name = $data['callback_query']['from']['first_name'] . ' ' . $data['callback_query']['from']['last_name'];
	}
	
	return $name;
}

//Получим ник участника в телеграме из данных запроса
function tinybot_gather_person_username($data) {
	
	//Данные запроса обычного сообщения в чате с ботом
	if ( isset($data['message']['from']) ) {
		$username = $data['message']['from']['username'];	
	}

	//Данные запроса при нажатии кнопки с колбеком
	elseif ( isset($data['callback_query']['data']) ) {
		$username = $data['callback_query']['from']['username'];
	}
	
	return $username;	
}


//Создаем нового участника, сохраняем его в базе
function tinybot_create_person($chat_id, $name, $username ) {
		$post_data = array(
			'post_title'    => $name,
			'post_content' => '',
			'post_status'   => 'publish',
			'post_author'   => 1,
			'post_type' => 'tg_person',
			'post_name' => $username
		);

		// Вставляем запись в базу данных
		$person_id = wp_insert_post( $post_data );
		
		update_post_meta( $person_id, 'chat_id', $chat_id);
		update_post_meta( $person_id, 'tg_count', 0);
		
		return $person_id;
}	

//Сохранием информацию о фотографии, отправленной боту в базе данных
function tinybot_save_tgphoto($data) {

	//Узнаем разамер массива
	$count = count($data['message']['photo']);

	//И индекс последнего элемента
	if ($count > 0) $count--;

	//Получаем fie_id оригинальный размер
	$file_id = $data['message']['photo'][$count]['file_id'];

	//Смотрим есть ли подпись у фотографии
	$caption = 'Без подписи';
	if ( isset($data['message']['caption']) ) $caption = $data['message']['caption'];		

	//Создаем новый файл
	$post_data = array(
		'post_title'    => $caption,
		'post_status'   => 'publish',
		'post_author'   => 1,
		'post_type' => 'tg_media',
	);

	$post_id =  wp_insert_post($post_data);

	//Добавляем мета-поля
	update_post_meta($post_id, 'file_id', $file_id);
	update_post_meta($post_id, 'file_type', 'photo');	
	update_post_meta($post_id, 'file_date', wp_date('j F, Y'));	
	update_post_meta($post_id, 'file_author', $data['message']['from']['username']);	
}	


//Сохранием информацию о видео, отправленного боту в базе данных
function tinybot_save_tgvideo($data) {

	//Получаем fie_id оригинальный размер
	$file_id = $data['message']['video']['file_id'];

	//Смотрим есть ли подпись у фотографии
	$caption = $data['message']['video']['file_name'];
	if ( isset($data['message']['caption']) ) $caption .= ' | ' . $data['message']['caption'];		

	//Создаем новый файл
	$post_data = array(
		'post_title'    => $caption,
		'post_status'   => 'publish',
		'post_author'   => 1,
		'post_type' => 'tg_media',
	);

	$post_id =  wp_insert_post($post_data);

	//Добавляем мета-поля
	update_post_meta($post_id, 'file_id', $file_id);
	update_post_meta($post_id, 'file_type', 'video');	
	update_post_meta($post_id, 'file_date', wp_date('j F, Y'));	
	update_post_meta($post_id, 'file_author', $data['message']['from']['username']);	
}	


//Очистить текст от лишних html-тегов, чтобы подготовить текст к отправки
function tinybot_clear_tags($text) {
	$clear = strip_tags($text, '<b><strong><i><em><u><ins><s><strike><del><a><code><pre>');
	$clear = str_replace('&nbsp;', '', $clear);
	return $clear;
}  