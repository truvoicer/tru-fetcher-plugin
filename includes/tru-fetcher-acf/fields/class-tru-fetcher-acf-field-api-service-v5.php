<?php

// exit if accessed directly
if (!defined('ABSPATH')) exit;


// check if class already exists
if (!class_exists('Tru_Fetcher_Acf_Field_Api_Service')) :

    class Tru_Fetcher_Acf_Field_Api_Service extends Tru_Fetcher_Acf_Field_Base
    {

        function __construct($settings)
        {

            /*
            *  name (string) Single word, no spaces. Underscores allowed
            */

            $this->name = 'API_SERVICE';


            /*
            *  label (string) Multiple words, can include spaces, visible when selecting a field type
            */

            $this->label = __('Api Service', 'TEXTDOMAIN');


            /*
            *  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
            */

            $this->category = 'Fetcher Api';


            /*
            *  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
            */

            $this->defaults = array(
                'fetcher_api_service' => "",
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
        *  render_field_settings()
        *
        *  Create extra settings for your field. These are visible when editing a field
        *
        *  @type	action
        *  @since	3.6
        *  @date	23/01/13
        *
        *  @param	$field (array) the $field being edited
        *  @return	n/a
        */

        function render_field_settings($field)
        {

            /*
            *  acf_render_field_setting
            *
            *  This function will create a setting for your field. Simply pass the $field parameter and an array of field settings.
            *  The array of settings does not require a `value` or `prefix`; These settings are found from the $field array.
            *
            *  More than one setting can be added by copy/paste the above code.
            *  Please note that you must also have a matching $defaults value for the field name (font_size)
            */

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

            $servicesList = $this->fetcherApi->getApiDataList("serviceList");
            if (is_wp_error($servicesList)) {
                return false;
            }

            // convert
            $value = acf_get_array($field['value']);
            $choices = acf_get_array($this->buildSelectList(
                "id",
                "service_label",
                $servicesList));


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

    new Tru_Fetcher_Acf_Field_Api_Service($this->settings);

endif;

?>