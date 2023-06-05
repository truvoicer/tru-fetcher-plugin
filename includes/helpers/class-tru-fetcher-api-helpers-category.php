<?php

namespace TruFetcher\Includes\Helpers;




use TruFetcher\Includes\DB\Traits\WP\Tru_Fetcher_DB_Traits_WP_Site;

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
class Tru_Fetcher_Api_Helpers_Category
{
    use Tru_Fetcher_DB_Traits_WP_Site;
    public const TR_NEWS_APP_CATEGORY_TAXONOMY = 'tr_news_app_categories';
    public const SUPPORTED_TAXONOMIES = [
        self::TR_NEWS_APP_CATEGORY_TAXONOMY
    ];

    private Tru_Fetcher_DB_Model_Category_Options $categoryOptionsModel;
    private Tru_Fetcher_DB_Repository_Category_Options $categoryOptionsRepo;

    public function __construct()
    {
        $this->categoryOptionsModel = new Tru_Fetcher_DB_Model_Category_Options();
        $this->categoryOptionsRepo = new Tru_Fetcher_DB_Repository_Category_Options();
    }

    public static function isTaxonomySupported(string $taxonomy)
    {
        return in_array($taxonomy, self::SUPPORTED_TAXONOMIES);
    }

    public static function getCategoryRequestData(\WP_REST_Request $request)
    {
        return $request->get_param('categoryOptions');
    }


    public static function getNewsAppCategoryTerm($key, $termValue)
    {
        return get_term_by($key, $termValue, self::TR_NEWS_APP_CATEGORY_TAXONOMY);
    }

    public static function getTermKeyFromCategoryOptionsData(array $categoryOptionsData)
    {
        if (!isset($categoryOptionsData['term_name']) && !isset($categoryOptionsData['term_id'])) {
            return new \WP_Error(
                Tru_Fetcher_Api_Admin_Category_Options_Response::API_RESPONSE_ERROR_CODE_PREFIX . '_data_error',
                'term_name or term_id does not exist in data'
            );
        }
        if (isset($categoryOptionsData['term_id'])) {
            return 'term_id';
        } else if (isset($categoryOptionsData['term_name'])) {
            return 'slug';
        }
        return new \WP_Error(
            Tru_Fetcher_Api_Admin_Category_Options_Response::API_RESPONSE_ERROR_CODE_PREFIX . '_data_error',
            'Error getting term key from category options data'
        );
    }

    public static function getTermFromCategoryOptionsData(string $key, array $categoryOptionsData)
    {
        $termValue = false;
        if (
            $key === 'slug' &&
            (!isset($categoryOptionsData['term_name']) || $categoryOptionsData['term_name'] !== '')
        ) {
            $termValue = $categoryOptionsData['term_name'];
        } elseif (
            $key === 'term_id' &&
            (!isset($categoryOptionsData['term_id']) || $categoryOptionsData['term_id'] !== '')
        ) {
            $termValue = $categoryOptionsData['term_id'];
        }
        if (!$termValue) {
            return new \WP_Error(
                Tru_Fetcher_Api_Admin_Category_Options_Response::API_RESPONSE_ERROR_CODE_PREFIX . '_term_error',
                'Term value is invalid'
            );
        }
        return self::getNewsAppCategoryTerm($key, $termValue);
    }

    public function createCategoryOptions(\WP_REST_Request $request)
    {
        return $this->categoryOptionsRepo->createNewsAppCategoryOptions(
            $request->get_params()
        );
    }

    public function updateCategoryOptions(\WP_REST_Request $request) {
        return $this->categoryOptionsRepo->updateNewsAppCategoryOptions($request->get_params());
    }

    public function deleteCategoryOptions(\WP_REST_Request $request) {
        $data = self::getCategoryRequestData($request);
        return $this->categoryOptionsRepo->deleteNewsAppCategoryOptions($data);
    }

    public function setSite(?\WP_Site $site): void
    {
        $this->site = $site;
        $this->db->setSite($site);
    }

    /**
     * @return Tru_Fetcher_DB_Repository_Category_Options
     */
    public function getCategoryOptionsRepo(): Tru_Fetcher_DB_Repository_Category_Options
    {
        return $this->categoryOptionsRepo;
    }

}
