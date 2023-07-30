<?php
namespace TruFetcher\Includes\Forms;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Skill;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_User_Skill;
use TruFetcher\Includes\Forms\ProgressGroups\Tru_Fetcher_Progress_Field_Groups;

class Tru_Fetcher_Forms_Helpers
{

    private Tru_Fetcher_Api_Form_Handler $apiFormHandler;
    private Tru_Fetcher_DB_Repository_Skill $skillsRepository;
    private Tru_Fetcher_DB_Repository_User_Skill $userSkillsRepository;

    public function __construct()
    {
        $this->skillsRepository = new Tru_Fetcher_DB_Repository_Skill();
        $this->userSkillsRepository = new Tru_Fetcher_DB_Repository_User_Skill();
        $this->apiFormHandler = new Tru_Fetcher_Api_Form_Handler();
    }

    public function init() {
        add_filter("tfr_form_progress_field_groups", [$this, "getFormFieldGroupsArray"], 10, 2);
        add_filter("tfr_data_source_data", [$this, "getDataSourceData"], 10, 2);
        add_filter("tfr_user_meta_select_data_source", [$this, "filterUserMetaSelectData"], 10, 2);
    }

    public function getFormFieldGroupsArray($formFieldGroups, \WP_User $user) {
        $progressGroupsObject = new Tru_Fetcher_Progress_Field_Groups();
        return (array) $progressGroupsObject;
    }

    public function getDataSourceData($field, \WP_User $user) {
        switch ($field["name"]) {
            case "skills":
                return $this->userSkillsRepository->findUserSkillsByUser($user);
        }
        return [];
    }

    public function filterUserMetaSelectData($field, \WP_User $user) {
        switch ($field["name"]) {
            case "skills":
                return $this->apiFormHandler->buildSelectList(
                    "name",
                    "label",
                    $this->userSkillsRepository->findUserSkillsByUser($user)
                );
        }
        return [];
    }
}
