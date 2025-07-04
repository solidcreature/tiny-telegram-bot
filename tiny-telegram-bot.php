<?php
/*
Plugin Name: Tiny Telegram Bot for WordPress
Description: Create bots, services, games, apps and more with WordPress and Telegram
Version: 2.1.0
Author: Nikolay Mironov
*/

//Задаем базовые константы для плагина
define( 'TINY_BOT_DIR', plugin_dir_path( __FILE__ ) );
define( 'TINY_BOT_URL', plugin_dir_url( __FILE__ ) );

//Зададим адрес REST-эндпоинта
define( 'TINY_BOT_ROUTE', 'tinybot' );
define( 'TINY_BOT_ROUTE_MAIN', 'main' );

//Получаем токен чат-бота из настроек плагина 
$tinybot_token = trim(get_option('tinybot_token'));
define( 'TINY_BOT_TOKEN', $tinybot_token );

//Подключаем необходимые файлы для работы плагина
include TINY_BOT_DIR . '/inc/ajax.php';
include TINY_BOT_DIR . '/inc/telegram-api.php';
include TINY_BOT_DIR . '/inc/plugin-options.php';
include TINY_BOT_DIR . '/inc/post-types.php';
include TINY_BOT_DIR . '/inc/utilities.php';
include TINY_BOT_DIR . '/inc/metaboxes.php';
include TINY_BOT_DIR . '/inc/styles.php';

//Подключаем дополнительные функции, определяющие базовую логику плагина
include TINY_BOT_DIR . '/functions/default-actions.php';
include TINY_BOT_DIR . '/functions/keyboards.php';
include TINY_BOT_DIR . '/functions/bot-logic.php';

//Демо-файл, содержит пример программирования базовой логики бота
//Можно отключить данный файл
//include TINY_BOT_DIR . '/functions/demo.php';

//Задаем end-поинт для общения между сайтом и чат-ботом
add_action( 'rest_api_init', function(){

	//Основной маршрут для работы с ботом	
	register_rest_route( TINY_BOT_ROUTE, '/' . TINY_BOT_ROUTE_MAIN, [
		'methods'  => 'post',
		'callback' => 'tinybot_main_function',
		'permission_callback' => null,
	] );

} );


//Ключевая функция, которая обрабатывает данные, полученные из телеграма
function tinybot_main_function($request) {
	
	//ХУК - получили данные запроса
	//Выполняем базовые функции, которые не зависят от содержания запроса и логики самого бота
	$data = $request->get_json_params();
	do_action('tinybot_data_recieved_hook', $data);

	
	//Для дальнейшей работы нам нужно определить $message (сообщение пользователя) и $chat_id (куда бот отправит ответ)
	//По-разному получаем значения переменных, в зависимости от структуры запроса (например текстовый запрос или колбек кнопки)
	//Если пользователь отправил медиа-файл или документ, то обрабатываем такой запрос на ХУКе tinybot_data_recieved_hook
	
	//Получаем $message и $chat_id из обычного текстового сообщения
	if ( $data['message']['text'] ) {
		$message = $data['message']['text']; 
		$chat_id = $data['message']['from']['id'];
		$from_id = $data['message']['chat']['id'];
	}
	
	//Получаем $message и $chat_id когда пользователь нажал inline-кнопку
	elseif ( $data['callback_query']['data'] or $data['callback_query']['data'] == '0' ) {
		$message = $data['callback_query']['data'];
		$chat_id = $data['callback_query']['from']['id'];
		$from_id = $data['callback_query']['chat']['id'];
	}
	
	
	
	//Логика бота сильно отличается при обработке сообщений в группе и от обработки личного сообщения
	//Вешаем ХУК на обработку группового сообщения, но дальше не идем
	//В зависимости от задач и применения бота данную логику можно изменить и убрать проверку или наоборот добавить передаваемых параметров
	//Сейчас это заглушка, чтобы бот не реагировал на каждое сообщение в чате
	if ( $chat_id != $from_id ) {
		$args = array();
		do_action('tinybot_group_messages_hook', $from_id, $args);
		exit('ok');				
	}		
	
	
	
	//На сайте каждому $chat_id соответствует запись типа tg_person, в мета-полях которой мы храним всю необходимую информацию о пользователе, который общается с ботом
	//Дальше в логике работы бота эта переменная идет как $person_id. Между $chat_id и $person_id есть однозначное соответствие. 
	//Идентифицируем Участника по его $chat_id
	$person_id = tinybot_get_person_id($chat_id);
		
	//Если $chat_id отсутствует добавляем в базу нового участника
	if (!$person_id) {
		$name = tinybot_gather_person_name($data);
		$username = tinybot_gather_person_username($data);		
		$person_id = tinybot_create_person($chat_id, $name, $username);
	}
	
	//Пользователь может написать боту, может прислать файл, видео, картинку, а может нажать инлайн-кнопку. Основная логика работы бота построена на обработке текстовых сообщений. Т.е. когда пользователь написал боту или нажал на обычную кнопку в меню, тогда текст кнопки интерпретируется как сообщение. Отедльная функция обрабатывает видео и картинки. Особенность инлайн-кнопок заключается в том, что они передают не текст кнопки, а заданную в кнопке команду, например commandname_25, что сильно расширяет возможности бота. Для обработки подобных команд и вводится разделение между обработкой текстовых сообщений и команд.
	//Очень важно, что данная история может использоваться для Deep Linking -- т.е. новый пользователь не просто начинает общаться с ботом нажимая кнопку Start, но уже передается определенный контекст, который, например, позволит разделить пользователей на разные группы или выдать пользователю вполне определенный текст / данные. Deep Linking работает только с командой /start поэтому добавлен следующий блок, который отделяет /start и /start + команда
	
	
	//Теперь проверяем является ли сообщение дип-линком
	$deeplink = explode(' ', $message);
	
	//Если поймали дип-линк, дальше передаем сообщение без /start
	if ( ($message != '/start') AND ($deeplink[0] == '/start') ) {
		if ( isset($deeplink[1])) {
			$message = $deeplink[1];
		}	
	}
	
	//Так как мы и задем логику бота, то все команды идут в формате commandname__param
	//Для расширения функционала в будщем используем __ (двойное нижнее подчеркивание) для разделения команды и параметра
	//Обычно такие сообщения с командами это колбек с инлайн-кнопок или диплинки
	$bot_command = explode('__', $message);
	if ( isset($bot_command[1]) ) {
		
		$command = $bot_command[0];
		$param = $bot_command[1];
		
		//ХУК - выполняем команды бота, добавляем спец. параметр
		do_action('tinybot_commands_hook', $chat_id, $message, $person_id, $command, $param);		
	}
	
	
	
	//ХУК - Пользователь нажал START и начал общение с ботом
	//На этот хук вешаем приветствие, а также сброс параметров и статуса участника
	if ($message == '/start') {
		do_action('tinybot_greetings_hook', $chat_id, $person_id);
		exit('ok');
	}	
	
	
	//Определяем Статус участника, статус влияет на то какой тип действия будет применен дальше
	$person_status = get_post_meta( $person_id, 'tg_status', true);

	
	//Данные хуки определяют основной функционал и логику работы бота
	if ($person_status != '') {
		//ХУК - обработка сообщения от пользователя у которого задан Статус
		//Если у пользователя задан Статус, то игнорируется любая логика бота, не связанная с данным Статусом
		do_action('tinybot_status_actions_hook', $chat_id, $message, $person_id, $person_status);
	}
	else {
		//ХУК - обработка сообщения от пользователя без статуса
		//Без статуса работает простая логика бота и базовые команды
		do_action('tinybot_actions_hook', $chat_id, $message, $person_id);
	}
	
	
	//ХУК - запрос не был обработан / пришла неизвестная команда
	//Если дошли до этого момента, значит команда нераспознана, обычно просто сообщаем об этом Участнику
	do_action('tinybot_nofound_hook', $chat_id, $message, $person_id);
	
	
	//В конце возвращаем "ok", чтобы сообщить Телеграму, что запрос обработан
	exit('ok'); 
}

