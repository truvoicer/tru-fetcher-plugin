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
		$this->defaultTemplateVariables = $this->getDefaultTemplateVariables();
		$this->options = get_fields("option");
	}

	public function loadDependencies() {

	}

	public function init() {
		add_filter( 'wp_mail_content_type', [$this, 'setHtmlEmailContentType'] );
		add_filter( 'wp_new_user_notification_email' , [$this, "userNotificationEmail"], 10, 3 );
	}


	public function userNotificationEmail( $email, $user, $blogname ) {
		$templateVars = array_merge($this->defaultTemplateVariables, [
			"EMAIL_TITLE" => sprintf("%s | %s", $blogname, "Confirmation"),
			"USER_EMAIL" => $user->user_email,
			"USERNAME" => $user->user_login,
		]);
		$message = file_get_contents(plugin_dir_path( dirname( __FILE__ ) ) . 'email/templates/email-confirmation.html');
		if ( $message && $message !== "" ) {
			$email['message'] = $this->filterEmailContent($message, $templateVars);
		}

		$email["subject"] = sprintf("Welcome to %s", $blogname);

		return $email;

	}

	private function filterEmailContent($content, $templateVars) {
		foreach ($this->defaultTemplateVariables as $key => $value) {
			$content = str_replace("###". $key ."###", $templateVars["$key"], $content);
		}
		return $content;
	}

	private function getDefaultTemplateVariables() {
		$date = new DateTime();
		$frontendUrl = get_option( 'siteurl' );
		if (isset($this->options["general_settings"]["frontend_url"])) {
			$frontendUrl = $this->options["general_settings"]["frontend_url"];
		}
		return [
			"SITE_NAME" => get_option( 'blogname' ),
			"SITE_EMAIL" => get_option( 'admin_email' ),
			"EMAIL_TITLE" => "",
			"USER_EMAIL" => "",
			"USERNAME" => "",
			"SITE_URL" => $frontendUrl,
			"DATE_YEAR" => $date->format("Y"),
		];
	}

	public function setHtmlEmailContentType() {
		return 'text/html';
	}
}
