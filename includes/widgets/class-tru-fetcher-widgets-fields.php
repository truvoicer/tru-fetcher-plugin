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
class Tru_Fetcher_Widgets_Fields
{
    private \WP_Widget $widget;
    private string $fieldId;
    private string $fieldName;
    private string $fieldLabel;
    private mixed $fieldValue;

    public function setWidget(\WP_Widget $widget): void
    {
        $this->widget = $widget;
    }

    public function __construct()
    {
    }

    public function renderField(string $type, string $id, string $label, $instance, ?array $options = null)
    {
        $this->fieldLabel = $label;
        $this->fieldId = $this->widget->get_field_id($id);
        $this->fieldName = $this->widget->get_field_name($id);
        $this->fieldValue = !empty($instance[$id]) ? esc_attr($instance[$id]) : '';
        switch ($type) {
            case 'text':
                $this->renderWidgetTextInput();
                break;
            case 'textarea':
                $this->renderWidgetTextareaInput();
                break;
            case 'checkbox':
                $this->renderWidgetCheckboxInput();
                break;
            case 'select':
                $this->renderWidgetSelectInput($options);
                break;
            case 'multi-select':
                $this->renderWidgetSelectInput($options, true);
                break;
            case 'image':
                $this->renderWidgetImageInput();
                break;
            case 'color':
                $this->renderWidgetColorInput();
                break;
            case 'number':
                $this->renderWidgetNumberInput();
                break;
            case 'url':
                $this->renderWidgetUrlInput();
                break;
            case 'email':
                $this->renderWidgetEmailInput();
                break;
            case 'date':
                $this->renderWidgetDateInput();
                break;
            case 'time':
                $this->renderWidgetTimeInput();
                break;
            case 'datetime':
                $this->renderWidgetDateTimeInput();
                break;
        }
    }

    public function renderWidgetTextInput()
    {
        ?>
        <p>
            <label for="<?php echo $this->fieldId; ?>"><?php _e($this->fieldLabel); ?></label>
            <input class="widefat" id="<?php echo $this->fieldId; ?>" name="<?php echo $this->fieldName; ?>" type="text"
                   value="<?php echo $this->fieldValue; ?>">
        </p>
        <?php
    }

    public function renderWidgetTextareaInput()
    {
        ?>
        <p>
            <label for="<?php echo $this->fieldId; ?>"><?php _e($this->fieldLabel); ?></label>
            <textarea class="widefat" id="<?php echo $this->fieldId; ?>"
                      name="<?php echo $this->fieldName; ?>"><?php echo $this->fieldValue; ?></textarea>
        </p>
        <?php
    }

    public function renderWidgetCheckboxInput()
    {
        ?>
        <p>
            <input class="checkbox" type="checkbox" id="<?php echo $this->fieldId; ?>"
                   name="<?php echo $this->fieldName; ?>" <?php checked($this->fieldValue, 'on'); ?>>
            <label for="<?php echo $this->fieldId; ?>"><?php _e($this->fieldLabel); ?></label>
        </p>
        <?php
    }

    public function renderWidgetSelectInput(array $options, ?bool $multiple = false)
    {
        ?>
        <p>
            <label for="<?php echo $this->fieldId; ?>"><?php _e($this->fieldLabel); ?></label>
            <select
                    <?php if ($multiple) : ?> multiple <?php endif; ?>
                    class="widefat"
                    id="<?php echo $this->fieldId; ?>"
                    name="<?php echo ($multiple)? "{$this->fieldName}[]" : $this->fieldName; ?>">
                <?php foreach ($options as $item) : ?>
                    <option value="<?php echo $item['value']; ?>" <?php selected($this->fieldValue, $item['value']); ?>><?php echo $item['label']; ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php
    }

    public function renderWidgetImageInput()
    {
        ?>
        <p>
            <label for="<?php echo $this->fieldId; ?>"><?php _e($this->fieldLabel); ?></label>
            <input class="widefat" id="<?php echo $this->fieldId; ?>" name="<?php echo $this->fieldName; ?>" type="text"
                   value="<?php echo $this->fieldValue; ?>">
            <button class="button button-primary js-image-upload">Upload Image</button>
        </p>
        <?php
    }

    public function renderWidgetColorInput()
    {
        ?>
        <p>
            <label for="<?php echo $this->fieldId; ?>"><?php _e($this->fieldLabel); ?></label>
            <input class="widefat js-color-picker" id="<?php echo $this->fieldId; ?>"
                   name="<?php echo $this->fieldName; ?>" type="text" value="<?php echo $this->fieldValue; ?>">
        </p>
        <?php
    }

    public function renderWidgetNumberInput()
    {
        ?>
        <p>
            <label for="<?php echo $this->fieldId; ?>"><?php _e($this->fieldLabel); ?></label>
            <input class="widefat" id="<?php echo $this->fieldId; ?>" name="<?php echo $this->fieldName; ?>"
                   type="number" value="<?php echo $this->fieldValue; ?>">
        </p>
        <?php
    }

    public function renderWidgetUrlInput()
    {
        ?>
        <p>
            <label for="<?php echo $this->fieldId; ?>"><?php _e($this->fieldLabel); ?></label>
            <input class="widefat" id="<?php echo $this->fieldId; ?>" name="<?php echo $this->fieldName; ?>" type="url"
                   value="<?php echo $this->fieldValue; ?>">
        </p>
        <?php
    }

    public function renderWidgetEmailInput()
    {
        ?>
        <p>
            <label for="<?php echo $this->fieldId; ?>"><?php _e($this->fieldLabel); ?></label>
            <input class="widefat" id="<?php echo $this->fieldId; ?>" name="<?php echo $this->fieldName; ?>"
                   type="email" value="<?php echo $this->fieldValue; ?>">
        </p>
        <?php
    }

    public function renderWidgetDateInput()
    {
        ?>
        <p>
            <label for="<?php echo $this->fieldId; ?>"><?php _e($this->fieldLabel); ?></label>
            <input class="widefat js-date-picker" id="<?php echo $this->fieldId; ?>"
                   name="<?php echo $this->fieldName; ?>" type="text" value="<?php echo $this->fieldValue; ?>">
        </p>
        <?php
    }

    public function renderWidgetTimeInput()
    {
        ?>
        <p>
            <label for="<?php echo $this->fieldId; ?>"><?php _e($this->fieldLabel); ?></label>
            <input class="widefat js-time-picker" id="<?php echo $this->fieldId; ?>"
                   name="<?php echo $this->fieldName; ?>" type="text" value="<?php echo $this->fieldValue; ?>">
        </p>
        <?php
    }

    public function renderWidgetDateTimeInput()
    {
        ?>
        <p>
            <label for="<?php echo $this->fieldId; ?>"><?php _e($this->fieldLabel); ?></label>
            <input class="widefat js-date-time-picker" id="<?php echo $this->fieldId; ?>"
                   name="<?php echo $this->fieldName; ?>" type="text" value="<?php echo $this->fieldValue; ?>">
        </p>
        <?php
    }


}
