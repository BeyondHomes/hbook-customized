<?php
class HbAdminPageAppearance extends HbAdminPage {

	public function __construct( $page_id, $hbdb, $utils, $options_utils ) {
		$this->data = array(
			'hb_text' => array(
				'form_saved' => esc_html__( 'Settings have been saved.', 'hbook-admin' ),
			)
		);
		parent::__construct( $page_id, $hbdb, $utils, $options_utils );
	}

	public function display() {
	?>

	<div class="wrap">

		<form id="hb-settings-form">

			<h1><?php esc_html_e( 'Appearance', 'hbook-admin' ); ?></h1>
			<?php $this->display_right_menu(); ?>

			<?php
			foreach ( $this->options_utils->get_appearance_settings() as $section_id => $section ) {
				$this->options_utils->display_section_title( $section['label'] );
				foreach ( $section['options'] as $id => $option ) {
					if ( $id == 'hb_calendar_colors' ) {
						$this->display_calendar_colors_options();
					} else if ( $id == 'hb_buttons_css_options' ) {
						echo( '<div class="hb-buttons-style-options-wrapper">' );
						$this->display_buttons_style_options();
						echo( '</div><!-- end .hb-buttons-style-options-wrapper -->' );
					} else if ( $id == 'hb_inputs_selects_css_options' ) {
						echo( '<div class="hb-inputs-selects-style-options-wrapper">' );
						$this->display_inputs_selects_style_options();
						echo( '</div><!-- end .hb-inputs-selects-style-options-wrapper -->' );
					} else {
						$function_to_call = 'display_' . $option['type'] . '_option';
						$this->options_utils->$function_to_call( $id, $option );
					}
				}
				$this->options_utils->display_save_options_section();
			}
			wp_nonce_field( 'hb_nonce_update_db', 'hb_nonce_update_db' );
			?>

			<input type="hidden" id="hb_buttons_css_options" name="hb_buttons_css_options" value="" />
			<input type="hidden" id="hb_inputs_selects_css_options" name="hb_inputs_selects_css_options" value="" />
			<input type="hidden" id="hb_calendar_colors" name="hb_calendar_colors" value="" />

			<input type="hidden" name="action" value="hb_update_appearance_settings" />
			<input id="hb-nonce" type="hidden" name="nonce" value="" />

		</form>

	</div>

	<?php
	}

	private function display_calendar_colors_options() {
		$calendar_color_css_rules = $this->utils->calendar_color_css_rules();
		$calendar_color_values = json_decode( get_option( 'hb_calendar_colors' ), true );
		foreach ( $calendar_color_css_rules as $rule_id => $rule_info ) {
			if ( isset( $calendar_color_values[ $rule_id ] ) ) {
				$color_value = $calendar_color_values[ $rule_id ];
			} else {
				$color_value = $rule_info['default'];
			}
			$this->display_color_option( $rule_id, $rule_info, $color_value, 'calendar' );
		}
	}

	private function display_buttons_style_options() {
		$buttons_css_rules = $this->utils->buttons_css_rules();
		$buttons_css_values = json_decode( get_option( 'hb_buttons_css_options' ), true );
		foreach ( $buttons_css_rules as $rule_id => $rule_info ) {
			if ( isset( $buttons_css_values[ $rule_id ] ) ) {
				$css_value = $buttons_css_values[ $rule_id ];
			} else {
				$css_value = $rule_info['default'];
			}
			$function_to_call = 'display_' . $rule_info['type'] . '_option';
			$this->$function_to_call( $rule_id, $rule_info, $css_value, 'buttons' );
		}
	}

	private function display_inputs_selects_style_options() {
		$inputs_selects_css_rules = $this->utils->inputs_selects_css_rules();
		$inputs_selects_css_values = json_decode( get_option( 'hb_inputs_selects_css_options' ), true );
		foreach ( $inputs_selects_css_rules as $rule_id => $rule_info ) {
			if ( isset( $inputs_selects_css_values[ $rule_id ] ) ) {
				$css_value = $inputs_selects_css_values[ $rule_id ];
			} else {
				$css_value = $rule_info['default'];
			}
			$function_to_call = 'display_' . $rule_info['type'] . '_option';
			$this->$function_to_call( $rule_id, $rule_info, $css_value, 'inputs_selects' );
		}
	}

	private function display_color_option( $rule_id, $rule_info, $color_value, $group ) {
	?>

	<p>
		<label class="hb-color-option-label"><?php echo( esc_html( $rule_info['name']  ) ); ?></label><br/>
		<?php $color_input_classes = 'hb-color-option hb-' . $group . '-color hb-' . $group . '-css-option'; ?>
		<input
			id="<?php echo( esc_attr( $rule_id ) ); ?>"
			value="<?php echo( esc_attr( $color_value ) ); ?>"
			data-default-color="<?php echo( esc_attr( $rule_info['default'] ) ); ?>"
			class="<?php echo( esc_attr( $color_input_classes ) ); ?>"
			type="text"
		/>
	</p>

	<?php
	}

	private function display_number_option( $rule_id, $rule_info, $value, $group ) {
	?>

	<p>
		<label for="<?php echo( esc_attr( $rule_id ) ); ?>"><?php echo( esc_html( $rule_info['name'] ) ); ?></label><br/>
		<?php $number_input_classes = 'hb-'  . $group . '-css-option hb-small-field'; ?>
		<input
			id="<?php echo( esc_attr( $rule_id ) ); ?>"
			value="<?php echo( esc_attr( $value ) ); ?>"
			class="<?php echo( esc_attr( $number_input_classes ) ); ?>"
			type="text"
		/>
	</p>

	<?php
	}

	private function display_choice_option( $rule_id, $rule_info, $value, $group ) {
	?>

	<p>
		<label for="<?php echo( esc_attr( $rule_id ) ); ?>"><?php echo( esc_html( $rule_info['name'] ) ); ?></label><br/>
		<?php
		foreach ( $rule_info['choices'] as $choice_id => $choice_label ) {
			$choice_input_class = 'hb-' . $group . '-css-option';
			?>
		<input
			id="<?php echo( esc_attr( $rule_id . '_' . $choice_id ) ); ?>"
			name="<?php echo( esc_attr( $rule_id ) ); ?>"
			value="<?php echo( esc_attr( $choice_id ) ); ?>"
			class="<?php echo( esc_attr( $choice_input_class ) ); ?>"
			type="radio"
			<?php echo( $value == $choice_id ? 'checked' : '' ); ?>
		/>
		<label for="<?php echo( esc_attr( $rule_id . '_' . $choice_id ) ); ?>"><?php echo( esc_html( $choice_label ) ); ?></label>&nbsp;&nbsp;
			<?php
			if ( count ( $rule_info['choices'] ) > 2 ) {
				echo( '<br/>' );
			}
		}
		?>
	</p>

	<?php
	}

}