'use strict';

registerBlockType( 'hbook/starting-price', {
	title: hb_blocks_text.starting_price_title,
	icon: 'money-alt',
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
	
		function on_accom_change( changes ) {
			setAttributes({ accom_id: changes });
		}

		return [
			el(
				InspectorControls,
				null,
				el(
					PanelBody,
					{ title: hb_blocks_text.starting_price_settings },
					el(
						SelectControl, {
							label: hb_blocks_text.accom,
							value: accom_id,
							onChange: on_accom_change,
							options: hb_blocks_data.accom_options_without_all,
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
					hb_blocks_text.starting_price_block
				),
				! accom_id &&
				el(
					'div',
					{ style: { color: '#d94f4f', fontSize: '13px' } },
					hb_blocks_text.select_accom
				)
			)
		];
	},
	save: function save() {
		return null;
	}
});