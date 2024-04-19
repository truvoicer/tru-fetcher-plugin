<?php

namespace TruFetcher\Includes\Widgets;

class Tru_Fetcher_Widgets_Base extends \WP_Widget
{
    private Tru_Fetcher_Widgets_Fields $widgetFields;
    protected array $config;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        if (empty($this->config) || !is_array($this->config)) {
            throw new \Exception('Widget configuration is required');
        }
        $widget_ops = array(
            'classname' => $this->config['classname'],
            'description' => $this->config['description'],
        );
        parent::__construct( $this->config['id'], $this->config['name'], $widget_ops );
        $this->widgetFields = new Tru_Fetcher_Widgets_Fields();
        $this->widgetFields->setWidget($this);
    }


    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {
        extract( $args );
        echo $before_widget;
        echo $before_title . apply_filters( 'widget_title', $this->config['title'] ) . $after_title;
        $this->renderContent($args, $instance);
        echo $after_widget;
    }

    /**
     * Outputs the options form on admin
     *
     * @param array $instance The widget options
     */
    public function form( $instance ) {
        // outputs the options form on admin
        foreach ($this->config['fields'] as $field) {
            $props = [
                $field['type'],
                $field['id'],
                $field['label'],
                $instance
            ];
            if (!empty($field['options']) && is_array($field['options'])) {
                $props[] = $field['options'];
            }
            $this->widgetFields->renderField(...$props);
        }
    }

    /**
     * Processing widget options on save
     *
     * @param array $new_instance The new options
     * @param array $old_instance The previous options
     *
     * @return array
     */
    public function update( $new_instance, $old_instance ) {
        // processes widget options to be saved
        $instance          = array();
        foreach ($this->config['fields'] as $field) {
            $instance[$field['id']] = ( ! empty( $new_instance[$field['id']] ) ) ?  $new_instance[$field['id']] : null;
        }
        return $instance;
    }

    public function renderContent($args, $instance): void
    {
        echo '<h2>' . $this->config['name'] . '</h2>';
        echo '<ul>';
        foreach ($this->config['fields'] as $field) {
            echo "<li>{$field['label']} :  {$instance[$field['id']]} </li>";
        }
        echo '</ul>';
    }
}
