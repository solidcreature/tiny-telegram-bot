<?php

add_action( 'wp_ajax_admin_send_tg_message', 'admin_send_tg_message_callback' );
add_action( 'wp_ajax_admin_send_tg_media', 'admin_send_tg_media_callback' );


function admin_send_tg_message_callback() {

	//sanitize POST data, retrieving only integer
	$chat_id = (int) $_POST['chat_id'];
	$message = esc_html( $_POST['message'] );
	
	
	$out = tinybot_send($chat_id, $message);

	
	echo json_encode(['message' => $out]);
	wp_die();
}


function admin_send_tg_media_callback() {

	//sanitize POST data, retrieving only integer
	$chat_id = (int) $_POST['admin_tg'];
	$file_id = esc_html( $_POST['file_id'] );
	$file_type = esc_html( $_POST['file_type'] );
	
	if ( $file_type == 'photo' ) {
		$out = tinybot_send_photo($chat_id, $file_id);
	}
	
	elseif ( $file_type == 'video' ) {
		$out = tinybot_send_video($chat_id, $file_id);		
	}
	
	echo json_encode(['message' => $out]);
	wp_die();
}