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
class Tru_Fetcher_Activator {

	private $dbClass;

	public function __construct() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/database/class-tru-fetcher-database.php';
		$this->dbClass = new Tru_Fetcher_Database();
	}

	public function activate() {
		$this->dbClass->createSavedItemsTable();
		$this->dbClass->createRatingsTable();
		$this->dbClass->updateVersion();
	}
}
