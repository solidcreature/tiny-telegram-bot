<?php
/*
Plugin Name: Tiny Telegram Bot for WordPress
Description: Create bots, services, games, apps and more with WordPress and Telegram
Version: 1.0.0
Author: Nikolay Mironov
*/

//Задаем базовые константы для плагина
define( 'TINY_BOT_DIR', plugin_dir_path( __FILE__ ) );
define( 'TINY_BOT_URL', plugin_dir_url( __FILE__ ) );
define( 'TINY_ROUTE', 'tiny_telegram_bot' );
define( 'TINY_ROUTE_MAIN', 'main' );

//Получаем токен чат-бота из настроек плагина 
$bot_options = get_option('tg_bot_options');
$tg_bot_token = trim($bot_options['tg_bot_token']);
define( 'TINY_BOT_TOKEN', $tg_bot_token );

//Подключаем необходимые файлы для работы плагина
include TINY_BOT_DIR . '/inc/telegram-api.php';
include TINY_BOT_DIR . '/inc/plugin-options.php';
include TINY_BOT_DIR . '/inc/post-types.php';
include TINY_BOT_DIR . '/inc/acf-groups.php';
include TINY_BOT_DIR . '/inc/utilities.php';

//Подключаем дополнительные функции, определяющие базовую логику плагина
include TINY_BOT_DIR . '/functions/bot_default_actions.php';
include TINY_BOT_DIR . '/functions/bot_keyboards.php';
include TINY_BOT_DIR . '/functions/bot_logic.php';


//Задаем end-поинт для общения между сайтом и чат-ботом
add_action( 'rest_api_init', function(){

	//Основной маршрут для работы с ботом	
	register_rest_route( TINY_ROUTE, '/' . TINY_ROUTE_MAIN, [
		'methods'  => 'post',
		'callback' => 'tinybot_main_function',
		'permission_callback' => null,
	] );

} );


//Ключевая функция, которая обрабатывает данные, полученные из телеграма
function tinybot_main_function($request) {
	//Получаем необходимые данные из запроса
	$data = $request->get_json_params();
	



	//По-разному получаем базовые параметры, в зависимости пришел текстовый запрос или колбек кнопки или фото
	if ( $data['message']['text'] ) {
		$message = $data['message']['text']; 
		$chat_id = $data['message']['from']['id'];
		$name = $data['message']['from']['first_name'] . ' ' . $l_name = $data['message']['from']['last_name'];	
	}
	
	elseif ( $data['callback_query']['data'] or $data['callback_query']['data'] == '0' ) {
		$message = $data['callback_query']['data'];
		$chat_id = $data['callback_query']['from']['id'];
		$name = $data['callback_query']['from']['first_name'] . ' ' . $data['callback_query']['from']['last_name'];
	}
	
	//Идентифицируем или создаем новую запись Участника с уникальным $chat_id
	$person_id = tinybot_get_person_id($chat_id, $name);
	$person_status = get_post_meta( $person_id, 'tg_status', true);
	
		

	//На этот хук вешаем обновление команд бота и сброс статуса участника
	if ($message == '/start') {
		tinybot_do_person_reset($chat_id, $person_id);
		tinybot_send_person_greetings($chat_id, $person_id);
		exit('ok');
	}
	
	
	//Выполняем команды бота, которые повешены на данные хуки
	if ($person_status != '') {
		do_action('tinybot_status_commands_hook', $chat_id, $message, $person_id, $person_status);
	}
	else {
		do_action('tinybot_commands_hook', $chat_id, $message, $person_id);
	}
	
	

	//Если дошли до этого момента, значит команда не была распознана
	tinybot_send_person_nofound_message($chat_id, $message, $person_id);
	

	//В конце возвращаем "ok", чтобы сообщить Телеграму, что запрос обработан
	exit('ok'); 
}

