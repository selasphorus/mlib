<?php
/**
 * MLib Options Page: "Music Library Settings"
 *
 * @link https://www.advancedcustomfields.com/resources/options-page/
 */

/**
 * Check if ACF PRO is active and function exists
 */
if ( function_exists( 'acf_add_options_page' ) ) {
	add_action( 'acf/init', 'mlib_register_options_page' );
}

function mlib_register_options_page() {

	// Add the top-level page
	acf_add_options_page(
		array(
			'page_title' => 'MLib Settings',
			'menu_slug'  => 'mlib_settings',
			'redirect'   => false,
		)
	);
	
	/*
	// Add module options page
	acf_add_options_sub_page(array(
		'page_title'	=> ucfirst($module).' Module Options',
		'menu_title'    => ucfirst($module).' Module Options',//'menu_title'    => 'Archive Options', //ucfirst($cpt_name).
		'menu_slug' 	=> $module.'-module-options',
		'parent_slug'   => 'edit.php?post_type='.$primary_cpt,
	));
	*/
	
	// Add 'Modules & Settings' field group
	acf_add_local_field_group(
		array(
			'key'      => 'group_mlib_modules',
			'title'    => 'Modules &amp; Settings',
			'fields'   => array(
				array(
					'key'           => 'field_mlib_modules',
					'label'         => 'Active Modules',
					'name'          => 'mlib_active_modules',
					'type'          => 'checkbox',
					'instructions' => 'Select the modules to activate.',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '25',
						'class' => '',
						'id' => '',
					),
					'choices' => array(
						'music' => 'Musical Works and Editions',
						'instruments' => 'Instruments',
						'organs' => 'Organs',
					),
					'default_value' => array(
					),
					'return_format' => 'value',
					'allow_custom' => 0,
					'layout' => 'vertical',
					'toggle' => 0,
					'save_custom' => 0,
					'custom_choice_button_text' => 'Add new choice',
					'aria-label' => '',
					'relevanssi_exclude' => 0,
				),
				array(
					'key'           => 'field_mlib_use_custom_caps',
					'label'         => 'Use custom capabilities?',
					'name'          => 'mlib_use_custom_caps',
					'type'          => 'true_false',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '15',
						'class' => '',
						'id' => '',
					),
					'default_value' => array(
					),
					'return_format' => 'value',
					'layout' => 'horizontal',
					'aria-label' => '',
					'relevanssi_exclude' => 0,
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'mlib_settings',
					),
				),
			),
		)
	);
	
}
