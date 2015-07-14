<?php
namespace TypeRocket;

class DevPlugin
{

    function __construct()
    {
        if ( ! function_exists( 'add_action' )) {
            echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
            exit;
        }
    }

    function make()
    {
        add_filter( 'admin_footer_text', array( $this, 'tr_remove_footer_admin' ) );
        add_action( 'admin_menu', array( $this, 'menu' ) );
    }

    function tr_remove_footer_admin()
    {
        echo 'TypeRocket developer mode! Run time is ' . (TR_END - TR_START);
    }

    public function menu()
    {
        add_menu_page( 'Dev', 'Dev', 'manage_options', 'tr_dev', array( $this, 'page' ) );
    }

    function page()
    {
        include( __DIR__ . '/page.php' );
    }

}

$tr_dev_plugin = new DevPlugin();
$tr_dev_plugin->make();
unset( $tr_dev_plugin );