<?php 

/**
 * Class Sendinblue_Integration
 */
class Sendinblue_Integration_Helpers {
	/**
	 * Sendinblue_Integration_Helpers constructor.
	 */
	public function __construct() {
		// Load settings
		$this->load_settings();
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
}
