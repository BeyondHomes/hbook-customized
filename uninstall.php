<?php

if ( ! defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

if ( get_option( 'hb_uninstall_delete_all' ) == 'yes' ) {

	require_once plugin_dir_path( __FILE__ ) . 'utils/options-utils.php';
	$settings = new HbOptionsUtils();
	$settings->delete_options();

	delete_option( 'hbook_version' );
	delete_option( 'hbook_previous_version' );
	delete_option( 'hbook_installing' );

	require_once plugin_dir_path( __FILE__ ) . 'database-actions/database-actions.php';
	$hbdb = new HbDataBaseActions();
	$accom = $hbdb->get_all_accom_ids( true );
	foreach ( $accom as $accom_id ) {
		wp_delete_post( $accom_id, true );
	}
	$hbdb->delete_plugin_tables();

}