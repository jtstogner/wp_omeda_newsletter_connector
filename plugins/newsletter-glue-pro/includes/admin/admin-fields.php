<?php
/**
 * Admin Fields.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Show a text field.
 */
function newsletterglue_text_field( $args ) {
	$type			= isset( $args['type'] ) ? $args['type'] : 'text';
	$id 			= isset( $args['id'] ) ? $args['id'] : '';
	$name			= isset( $args['name'] ) ? $args['name'] : $id;
	$label			= isset( $args['label'] ) ? $args['label'] : '';
	$placeholder	= isset( $args['placeholder'] ) ? $args['placeholder'] : '';
	$helper 		= isset( $args['helper'] ) ? $args['helper'] : '';
	$helper_right 	= isset( $args['helper_right'] ) ? $args['helper_right'] : '';
	$value 			= isset( $args['value'] ) ? $args['value'] : '';
	$class 			= isset( $args['class'] ) ? $args['class'] : '';
	$disabled		= isset( $args['disabled'] ) && $args['disabled'] ? 'disabled="disabled"' : '';
	?>
	<div class="ui input <?php echo strstr( $class, 'ngl-ajax' ) ? 'ngl-ajax-field' : ''; ?>">
		<?php if ( $label ) { ?>
		<div class="ngl-title">
			<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
		</div>
		<?php } ?>
		<input type="<?php echo esc_attr( $type ); ?>" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $id ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>" value="<?php echo esc_attr( $value ); ?>" class="<?php echo esc_attr( $class ); ?>" spellcheck="false" <?php echo esc_attr( $disabled ); ?> >
	</div>
	<?php if ( $helper ) { ?>
	<div class="ngl-helper" <?php if ( $helper_right ) echo 'style="text-align:right;"'; ?> ><?php echo newsletterglue_kses_post( $helper ); // phpcs:ignore ?></div>
	<?php
	}
}

/**
 * Show a select field.
 */
function newsletterglue_select_field( $args ) {

	$id 			= isset( $args['id'] ) ? $args['id'] : '';
	$class 			= isset( $args['class'] ) ? $args['class'] : '';
	$name			= isset( $args['name'] ) ? $args['name'] : $id;
	$label			= isset( $args['label'] ) ? $args['label'] : '';
	$placeholder	= isset( $args['placeholder'] ) ? $args['placeholder'] : '';
	$helper 		= isset( $args['helper'] ) ? $args['helper'] : '';
	$helper_right 	= isset( $args['helper_right'] ) ? $args['helper_right'] : '';
	$options 		= isset( $args['options'] ) ? $args['options'] : '';
	$default 		= isset( $args['default'] ) ? $args['default'] : '';
	$value 		    = isset( $args['value'] ) ? $args['value'] : $default; // Use value if provided, otherwise fall back to default
	$legacy			= isset( $args['legacy'] ) ? $args['legacy'] : false;
	$has_icons		= isset( $args['has_icons'] ) ? true : false;
	$multiple		= isset( $args['multiple'] ) ? true : false;
	$searchable		= isset( $args['searchable'] ) ? true : false;

	?>
	<div class="field <?php echo strstr( $class, 'ngl-ajax' ) ? 'ngl-ajax-field' : ''; ?>">

		<?php if ( $label ) { ?>
			<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
		<?php } ?>

		<?php if ( $legacy ) { ?>

		<select name="<?php echo esc_attr( $name ); ?><?php if ( $multiple ) echo '[]'; ?>" id="<?php echo esc_attr( $id ); ?>" class="ui dropdown <?php echo esc_attr( $class ); ?>" <?php if ( $multiple ) echo 'multiple=""'; ?> >
			<option value=""><?php echo esc_html( $placeholder ); ?></option>
			<?php if ( $options ) { ?>
				<?php foreach( $options as $key => $option_value ) { ?>
					<option <?php if ( strstr( $key, 'optgroup' ) ) echo 'disabled'; ?> value="<?php echo esc_attr( $key ); ?>" <?php echo esc_html( newsletterglue_selected( $key, $value ) ); ?>><?php echo esc_html( $option_value ); ?></option>
				<?php } ?>
			<?php } ?>
		</select>

		<?php } else { ?>

		<div class="ui selection dropdown <?php if ( $multiple ) echo 'multiple'; ?> <?php if ( $searchable ) echo 'search'; ?> <?php echo esc_attr( $class ); ?>">
			<input type="hidden" name="<?php echo esc_attr( $name ); ?><?php if ( $multiple ) echo '[]'; ?>" id="<?php echo esc_attr( $id ); ?>" value="<?php echo $multiple ? '' : esc_attr( $value ); ?>">
			<?php if ( $multiple && is_array( $value ) ) : ?>
				<?php foreach( $value as $val ) : ?>
					<input type="hidden" name="<?php echo esc_attr( $name ); ?>[]" value="<?php echo esc_attr( $val ); ?>">
				<?php endforeach; ?>
			<?php endif; ?>
			<div class="default text"><?php echo esc_html( $placeholder ); ?></div>
			<svg class="ngl-dropdown-arrow" stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
			<div class="menu">
				<?php foreach( $options as $key => $option_value ) { ?>
				<div class="item" data-value="<?php echo esc_attr( $key ); ?>">
					<?php if ( $has_icons ) { ?>
					<img class="ui avatar image" src="<?php echo esc_url( newsletterglue_get_url( $key ) ); ?>/assets/icon.png">
					<?php } ?>
					<?php echo wp_kses_post( $option_value ); ?>
				</div>
				<?php } ?>
			</div>
		</div>

		<?php } ?>

		<?php if ( $helper ) { ?>
		<div class="ngl-helper" <?php if ( $helper_right ) echo 'style="text-align:right;"'; ?> ><?php echo newsletterglue_kses_post( $helper ); // phpcs:ignore ?></div>
		<?php } ?>

	</div>
	<?php
}

/**
 * Show a radio field.
 */
function newsletterglue_radio_field( $args ) {

	$id 			= isset( $args['id'] ) ? $args['id'] : '';
	$class 			= isset( $args['class'] ) ? $args['class'] : '';
	$name			= isset( $args['name'] ) ? $args['name'] : $id;
	$label			= isset( $args['label'] ) ? $args['label'] : '';
	$placeholder	= isset( $args['placeholder'] ) ? $args['placeholder'] : '';
	$helper 		= isset( $args['helper'] ) ? $args['helper'] : '';
	$helper_right 	= isset( $args['helper_right'] ) ? $args['helper_right'] : '';
	$options 		= isset( $args['options'] ) ? $args['options'] : '';
	$default 		= isset( $args['default'] ) ? $args['default'] : '';
	$legacy			= isset( $args['legacy'] ) ? $args['legacy'] : false;
	$has_icons		= isset( $args['has_icons'] ) ? true : false;
	$multiple		= isset( $args['multiple'] ) ? true : false;

	?>
	<div class="grouped fields ngl-radio-group">

		<?php if ( $label ) { ?>
		<label><?php echo esc_html( $label ); ?></label>
		<?php } ?>

		<?php foreach( $options as $key => $value ) { 
			if ( $key == $default ) {
				$choice_check = 'choice-checked';
			} else {
				$choice_check = 'choice-unchecked';
			}
		?>
		<div class="field <?php echo esc_attr( $choice_check ); ?> <?php echo strstr( $class, 'ngl-ajax' ) ? 'ngl-ajax-field' : ''; ?>" data-id="<?php echo esc_attr( $key ); ?>">

			<div class="ui radio checkbox">
				<input type="radio" name="<?php echo esc_attr( $id ); ?>" id="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $key ); ?>" <?php checked( $key, $default ); ?>>
				<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></label>
			</div>

		</div>
		<?php } ?>

		<?php if ( $helper ) { ?>
		<div class="ngl-helper" <?php if ( $helper_right ) echo 'style="text-align:right;"'; ?> ><?php echo newsletterglue_kses_post( $helper ); // phpcs:ignore ?></div>
		<?php } ?>

	</div>
	<?php
}

/**
 * Returns if a value is among selected value(s).
 */
function newsletterglue_selected( $value, $selected ) {
	$output = '';
	if ( $selected ) {
		if ( ! is_array( $selected ) ) {
			if ( $value == $selected ) {
				$output = 'selected';
			}
		} else {
			if ( in_array( $value, $selected ) ) {
				$output = 'selected';
			}
		}
	}
	return $output;
}