<?php
//Регистрируем страницу настроек плагина в админ-панели
function tinybot_register_options_page() {
  add_menu_page('Tiny Telegram Bot', 'Tiny Telegram Bot', 'manage_options', 'tg_bot', 'tinybot_options_page','dashicons-format-chat');
}
add_action('admin_menu', 'tinybot_register_options_page');


//Добавляем форму на страницу настроек плагина
function tinybot_options_page() { ?>
	
	
	<div class="tg-starter__wrapper">
		
		<h2 class="tg-starter__title"><?php 'Настройки токена бота'; ?></h2>
		
		
		<form class="tg-starter__form" action="options.php" method="post">
			<?php settings_fields('tg_bot_options'); ?>
			<?php do_settings_sections('tg_bot_options-main'); ?>
			<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
		</form>
		
		
		<div class="tg-starter__webhook">
			<?php 
				$site = site_url(); 
				$options = get_option('tg_bot_options');
				$tg_bot_token = trim($options['tg_bot_token']);
				
				$link = "https://api.telegram.org/bot$tg_bot_token/setWebhook?url=$site//wp-json/" . TINY_ROUTE . "/" . TINY_ROUTE_MAIN;
				
				if ($tg_bot_token):
					echo '<h3>' . __( 'Для работы бота необходимо подключить веб-хук, пройдя по этой ссылке:', 'tg_starter' ) . '</h3>';
					echo '<p><a href="' . $link . '" target="_blank">' . $link . '</a></p>';
					echo '<p>' . __( 'Сайт должен работать по протоколу https с активным установленным SSL сертификатом', 'tg_starter' ) . '</p>';
				endif;	
			?>	
		</div><!-- webhhok -->
	
	</div><!-- wrapper -->
	 
	<?php

} 


//Здесь начинаем добавлять настройки
function tinybot_options_fields(){
register_setting( 'tg_bot_options', 'tg_bot_options');
add_settings_section('tg_bot_section1', '', 'tinybot_section1_func', 'tg_bot_options-main');
add_settings_field('tg_bot_token', 'Введите токен, полученный от BotFather', 'tinybot_token_func', 'tg_bot_options-main', 'tg_bot_section1');
add_settings_field('tg_bot_name', '', 'tinybot_name_func', 'tg_bot_options-main', 'tg_bot_section1');
}

add_action('admin_init', 'tinybot_options_fields');

function tinybot_section1_func() {
	return '';
}

function tinybot_token_func() {
$options = get_option('tg_bot_options');
echo "<input id='tg_bot_token' name='tg_bot_options[tg_bot_token]' size='110' type='text' value='{$options['tg_bot_token']}' />";
} 

function tinybot_name_func() {
$options = get_option('tg_bot_options');
echo "<input style='display:none;' id='tg_bot_name' name='tg_bot_options[tg_bot_name]' size='60' type='text' value='{$options['tg_bot_name']}' />";
} 
