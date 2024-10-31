<?php
/*
Plugin Name: Promotator
Description: This plugin will help you to make mass mailings to a specific user group with a specific template including unique posts!
Version: 1.1
Author: GeroNikolov
Author URI: http://geronikolov.com
License: GPLv2
*/

class PROMOTATOR {
    function __construct() {
        // Add menu page
        add_action( "admin_menu", array( $this, "prom_dashboard_controller" ) );

        // Register AJAX call for the prom_send_mailing method
		add_action( 'wp_ajax_prom_send_mailing', array( $this, 'prom_send_mailing' ) );
		add_action( 'wp_ajax_nopriv_prom_send_mailing', array( $this, 'prom_send_mailing' ) );

        //Add scripts and styles for the Back-end part
		add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_JS' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_CSS' ) );
    }

    function __destruct() {}

    function prom_dashboard_controller() {
        add_menu_page( "Promotator", "Promotator", "administrator", "promotator", array( $this, "prom_dashboard_builder" ), "dashicons-format-status", NULL );
    }

    function prom_dashboard_builder() {
        require_once plugin_dir_path( __FILE__ ) ."pages/dashboard.php";
    }

	function add_admin_JS( $hook ) {
		wp_enqueue_script( 'ful-admin-js', plugins_url( '/assets/admin.js' , __FILE__ ), array('jquery'), '1.0', true );
	}

	function add_admin_CSS( $hook ) {
		wp_enqueue_style( 'ful-admin-css', plugins_url( '/assets/admin.css', __FILE__ ), array(), '1.0', 'screen' );
	}

    function prom_send_mailing() {
        $receivers_ = isset( $_POST[ "receivers" ] ) && !empty( $_POST[ "receivers" ] ) ? sanitize_text_field( $_POST[ "receivers" ] ) : "";
        $template_ = isset( $_POST[ "template" ] ) && !empty( $_POST[ "template" ] ) ? sanitize_text_field( $_POST[ "template" ] ) : "";
        $posts_ = isset( $_POST[ "posts" ] ) && !empty( $_POST[ "posts" ] ) ? $_POST[ "posts" ] : "";
        $subject_ = isset( $_POST[ "subject" ] ) && !empty( $_POST[ "subject" ] ) ? $_POST[ "subject" ] : "";

        if ( !empty( $receivers_ ) && !empty( $template_ ) && !empty( $posts_ ) && !empty( $subject_ ) ) {
            // Get the receivers
            $args = array(
                "role" => $receivers_,
                "orderby" => "ID",
                "order" => "DESC",
                "number" => -1
            );
            $users_ = get_users( $args );

            // Get the email template and configure it to work with the dynamicly selected posts
            $template_ = file_get_contents( plugin_dir_path( __FILE__ ) . "mailings/". $template_ );

            $post_container = explode( "<!-- post-container -->", $template_ )[1];
            $post_container = explode( "<!-- /post-container -->", $post_container )[0];

            $posts_view = "";
            foreach ( $posts_ as $post_id ) {
                $post_ = get_post( $post_id );
                $post_url = get_permalink( $post_id );
                $post_featured_image = get_the_post_thumbnail_url( $post_id, "full" );
                $post_title = $post_->post_title;
                $post_excerpt = wp_trim_words( $post_->post_content, 35, "..." );

                $posts_view .= $post_container;
                $posts_view = str_replace( "[link]", $post_url, $posts_view );
                $posts_view = str_replace( "[featured-src]", $post_featured_image, $posts_view );
                $posts_view = str_replace( "[title]", $post_title, $posts_view );
                $posts_view = str_replace( "[text]", $post_excerpt, $posts_view );
            }

            $clean_template = explode( "<!-- post-container -->", $template_ )[0];
            $clean_template .= $posts_view;
            $clean_template .= explode( "<!-- /post-container -->", $template_ )[1];

            $clean_template = str_replace( "[site-url]", get_site_url(), $clean_template );
            $clean_template = str_replace( "[site-icon]", get_site_icon_url(), $clean_template );
			$clean_template = str_replace( "[site-name]", get_bloginfo( "name" ), $clean_template );
            $clean_template = str_replace( "[date]", date( "d M Y" ), $clean_template );

            // Send the mailing to the users
            foreach ( $users_ as $user_ ) {
                wp_mail(
                    $user_->data->user_email,
                    $subject_,
                    $clean_template,
                    array( "Content-Type: text/html; charset=UTF-8" )
                );
            }

            echo json_encode( "sent" );
        }

        die( "" );
    }
}

$promotator_ = new PROMOTATOR;
?>
