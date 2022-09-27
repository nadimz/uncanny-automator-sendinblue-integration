<?php

use Uncanny_Automator\Recipe;

/**
 * Class Add_SendinBlue_Integration
 */
class Add_SendinBlue_Integration {
	use Recipe\Integrations;

	/**
	 * Add_SendinBlue_Integration constructor.
	 */
	public function __construct() {
		$this->setup();
	}

	/**
	 *
	 */
	protected function setup() {
		$this->set_integration( 'SENDINBLUE' );
		$this->set_name( 'SendinBlue' );
		$this->set_icon( 'sendinblue-logo.svg' );
		$this->set_icon_path( __DIR__ . '/img/' );
		$this->set_plugin_file_path( dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'uncanny-automator-sendinblue-integration.php' );
		$this->set_external_integration( true );
	}

	/**
	 * Explicitly return true because this plugin code will only run if it's active.
	 * Add your own plugin active logic here, for example, check for a specific function exists before integration is
	 * returned as active.
	 *
	 * This is an option override. By default Uncanny Automator will check $this->get_plugin_file_path() to validate
	 * if plugin is active.
	 *
	 * @return bool
	 */
	public function plugin_active() {
		return true;
	}
}
