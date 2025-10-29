<?php

/**
 * Newsletter Metabox.
 */

// Exit if accessed directly
if (! defined('ABSPATH')) exit;

$current_offset = get_option('gmt_offset');
$tzstring       = get_option('timezone_string');

$check_zone_info = true;

// Remove old Etc mappings. Fallback to gmt_offset.
if (false !== strpos($tzstring, 'Etc/GMT')) {
	$tzstring = '';
}

if (empty($tzstring)) { // Create a UTC+- zone if no timezone string exists.
	$check_zone_info = false;
	if (0 == $current_offset) {
		$tzstring = 'UTC+0';
	} elseif ($current_offset < 0) {
		$tzstring = 'UTC' . $current_offset;
	} else {
		$tzstring = 'UTC+' . $current_offset;
	}
}

$timezone_format = _x('H:i:s', 'timezone date format');

?>

<?php if (newsletterglue_is_automation()) : ?>
	<h3>Automation details</h3>

	<div class="ngl-metabox-flex">

		<div class="ngl-metabox-flex">

			<div class="ngl-metabox-header">
				<label for="ngl_frequency"><?php esc_html_e('Email frequency', 'newsletter-glue'); ?></label>
			</div>

			<div class="ngl-field ngl-select-frequency">
				<?php
				newsletterglue_select_field(array(
					'id' 			=> 'ngl_frequency',
					'options'		=> $automation->get_schedule_options(),
					'default'		=> isset($settings->frequency) ? $settings->frequency : 'weekly',
					'legacy'		=> true,
					'class'			=> 'is-required',
				));
				?>
			</div>

			<div class="ngl-field-multi ngl-select-frequency-on">
				<div class="ngl-select-on"><?php _e('On', 'newsletter-glue'); ?> <span class="ngl-field ngl-select-monthfr"><?php _e('the first', 'newsletter-glue'); ?></span></div>
				<div class="ngl-field ngl-select-monthday">
					<?php
					newsletterglue_select_field(array(
						'id' 			=> 'ngl_frequency_day2',
						'options'		=> $automation->get_weekdays(true),
						'default'		=> isset($settings->frequency_day2) ? $settings->frequency_day2 : 7,
						'legacy'		=> true,
						'class'			=> 'is-required',
					));
					?>
				</div>
				<div class="ngl-field ngl-select-weekday">
					<?php
					newsletterglue_select_field(array(
						'id' 			=> 'ngl_frequency_day',
						'options'		=> $automation->get_weekdays(),
						'default'		=> isset($settings->frequency_day) ? $settings->frequency_day : 1,
						'legacy'		=> true,
						'class'			=> 'is-required',
					));
					?>
				</div>
				<div class="ngl-field ngl-select-time">
					<?php
					newsletterglue_select_field(array(
						'id' 			=> 'ngl_frequency_time',
						'options'		=> $automation->get_times(),
						'default'		=> isset($settings->frequency_time) ? $settings->frequency_time : '7pm',
						'legacy'		=> true,
						'class'			=> 'is-required',
					));
					?>
				</div>
				

				<div class="ngl-field ngl-select-day-exception" style="padding: 10px 0;">
				<div class="ngl-metabox-header" style="display: block;">
					<label for="ngl_frequency_day_exception"><?php esc_html_e('Day exception', 'newsletter-glue'); ?></label>
				</div>
					<?php
					newsletterglue_select_field(
						array(
							'id'     => 'ngl_frequency_day_exception',
							'placeholder' => __('Day exception', 'newsletter-glue'),
							'helper' => __('Select the days you want to exclude from the automation.', 'newsletter-glue'),
							'value'  => $automation->get_day_exceptions(),
							'multiple' => true,
							'options' => $automation->get_weekdays(),
							'searchable' => true,
						)
					);
					?>
				</div>



			</div>

			<div class="ngl-helper ngl-helper-muted">
				<?php echo sprintf(esc_html__('The current time on this site is %s. %s.', 'newsletter-glue'), esc_html(date_i18n($timezone_format)) . ' (' . esc_html($tzstring) . ')', '<a href="' . esc_url(admin_url('options-general.php')) . '">' . esc_html__('Update time', 'newsletter-glue') . '</a>'); ?>
			</div>

			<div class="ngl-field" style="display: flex; align-items: center; margin-top: 15px;">
				<label for="ngl_send_now" style="margin-right: 10px;"><?php esc_html_e('Send Now', 'newsletter-glue'); ?></label>
				<div class="ui toggle checkbox ngl-brand-toggle">
					<input type="checkbox" name="ngl_send_now" id="ngl_send_now" value="1">
					<label for="ngl_send_now"></label>
				</div>
			</div>
			<style>
				/* Custom toggle color for Newsletter Glue brand */
				.ui.toggle.checkbox.ngl-brand-toggle input:checked ~ .box:before,
				.ui.toggle.checkbox.ngl-brand-toggle input:checked ~ label:before {
					background-color: #007489 !important;
				}
			</style>
			<div class="ngl-helper">
				<?php esc_html_e('Check to send the newsletter immediately. The schedule will be maintained for the next run.', 'newsletter-glue'); ?>
			</div>




		</div>

		<div class="ngl-metabox-flex">

			<div class="ngl-metabox-header">
				<label for="ngl_send_type"><?php esc_html_e('Automation type', 'newsletter-glue'); ?></label>
			</div>

			<div class="ngl-field">
				<?php
				newsletterglue_radio_field(array(
					'id'		=> 'ngl_send_type',
					'options'	=> $automation->get_send_types(),
					'class'		=> 'is-required',
					'default'	=> isset($settings->send_type) ? $settings->send_type : 'draft',
				));
				?>
			</div>

		</div>

	</div>
<?php endif; ?>

<div class="ngl-metabox-flex">

	<div class="ngl-metabox-flex">

		<div class="ngl-metabox-header">
			<label for="ngl_subject"><?php esc_html_e('Subject', 'newsletter-glue'); ?></label>
		</div>

		<div class="ngl-field">
			<?php
			$subject = isset($settings->subject) ? $settings->subject : $defaults->subject;
			if (empty($subject) && defined('NEWSLETTERGLUE_DEMO')) {
				$subject = 'This is your newsletter subject';
			}

			$subject_help = __('Short, catchy subject lines get more opens.', 'newsletter-glue');

			// Define tags with their explanations
			$tags_with_explanations = array(
				'{{newsletter_title}}' => __('The title of your newsletter', 'newsletter-glue'),
				'{{first_name}}' => __('The subscriber\'s first name (if available)', 'newsletter-glue'),
				'{{newsletter_date}}' => __('The current date when the newsletter is sent', 'newsletter-glue'),
				'{{latest_post_title}}' => __('The title of your most recent blog post', 'newsletter-glue'),
				'{{latest_post_title_inside}}' => __('The title of the first post in your campaign added with the Latest Posts block. Works with manual campaigns and automations', 'newsletter-glue')
			);
			
			$subject_help .= ' <a href="#" id="ngl-open-merge-tags">' . __('Click to see available merge tags', 'newsletter-glue') . '</a>';
			
			// Add the modal with existing tags
			$modal_content = '<div id="ngl-merge-tags-modal" style="display: none; position: fixed; z-index: 10000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">';
			$modal_content .= '<div style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 600px; border-radius: 5px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">';
			$modal_content .= '<div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-bottom: 20px;">';
			$modal_content .= '<h3 style="margin: 0; font-size: 18px;">' . __('Available Merge Tags', 'newsletter-glue') . '</h3>';
			$modal_content .= '<span id="ngl-close-merge-tags" style="cursor: pointer; font-size: 24px;">&times;</span>';
			$modal_content .= '</div>';
			$modal_content .= '<div style="margin-bottom: 15px; color: #666; font-size: 14px;">' . __('Click any tag below to add it to your subject line:', 'newsletter-glue') . '</div>';
			
			// Add each tag with its explanation
			foreach ($tags_with_explanations as $tag => $explanation) {
				$modal_content .= '<div class="ngl-merge-tag-card" data-tag="' . esc_attr($tag) . '" style="margin-bottom: 12px; padding: 10px; border: 1px solid #e0e0e0; border-radius: 4px; background-color: #f9f9f9; cursor: pointer; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor=\'#f0f0f0\'" onmouseout="this.style.backgroundColor=\'#f9f9f9\'">';
				$modal_content .= '<div style="margin-bottom: 5px;"><span class="ngl-input-tags"><u>' . esc_html($tag) . '</u></span></div>';
				$modal_content .= '<div style="font-size: 13px; color: #666; font-style: italic;">' . esc_html($explanation) . '</div>';
				$modal_content .= '</div>';
			}
			
			$modal_content .= '<div style="margin-top: 20px; text-align: right; border-top: 1px solid #ddd; padding-top: 10px;">';
			$modal_content .= '<button id="ngl-close-merge-tags-btn" style="padding: 8px 16px; background-color: #ccc; border: none; border-radius: 4px; cursor: pointer;">' . __('Close', 'newsletter-glue') . '</button>';
			$modal_content .= '</div></div></div>';
			
			// Add modal to the admin footer
			add_action('admin_footer', function() use ($modal_content) {
				echo $modal_content;
			});

			newsletterglue_text_field(array(
				'id' 			=> 'ngl_subject',
				'class'			=> 'is-required',
				'helper'		=> $subject_help,
				'value'			=> $subject,
			));
			?>
		</div>

	</div>

	<div class="ngl-metabox-flex">

		<div class="ngl-metabox-header">
			<label for="ngl_preview_text"><?php esc_html_e('Preview text', 'newsletter-glue'); ?></label>
		</div>

		<div class="ngl-field">
			<?php
			newsletterglue_text_field(array(
				'id' 			=> 'ngl_preview_text',
				'helper'		=> __('Snippet of text that appears after your subject in subscribers\' inboxes.', 'newsletter-glue'),
				'value'			=> isset($settings->preview_text) ? $settings->preview_text : $defaults->preview_text,
			));
			?>
		</div>

	</div>

</div>