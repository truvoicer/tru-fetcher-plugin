<?php

namespace TruFetcher\Includes\Widgets;


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
class Tru_Fetcher_Widgets
{
    private \WP_Widget $widget;

    public function setWidget(\WP_Widget $widget): void
    {
        $this->widget = $widget;
    }


    public function renderWidgetTextInput(string $field, string $label, $instance)
    {
        $fieldId = $this->widget->get_field_id($field);
        $fieldName = $this->widget->get_field_name($field);
        $fieldValue = !empty($instance[$field]) ? esc_attr($instance[$field]) : '';
        ?>
        <p>
            <label for="<?php echo $fieldId; ?>"><?php _e($label); ?></label>
            <input class="widefat" id="<?php echo $fieldId; ?>" name="<?php echo $fieldName; ?>" type="text" value="<?php echo $fieldValue; ?>">
        </p>
        <?php
    }

    public function renderWidgetTextareaInput(string $field, string $label, $instance)
    {
        $fieldId = $this->widget->get_field_id($field);
        $fieldName = $this->widget->get_field_name($field);
        $fieldValue = !empty($instance[$field]) ? esc_attr($instance[$field]) : '';
        ?>
        <p>
            <label for="<?php echo $fieldId; ?>"><?php _e($label); ?></label>
            <textarea class="widefat" id="<?php echo $fieldId; ?>" name="<?php echo $fieldName; ?>"><?php echo $fieldValue; ?></textarea>
        </p>
        <?php
    }

    public function renderWidgetCheckboxInput(string $field, string $label, $instance)
    {
        $fieldId = $this->widget->get_field_id($field);
        $fieldName = $this->widget->get_field_name($field);
        $fieldValue = !empty($instance[$field]) ? esc_attr($instance[$field]) : '';
        ?>
        <p>
            <input class="checkbox" type="checkbox" id="<?php echo $fieldId; ?>" name="<?php echo $fieldName; ?>" <?php checked($fieldValue, 'on'); ?>>
            <label for="<?php echo $fieldId; ?>"><?php _e($label); ?></label>
        </p>
        <?php
    }

    public function renderWidgetSelectInput(string $field, string $label, $instance, $options)
    {
        $fieldId = $this->widget->get_field_id($field);
        $fieldName = $this->widget->get_field_name($field);
        $fieldValue = !empty($instance[$field]) ? esc_attr($instance[$field]) : '';
        ?>
        <p>
            <label for="<?php echo $fieldId; ?>"><?php _e($label); ?></label>
            <select class="widefat" id="<?php echo $fieldId; ?>" name="<?php echo $fieldName; ?>">
                <?php foreach ($options as $key => $value) : ?>
                    <option value="<?php echo $key; ?>" <?php selected($fieldValue, $key); ?>><?php echo $value; ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php
    }

    public function renderWidgetImageInput(string $field, string $label, $instance)
    {
        $fieldId = $this->widget->get_field_id($field);
        $fieldName = $this->widget->get_field_name($field);
        $fieldValue = !empty($instance[$field]) ? esc_attr($instance[$field]) : '';
        ?>
        <p>
            <label for="<?php echo $fieldId; ?>"><?php _e($label); ?></label>
            <input class="widefat" id="<?php echo $fieldId; ?>" name="<?php echo $fieldName; ?>" type="text" value="<?php echo $fieldValue; ?>">
            <button class="button button-primary js-image-upload">Upload Image</button>
        </p>
        <?php
    }

    public function renderWidgetColorInput(string $field, string $label, $instance)
    {
        $fieldId = $this->widget->get_field_id($field);
        $fieldName = $this->widget->get_field_name($field);
        $fieldValue = !empty($instance[$field]) ? esc_attr($instance[$field]) : '';
        ?>
        <p>
            <label for="<?php echo $fieldId; ?>"><?php _e($label); ?></label>
            <input class="widefat js-color-picker" id="<?php echo $fieldId; ?>" name="<?php echo $fieldName; ?>" type="text" value="<?php echo $fieldValue; ?>">
        </p>
        <?php
    }

    public function renderWidgetNumberInput(string $field, string $label, $instance)
    {
        $fieldId = $this->widget->get_field_id($field);
        $fieldName = $this->widget->get_field_name($field);
        $fieldValue = !empty($instance[$field]) ? esc_attr($instance[$field]) : '';
        ?>
        <p>
            <label for="<?php echo $fieldId; ?>"><?php _e($label); ?></label>
            <input class="widefat" id="<?php echo $fieldId; ?>" name="<?php echo $fieldName; ?>" type="number" value="<?php echo $fieldValue; ?>">
        </p>
        <?php
    }

    public function renderWidgetUrlInput(string $field, string $label, $instance)
    {
        $fieldId = $this->widget->get_field_id($field);
        $fieldName = $this->widget->get_field_name($field);
        $fieldValue = !empty($instance[$field]) ? esc_attr($instance[$field]) : '';
        ?>
        <p>
            <label for="<?php echo $fieldId; ?>"><?php _e($label); ?></label>
            <input class="widefat" id="<?php echo $fieldId; ?>" name="<?php echo $fieldName; ?>" type="url" value="<?php echo $fieldValue; ?>">
        </p>
        <?php
    }

public function renderWidgetEmailInput(string $field, string $label, $instance)
    {
        $fieldId = $this->widget->get_field_id($field);
        $fieldName = $this->widget->get_field_name($field);
        $fieldValue = !empty($instance[$field]) ? esc_attr($instance[$field]) : '';
        ?>
        <p>
            <label for="<?php echo $fieldId; ?>"><?php _e($label); ?></label>
            <input class="widefat" id="<?php echo $fieldId; ?>" name="<?php echo $fieldName; ?>" type="email" value="<?php echo $fieldValue; ?>">
        </p>
        <?php
    }

    public function renderWidgetDateInput(string $field, string $label, $instance)
    {
        $fieldId = $this->widget->get_field_id($field);
        $fieldName = $this->widget->get_field_name($field);
        $fieldValue = !empty($instance[$field]) ? esc_attr($instance[$field]) : '';
        ?>
        <p>
            <label for="<?php echo $fieldId; ?>"><?php _e($label); ?></label>
            <input class="widefat js-date-picker" id="<?php echo $fieldId; ?>" name="<?php echo $fieldName; ?>" type="text" value="<?php echo $fieldValue; ?>">
        </p>
        <?php
    }

public function renderWidgetTimeInput(string $field, string $label, $instance)
    {
        $fieldId = $this->widget->get_field_id($field);
        $fieldName = $this->widget->get_field_name($field);
        $fieldValue = !empty($instance[$field]) ? esc_attr($instance[$field]) : '';
        ?>
        <p>
            <label for="<?php echo $fieldId; ?>"><?php _e($label); ?></label>
            <input class="widefat js-time-picker" id="<?php echo $fieldId; ?>" name="<?php echo $fieldName; ?>" type="text" value="<?php echo $fieldValue; ?>">
        </p>
        <?php
    }

    public function renderWidgetDateTimeInput(string $field, string $label, $instance)
    {
        $fieldId = $this->widget->get_field_id($field);
        $fieldName = $this->widget->get_field_name($field);
        $fieldValue = !empty($instance[$field]) ? esc_attr($instance[$field]) : '';
        ?>
        <p>
            <label for="<?php echo $fieldId; ?>"><?php _e($label); ?></label>
            <input class="widefat js-date-time-picker" id="<?php echo $fieldId; ?>" name="<?php echo $fieldName; ?>" type="text" value="<?php echo $fieldValue; ?>">
        </p>
        <?php
    }





}
