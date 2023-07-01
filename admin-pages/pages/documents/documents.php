<?php
class HbAdminPageDocuments extends HbAdminPage {

	public function __construct( $page_id, $hbdb, $utils, $options_utils ) {
		$langs = $utils->get_langs();
		$doc_langs = array();
		$doc_langs[] = array(
			'lang_value' => '',
			'lang_name' => esc_html__( 'Admin language', 'hbook-admin' )
		);
		foreach ( $langs as $locale => $lang_name ) {
			$lang = array(
				'lang_value' => $locale,
				'lang_name' => $lang_name
			);
			$doc_langs[] = $lang;
		}
		$this->data = array(
			'hb_text' => array(
				'new_document_tmpl' => esc_html__( 'New document template', 'hbook-admin' ),
			),
			'document_tmpls' => $hbdb->get_all( 'document_templates' ),
			'hb_doc_langs' => $doc_langs,
		);
		parent::__construct( $page_id, $hbdb, $utils, $options_utils );
	}

	public function display() {
	?>

	<div class="wrap">

		<h2>
			<?php esc_html_e( 'Document templates', 'hbook-admin' ); ?>
			<a href="#" class="add-new-h2" data-bind="click: create_document_tmpl"><?php esc_html_e( 'Add new document template', 'hbook-admin' ); ?></a>
			<span class="hb-add-new spinner"></span>
		</h2>

		<?php $this->display_right_menu(); ?>

		<br/>

		<!-- ko if: document_tmpls().length == 0 -->
		<?php esc_html_e( 'No document templates set yet.', 'hbook-admin' ); ?>
		<!-- /ko -->

		<!-- ko if: document_tmpls().length > 0 -->
		<?php
		$table_class = 'hb-table';
		if ( $this->utils->is_site_multi_lang() ) {
			$table_class .= ' hb-doc-multiple-lang';
		}
		?>

		<div class="<?php echo( esc_attr( $table_class ) ); ?>">

			<div class="hb-table-head hb-clearfix">
				<div class="hb-table-head-data"><?php esc_html_e( 'Name', 'hbook-admin' ); ?></div>
				<div class="hb-table-head-data hb-data-doc-content"><?php esc_html_e( 'Template', 'hbook-admin' ); ?></div>
				<div class="hb-table-head-data hb-data-doc-lang"><?php esc_html_e( 'Language', 'hbook-admin' ); ?></div>
				<div class="hb-table-head-data hb-table-head-data-action"><?php esc_html_e( 'Actions', 'hbook-admin' ); ?></div>
			</div>
			<div data-bind="template: { name: template_to_use, foreach: document_tmpls, beforeRemove: hide_setting }"></div>

			<script id="text_tmpl" type="text/html">
				<div class="hb-table-row hb-clearfix">
					<div class="hb-table-data" data-bind="text: name"></div>
					<div class="hb-table-data hb-data-doc-content" data-bind="html: content_html"></div>
					<div class="hb-table-data hb-data-doc-lang" data-bind="text: lang_text"></div>
					<div class="hb-table-data hb-table-data-action"><?php $this->display_admin_action(); ?></div>
				</div>
			</script>

			<script id="edit_tmpl" type="text/html">
				<div class="hb-table-row hb-clearfix">
					<div class="hb-table-data"><input data-bind="value: name" type="text" /></div>
					<div class="hb-table-data hb-data-doc-content">
						<textarea class="hb-doc-textarea" data-bind="value: content" /></textarea>
					</div>
					<div class="hb-table-data hb-data-doc-lang">
						<select
							data-bind="
								options: hb_doc_langs,
								optionsValue: 'lang_value',
								optionsText: function ( item ) {
									return hb_decode_entities( item.lang_name )
								},
								value: lang
							"
						>
						</select>
					</div>
					<div class="hb-table-data hb-table-data-action"><?php $this->display_admin_on_edit_action(); ?></div>
				</div>
			</script>

		</div>

		<!-- ko if: document_tmpls().length > 5 -->
		<br/>
		<a href="#" class="add-new-h2 add-new-below" data-bind="click: create_document_tmpl"><?php esc_html_e( 'Add new document template', 'hbook-admin' ); ?></a>
		<span class="hb-add-new spinner"></span>
		<!-- /ko -->

		<p>
			<?php esc_html_e( 'You can use the following variables:', 'hbook-admin' ); ?><br/>
			<?php echo( esc_html( $this->utils->get_ical_email_document_available_vars() ) ); ?>
		</p>

		<!-- /ko -->

	</div>

	<?php
	}

}