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
class Tru_Fetcher_Blocks extends Tru_Fetcher_Base {

    public function blocks_init() {
        $this->registerBlocks();
    }

    public function registerBlocks()
    {
		$this->directoryIncludes( 'blocks/register-blocks', 'acf-register.php' );
    }

    private function directoryIncludes( $pathName, $fileName ) {
        $dir = new DirectoryIterator( plugin_dir_path( dirname( __FILE__ ) ) . $pathName );
        foreach ( $dir as $fileinfo ) {
            if ( $fileinfo->isDot() ) {
                continue;
            }
            $fileDir = $fileinfo->getRealPath() . '/' . $fileName;
            if (file_exists($fileDir)) {
                require_once( $fileDir );
            }
        }
    }
}
