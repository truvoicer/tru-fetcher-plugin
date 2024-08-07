<?php
namespace TruFetcher\Includes\Forms;
use TruFetcher\Includes\Forms\ProgressGroups\Tru_Fetcher_Progress_Field_Groups;
use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Locale;
use TruFetcher\Includes\Tru_Fetcher_Filters;

class Tru_Fetcher_Forms_Helpers
{

    private Tru_Fetcher_Api_Form_Handler $apiFormHandler;
    private Tru_Fetcher_Api_Helpers_Locale $localeHelpers;

    public function __construct()
    {
        $this->apiFormHandler = new Tru_Fetcher_Api_Form_Handler();
        $this->localeHelpers = new Tru_Fetcher_Api_Helpers_Locale();
    }

    public function init() {
        add_filter(Tru_Fetcher_Filters::TRU_FETCHER_FILTER_FORM_PROGRESS_FIELD_GROUPS, [$this, "getFormFieldGroupsArray"], 10, 2);
        add_filter(Tru_Fetcher_Filters::TRU_FETCHER_FILTER_DATA_SOURCE_DATA, [$this, "getDataSourceData"], 10, 2);
        add_filter(Tru_Fetcher_Filters::TRU_FETCHER_FILTER_USER_META_SELECT_DATA_SOURCE, [$this, "filterUserMetaSelectData"], 10, 2);
    }

    public function getFormFieldGroupsArray($formFieldGroups, \WP_User $user) {
        $progressGroupsObject = new Tru_Fetcher_Progress_Field_Groups();
        return (array) $progressGroupsObject;
    }

    public function getDataSourceData($field, \WP_User $user) {
//        switch ($field["name"]) {
//            case "skills":
//                return $this->userSkillsRepository->findUserSkillsByUser($user);
//        }
        return [];
    }

    public function filterUserMetaSelectData($field, \WP_User $user) {
        switch ($field["name"]) {
            case "skills":
            case "skill":
                return $this->apiFormHandler->buildSelectList(
                    $this->userSkillModel->getAlias(),
                    'id',
                    "name",
                    "label",
                    $this->userSkillsRepository->findUserSkillsByUser($user)
                );
            case "country":
                return get_user_meta($user->ID, 'country', true);
        }
        return [];
    }

    public function syncUserEntityData(\WP_User $user, \WP_REST_Request $request) {

        $entityRequestData = apply_filters(Tru_Fetcher_Filters::TRU_FETCHER_FILTER_USER_ENTITY_REQUEST_DATA, $request->get_params(), $user);
        $entityRequestData = apply_filters(Tru_Fetcher_Filters::TRU_FETCHER_FILTER_USER_ENTITY_FIND, $request->get_params(), $user);

//        array_filter($skillsArray, function ($skill) {
//            return !empty($skill['entity']) && $skill['entity'] === $this->skillModel->getAlias();
//        });
//        $updateUserSkills = $this->syncUserSkills($user, );
    }
}
