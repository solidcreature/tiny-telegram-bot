<?php

//Новый тип записи -- Участник
//Registering Custom Post Type -- Участник
function tinybot_register_post_types() {

	$labels = array(
		'name'                  => 'Участники',
		'singular_name'         => 'Участник',
		'menu_name'             => 'Участники',
		'name_admin_bar'        => 'Участника',
		'archives'              => 'Архив участников',
		'attributes'            => 'Атрибуты участника',
		'parent_item_colon'     => 'Родительский элемент',
		'all_items'             => 'Все участники',
		'add_new_item'          => 'Добавить нового участника',
		'add_new'               => 'Добавить нового',
		'new_item'              => 'Новый участник',
		'edit_item'             => 'Редактировать участника',
		'update_item'           => 'Обновить участника',
		'view_item'             => 'Посмотреть участника',
		'view_items'            => 'Посмотреть участников',
		'search_items'          => 'Искать участника',
		'not_found'             => 'Не найдены',
		'not_found_in_trash'    => 'Не найдены в удаленных',
		'featured_image'        => 'Фотография участника',
		'set_featured_image'    => 'Задать фотографию',
		'remove_featured_image' => 'Удалить фотографию',
		'use_featured_image'    => 'Использовать',
		'insert_into_item'      => 'Использовать для участника',
		'uploaded_to_this_item' => 'Загружено для участника',
		'items_list'            => 'Список участников',
		'items_list_navigation' => 'Навигация по участникам',
		'filter_items_list'     => 'Отсортировать список участников',
	);
	$args = array(
		'label'                 => 'Участник',
		'description'           => 'Пользователи Телеграма, которые начали общаться с ботом',
		'labels'                => $labels,
		'supports'              => array( 'title' ),
		'taxonomies'            => array( ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => false,
		'show_in_nav_menus'     => false,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => true,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'tg_person', $args );

}
add_action( 'init', 'tinybot_register_post_types', 0 );
