<?php

namespace TruFetcher\Includes\DB\Repository;

use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Api_Tokens;

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
class Tru_Fetcher_DB_Repository_Api_Tokens extends Tru_Fetcher_DB_Repository_Base {

    private Tru_Fetcher_DB_Model_Api_Tokens $apiTokensModel;
    public function __construct()
    {
        $this->apiTokensModel = new Tru_Fetcher_DB_Model_Api_Tokens();
        parent::__construct($this->apiTokensModel);
    }

    public function getUserToken(string $type, \WP_User $user)
    {
        $this->where = [
            [
                'column' => $this->apiTokensModel->getUserIdColumn(),
                'value' => $user->ID,
                'compare' => '=',
            ],
            [
                'operator' => 'AND',
                'column' => $this->apiTokensModel->getTypeColumn(),
                'value' => $type,
                'compare' => '=',
            ]
        ];
        $this->orderBy = [$this->apiTokensModel->getIssuedAtColumn()];
        $this->orderByDir = 'DESC';
        return $this->findOne();
    }

    public function buildInsertData(\WP_User $user, string $type, string $token, string $issuedAt, string $expiresAt) {
        $data = [
            $this->apiTokensModel->getUserIdColumn() => $user->ID,
            $this->apiTokensModel->getTypeColumn() => $type,
            $this->apiTokensModel->getTokenColumn() => $token,
            $this->apiTokensModel->getIssuedAtColumn() => $issuedAt,
            $this->apiTokensModel->getExpiresAtColumn() => $expiresAt,
        ];
        return $data;
    }
    public function buildUpdateData(int $id, string $expiresAt) {
        $data = [
            'id' => $id,
            'expires_at' => $expiresAt,
        ];
        return $data;
    }
    public function insertApiToken(\WP_User $user, string $type, string $token, string $issuedAt, string $expiresAt)
    {
        $buildInsertData = $this->buildInsertData($user, $type, $token, $issuedAt, $expiresAt);
        if (!$buildInsertData) {
            return false;
        }
        return $this->insert($buildInsertData);
    }

    public function updateApiToken(int $id, string $expiresAt)
    {
        $buildUpdateData = $this->buildUpdateData($id, $expiresAt);
        if (!$buildUpdateData) {
            return false;
        }
        return $this->update($buildUpdateData);
    }

    public function deleteApiTokensByUserId(int $userId)
    {
        $this->where = [
            [
                'column' => $this->apiTokensModel->getUserIdColumn(),
                'value' => $userId,
                'compare' => '=',
            ]
        ];
        return $this->delete();
    }

    public function deleteApiTokens(array $data)
    {
        $this->setWhereQueryConditions($this->defaultWhereConditions());
        return $this->deleteMany($data);
    }

    /**
     * @return Tru_Fetcher_DB_Model_Api_Tokens
     */
    public function getApiTokensModel(): Tru_Fetcher_DB_Model_Api_Tokens
    {
        return $this->apiTokensModel;
    }

}
