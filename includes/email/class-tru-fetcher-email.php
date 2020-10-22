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

	const DEFAULT_TEMPLATE_VARS = [
		"SITE_NAME"   => "",
		"SITE_EMAIL"  => "",
		"EMAIL_TITLE" => "",
		"USER_EMAIL"  => "",
		"USERNAME"    => "",
		"SITE_URL"    => "",
		"FRONTEND_URL"    => "",
		"DATE_YEAR"   => "",
	];
	private array $defaultTemplateVariables;
	private $options;

	public function __construct() {
		$this->loadDependencies();
		$this->defaultTemplateVariables = $this->getDefaultTemplateVariables();
		$this->options                  = get_fields( "option" );
	}

	public function loadDependencies() {

	}

	public function getEmailTemplate( $templateName = null ) {
		if ( $templateName === null ) {
			return false;
		}

		return file_get_contents(
			plugin_dir_path( dirname( __FILE__ ) ) . 'email/templates/' . $templateName . '.html'
		);
	}

	public function init() {
		add_filter( 'wp_mail_content_type', [ $this, 'setHtmlEmailContentType' ] );
		add_filter( 'wp_new_user_notification_email', [ $this, "userNotificationEmail" ], 10, 3 );
	}

	public function sendEmail( $to = null, $subject = null, $templateName = null, $templateVars = [] ) {
		$getTemplate = $this->getEmailTemplate( $templateName );
		if ( ! $getTemplate ) {
			return false;
		}
		var_dump($to, $subject);
		return false;
		$emailContent = $this->filterEmailContent( $getTemplate, array_merge($this->defaultTemplateVariables, $templateVars) );

		return wp_mail( $to, $subject, $emailContent );
	}

	public function userNotificationEmail( $email, $user, $blogname ) {
		$templateVars = array_merge( $this->defaultTemplateVariables, [
			"EMAIL_TITLE" => sprintf( "%s | %s", $blogname, "Confirmation" ),
			"USER_EMAIL"  => $user->user_email,
			"USERNAME"    => $user->user_login,
		] );
		$message      = $this->getEmailTemplate( "email-confirmation" );
		if ( $message && $message !== "" ) {
			$email['message'] = $this->filterEmailContent( $message, $templateVars );
		}

		$email["subject"] = sprintf( "Welcome to %s", $blogname );

		return $email;

	}

	private function filterEmailContent( $content, $templateVars ) {
		foreach ( $templateVars as $key => $value ) {
			$content = str_replace( "###" . $key . "###", $templateVars["$key"], $content );
		}

		return $content;
	}

	private function getDefaultTemplateVariables() {
		$date        = new DateTime();
		return array_merge( self::DEFAULT_TEMPLATE_VARS, [
			"SITE_NAME"  => get_option( 'blogname' ),
			"SITE_URL"   => get_option( 'siteurl' ),
			"SITE_EMAIL" => get_option( 'admin_email' ),
			"FRONTEND_URL"   => Tru_Fetcher::getFrontendUrl(),
			"DATE_YEAR"  => $date->format( "Y" ),
		] );
	}

	public function setHtmlEmailContentType() {
		return 'text/html';
	}
}
