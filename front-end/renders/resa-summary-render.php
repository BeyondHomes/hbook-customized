<?php
class HBookResaSummary extends HBookRender {

	public function render() {
		$this->utils->load_jquery();
		$this->utils->load_datepicker();
		$this->utils->load_front_end_script( 'utils' );
		$this->utils->load_front_end_script( 'summary' );

		require_once $this->utils->plugin_directory . '/utils/resa-summary.php';
		$summary = new HbResaSummary( $this->hbdb, $this->utils, $this->strings );
		if ( isset( $_POST['hb-resa-id'] ) ) {
			$id = intval( $_POST['hb-resa-id'] );
			if ( $_POST['hb-resa-is-parent'] ) {
				$parent_resa = $this->hbdb->get_single( 'parents_resa', $id );
				$resa = $this->hbdb->get_resa_by_parent_id( $id );
				$customer_info = $this->hbdb->get_customer_info( $parent_resa['customer_id'] );
			} else {
				$resa = $this->hbdb->get_single( 'resa', $id );
				$customer_info = $this->hbdb->get_customer_info( $resa['customer_id'] );
				$resa = array( $resa );
				$parent_resa = false;
			}
			return $summary->get_summary( $resa, $parent_resa, $customer_info, $_POST['hb-resa-payment-type'] );
		} else {
			return '';
		}
	}

}