<?php

/**
 * SendinBlue Integration Settings
 */
class SendinBlue_Integration_Settings {
	private static $initializd = false;

	/**
	 * Creates the settings page
	 */
	public function __construct() {
		$this->initialze();
	}

	private function initialze() {
		if (self::$initializd == false) {
			self::$initializd = true;
			add_action( 'admin_menu', array($this, 'add_settings') );
			add_action( 'admin_init',  array($this, 'register_settings') );

			register_deactivation_hook(AUTOMATOR_SENDINBLUE_INTEGRATION_BASE_FILE,
									array($this, 'delete_options'));
		}
	}

	public static function delete_options() {
		delete_option('automator_sendinblue_integration_api_key');
	}

	/**
	 * Add the settings
	 */
	public function add_settings() {
		add_options_page('Uncanny Automator SendinBlue Integration Settings', // Page title
						 'Automator SendinBlue Settings', // Menu (link) title
						 'manage_options', // User capabilities
						 'uo_sendinblue', // Page slug
						 array($this, 'load_view') // Callback to print content
						);
	}

	/**
	 * Register the settings
	 */
	public function register_settings() {
		// Create section
		add_settings_section(
			'uo_sendinblue_section_1', // section ID
			'', // Title (optional)
			'', // Callback function to display the section (optional)
			'uo_sendinblue'
		);

		// Register settings
		register_setting( 'automator-sendinblue-integration-settings', 'automator_sendinblue_integration_api_key', array('type' => 'string',
																														 'sanitize_callback' => 'sanitize_text_field',
																														 'default' => NULL
																														)
		);

		// Add fields
		add_settings_field('automator_sendinblue_integration_api_key', // Field ID (slug name)
						   'API Key', // Label
						   array($this, 'setting_api_key'), // Callback to print content
						   'uo_sendinblue', // Page slug
						   'uo_sendinblue_section_1', // Section ID
						  );

	}

	public function setting_api_key() {
		printf(
			'<input type="text" id="%s" name="%s" value="%d" size="100"/>',
			'automator_sendinblue_integration_api_key',
			'automator_sendinblue_integration_api_key',
			get_option( 'automator_sendinblue_integration_api_key' )
		);
	}

	/**
	 * Creates the output of the settings page
	 */
	public function load_view() {
		//$api_key = esc_attr( get_option( 'automator_sendinblue_integration_api_key' ) );
		?>
		<div class="wrap">
			<h1><?php echo get_admin_page_title() ?></h1>
			<form method="post" action="options.php">
				<?php
					settings_fields( 'automator-sendinblue-integration-settings' ); // Settings group name
					do_settings_sections( 'uo_sendinblue' ); // just a page slug
					submit_button(); // "Save Changes" button
				?>
			</form>
		</div>
		<?php
	}
}
