<?php
/* 
Plugin Name: Awdd Plugin
Plugin URI: http://www.awdd.com.br/plugins/awdd.zip
Description: This is my demo plugin
Version: 0.0.1
Author: Bruno Alves de Oliveira
Author URI: http://www.awdd.com.br
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
 
require_once('Awdd/__autoloader.php');


use Awdd\Module;

$module = new Module();
add_action('admin_menu', array($module, 'uninstallAction'));


?>