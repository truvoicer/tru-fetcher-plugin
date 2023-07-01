<?php

namespace TruFetcher\Includes\Taxonomy;

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
class Tru_Fetcher_Taxonomy {

    public const LISTINGS_CATEGORIES_TAXONOMY = 'listings_categories';
    use Tru_Fetcher_DB_Traits_WP_Site, Tru_Fetcher_Traits_Errors;
    public const ERROR_PREFIX = TRU_FETCHER_ERROR_PREFIX . '_taxonomy';

    protected string $name;
    private string $menuName;
    private string $menuAdminBar;

    protected array $argDefaults = [
        'hierarchical'               => false,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'show_in_rest'               => true,
    ];

    public function __construct()
    {
        require_once ABSPATH . 'wp-admin/includes' . '/taxonomy.php';
    }

    protected function registerTaxonomy(string $name, string $singularName, array $objectTypes = [], ?array $args = []): void
    {
        $args = $this->getArgs($name, $singularName, $args);

        add_action( 'init', function () use ($name, $args, $objectTypes) {
            register_taxonomy( $this->name, $objectTypes, $args );
        }, 0 );

    }
    protected function getArgs(string $name, string $singularName, ?array $args = []): array
    {
        $labels = $this->getLabels($name, $singularName);
        $args = array_merge($this->argDefaults, $args);
        $args['label'] = $labels['name'];
        $args['labels'] = $labels;
        return $args;
    }

    protected function getLabels(string $name, string $singularName): array
    {
        $labels = [
            'name'                  => _x( $name, $name, 'text_domain' ),
            'singular_name'         => _x( $singularName, $singularName, 'text_domain' ),
        ];
        $menuName = $name;
        $menuAdminBar = $name;
        if (!empty($this->menuName)) {
            $menuName = $this->menuName;
        }
        if (!empty($this->menuAdminBar)) {
            $menuAdminBar = $this->menuAdminBar;
        }
        $labels['menu_name'] = __( $menuName, 'text_domain' );
        $labels['name_admin_bar'] = __( $menuAdminBar, 'text_domain' );
        return $labels;
    }

    public static function getPostTypeData(string $postType)
    {
        return get_posts([
            'post_type' => $postType,
            'posts_per_page' => -1,
            'post_status' => 'any',
        ]);
    }

    public function getCategories() {
        return get_categories();
    }
    public function getTerms($taxonomy) {
        $getTerms = get_terms([
            'taxonomy' => $taxonomy,
            "hide_empty" => false
        ]);
        if (is_wp_error($getTerms)) {
            $this->addError($getTerms);
            return false;
        }
        return $getTerms;
    }

    public function doesTermExistsInArray(array $data, \WP_Term $WP_Term) {
        foreach ($data as $item) {
            if (!$item instanceof \WP_Term) {
                return new \WP_Error(
                    'tr_news_app_taxonomy_error',
                    'Invalid taxonomy in list',
                    $data
                );
            }
            if ($item->term_id === $WP_Term->term_id && $item->taxonomy === $WP_Term->term_taxonomy_id) {
                return true;
            }
        }
        return false;
    }

    public function getTermsRequestData(\WP_REST_Request $request)
    {
        return $request->get_param('terms');
    }

    public function buildTermItem(array $term) {
        if (!isset($term['name']) || $term['name'] === '') {
            return new \WP_Error(
                self::ERROR_PREFIX . '_term_name_invalid',
                'Term name is invalid',
                $term
            );
        }
        if (!isset($term['slug']) || $term['slug'] === '') {
            $term['slug'] = strtolower(
                str_replace(
                    [' '],
                    ['-'],
                    $term['name']
                )
            );
        } else {
            $term['slug'] = strtolower($term['slug']);
        }
        return $term;
    }

    public function getTermId(array $term) {
        if (!isset($term['term_id']) || $term['term_id'] === '') {
            return new \WP_Error(
                self::ERROR_PREFIX . '_term_name_invalid',
                'Term id is invalid',
                $term
            );
        }
        return $term['term_id'];
    }
    public function fetchTerm($termId, $taxonomy) {
        $term = get_term($termId, $taxonomy);
        if (!$term) {
            return new \WP_Error(
                self::ERROR_PREFIX . '_term_fetch',
                'Error fetching term',
                [
                    'taxonomy' => $taxonomy,
                    'termId' => $termId
                ]
            );
        } elseif (is_wp_error($term)) {
            return $term;
        }
        return $term;
    }
    public function saveTerms(array $terms, string $taxonomy) {
        $errors = [];
        foreach ($terms as $term) {
            if (!isset($term['state']) || $term['state'] === '') {
                $this->addError(
                    new \WP_Error(
                        self::ERROR_PREFIX . '_state_invalid',
                        'State is invalid'
                    )
                );
                $errors[] = true;
                continue;
            }
            $request = false;
            switch ($term['state']) {
                case 'create':
                    $request = $this->createTerm($taxonomy, $term);
                    break;
                case 'update':
                    $request = $this->updateTerm($taxonomy, $term);
                    break;
                case 'delete':
                    $request = $this->deleteTerm($taxonomy, $term);
                    break;
            }
            if (!$request) {
                $errors[] = true;
            }
        }
        return count($errors) === 0;
    }

    public function createTermFromRequest(\WP_REST_Request $request) {
        $term = [
            'name' => $request->get_param('name'),
            'slug' => $request->get_param('slug'),
        ];
        return $this->createTerm(
            $request->get_param('taxonomy'),
            $term
        );
    }
    public function createTerm($taxonomy, $term) {
        $buildTerm = $this->buildTermItem($term);
        if (is_wp_error($buildTerm)) {
            $this->addError($buildTerm);
            return false;
        }
        $insert = \wp_insert_term($buildTerm['name'], $taxonomy, $buildTerm);
        if (is_wp_error($insert)) {
            $this->addError($insert);
            return false;
        }
        return true;
    }

    public function updateTermFromRequest(\WP_REST_Request $request) {
        return $this->updateTerm(
            $request->get_param('taxonomy'),
            $request->get_params()
        );
    }
    public function updateTerm($taxonomy, $term) {
        $buildTerm = $this->buildTermItem($term);
        if (is_wp_error($buildTerm)) {
            $this->addError($buildTerm);
            return false;
        }
        $termId = $this->getTermId($term);
        if (is_wp_error($termId)) {
            $this->addError($termId);
            return false;
        }
        $term = $this->fetchTerm($termId, $taxonomy);
        if (is_wp_error($term)) {
            $this->addError($term);
            return false;
        }
        $update = wp_update_term($term->term_id, $taxonomy, $buildTerm);
        if (is_wp_error($update)) {
            $this->addError($update);
            return false;
        }
        return true;
    }

    public function deleteTermBatch($taxonomy, $terms) {
        $errors = [];
        foreach ($terms as $term) {
            if (!$this->deleteTerm($taxonomy, $term)) {
                $errors[] = true;
            }
        }
        return count($errors) === 0;
    }
    public function deleteTerm($taxonomy, $term) {
        $termId = $this->getTermId($term);
        if (is_wp_error($termId)) {
            $this->addError($termId);
            return false;
        }
        $term = $this->fetchTerm($termId, $taxonomy);
        if (is_wp_error($term)) {
            $this->addError($term);
            return false;
        }
        $delete = wp_delete_term($term->term_id, $taxonomy);
        if (is_wp_error($delete)) {
            $this->addError($delete);
            return false;
        }
        return true;
    }

    /**
     * @param string $menuName
     */
    public function setMenuName(string $menuName): void
    {
        $this->menuName = $menuName;
    }

    /**
     * @param string $menuAdminBar
     */
    public function setMenuAdminBar(string $menuAdminBar): void
    {
        $this->menuAdminBar = $menuAdminBar;
    }

}
