'use strict';

registerBlockType( 'hbook/rates', {
	title: hb_blocks_text.rates_title,
	icon: 'grid-view',
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
		var type = props.attributes.type;
		var sorting = props.attributes.sorting;

		function on_accom_change( changes ) {
			setAttributes({ accom_id: changes });
		}

		function on_type_change( changes ) {
			setAttributes({ type: changes });
		}

		function on_sorting_change( changes ) {
			setAttributes({ sorting: changes });
		}

		return [
			el(
				InspectorControls,
				null,
				el(
					PanelBody,
					{ title: hb_blocks_text.rates_settings },
					el(
						SelectControl, {
							label: hb_blocks_text.accom,
							value: accom_id,
							onChange: on_accom_change,
							options: hb_blocks_data.accom_options_without_all
						}
					),
					el(
						SelectControl, {
							label: hb_blocks_text.rates_type,
							value: type,
							onChange: on_type_change,
							options: [{ value: 'accom', label: hb_blocks_text.rates_type_accom }, { value: 'extra_adults', label: hb_blocks_text.rates_type_adults }, { value: 'extra_children', label: hb_blocks_text.rates_type_children }]
						}
					),
					el(
						SelectControl, {
							label: hb_blocks_text.rates_sorting,
							value: sorting,
							onChange: on_sorting_change,
							options: [{ value: 'grouped', label: hb_blocks_text.rates_sorting_grouped }, { value: 'chrono', label: hb_blocks_text.rates_sorting_chrono }]
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
					hb_blocks_text.rates_block
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