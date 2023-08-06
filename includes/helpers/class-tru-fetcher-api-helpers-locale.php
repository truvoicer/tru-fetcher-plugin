<?php

namespace TruFetcher\Includes\Helpers;

use TruFetcher\Includes\DB\Engine\Tru_Fetcher_DB_Engine;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Locale;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Locale;
use TruFetcher\Includes\DB\Traits\WP\Tru_Fetcher_DB_Traits_WP_Site;
use TruFetcher\Includes\Traits\Tru_Fetcher_Traits_Errors;

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
class Tru_Fetcher_Api_Helpers_Locale
{

    use Tru_Fetcher_DB_Traits_WP_Site, Tru_Fetcher_Traits_Errors;

    public const ERROR_PREFIX = TRU_FETCHER_ERROR_PREFIX . '_locales';

    private Tru_Fetcher_DB_Engine $db;
    protected Tru_Fetcher_DB_Model_Locale $localeModel;

    private Tru_Fetcher_DB_Repository_Locale $localeRepository;

    public function __construct()
    {
        $this->localeModel = new Tru_Fetcher_DB_Model_Locale();
        $this->localeRepository = new Tru_Fetcher_DB_Repository_Locale();
        $this->db = new Tru_Fetcher_DB_Engine();
    }

    public function getSkill(string $name)
    {
        $skill = $this->localeRepository->findSkillByName($name);
        if (!$skill) {
            return false;
        }
        return $skill;
    }

    public function findLocales()
    {
        return $this->localeRepository->findLocales();
    }

    public function createLocale(array $data)
    {
        $saveLocale = $this->localeRepository->insertLocale($data);
        if ($this->localeRepository->hasErrors()) {
            $this->mergeErrors([$this->localeRepository->getErrors()]);
            return false;
        }
        return $saveLocale;
    }

    public function updateLocale(array $data)
    {
        if (empty($data[$this->localeModel->getIdColumn()])) {
            $this->addError(new \WP_Error('missing_id', 'Missing id'));
            return false;
        }
        $saveLocale = $this->localeRepository->updateLocale(
            (int)$data[$this->localeModel->getIdColumn()],
            $data
        );
        if ($this->localeRepository->hasErrors()) {
            $this->mergeErrors([$this->localeRepository->getErrors()]);
            return false;
        }
        return $saveLocale;
    }

    public function deleteLocale(array $data)
    {
        if (empty($data[$this->localeModel->getIdColumn()])) {
            $this->addError(new \WP_Error('missing_id', 'Missing id'));
            return false;
        }

        $delete = $this->localeRepository->deleteById($data[$this->localeModel->getIdColumn()]);
        if ($this->localeRepository->hasErrors()) {
            $this->mergeErrors([$this->localeRepository->getErrors()]);
            return false;
        }
        return $delete;
    }

    /**
     * @return Tru_Fetcher_DB_Engine
     */
    public function getDb(): Tru_Fetcher_DB_Engine
    {
        return $this->db;
    }

    /**
     * @param Tru_Fetcher_DB_Engine $db
     */
    public function setDb(Tru_Fetcher_DB_Engine $db): void
    {
        $this->db = $db;
    }

    public function setSite(?\WP_Site $site): void
    {
        $this->site = $site;
        $this->db->setSite($site);
    }

    /**
     * @return Tru_Fetcher_DB_Model_Locale
     */
    public function getLocaleModel(): Tru_Fetcher_DB_Model_Locale
    {
        return $this->localeModel;
    }

    /**
     * @return Tru_Fetcher_DB_Repository_Locale
     */
    public function getLocaleRepository(): Tru_Fetcher_DB_Repository_Locale
    {
        return $this->localeRepository;
    }

}
