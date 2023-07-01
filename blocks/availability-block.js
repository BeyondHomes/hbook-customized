'use strict';

registerBlockType( 'hbook/availability', {
	title: hb_blocks_text.availability_title,
	icon: 'calendar-alt',
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
					{ title: hb_blocks_text.availability_settings },
					el(
						SelectControl, {
							label: hb_blocks_text.accom,
							value: accom_id,
							onChange: on_accom_change,
							options: hb_blocks_data.accom_options
						}
					)
				)
			),
			el(
				'div',
				{ style: { background: '#fff', border: '1px solid', padding: '10px 15px' } },
				hb_blocks_text.availability_block
			)
		];
	},
	save: function save() {
		return null;
	}
});