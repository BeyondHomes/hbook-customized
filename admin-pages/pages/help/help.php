<?php
class HbAdminPageHelp extends HbAdminPage {

	public function __construct( $page_id, $hbdb, $utils, $options_utils ) {
		$this->data = array(
			'hb_text' => array()
		);
		parent::__construct( $page_id, $hbdb, $utils, $options_utils );
	}

	public function display() {
	?>

	<div class="wrap">

		<h1><?php esc_html_e( 'Help', 'hbook-admin' ); ?></h1>
		<?php $this->display_right_menu(); ?>

		<hr/>

		<p>
			<b><?php esc_html_e( 'If you need help using HBook plugin here are some useful ressources.', 'hbook-admin' ); ?></b>
		</p>

		<p>
			<?php
			global $locale;
			if ( $locale == 'fr_FR' ) {
				$doc_lang = 'fr';
			} else {
				$doc_lang = 'en';
			}
			?>
			<?php esc_html_e( 'Before setting up the plugin you should have a look at its documentation. You will find all the basic information you need to use HBook.', 'hbook-admin' ); ?><br/>
			<a href="https://maestrel.com/documentation/hbook/<?php echo( esc_html( $doc_lang ) ); ?>/" target="_blank"><?php esc_html_e( 'Go to the documentation', 'hbook-admin' ); ?></a>
		</p>

		<p>
			<?php esc_html_e( 'If you need to solve a specific issue you can try to enter your question in the search engine of our knowledge base and you might directly find the answer.', 'hbook-admin' ); ?><br/>
			<a href="https://maestrel.com/knowledgebase/" target="_blank"><?php esc_html_e( 'Go to the knowledgebase', 'hbook-admin' ); ?></a><br/>
		</p>

		<p>
			<?php esc_html_e( 'If you can not find the answer you are looking for in the documentation or in the knowledgebase, feel free to send us a message via our contact form.', 'hbook-admin' ); ?><br/>
			<a href="https://maestrel.com/contact-support/" target="_blank"><?php esc_html_e( 'Go to the contact support form', 'hbook-admin' ); ?></a>
		</p>

	</div><!-- end .wrap -->

	<?php
	}
}