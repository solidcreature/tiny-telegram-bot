<?php
add_action( 'admin_enqueue_scripts', function(){
	wp_enqueue_style( 'tinybot-styles', TINY_BOT_URL .'inc/assets/tinybot-styles.css' );
}, 99 );