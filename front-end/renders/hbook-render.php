<?php
class HBookRender {

	public $hbdb;
	public $utils;
	public $strings;

	public function __construct( $hbdb, $utils ) {
		$this->hbdb = $hbdb;
		$this->utils = $utils;
		static $strings = false;
		if ( ! $strings ) {
			$strings = $hbdb->get_strings();
		}
		$this->strings = $strings;
	}
}