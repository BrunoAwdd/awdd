<?php
use Awdd\Module;

//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
    exit();

//$module = new Module();
//$module->uninstallAction();

// Deleta as op��es
delete_option( 'awdd' );
delete_site_option( 'awdd' );
