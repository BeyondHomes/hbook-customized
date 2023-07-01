<?php
class HbPluginCheck {

	private $current_version;
	private $latest_version;
	private $plugin_slug;
	private $download_url;

	function __construct( $current_version ) {
		$this->current_version = $current_version;
		$this->plugin_slug = 'hbook';
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );
		add_filter( 'plugins_api', array( $this, 'check_info' ), 100, 3 );
	}

	private function get_latest_version_info() {
		$force_check = false;
		if ( isset( $_GET['force-check'] ) && ( '1' == $_GET['force-check'] ) ) {
			$force_check = true;
		}
		$request = wp_remote_post(
			'https://maestrel.com/updates/hbook-data/version.php',
			array(
				'body' => array(
					'site_url' => get_site_url(),
					'key' => get_option( 'hb_purchase_code' ),
					'force-check' => $force_check,
				)
			)
		);
		if ( ! is_wp_error( $request ) ) {
			$latest_version_info = json_decode( $request['body'], true );
			if ( isset( $latest_version_info['latest_version'] ) && isset( $latest_version_info['tested'] ) ) {
				return $latest_version_info;
			}
		}
		return false;
	}

	public function check_update( $transient ) {
		$latest_version_info = $this->get_latest_version_info();
		if ( ! $latest_version_info ) {
			return $transient;
		}
		if ( version_compare( $latest_version_info['latest_version'], $this->current_version ) > 0 ) {
			$obj = new stdClass();
			$obj->slug = $this->plugin_slug;
			$obj->plugin = 'hbook/hbook.php';
			$obj->new_version = $latest_version_info['latest_version'];
			$obj->package = 'https://maestrel.com/updates/hbook-data/download.php?key=' . get_option( 'hb_purchase_code' );
			$obj->icons = array( 'default' => 'https://maestrel.com/updates/hbook-data/hbook.png' );
			$obj->tested = $latest_version_info['tested'];
			$transient->response[ $obj->plugin ] = $obj;
		} else {
			$obj = new stdClass();
			$obj->slug = $this->plugin_slug;
			$obj->plugin = 'hbook/hbook.php';
			$obj->new_version =  $this->current_version;
			$obj->package = '';
			$obj->icons = array( 'default' => 'https://maestrel.com/updates/hbook-data/hbook.png' );
			$obj->tested = $latest_version_info['tested'];
			$transient->no_update[ $obj->plugin ] = $obj;
		}
		return $transient;
	}

	public function check_info( $original, $action, $args ) {
		if ( isset( $args->slug ) && ( $args->slug == $this->plugin_slug ) ) {
			$info = $this->get_remote_info();
			$obj = new stdClass();
			$obj->name = 'HBook';
			$obj->slug = $this->plugin_slug;
			$obj->version = $info['latest_version'];
			if ( isset( $info['requires'] ) ) {
				$obj->requires = $info['requires'];
			}
			if ( isset( $info['tested'] ) ) {
				$obj->tested = $info['tested'];
			}
			//$obj->last_updated = '';
			$changelog = 'Could not fetch changelog.';
			if ( isset( $info['changelog'] ) ) {
				$changelog = $info['changelog'];
			}
			$obj->sections = array(
				'changelog' => $changelog
			);
			//$obj->download_link = '';
			return $obj;
		} else {
			return $original;
		}
	}

	private function get_remote_info() {
		$request = wp_remote_post( 'https://maestrel.com/updates/hbook-data/info.php' );
		if ( ! is_wp_error( $request ) ) {
			return json_decode( $request['body'], true );
		} else {
			return false;
		}
	}

}