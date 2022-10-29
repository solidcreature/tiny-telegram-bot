<?php

//Получить ID участника по идентификатору тг-чата, если нет, создать нового
function tinybot_get_person_id($chat_id, $name) {
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
		$post_data = array(
		'post_title'    => $name,
		'post_content' => '',
		'post_status'   => 'publish',
		'post_author'   => 1,
		'post_type' => 'tg_person'
		);

		// Вставляем запись в базу данных
		$person_id = wp_insert_post( $post_data );
		
		update_post_meta( $person_id, 'chat_id', $chat_id);
		update_post_meta( $person_id, 'tg_count', $chat_id);
		
		return $person_id;
	}
	
	wp_reset_postdata();
}

//Очистить текст от лишних html-тегов, чтобы подготовить текст к отправки
function tinybot_clear_tags($text) {
	$clear = strip_tags($text, '<b><strong><i><em><u><ins><s><strike><del><a><code><pre>');
	$clear = str_replace('&nbsp;', '', $clear);
	return $clear;
}  