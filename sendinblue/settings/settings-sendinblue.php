<?php

/**
 * SendinBlue Integration Settings
 */
class SendinBlue_Integration_Settings {
	private $settings_group;
	private $settings_page;
	private $settings_page_section;

	private $options;

	/**
	 * Creates the settings page
	 */
	public function __construct() {
		$this->settings_group = 'ua-sendinblue-integration-settings';
		$this->settings_page  = 'ua_sendinblue_settings';
		$this->settings_page_section = 'ua_sendinblue_settings_section';

		$this->options = array(
			'ua_sendinblue_integration_api_key' => array(
				'label' => 'Your SendinBlue API key',
				'callback' => array($this, 'output_setting_api_key')
			)
		);

		add_action('admin_menu',  array($this, 'add_settings'));
		add_action('admin_init',  array($this, 'register_settings'));

		// Delete options when the plugin is desactivated
		register_deactivation_hook(AUTOMATOR_SENDINBLUE_INTEGRATION_BASE_FILE,
								   array($this, 'delete_options'));
	}

	public function delete_options() {
		foreach($this->options as $opt_id => $opt) {
			delete_option($opt_id);
		}
	}

	/**
	 * Add the settings
	 */
	public function add_settings() {
		add_options_page(
			'Uncanny Automator SendinBlue Integration Settings', // Page title
			'Automator SendinBlue Settings', // Menu (link) title
			'manage_options', // User capabilities
			$this->settings_page, // Page slug
			array($this, 'load_view') // Callback to print content
		);
	}

	/**
	 * Register the settings
	 */
	public function register_settings() {
		// Create section
		add_settings_section(
			$this->settings_page_section, // Section ID
			'', // Title (optional)
			'', // Callback function to display the section (optional)
			$this->settings_page // Page slug
		);
		do_action( 'qm/debug', 'befofre foreach');
		foreach($this->options as $opt_id => $opt) {
			// Register settings/options
			register_setting(
				$this->settings_group,
				$opt_id,
				array(
					'type' => 'string',
					'sanitize_callback' => 'sanitize_text_field',
					'default' => NULL
				)
			);
			do_action( 'qm/debug', 'register_settings {opt}', ['opt' => $opt_id]);
			// Add fields
			add_settings_field(
				$opt_id, // Field ID (slug name)
				$opt['label'], // Label
				$opt['callback'], // Callback to print content
				$this->settings_page, // Page slug
				$this->settings_page_section, // Section ID
			);
		}
		do_action( 'qm/debug', 'after foreach');
	}

	public function output_setting_api_key() {
		$option = 'ua_sendinblue_integration_api_key';
		$api_key = get_option($option);
		printf(
			'<input type="text" id="%s" name="%s" value="%s" size="100" autocomplete="off"/>',
			$option,
			$option,
			$api_key
		);
	}

	/**
	 * Creates the output of the settings page
	 */
	public function load_view() {
		?>
		<div class="wrap">
			<h1><?php echo get_admin_page_title() ?></h1>
			<form method="post" action="options.php">
				<?php
					settings_fields($this->settings_group); // Settings group name
					do_settings_sections($this->settings_page); // Page slug
					submit_button(); // "Save Changes" button
				?>
			</form>
		</div>
		<?php
	}
}
