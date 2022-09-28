<?php

/**
 * Sample Integration Settings
 */

?>

<div class="uap-settings-panel">
	<div class="uap-settings-panel-top">

		<div class="uap-settings-panel-title">
			<?php esc_html_e( 'SendinBlue integration settings', 'automator-sendinblue-integration' ); ?>
		</div>

		<div class="uap-settings-panel-content">

			<form method="POST" action="options.php">

				<?php

				// Add setting fields
				wp_nonce_field( 'automator-sendinblue-integration' );

				?>

				<uo-text-field
					id="automator_sendinblue_integration_api_key"
					value="<?php echo esc_attr( $sendinblue_api_key ); ?>"
					required

					label="<?php esc_attr_e( 'API Key', 'automator-sendinblue-integration' ); ?>"

					class="uap-spacing-top"
				></uo-text-field>

				<uo-button
					type="submit"
					class="uap-spacing-top"
				>
					<?php esc_html_e( 'Save', 'uncanny-automator' ); ?>
				</uo-button>

			</form>

		</div>

	</div>

</div>