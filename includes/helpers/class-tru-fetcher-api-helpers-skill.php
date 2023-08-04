<?php

namespace TruFetcher\Includes\Helpers;

use TruFetcher\Includes\DB\Engine\Tru_Fetcher_DB_Engine;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Form_Presets;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Settings;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Skill;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_User_Skill;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Form_Presets;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Settings;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Skill;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_User_Skill;
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
class Tru_Fetcher_Api_Helpers_Skill {

    use Tru_Fetcher_DB_Traits_WP_Site, Tru_Fetcher_Traits_Errors;
    public const ERROR_PREFIX = TRU_FETCHER_ERROR_PREFIX . '_skills';

    private Tru_Fetcher_DB_Engine $db;
    protected Tru_Fetcher_DB_Model_Skill $skillModel;
    protected Tru_Fetcher_DB_Model_User_Skill $userSkillModel;

    private Tru_Fetcher_DB_Repository_Skill $skillRepository;
    private Tru_Fetcher_DB_Repository_User_Skill $userSkillRepository;

    public function __construct()
    {
        $this->skillModel = new Tru_Fetcher_DB_Model_Skill();
        $this->userSkillModel = new Tru_Fetcher_DB_Model_User_Skill();
        $this->skillRepository = new Tru_Fetcher_DB_Repository_Skill();
        $this->userSkillRepository = new Tru_Fetcher_DB_Repository_User_Skill();
        $this->db = new Tru_Fetcher_DB_Engine();
    }

    public function getSkill(string $name) {
        $skill = $this->skillRepository->findSkillByName($name);
        if (!$skill) {
            return false;
        }
        return $skill;
    }

    public function findUserSkillByLabel(\WP_User $user, string $label) {
        return $this->userSkillRepository->findUserSkillByLabel($user, $label);
    }
    public function createUserSkillByLabel(\WP_User $user, string $label)
    {
        $saveSkill = $this->createSkillByLabel($label);
        if (!$saveSkill) {
            return false;
        }
        $insertUserSkill = $this->createUserSkill($user, $saveSkill[$this->skillModel->getIdColumn()]);
        if (!$insertUserSkill) {
            return false;
        }
        return $saveSkill;
    }
    public function createUserSkill(\WP_User $user, int $skillId)
    {
        $insertUserSkill = $this->userSkillRepository->insertUserSkills($user, $skillId);
        if (!$insertUserSkill) {
            $this->addError(
                new \WP_Error(
                    self::ERROR_PREFIX . '_create_user_skill',
                    'Failed to create user skill',
                    [
                        'skillId' => $skillId,
                    ]
                )
            );
            return false;
        }
        return $insertUserSkill;
    }
    public function createSkillByLabel(string $label)
    {
        $skillName = strtolower(str_replace(" ", "_", $label));
        $saveSkill = $this->skillRepository->insertSkills(
            [
                'name' => $skillName,
                'label' => $label
            ]
        );
        if (!$saveSkill) {
            $this->addError(
                new \WP_Error(
                    self::ERROR_PREFIX . '_create_skill',
                    'Failed to create skill',
                    [
                        'label' => $label,
                        'name' => $skillName
                    ]
                )
            );
            return false;
        }
        if (empty($saveSkill[$this->skillModel->getIdColumn()])) {
            $this->addError(
                new \WP_Error(
                    self::ERROR_PREFIX . '_create_skill',
                    'Failed to create skill | invalid id',
                    [
                        'label' => $label,
                        'name' => $skillName
                    ]
                )
            );
            return false;
        }
        return $saveSkill;
    }

    public function deleteSkill(array $data)
    {
        if (empty($data[$this->skillModel->getIdColumn()])) {
            $this->addError(new \WP_Error('missing_id', 'Missing id'));
            return false;
        }
        return $this->skillRepository->deleteById($data[$this->skillModel->getIdColumn()]);
    }
    public function deleteUserSkill(array $data)
    {
        if (empty($data[$this->userSkillModel->getIdColumn()])) {
            $this->addError(new \WP_Error('missing_id', 'Missing id'));
            return false;
        }
        return $this->userSkillRepository->deleteById($data[$this->userSkillModel->getIdColumn()]);
    }

    public function updateUserProfileSkillsBatch(\WP_User $user, array $skillsArray = [])
    {
        $errors = [];
        foreach ($skillsArray as $skill) {
            $findUserSkill = $this->findUserSkillByLabel($user, $skill["label"]);
            if ($findUserSkill) {
                if (!$this->deleteUserSkill($findUserSkill)) {
                    $errors[] = true;
                    continue;
                }
            }
            $getSkill = $this->skillRepository->findSkillByNameOrLabel(
                strtolower(str_replace(" ", "_", $skill["value"])),
                $skill["label"]
            );

            if (!$getSkill) {
                $saveSkill = $this->createSkillByLabel($skill["label"]);
                if (!$saveSkill) {
                    $errors[] = true;
                    continue;
                }
                $getSkill = $saveSkill;
            }
            $saveSkill = $this->createUserSkill($user, $getSkill[$this->skillModel->getIdColumn()]);
            if (!$saveSkill) {
                $errors[] = true;
            }
        }
        return count($errors) === 0;
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



}
