<?php

namespace TruFetcher\Includes;

use TruFetcher\Includes\DB\Engine\Tru_Fetcher_DB_Engine;
use TruFetcher\Includes\DB\Traits\WP\Tru_Fetcher_DB_Traits_WP_Site;

/**
 * @package    Tru_Fetcher
 * @subpackage Tru_Fetcher/includes
 * @author     Michael <michael@local.com>
 */
class Tru_Fetcher_Health_Check
{
    use Tru_Fetcher_DB_Traits_WP_Site;

    const HEALTH_CHECK_MISSING_TABLES = 'HEALTH_CHECK_MISSING_TABLES';
    const HEALTH_CHECK_MISSING_COLUMNS = 'HEALTH_CHECK_MISSING_COLUMNS';
    const HEALTH_CHECK_REQUIRED_DATA = 'HEALTH_CHECK_REQUIRED_DATA';
    private ?\WP_Error $missingTables = null;
    private ?\WP_Error $missingColumns = null;
    private ?\WP_Error $requiredData = null;
    private Tru_Fetcher_DB_Engine $dbEngine;

    public function __construct()
    {
        $this->dbEngine = new Tru_Fetcher_DB_Engine();
    }

    public function checkMissingColumns()
    {
        global $wpdb;
        $wpdb->hide_errors();
        $checkMissingColumns = $this->dbEngine->checkMissingColumns();
        $wpdb->show_errors();
        if (count($checkMissingColumns['errors'])) {
            return new \WP_Error(
                'tr_news_app_error_health_check',
                'Errors checking missing table columns',
                [
                    'success' => false,
                    'errors' => $checkMissingColumns['errors']
                ]
            );
        }
        if (count($checkMissingColumns['results'])) {
            return new \WP_Error(
                'tr_news_app_error_health_check',
                'New additional table columns',
                [
                    'success' => true,
                    'data' => $checkMissingColumns['results']
                ]
            );
        }
        return true;
    }


    public function initialInstall()
    {
        $checkMissingTables = $this->dbEngine->checkAllMissingTables();
        if (is_wp_error($checkMissingTables)) {
            $installTableResponseData = $this->installRequiredTables();
            if (!isset($installTableResponseData['success']) || !$installTableResponseData['success']) {
                error_log(json_encode($installTableResponseData));
                return false;
            }
        }

        $installDataResponseData = $this->installInitialModelData();
        if (!isset($installDataResponseData['success']) || !$installDataResponseData['success']) {
            error_log(json_encode($installDataResponseData));
            return false;
        }

        return true;
    }

    private function dbHealthCheckHandler($callbackData) {

        $results = [];
        $checkMissingTables = $this->dbEngine->checkAllMissingTables();
        if (is_wp_error($checkMissingTables)) {
            $this->setMissingTables($checkMissingTables);
            $results['tables'] = $checkMissingTables->get_error_data();
            Tru_Fetcher_Helpers::callbackHandler(
                $callbackData,
                self::HEALTH_CHECK_MISSING_TABLES,
                $results
            );
        }
        $checkMissingColumns = $this->checkMissingColumns();
        if (is_wp_error($checkMissingColumns)) {
            $this->setMissingColumns($checkMissingColumns);
            $results['missing_columns'] = $checkMissingColumns->get_error_data();
            Tru_Fetcher_Helpers::callbackHandler(
                $callbackData,
                self::HEALTH_CHECK_MISSING_COLUMNS,
                $results
            );
        }
        $checkRequiredData = $this->dbEngine->checkRequiredData();
        if (is_wp_error($checkRequiredData)) {
            $this->setRequiredData($checkRequiredData);
            $results['required_data'] = $checkRequiredData->get_error_data();
            Tru_Fetcher_Helpers::callbackHandler(
                $callbackData,
                self::HEALTH_CHECK_REQUIRED_DATA,
                $results
            );
        }

        if (
            is_wp_error($this->getMissingTables()) ||
            is_wp_error($this->getMissingColumns()) ||
            is_wp_error($this->getRequiredData())
        ) {
            return false;
        }
        return true;
    }

    public function renderAdminNotice(string $type, array $data) {
        $this->dbEngine->adminNoticeMissingTable($data);
    }
    public function runAdminHealthCheck()
    {
        $dbHealthCheck = $this->dbHealthCheckHandler([$this, 'renderAdminNotice']);
        return $dbHealthCheck;
    }

    public function responseHasErrors(array $data)
    {
        $filterData = array_filter($data, function ($result) {
            return (
                !isset($result['result']) ||
                !isset($result['result']['success']) ||
                !$result['result']['success']
            );
        }, ARRAY_FILTER_USE_BOTH);
        return  (count($filterData));
    }

    private function buildInstallResponseData(array $data, string $code, string $errorMessage, string $successMessage)
    {
        if ($this->responseHasErrors($data)) {
            return [
                'success' => false,
                'code' => "{$code}_error",
                'message' => $errorMessage,
                'data' => [$code => $data]
            ];
        }
        return [
            'success' => true,
            'code' => "{$code}_success",
            'message' => $successMessage,
            'data' => [$code => $data]
        ];
    }

    public function databaseNetworkInstallAction()
    {
        $this->databaseInstallAction(true);
    }

    public function installInitialModelData() {
        $installRequiredTableData = $this->dbEngine->buildInitialModelData();
        $installDataResponseData = $this->buildInstallResponseData(
            $installRequiredTableData,
            'tr_news_app_db_req_data_install',
            'Error installing required data',
            'Successfully installed required data'
        );
        $installDataResponseData['data']['tr_news_app_db_req_data_install'] = $installRequiredTableData;
        return $installDataResponseData;
    }
    public function installRequiredTables() {
        $installRequiredTables = $this->dbEngine->installRequiredTables();
        return $this->buildInstallResponseData(
            $installRequiredTables,
            'tr_news_app_db_install',
            'Error installing tables',
            'Successfully installed tables'
        );
    }
    public function databaseInstallAction(?bool $isNetworkWide = false)
    {
        $this->setIsNetworkWide($isNetworkWide);
        $installTableResponseData = $this->installRequiredTables();
        if (!isset($installTableResponseData['success']) || !$installTableResponseData['success']) {
            error_log(1 . json_encode($installTableResponseData));
            wp_send_json_error($installTableResponseData);
            wp_die();
        }
        wp_send_json_success($installTableResponseData);
    }

    public function databaseNetworkRequiredDataInstallAction()
    {
        $this->setIsNetworkWide(true);
        $this->databaseRequiredDataInstallAction();
    }
    public function databaseRequiredDataInstallAction()
    {
        $models = null;
        if (!empty($_POST['models']) && is_array($_POST['models']) && count($_POST['models'])) {
            $models = $_POST['models'];
        }
        $installRequiredTableData = $this->dbEngine->buildInitialModelData($models);
        $responseData = $this->buildInstallResponseData(
            $installRequiredTableData,
            'tr_news_app_db_req_data_install',
            'Error installing required data',
            'Successfully installed required data'
        );

        if (!isset($responseData['success']) || !$responseData['success']) {
            wp_send_json_error($responseData);
            wp_die();
        }
        wp_send_json_success($responseData);
    }

    public function databaseNetworkMissingColumnsUpdateAction()
    {
        $this->setIsNetworkWide(true);
        $this->databaseMissingColumnsUpdateAction();
    }
    public function databaseMissingColumnsUpdateAction()
    {
        $installRequiredTableData = $this->dbEngine->updateAllTableColumns();
        $responseData = $this->buildInstallResponseData(
            $installRequiredTableData,
            'tr_news_app_db_update_columns',
            'Error updating columns',
            'Successfully updated columns'
        );

        if (!isset($responseData['success']) || !$responseData['success']) {
            wp_send_json_error($responseData);
            wp_die();
        }
        wp_send_json_success($responseData);
    }

    public function runHealthCheck() {
        $dbCheck = $this->dbHealthCheckHandler(function (string $type, $results) {
            return;
        });
//        $configCheck = Tru_Fetcher_Health_Check::firebaseConfigCheck();
        return $dbCheck;
    }
    public function dbUpdate() {
        if (is_wp_error($this->getMissingTables())) {
            $installRequiredTables = $this->dbEngine->installRequiredTables();
            if ($this->responseHasErrors($installRequiredTables)) {
                error_log(json_encode(['$installRequiredTables' => $installRequiredTables]));
                return false;
            }
        }
        if (is_wp_error($this->getMissingColumns())) {
            $updateTableColumns = $this->dbEngine->updateAllTableColumns();
            if ($this->responseHasErrors($updateTableColumns)) {
                error_log(json_encode(['$updateTableColumns' => $updateTableColumns]));
                return false;
            }

        }
        if (is_wp_error($this->getRequiredData())) {
            $installRequiredData = $this->dbEngine->buildInitialModelData();
//            error_log(json_encode(['$installRequiredData' => $installRequiredData]));
            if ($this->responseHasErrors($installRequiredData)) {
                error_log(json_encode(['$installRequiredDataErrs' => true]));
                return false;
            }

        }
        return true;
    }
    public function firebaseConfigCheck()
    {
        $config = Tru_Fetcher_Firebase::CONFIG_FILE;
        $configToJson = json_encode($config);
        if (!Tru_Fetcher_Helpers::getConfigContents("{$config}")) {
//            Tru_Fetcher_Admin::adminNotice(
//                'error',
//                "Error finding firebase credentials {$configToJson}"
//            );
            return false;
        }
        return true;
    }

    /**
     * @return \WP_Error|null
     */
    public function getMissingTables(): ?\WP_Error
    {
        return $this->missingTables;
    }

    /**
     * @param \WP_Error|null $missingTables
     */
    public function setMissingTables(?\WP_Error $missingTables): void
    {
        $this->missingTables = $missingTables;
    }

    /**
     * @return \WP_Error|null
     */
    public function getMissingColumns(): ?\WP_Error
    {
        return $this->missingColumns;
    }

    /**
     * @param \WP_Error|null $missingColumns
     */
    public function setMissingColumns(?\WP_Error $missingColumns): void
    {
        $this->missingColumns = $missingColumns;
    }

    /**
     * @return \WP_Error|null
     */
    public function getRequiredData(): ?\WP_Error
    {
        return $this->requiredData;
    }

    /**
     * @param \WP_Error|null $requiredData
     */
    public function setRequiredData(?\WP_Error $requiredData): void
    {
        $this->requiredData = $requiredData;
    }

    /**
     * @return bool
     */
    public function isNetworkWide(): bool
    {
        return $this->isNetworkWide;
    }

    /**
     * @param bool $isNetworkWide
     */
    public function setIsNetworkWide(bool $isNetworkWide): void
    {
        $this->dbEngine->setIsNetworkWide($isNetworkWide);
    }

}
