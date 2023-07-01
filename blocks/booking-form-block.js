'use strict';

var el = wp.element.createElement;
var registerBlockType = wp.blocks.registerBlockType;
var InspectorControls = wp.blockEditor.InspectorControls;
var PanelBody = wp.components.PanelBody;
var ToggleControl = wp.components.ToggleControl;
var SelectControl = wp.components.SelectControl;
var TextControl = wp.components.TextControl;

registerBlockType( 'hbook/booking-form', {
	title: hb_blocks_text.booking_form_title,
	icon: 'welcome-widgets-menus',
	category: 'hbook-blocks',
	supports: {
		className: false,
		customClassName: false,
		html: false
	},

	edit: function edit( props ) {
		var setAttributes = props.setAttributes;
		var attributes = props.attributes;
		var accom_id = props.attributes.accom_id;
		var search_only = props.attributes.search_only;
		var redirection_page_id = props.attributes.redirection_page_id;
		var thank_you_page_id = props.attributes.thank_you_page_id;

		function on_accom_change( changes ) {
			setAttributes({ accom_id: changes });
			if ( changes != 'all' ) {
				setAttributes({ search_only: false });
			}
		}

		function on_redirection_page_change( changes ) {
			setAttributes({ redirection_page_id: changes });
		}

		function on_thank_you_page_change( changes ) {
			setAttributes({ thank_you_page_id: changes });
		}

		function on_search_only_change() {
			setAttributes({ search_only: !search_only });
		}

		return [
			el (
				InspectorControls,
				null,
				el(
					PanelBody,
					{ title: hb_blocks_text.booking_form_settings },
					el(
						SelectControl, {
							label: hb_blocks_text.accom,
							value: accom_id,
							onChange: on_accom_change,
							options: hb_blocks_data.accom_options
						}
					),
					hb_blocks_data.pages_options.length > 0 &&
					accom_id == 'all' &&
					el(
						ToggleControl, {
							label: hb_blocks_text.search_only,
							checked: search_only,
							onChange: on_search_only_change
						}
					),
					el(
						SelectControl, {
							label: hb_blocks_text.redirection_page,
							value: redirection_page_id,
							onChange: on_redirection_page_change,
							options: hb_blocks_data.pages_options
						}
					),
					! search_only &&
					redirection_page_id == 'none' &&
					el(
						SelectControl, {
							label: hb_blocks_text.thank_you_page,
							value: thank_you_page_id,
							onChange: on_thank_you_page_change,
							options: hb_blocks_data.pages_options
						}
					)
				)
			),
			el(
				'div',
				{ style: { background: '#fff', border: '1px solid', padding: '10px 15px' } },
				el(
					'div',
					null,
					hb_blocks_text.booking_form_block
				),
				search_only &&
				redirection_page_id == 'none' &&
				el(
					'div',
					{ style: { color: '#d94f4f', fontSize: '13px' } },
					hb_blocks_text.select_redirection_page
				)
			)
		];
	},
	save: function save() {
		return null;
	}
});