<?php

/**
 * Fired during plugin activation
 *
 * @link       https://truvoicer.co.uk
 * @since      1.0.0
 *
 * @package    Tru_Fetcher
 * @subpackage Tru_Fetcher/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Tru_Fetcher
 * @subpackage Tru_Fetcher/includes
 * @author     Michael <michael@local.com>
 */
class Tru_Fetcher_Email {

	private array $defaultTemplateVariables;
	private $options;

	public function __construct() {
		$this->loadDependencies();
		$this->options = get_fields("option");
	}

	public function loadDependencies() {

	}

	public function init() {
		add_filter( 'wp_mail_content_type', [$this, 'wpdocs_set_html_mail_content_type'] );
		add_filter( 'wp_new_user_notification_email' , [$this, "userNotificationEmail"], 10, 3 );
	}


	public function userNotificationEmail( $email, $user, $blogname ) {
//		$user->user_login
//		get_option( 'admin_email' )
		//		$message = sprintf(__( "Welcome to %s!" ),  ) . "\r\n";
//		$message .= wp_login_url() . "\r\n";
//		$message .= sprintf(__( 'Username: %s' ),  ) . "\r\n";
//		$message .= sprintf(__( 'If you have any problems, please contact me at %s.'),  ) . "\r\n";
//		$message .= __('Adios!');
		$message = file_get_contents(plugin_dir_path( dirname( __FILE__ ) ) . 'templates/email-confirmation.html');


		$email['message'] = $message;

		return $email;

	}

	private function setDefaultTemplateVariables() {
		$date = new DateTime();
		return [
			"SITE_NAME" => "",
			"EMAIL_TITLE" => "",
			"USER_EMAIL" => "",
			"USERNAME" => "",
			"SITE_URL" => $this->options[""],
			"DATE_YEAR" => $date->format("Y"),
		];
	}

	public function wpdocs_set_html_mail_content_type() {
		return 'text/html';
	}
}
