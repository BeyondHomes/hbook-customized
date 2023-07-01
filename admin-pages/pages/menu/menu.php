<?php
class HbAdminPageMenu extends HbAdminPage {

	public function __construct( $page_id, $hbdb, $utils, $options_utils ) {
		$this->data = array(
			'hb_text' => array()
		);
		parent::__construct( $page_id, $hbdb, $utils, $options_utils );
	}

	public function display() {
		$hbook_pages = $this->utils->get_hbook_pages();
		foreach ( $hbook_pages as $page ) :
			if ( current_user_can( 'manage_' . $page['id'] ) ) :
				?>
				<a href="<?php echo( esc_url( admin_url( 'admin.php?page=' . $page['id'] ) ) ); ?>" class="hb-menu-box">
					<span class="<?php echo( esc_attr( 'dashicons ' . $page['icon'] ) ); ?>"></span>
					<p><?php echo( esc_html( $page['name'] ) ); ?></p>
				</a>
			<?php endif;
		endforeach;
	}

}