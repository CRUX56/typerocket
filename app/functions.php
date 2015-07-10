<?php
function tr_taxonomy(
	$singular,
	$plural,
	$settings = array(),
	$use = array()
) {
	$obj = new \TypeRocket\Taxonomy();
	$obj->make( $singular, $plural, $settings )->apply( $use )->reg();

	return $obj;
}

function tr_post_type(
	$singular,
	$plural,
	$settings = array(),
	$use = array()
) {
	$obj = new \TypeRocket\PostType();
	$obj->make( $singular, $plural, $settings )->apply( $use )->reg();

	return $obj;
}

function tr_meta_box(
	$name = null,
	$settings = array(),
	$use = array()
) {
	$obj = new \TypeRocket\Metabox();
	$obj->make( $name, $settings )->apply( $use )->reg();

	return $obj;
}

function tr_form() {
	$obj = new \TypeRocket\Form();
	$obj->make();
	return $obj;
}

function tr_post_field( $name, $item_id = null ) {
	global $post;

	if ( isset( $post->ID ) && is_null( $item_id ) ) {
		$item_id = $post->ID;
	}

	$getter = new \TypeRocket\GetValue();

	return $getter->value( $name, $item_id, 'post' );
}

function tr_user_field( $name, $item_id = null ) {
	global $user_id, $post;

	if ( isset( $user_id ) && is_null( $item_id ) ) {
		$item_id = $user_id;
	} elseif ( is_null( $item_id ) && isset( $post->ID ) ) {
		$item_id = get_the_author_meta( 'ID' );
	} elseif ( is_null( $item_id ) ) {
		$item_id = get_current_user_id();
	}

	$getter = new \TypeRocket\GetValue();

	return $getter->value( $name, $item_id, 'user' );
}

function tr_option_field( $name ) {
	$getter = new \TypeRocket\GetValue();

	return $getter->value( $name, null, 'option' );
}

function tr_comment_field( $name, $item_id = null ) {
	global $comment;

	if ( isset( $comment->comment_ID ) && is_null( $item_id ) ) {
		$item_id = $comment->comment_ID;
	}

	$getter = new \TypeRocket\GetValue();

	return $getter->value( $name, $item_id, 'comment' );
}

function tr_db_field( $the_field, $item_id, $controller ) {
	/** @var \wpdb $wpdb */
	global $wpdb;

	$table  = $wpdb->prefix . $controller;
	$data   = null;
	$fields = $wpdb->get_row( "SELECT * FROM {$table} WHERE id = '{$item_id}';", ARRAY_A ); // WP caches for you
	$data   = $fields[ $the_field ];

	return maybe_unserialize( $data );
}