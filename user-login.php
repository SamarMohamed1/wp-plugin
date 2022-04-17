<?php
/*
Plugin Name: user-login
Description:  add age to user plugin 
Author: samar mohamed
Version: 1.0.0
Author URI: sm3021419@gmail.com
*/

if(!defined("ABSPATH"))
exit;

if(!defined("PLUGIN_DIR_PATH"))
define("PLUGIN_DIR_PATH",plugin_dir_path(__FILE__));

if(!defined("PLUGIN_URL"))
define("PLUGIN_URL",plugins_url()."./user-login");

echo PLUGIN_DIR_PATH . " , " . PLUGIN_URL;die;

function user_plugin(){

    add_menu_page( 
    'add user',
    'add user',
    'manage_options',
    'user-plugin', 
    'user_form', 
    'dashicons-dashboard',
    11 );

    add_submenu_page( 
      'edit user', 
      'edit user', 
      'manage_options', 
      'edit_user', 
      'edit-user', 
      'edit_user', 
      12
    );

}

add_action("admin_menu","user_plugin");

function user_form(){
  include_once PLUGIN_DIR_PATH."/views/user-form.php";
}

function show_users(){
    include_once PLUGIN_DIR_PATH."/views/show-user.php";
}



function login_user_table(){
  global $wpdb;
  return $wpdb->prefix."login_user";
}

function login_user_generate_table_script(){
  global $wpdb;
  require_once ABSPATH.'wp-admin/includes/upgrade.php';

  $sql="CREATE TABLE `".login_user_table()."` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(200) NOT NULL,
        `email` varchar(200) NOT NULL,
        `age` int(11) NOT NULL unsigned,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

  dbDelta($sql);    
}

function drop_table_plugin_user_login(){
  global $wpdb;
  $wpdb->query("DROP TABLE IF EXISTS".login_user_table());
}

register_activation_hook(__FILE__,"login_user_generate_table_script");

function edit_user(){
  
}



function wl_users(){

$args=[
  'numberusers'=>10,
  'age'=>'user_plugin'
];

$users=get_users($args);

$data=[];
$i=0;

foreach($users as $user){
  $data[$i]['id']=$user->age;
  $i++;
}

return $data;

}


add_action('rest_api_init',function(){
  register_rest_route('wp/v1','plugins',[
    'method'=>'GET',
    'callback'=>'wl_users'
  ]);
});

add_action('rest_api_init',function(){
  register_rest_route('wp/v2','plugins/<plugin>?',[
    'method'=>'POST',
    'callback'=>'edit_user'
  ]);
});



