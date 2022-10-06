<?php 

/**
 * Class Sendinblue_Integration
 */
class Sendinblue_Integration_Helpers {
	/*
	 * Sendinblue API implementation
	 */
	public $api;

	/**
	 * Sendinblue_Integration_Helpers constructor.
	 */
	public function __construct() {
		// Load settings
		$this->load_settings();

		include_once __DIR__ . '/class-sendinblue-api.php';
		$this->api = new Sendinblue_API();
	}

	/**
	 * Load the settings
	 * 
	 * @return void
	 */
	private function load_settings() {
		include_once __DIR__ . '/../settings/settings-sendinblue.php';
		new Sendinblue_Integration_Settings();
	}

	private function get_verified_arg($arg) {
		return isset( $arg ) ? $arg :  '';
	}

	public function get_sanitized_email($arg) {
		return sanitize_email($this->get_verified_arg( $arg ) );
	}

	public function get_sanitized_text($arg) {
		return sanitize_text_field($this->get_verified_arg( $arg ) );
	}
}
