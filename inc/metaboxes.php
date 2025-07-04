<?php 
//************************************************************************************************
//РЕГИСТРИРУЕМ МЕТАБОКСЫ ДЛЯ ТИПА ЗАПИСЕЙ TG_PERSON
//Один метабокс для вывода базовых данных участника, второй матабокс для отправки личных сообщений
//************************************************************************************************
	  
add_action('add_meta_boxes', 'tynybot_register_tgperson_metabox');

function tynybot_register_tgperson_metabox(){
	$screens = array( 'post', 'page' );
	add_meta_box( 'tgperson_details', 'Характеристики пользователя Телеграм', 'tynybot_show_tgperson_metabox', 'tg_person' );
	add_meta_box( 'tgperson_message', 'Отправка индивидуального сообщения', 'tynybot_show_sendmessage_metabox', 'tg_person' );
}


//Функция которая ввыодит данные Участника Chat_ID, текущий статус и значение счетчика
function tynybot_show_tgperson_metabox($post, $meta) {
	//Получаем ID поста текущей страницы
	$person_id = $post -> ID;
	
	//Выводим мета-данные Участника
	echo '<div class="tgperson-metabox">';
	echo '<div class="tgperson-metabox__inner">';
	echo '<div class="tgperson-metabox__column"><p class="tgperson-metabox__label">Идентификатор чата</p>' . get_post_meta($post -> ID, 'chat_id', true) .'</div>';
	echo '<div class="tgperson-metabox__column"><p class="tgperson-metabox__label">Статус участника</p>' . get_post_meta($post -> ID, 'tg_status', true) .'</div>';
	echo '<div class="tgperson-metabox__column"><p class="tgperson-metabox__label">Счетчик</p>' . get_post_meta($post -> ID, 'tg_count', true) .'</div>';	
	echo '</div>';
	echo '</div>';
}


//Функция с помощью которой можно отправить личное текстовое сообщение Участнику от лица бота
function tynybot_show_sendmessage_metabox($post, $meta) {
	
	//HTML-код для вывода тектового поля и кнопки
	echo '<div class="sendmessage-metabox">';
	echo '<div class="sendmessage-metabox__inner">';
	echo '<textarea class="sendmessage-metabox__textarea" placeholder="Введите любой текст и нажмите кнопку Отправить"></textarea>';
	echo '<button data-tgperson="' . get_post_meta($post -> ID, 'chat_id', true) . '" data-ajaxurl="' . admin_url('admin-ajax.php') . '" class="js-sendmessage ">Отправить сообщение</button>';
	echo '</div>';
	echo '<div class="ajax-message"></div>';
	echo '</div>';
	?>
	
	<script>
		//Скрипт, который отправляет ajax-запрос при нажатии кнопки
		jQuery('.js-sendmessage').click( function() {
			//Получаем данные для передачи в Ajax
			let chatID = jQuery(this).data('tgperson');
			let chatMessage = jQuery('.sendmessage-metabox__textarea').val();
			let ajaxURL = jQuery(this).data('ajaxurl');
			
			//Делаем базовые проверки, что есть ID чата и текст сообщения
			if (!chatID) {
				alert('Ошибка. Не задан Chat ID пользователя, отправка невозможна!');
				return;
			}
			
			if (!chatMessage) {
				alert('Ошибка. Нельзя отправить пустое сообщение!');
				return;
			}
			
			//Делаем AJAX-запрос, чтобы отправить сообщение
			jQuery.ajax( { type:"post", url:ajaxURL, dataType:"json", data:{ action:'admin_send_tg_message', chat_id:chatID, message:chatMessage }
			}).always( function( out ){
				str = JSON.stringify(out.message, null, 4);
				jQuery( ".ajax-message" ).text( str );
			});
			
			return false;
			
		});			
	</script>		

	<?php 
}	