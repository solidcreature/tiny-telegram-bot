<?php 
//В данном файле можно прописать всю логику бота.
//В качестве примера добавлены функции tinybot_some_action и tinybot_other_action
//Более функциональные пример находятся в файле demo.php вы можете его подключить убрав комментарии со строки 37 в основном файле плагина

//
add_action('tinybot_actions_hook','tinybot_some_action', 10, 3);
add_action('tinybot_actions_hook','tinybot_other_action', 10, 3);

function tinybot_some_action($chat_id, $message, $person_id) {
	if ($message != 'Первая кнопка') return;
	
	$text = '<em>{Бот выполняет какое-то действие}</em>';
	tinybot_send($chat_id, $text);
	
	exit('ok');
}

function tinybot_other_action($chat_id, $message, $person_id) {
	if ($message != 'Вторая кнопка') return;
	
	$text = '<em>{Бот выполняет другое действие}</em>';
	tinybot_send($chat_id, $text);
	
	exit('ok');
}