<?php
//*********************************
//РЕГИСТРАЦИЯ СТРАНИЦ НАСТРОЕК БОТА
//*********************************

//На хук admin_menu добавляем регистрацию страниц настроек плагина 
add_action('admin_menu', 'tinybot_register_options_pages');

//Регистрируем страницу настроек плагина в админ-панели
function tinybot_register_options_pages() {
  add_menu_page('Настройки Telegram-бота', 'Tiny Telegram Bot', 'manage_options', 'tinybot-options', 'tinybot_show_options_page','dashicons-format-chat');
  add_submenu_page( 'tinybot-options', 'Приветствие', 'Приветствие', 'manage_options', 'tinybot-greetings', 'tinybot_greetings_page', 10 );
  add_submenu_page( 'tinybot-options', 'Лог сообщений', 'Лог сообщений', 'manage_options', 'tinybot-lastmessage', 'tinybot_log_page', 20 );
  add_submenu_page( 'tinybot-options', 'Медиа-файлы', 'Медиа-файлы', 'manage_options', 'tinybot-media', 'tinybot_show_tgmedia_page', 30 );
}



//*********************************************
//РЕГИСТРАЦИЯ ОПЦИЙ БОТА
//которые будут выводится на страницах настроек
//*********************************************

//На хук admin_init добавляем список опций, которые нужны для работы плагина
add_action('admin_init', 'tinybot_register_options_fields');

//Здесь начинаем добавлять настройки
/*
 * Список и описание настроек
 * tinybot_token — Токен бота, полученный от BotFather, необходим для работы бота и связи бота с сайтом
 * tinybot_adminonly — Сохранять медиа-файлы только из чата с администратором
 * tinybot_admintg — ID аккаунта администратора в Телеграме
 * 
 * Дополнительные опции, которые выводим в формате только для чтения, поэтому не регистрируем
 * tinybot_lastmessage — Отображаем последнее сообщение в боте
 * tinybot_lastresponse — Отображаем результат последнего запроса
 * tinybot_tempdata — Просто опция в которой можно хранить отладочные данные
*/

function tinybot_register_options_fields() {
	//Зарегистрируем одну секцию настроек для основной страницы
	add_settings_section('tinybot_token_group', 'Подключение телеграм-бота к сайту', '__return_empty_string', 'tinybot-options');	
	add_settings_field('tinybot_token', 'Введите токен, полученный от BotFather', 'tinybot_options_token_callback', 'tinybot-options', 'tinybot_token_group');	
	register_setting( 'tinybot-options', 'tinybot_token');

	//Зарегистрируем секцию настроек для приветствия
	add_settings_section('tinybot_greetings_group', 'Введите file_id фотографии из раздела Медиа-файлы бота', '__return_empty_string', 'tinybot-greetings');	
	add_settings_field('tinybot_greetingsphoto', 'Картинка приветствия', 'tinybot_options_greetingsphoto_callback', 'tinybot-greetings', 'tinybot_greetings_group');
	add_settings_field('tinybot_greetingstext', 'Текст приветствия', 'tinybot_options_greetingstext_callback', 'tinybot-greetings', 'tinybot_greetings_group');
	add_settings_field('tinybot_greetingskeyboard', 'Стандартная клавиатура', 'tinybot_options_greetingskeyboard_callback', 'tinybot-greetings', 'tinybot_greetings_group');
	register_setting( 'tinybot-greetings', 'tinybot_greetingsphoto');	
	register_setting( 'tinybot-greetings', 'tinybot_greetingstext');
	register_setting( 'tinybot-greetings', 'tinybot_greetingskeyboard');
	
	//Зарегистрируем вторую секцию настроек
	add_settings_section('tinybot_media_group', 'Сохранение данных о загруженных в чат-бот медиа-файлах', '__return_empty_string', 'tinybot-media');	
	add_settings_field('tinybot_adminonly', 'Сохранять Media-файлы', 'tinybot_options_adminonly_callback', 'tinybot-media', 'tinybot_media_group');
	add_settings_field('tinybot_admintg', 'ID телеграм-канала администратора', 'tinybot_options_admintg_callback', 'tinybot-media', 'tinybot_media_group');
	register_setting( 'tinybot-media', 'tinybot_admintg');	
	register_setting( 'tinybot-media', 'tinybot_adminonly');	
}

//Функция обработки и вывода параметра tinybot_token
function tinybot_options_greetingsphoto_callback() {
	$tinybot_greetingsphoto = get_option('tinybot_greetingsphoto');
	echo "<input name='tinybot_greetingsphoto' size='110' type='text' value='{$tinybot_greetingsphoto}' />";
} 

//Функция обработки и вывода параметра tinybot_token
function tinybot_options_greetingstext_callback() {
	$tinybot_greetingstext = get_option('tinybot_greetingstext','Tiny Telegram Bot v 2.1.0');
	echo '<textarea name="tinybot_greetingstext" cols="110" rows="10">' . $tinybot_greetingstext . '</textarea>';
} 

//Функция обработки и вывода параметра tinybot_token
function tinybot_options_greetingskeyboard_callback() {
	$tinybot_greetingskeyboard = get_option('tinybot_greetingskeyboard');

	$no_keyboard = '';
	$default_keyboard ='';
	
	if (!$tinybot_greetingskeyboard) {
		$no_keyboard = 'selected';
	}	
	else {
		$default_keyboard = 'selected';
	}
	
	echo '<select name="tinybot_greetingskeyboard">';
	echo '<option value="1" ' . $default_keyboard .' >Стандартная клавиатура</option>';
	echo '<option value="0" ' . $no_keyboard . '>Без клавиатуры</option>';
	echo '</select>';
} 



//Функция обработки и вывода параметра tinybot_token
function tinybot_options_token_callback() {
	$tinybot_token = get_option('tinybot_token');
	echo "<input name='tinybot_token' size='110' type='text' value='{$tinybot_token}' />";
} 


//Функция обработки и вывода параметра tinybot_admintg
function tinybot_options_admintg_callback() {
	$tinybot_admintg = get_option('tinybot_admintg');
	echo "<input name='tinybot_admintg' size='40' type='text' value='{$tinybot_admintg}' />";
} 

//Функция обработки и вывода параметра tinybot_adminonly
function tinybot_options_adminonly_callback(){

	$tinybot_adminonly = get_option('tinybot_adminonly');	
	$selected_all = '';
	$selected_admin = '';
	
	if ($tinybot_adminonly) {
		$selected_admin = 'selected';
	}	
	else {
		$selected_all = 'selected';
	}
	
	echo '<select name="tinybot_adminonly">';
	echo '<option value="0" ' . $selected_all . '>От всех пользователей</option>';
	echo '<option value="1" ' . $selected_admin .' >От администратора</option>';
	echo '</select>';
}



//*********************************************
//СОДЕРЖАНИЕ СТРАНИЦ С НАСТРОЙКАМИ БОТА
//*********************************************

//СТРАНИЦА ОСНОВНЫХ НАСТРОЕК БОТА
//Здесь мы задаем Token бота и подключаем сайт к Телеграму
function tinybot_show_options_page() { 
?>
		
	<div class="wrap tinybot-options">
		
		<h1 class="tinybot-options__title">Настройки телеграм-бота</h1>
		
		<div class="tinybot-options__block">
				
			<form class="" action="options.php" method="post">
				<?php settings_fields('tinybot-options'); ?>
				<?php do_settings_sections('tinybot-options'); ?>
				<?php submit_button(); ?>
			</form>


			<div class="tinybot-options__secondary">
				<?php 
					$site = site_url();

					$link = 'https://api.telegram.org/bot' . TINY_BOT_TOKEN . '/setWebhook?url=' . $site . '//wp-json/' . TINY_BOT_ROUTE . '/' . TINY_BOT_ROUTE_MAIN;

					if (TINY_BOT_TOKEN):		  
						echo '<h4>Для работы бота необходимо подключить веб-хук, пройдя по этой ссылке:</h>';
						echo '<p><a href="' . $link . '" target="_blank">' . $link . '</a></p>';
						echo '<p>Сайт должен работать по протоколу https с активным установленным SSL сертификатом</p>';
					endif;	
				?>	
			</div>
			
		</div>	
		
		<?php
		if (TINY_BOT_TOKEN):									  
									  
			//Получаем данные о текущем статусе бота							  
			$info = tinybot_get_webhook_info();		
			$getme = tinybot_get_info();						  
			?>							  

		
			<div class="tinybot-options___wrap">				
	
				
				<div class="tinybot-options__block is--column">
					<h3 class="tinybot-options__subtitle has--underline">Информация о боте:</h3>
					<?php print_r($getme); ?>					
				</div>	
				
				<div class="tinybot-options__block  is--column">
					<h3 class="tinybot-options__subtitle has--underline">Статус работы бота:</h3>
					<p>Неотправленные сообщения: <?php echo $info -> pending_update_count; ?></p>
					<p>Дата и время последней ошибки: <?php echo wp_date( 'j F Y H:i:s', $info -> last_error_date ); ?></p>
					<p>Содержание последней ошибки: <?php echo $info -> last_error_message; ?></p>
					<p>url: <?php echo $info -> url; ?></p>
					<p>has_custom_certificate:<?php echo $info -> has_custom_certificate; ?></p>
					<p>max_connections: <?php echo $info -> max_connections; ?></p>
					<p>ip_address: <?php echo $info -> ip_address; ?></p>
				</div>
				
			</div>	
		
		<?php endif; ?>
	
	</div>
	 
<?php
} 


//СТРАНИЦА ПРИВЕТСТВИЯ БОТА
//Можно задать для первого сообщения картинку + текст
function tinybot_greetings_page() {
	echo '<div class="wrap">';
	echo '<h1>Приветствие бота</h1>';	
	
	echo '<form class="tg-starter__form" action="options.php" method="post">';
		settings_fields('tinybot-greetings'); 
		do_settings_sections('tinybot-greetings'); 
		submit_button(); 
	echo '</form>';
	
	echo '</div>';
}	

//СТРАНИЦА С ПОСЛЕДНИМИ СООБЩЕНИЯМИ БОТА
//Выводим содержание трех полей из таблицы Options, которые обновляются после каждого действия в боте
function tinybot_log_page() {
	?>
	
	<div class="wrap tinybot-options">
		
		<h1 class="tinybot-options__title">Логирование</h1>

		<div class="tinybot-options__block">
			<h3 class="tinybot-options__subtitle has--underline">Временные данные</h3>
			<pre><?php print_r( get_option('tinybot_tempdata') ); ?></pre>
		</div>

		<div class="tinybot-options___wrap">
			<div class="tinybot-options__block is--column">
				<h3 class="tinybot-options__subtitle has--underline">Текущее сообщение</h3>
				<pre><?php print_r( get_option('tinybot_lastmessage') ); ?></pre>
			</div>

			<div class="tinybot-options__block  is--column">
				<h3 class="tinybot-options__subtitle has--underline">Ответ API Телеграма</h3>
				<pre><?php print_r( get_option('tinybot_lastresponse') ); ?></pre>
			</div>	
		</div>	
	
	</div>

	<?php
}

//СТРАНИЦА С МЕДИА-ФАЙЛАМИИ, ЗАГРУЖЕННЫМИ В ТЕЛЕГРАМ-БОТ
//Сохраняются только медиа из персональных чатов. Можно включить опцию, чтобы сохранять только медиа-файлы от администратора
function tinybot_show_tgmedia_page() {
	
	//Базовая HTML-разметка страницы
	echo '<div class="wrap">';
	echo '<h1>Загруженные видео-файлы и картинки</h1>';
		
	//Вывод формы опций в начале страницы
	echo '<form class="tg-starter__form" action="options.php" method="post">';
		settings_fields('tinybot-media'); 
		do_settings_sections('tinybot-media'); 
		submit_button(); 
	echo '</form>';
	
	
	//Начинаем блок в котором выводим данные о загруженных медиа-файлах (это отдельный приватный тип записей tg_media)
	//Храним в базе только ID файлов, чтобы оперировать ими в боте, сами медиа-файлы на сайт не загружаем
	//Используем класс WP_Query чтобы вывести последние 199 записей с информацией о медиа-файлах
	$args = array(
		'post_type' => 'tg_media',
		'posts_per_page' => 199,
		'orderby' => 'date'
	);
	
	$query = new WP_Query($args);
	
	//Ссылка на AJAX нужна для работы кнопки Отправить админу
	$ajaxurl = admin_url('admin-ajax.php');
	
	//HTML-блок в котором выводим информацию о последних медиа-файлах
	echo '<section class="tinybot-media">';
	
		while ($query -> have_posts()): $query -> the_post();
			
			$post_id = get_the_ID();	
			echo '<div class="tinybot-media__item item--' . $post_id . '">';
			echo '<button class="js-send-media tinybot-media__item-button" data-mediaid="' . $post_id . '" data-ajaxurl="' . $ajaxurl . '" data-fileid="' . get_post_meta($post_id, 'file_id', true) . '" data-type="' . get_post_meta($post_id, 'file_type', true) .'" data-admintg="' . get_option('tinybot_admintg')  . '">Отправить админу</button>';
			echo '<p class="tinybot-media__item-uptitle">file_id</p>';
			echo '<h3 class="tinybot-media__item-title">' . get_post_meta($post_id, 'file_id', true) . '</h3>';
			echo '<ul class="tinybot-media__item-meta"><li>' . get_post_meta($post_id, 'file_type', true) . '</li><li>' . get_the_title($post_id) . '</li><li>' . get_post_meta($post_id, 'file_author', true) . '</li><li>' . get_post_meta($post_id, 'file_date', true) . '</li></ul>';
			echo '</div>';

		endwhile;
	
	echo '</section>';	
	echo '</div>';
		
	wp_reset_postdata();
	
	?>

	<script>
		/* Реализуем отправку медиа-файла администратору через ajax-запрос */
		
		jQuery('.js-send-media').click( function() {
			let fileType = jQuery(this).data('type');
			let fileID = jQuery(this).data('fileid');	
			let adminTg = jQuery(this).data('admintg');	
			let mediaId = jQuery(this).data('mediaid');	
			let ajaxURL = jQuery(this).data('ajaxurl');	
			
			if (!fileID) {
				alert('Не задан File ID, отправка медиа невозможна');
				return;
			}
			
			if (!adminTg) {
				alert('Не задан Телеграм администратора, отправка медиа невозможна');
				return;
			}
						
			//Делаем AJAX-запрос, чтобы отправить сообщение
			jQuery.ajax( { type:"post", url:ajaxURL, dataType:"json", data:{ action:'admin_send_tg_media', admin_tg:adminTg, file_id:fileID, file_type:fileType }
			}).always( function( out ){
				str = JSON.stringify(out.message, null, 4);
				jQuery( ".item--" + mediaId + " .ajax-message" ).text( str );
			});
			
			return false;
		});
		
	</script>	

	<?php
}
