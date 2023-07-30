<?php

namespace TruFetcher\Includes\Forms\ProgressGroups;

class Tru_Fetcher_Progress_Field_Groups
{
    public array $personal_group;
    public array $experiences_group;
    public array $education_group;
    public array $skills_group;
    public array $cv_group;

    /**
     * Tfr_Profile_Field_Group constructor.
     */
    public function __construct()
    {
        $this->setPersonalGroup();
        $this->setCvGroup();
        $this->setEducationGroup();
        $this->setExperiencesGroup();
        $this->setSkillsGroup();
    }


    /**
     */
    public function setPersonalGroup(): void
    {
        $this->personal_group = [
            [
                "name" => "profile_picture",
                "type" => "file",
                "label" => "Profile Picture",
                "incomplete_text" => "Add a photo to your profile."
            ],
            [
                "name" => "display_name",
                "label" => "Display Name",
                "incomplete_text" => "Add a display name to your profile."
            ],
            [
                "name" => "first_name",
                "label" => "First Name",
                "incomplete_text" => "Add your first name to your profile."
            ],
            [
                "name" => "surname",
                "label" => "Surname",
                "incomplete_text" => "Add your surname to your profile."
            ],
            [
                "name" => "telephone",
                "label" => "Contact Number",
                "incomplete_text" => "Add your contact number to your profile."
            ],
            [
                "name" => "town",
                "label" => "Town/City/Region",
                "incomplete_text" => "Add your town/city to your profile."
            ],
            [
                "name" => "short_description",
                "label" => "Short Description",
                "incomplete_text" => "Add a short description about yourself."
            ],
            [
                "name" => "personal_statement",
                "label" => "Personal Statement",
                "incomplete_text" => "Add a personal statement to your profile."
            ],
            [
                "name" => "country",
                "label" => "Country",
                "incomplete_text" => "Add your country to your profile."
            ],
        ];
    }

    /**
     */
    public function setExperiencesGroup(): void
    {
        $this->experiences_group = [
            [
                "name" => "form_experiences",
                "label" => "Work Experiences",
                "incomplete_text" => "Add some of your previous work experiences to your profile."
            ],
        ];
    }

    /**
     */
    public function setEducationGroup(): void
    {
        $this->education_group = [
            [
                "name" => "form_education",
                "label" => "Education",
                "incomplete_text" => "Add some of your education history to your profile."
            ],
        ];
    }

    /**
     */
    public function setSkillsGroup(): void
    {
        $this->skills_group = [
            [
                "type" => "data_source",
                "name" => "skills",
                "label" => "Skills",
                "incomplete_text" => "Add some skills to your profile."
            ]
        ];
    }

    /**
     */
    public function setCvGroup(): void
    {
        $this->cv_group = [
            [
                "type" => "file",
                "name" => "cv_file",
                "label" => "CV/Resume",
                "incomplete_text" => "Add your cv/resume to your profile."
            ],
        ];
    }


}
