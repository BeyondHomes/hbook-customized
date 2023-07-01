'use strict';

registerBlockType( 'hbook/accom-list', {
	title: hb_blocks_text.accom_list_title,
	icon: 'editor-ul',
	category: 'hbook-blocks',
	supports: {
		className: false,
		customClassName: false,
		html: false
	},

	edit: function edit( props ) {
		var setAttributes = props.setAttributes;
		var attributes = props.attributes;
		var nb_columns = props.attributes.nb_columns;
		var show_thumb = props.attributes.show_thumb;
		var link_thumb = props.attributes.link_thumb;
		var link_title = props.attributes.link_title;
		var thumb_width = props.attributes.thumb_width;
		var thumb_height = props.attributes.thumb_height;
		var view_button = props.attributes.view_button;
		var book_button = props.attributes.book_button;
		var redirection_page_id = props.attributes.redirection_page_id;

		function on_nb_columns_change( changes ) {
			setAttributes({ nb_columns: changes });
		}

		function on_show_thumb_change() {
			setAttributes({ show_thumb: ! show_thumb });
		}

		function on_link_thumb_change() {
			setAttributes({ link_thumb: ! link_thumb });
		}

		function on_link_title_change() {
			setAttributes({ link_title: ! link_title });
		}

		function on_thumb_width_change( changes ) {
			setAttributes({ thumb_width: changes });
		}

		function on_thumb_height_change( changes ) {
			setAttributes({ thumb_height: changes });
		}

		function on_view_button_change() {
			setAttributes({ view_button: ! view_button });
		}

		function on_book_button_change() {
			setAttributes({ book_button: ! book_button });
		}

		function on_redirection_page_id_change( changes ) {
			setAttributes({ redirection_page_id: changes });
		}

		return [
			el(
				InspectorControls,
				null,
				el(
					PanelBody,
					{ title: hb_blocks_text.accom_list_settings },
					el(
						SelectControl, {
							label: hb_blocks_text.nb_columns,
							value: nb_columns,
							onChange: on_nb_columns_change,
							options: hb_blocks_data.nb_columns_options
						}
					),
					el(
						ToggleControl, {
							label: hb_blocks_text.link_title_to_accom,
							checked: link_title,
							onChange: on_link_title_change
						}
					),
					el(
						ToggleControl, {
							label: hb_blocks_text.show_thumb,
							checked: show_thumb,
							onChange: on_show_thumb_change
						}
					),
					show_thumb &&
					el(
						ToggleControl, {
							label: hb_blocks_text.link_thumb_to_accom,
							checked: link_thumb,
							onChange: on_link_thumb_change
						}
					),
					show_thumb &&
					el(
						TextControl, {
							label: hb_blocks_text.thumb_width,
							value: thumb_width,
							onChange: on_thumb_width_change,
							type: 'number',
							step: '10'
						}
					),
					show_thumb &&
					el(
						TextControl, {
							label: hb_blocks_text.thumb_height,
							value: thumb_height,
							onChange: on_thumb_height_change,
							type: 'number',
							step: '10'
						}
					),
					el(
						ToggleControl, {
							label: hb_blocks_text.view_button,
							checked: view_button,
							onChange: on_view_button_change,
						}
					),
					el(
						ToggleControl, {
						label: hb_blocks_text.book_button,
						checked: book_button,
						onChange: on_book_button_change,
						}
					),
					hb_blocks_data.pages_options.length > 0 && book_button &&
					el(
						SelectControl, {
							label: hb_blocks_text.redirection_page,
							value: redirection_page_id,
							onChange: on_redirection_page_id_change,
							options: hb_blocks_data.pages_options
						}
					),
				),
			),
			el(
				'div',
				{ style: { background: '#fff', border: '1px solid', padding: '10px 15px' } },
				hb_blocks_text.accom_list_block
			)
		];
	},
	save: function save() {
		return null;
	}
});