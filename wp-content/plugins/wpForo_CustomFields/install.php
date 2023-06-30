<?php
/**
*   @package wpForo Custom Fields
*   This is the installation code for the plugin.
*   This file is called when the plugin is activated.
*   Do not run this file directly.
*
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
function check_for_tables(){
    // Check if the tables exist
    global $wpdb;
    $table_prefix = $wpdb->prefix;
    $fields_table_name = $table_prefix . 'custom_wpforo_fields';
    $forms_table_name = $table_prefix . 'custom_wpforo_forms';
    $junction_table_name = $table_prefix . 'custom_wpforo_form_fields';
    $forum_form_table_name = $table_prefix . 'custom_wpforo_forum_forms';
    $custom_post_table_name = $table_prefix . 'custom_wpforo_posts';
    
    $fields_table_exists = $wpdb->get_var("SHOW TABLES LIKE '$fields_table_name'") == $fields_table_name;
    if(!$fields_table_exists){
        create_field_table();
    }
    $forms_table_exists = $wpdb->get_var("SHOW TABLES LIKE '$forms_table_name'") == $forms_table_name;
    if(!$forms_table_exists){
        create_form_table();
    }
    $junction_table_exists = $wpdb->get_var("SHOW TABLES LIKE '$junction_table_name'") == $junction_table_name;
    if(!$junction_table_exists){
        create_field_form_table();
    }
    $forum_form_table_exists = $wpdb->get_var("SHOW TABLES LIKE '$forum_form_table_name'") == $forum_form_table_name;
    if(!$forum_form_table_exists){
        create_forum_form_table();
    }
    $custom_post_table_exists = $wpdb->get_var("SHOW TABLES LIKE '$custom_post_table_name'") == $custom_post_table_name;
    if(!$custom_post_table_exists){
        create_post_table();
    }
}
function create_field_table(){
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    $table_prefix = $wpdb->prefix;
    // Create the fields table
    $fields_table_name = $table_prefix . 'custom_wpForo_fields';
    $fields_table_sql = "CREATE TABLE $fields_table_name (
        id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        field_name VARCHAR(25) NOT NULL,
        field_type VARCHAR(30) NOT NULL,
        field_label VARCHAR(50) NOT NULL,
        field_placeholder VARCHAR(50) NOT NULL DEFAULT '',
        field_fa_icon VARCHAR(50) NOT NULL DEFAULT '',
        field_description VARCHAR(255) NOT NULL DEFAULT '',
        field_default_value VARCHAR(255) NOT NULL DEFAULT '',
        field_required BOOLEAN NOT NULL,
        field_options VARCHAR(255) NOT NULL DEFAULT '',
        PRIMARY KEY (id)
    ) $charset_collate;";
    dbDelta($fields_table_sql);
}
function create_form_table(){
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    $table_prefix = $wpdb->prefix;
    // Create the forms table
    $forms_table_name = $table_prefix . 'custom_wpForo_forms';
    $forms_table_sql = "CREATE TABLE $forms_table_name (
        id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        form_name VARCHAR(255) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";
    dbDelta($forms_table_sql);
}
function create_field_form_table(){
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    $table_prefix = $wpdb->prefix;
    // Create the junction table for form-field relationships
    $junction_table_name = $table_prefix . 'custom_wpForo_form_fields';
    $junction_table_sql = "CREATE TABLE $junction_table_name (
        form_id INT(11) UNSIGNED NOT NULL,
        field_id INT(11) UNSIGNED NOT NULL,
        PRIMARY KEY (form_id, field_id),
        FOREIGN KEY (form_id) REFERENCES {$wpdb->prefix}custom_wpforo_forms(id),
        FOREIGN KEY (field_id) REFERENCES {$wpdb->prefix}custom_wpforo_fields(id)
    ) $charset_collate;";
    dbDelta($junction_table_sql);
}
function create_forum_form_table(){
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    $table_prefix = $wpdb->prefix;
    //Each forum can have ony ONE form
    $forum_form_table_name = $table_prefix . 'custom_wpforo_forum_forms';
    $forum_form_table_sql = "CREATE TABLE $forum_form_table_name (
        id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        forum_id INT(10) UNSIGNED NOT NULL,
        form_id INT(11) UNSIGNED NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY (forum_id),
        FOREIGN KEY (forum_id) REFERENCES {$wpdb->prefix}wpforo_forums(forumid),
        FOREIGN KEY (form_id) REFERENCES {$wpdb->prefix}custom_wpforo_forms(id)
    ) $charset_collate;";
    dbDelta($forum_form_table_sql);
}
function create_post_table(){
    global $wpdb;
    //Make sure that posts table has Innodb engine
    $success=$wpdb->query("ALTER TABLE {$wpdb->prefix}wpforo_posts ENGINE = InnoDB");
    if(!$success){
        echo "Failed to change engine of wpforo_posts table to InnoDB";
    }
    $charset_collate = $wpdb->get_charset_collate();
    $table_prefix = $wpdb->prefix;
    // Create the posts table
    $posts_table_name = $table_prefix . 'custom_wpforo_posts';
    $posts_table_sql = "CREATE TABLE $posts_table_name (
        post_id BIGINT(20) UNSIGNED NOT NULL,
        field_id INT(11) UNSIGNED NOT NULL,
        field_value VARCHAR(255) NOT NULL,
        PRIMARY KEY (post_id, field_id),
        FOREIGN KEY (post_id) REFERENCES {$wpdb->prefix}wpforo_posts(postid),
        FOREIGN KEY (field_id) REFERENCES {$wpdb->prefix}custom_wpforo_fields(id)
    ) $charset_collate;";
    dbDelta($posts_table_sql);
}
check_for_tables();
?>