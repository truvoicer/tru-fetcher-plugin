<?php

namespace TruFetcher\Includes\Forms\ProgressGroups;

class Tru_Fetcher_Progress_Field_Groups
{
    public array $profile;

    /**
     * Tfr_Profile_Field_Group constructor.
     */
    public function __construct()
    {
        $this->setProfile();
    }

    /**
     */
    public function setProfile(): void
    {
        $this->profile = [
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
    public function getFieldGroups(array $fieldNames): array
    {
        $fieldGroups = [];
        foreach ($fieldNames as $fieldName) {
            $fieldGroups[$fieldName] = $this->$fieldName;
        }
        return $fieldGroups;
    }
}
