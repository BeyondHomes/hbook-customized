'use strict';

registerBlockType( 'hbook/resa-summary', {
	title: hb_blocks_text.resa_summary_title,
	icon: 'list-view',
	category: 'hbook-blocks',
	supports: {
		className: false,
		customClassName: false,
		html: false
	},

	edit: function edit( props ) {
		return [
			el(
				InspectorControls,
				null,
				null
			),
			el(
				'div',
				{ style: { background: '#fff', border: '1px solid', padding: '10px 15px' } },
				el(
					'div',
					null,
					hb_blocks_text.resa_summary_block
				)
			)
		];
	},
	save: function save() {
		return null;
	}
});