<?php

// exit if accessed directly
use TruFetcher\Includes\Tru_Fetcher;

if (!defined('ABSPATH')) exit;


// check if class already exists
if (!class_exists('Tru_Fetcher_Acf_Field_Api_Data_Keys')) :


    class Tru_Fetcher_Acf_Field_Api_Data_Keys extends Tru_Fetcher_Acf_Field_Base
    {

        /*
        *  __construct
        *
        *  This function will setup the field type data
        *
        *  @type	function
        *  @date	5/03/2014
        *  @since	5.0.0
        *
        *  @param	n/a
        *  @return	n/a
        */

        function __construct($settings)
        {
            /*
            *  name (string) Single word, no spaces. Underscores allowed
            */

            $this->name = 'API_DATA_KEYS';


            /*
            *  label (string) Multiple words, can include spaces, visible when selecting a field type
            */

            $this->label = __('Api Data Keys', 'TEXTDOMAIN');


            /*
            *  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
            */

            $this->category = 'Fetcher Api';


            /*
            *  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
            */

            $this->defaults = array(
                'font_size' => 14,
            );


            /*
            *  l10n (array) Array of strings that are used in JavaScript. This allows JS strings to be translated in PHP and loaded via:
            *  var message = acf._e('FIELD_NAME', 'error');
            */

            $this->l10n = array(
                'error' => __('Error! Please enter a higher value', 'TEXTDOMAIN'),
            );

            // do not delete!
            parent::__construct($settings);

        }


        /*
        *  render_field()
        *
        *  Create the HTML interface for your field
        *
        *  @param	$field (array) the $field being rendered
        *
        *  @type	action
        *  @since	3.6
        *  @date	23/01/13
        *
        *  @param	$field (array) the $field being edited
        *  @return	n/a
        */

        function render_field($field)
        {
            global $post;
            $getPostListingCategories = get_the_terms($post, "listings_categories");
            $requestData = [
                "count" => 1000,
                "order" => "asc",
                "sort" => "key_name",
            ];
            if (is_array($getPostListingCategories) && count($getPostListingCategories) > 0) {
                $requestData["service_name"] = str_replace("-", "_", $getPostListingCategories[0]->slug);
            }
            else if (isset(Tru_Fetcher::getTruFetcherSettings()["default_api_service"])) {
                $requestData["service_id"] = Tru_Fetcher::getTruFetcherSettings()["default_api_service"];
            } else {
                return false;
            }

            $responseKeys = $this->fetcherApi->getApiDataList(
                "serviceResponseKeyList",
                [],
                $requestData
            );
            if (is_wp_error($responseKeys)) {
                return false;
            }

            // convert
            $value = acf_get_array($field['value']);
            $choices = acf_get_array($this->buildSelectList(
                "key_value",
                "key_value",
                $responseKeys));


            // placeholder
            if( empty($field['placeholder']) ) {
                $field['placeholder'] = _x('Select', 'verb', 'acf');
            }


            // add empty value (allows '' to be selected)
            if( empty($value) ) {
                $value = array('');
            }



            // vars
            $select = array(
                'id'				=> $field['id'],
                'class'				=> $field['class'],
                'name'				=> $field['name'],
            );


            // append
            $select['value'] = $value;
            $select['choices'] = $choices;


            // render
            acf_select_input( $select );
        }

    }

// initialize
    new Tru_Fetcher_Acf_Field_Api_Data_Keys($this->settings);

// class_exists check
endif;

?>
