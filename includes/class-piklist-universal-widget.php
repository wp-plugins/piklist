<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Piklist_Universal_Widget
 * Controls modifications and features for Universal Widgets.
 *
 * @package     Piklist
 * @subpackage  Universal Widget
 * @copyright   Copyright (c) 2012-2015, Piklist, LLC.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
class Piklist_Universal_Widget extends WP_Widget 
{
  public $widgets = array();

  public $instance = array();

  public $widget_core_name = 'piklist_universal_widget';
  
  public $widget_name = '';

  public $widgets_path = '';
  
  /**
   * Piklist_Universal_Widget
   * Insert description here
   *
   * @param $name
   * @param $title
   * @param $description
   * @param $path
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public function Piklist_Universal_Widget($name, $title, $description, $path = array(), $control_options = array()) 
  {
    global $pagenow;
    
    $this->widget_name = $name;
    $this->widgets_path = $path;
    
    if ($pagenow == 'customize.php')
    {
      $control_options['width'] = 300;
      $control_options['height'] = 200;
    }

    parent::__construct(
      ucwords(piklist::dashes($this->widget_name))
      ,__($title)
      ,array(
        'classname' => piklist::dashes($this->widget_core_name)
        ,'description' => __($description)
      )
      ,$control_options
    );
    
    add_filter('piklist_part_id-widgets', array(&$this, 'part_id'), 10, 4);
 
    add_action('wp_ajax_' . $name, array(&$this, 'ajax'));
  }
  
  /**
   * setup
   * Insert description here
   *
   * @param $widget
   *
   * @access public
   * @since 1.0
   */
  public function setup($widget)
  {
    $this->register_widgets();
    
    piklist_widget::$current_widget = $widget;
  }
  
  /**
   * form
   * Insert description here
   *
   * @param $instance
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public function form($instance) 
  {
    $this->setup($this->widget_name);

    $this->instance = $instance;

    piklist::render('shared/widget-select', array(
      'instance' => $instance
      ,'widgets' => $this->widgets
      ,'name' => $this->widget_core_name
      ,'widget_name' => $this->widget_name
      ,'class_name' => piklist::dashes($this->widget_core_name)
      ,'widget' => isset($this->instance['widget']) ? maybe_unserialize($this->instance['widget']) : null
    ));

    return $instance;
  }
  
  /**
   * ajax
   * Insert description here
   *
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public function ajax()
  {
    global $wp_widget_factory;

    $widget = isset($_REQUEST['widget']) ? esc_attr($_REQUEST['widget']) : null;
    
    if ($widget && current_user_can('edit_theme_options'))
    {
      $this->setup($this->widget_name);
      
      if (isset($_REQUEST['number']))
      {
        $instances = get_option('widget_' . piklist::dashes($this->widget_name));
      
        piklist_widget::widget()->_set($_REQUEST['number']);
        
        if (isset($instances[$_REQUEST['number']]))
        {
          piklist_widget::widget()->instance = $instances[$_REQUEST['number']];
        }
      }

      if (isset($this->widgets[$widget]))
      {
        ob_start();
        
        do_action('piklist_notices');
      
        foreach ($this->widgets[$widget]['render'] as $render)
        {
          if (strstr($render, '-form.php'))
          {
             piklist::render($render);
          }
        }
        
        piklist_form::save_fields();

        $output = ob_get_contents();
  
        ob_end_clean();
        
        wp_send_json(array(
          'form' => $output
          ,'widget' => $this->widgets[$widget]
        ));
      }
    }
    
    wp_send_json_error();
  }

  /**
   * update
   * Insert description here
   *
   * @param $new_instance
   * @param $old_instance
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public function update($new_instance, $old_instance)
  {
    if (false !== ($fields = piklist_validate::check($new_instance)))
    { 
      $instance = array();
    
      foreach ($new_instance as $key => $value)
      {
        if (!empty($value))
        {
          $instance[$key] = is_array($value) ? maybe_serialize($value) : stripslashes($value);
        }
      }
      
      return $instance;
    }
    elseif (count($old_instance) <= 1)
    {
      return array(
        'widget' => $new_instance['widget']
      );
    }
    
    $old_instance['widget'] = $new_instance['widget'];
    
    return $old_instance;
  }

  /**
   * widget
   * Insert description here
   *
   * @param $arguments
   * @param $instance
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public function widget($arguments, $instance) 
  {
    extract($arguments);
    
    if (isset($instance['widget']))
    {
      $widget = $instance['widget'];
      
      unset($instance['widget']);
    }

    if (isset($widget) && !empty($instance))
    {
      $this->setup($this->widget_name);

      foreach ($instance as $field => &$value)
      {
        $value = maybe_unserialize($value);
      }
      
      $this->widgets[$widget]['instance'] = $instance;
    
      do_action('piklist_pre_render_widget', $this->widgets[$widget]);
      
      foreach ($this->widgets[$widget]['render'] as $render)
      {
        if (!strstr($render, '-form.php'))
        {
           piklist::render($render, array(
            'instance' => $instance
            ,'settings' => $instance
            ,'before_widget' => str_replace('class="', 'class="' . piklist::dashes($this->widgets[$widget]['add_on'] . ' ' . $this->widgets[$widget]['id']) . ' ' . $this->widgets[$widget]['data']['class'] . ' ', $before_widget)
            ,'after_widget' => $after_widget
            ,'before_title' => $before_title
            ,'after_title' => $after_title
            ,'data' => $this->widgets[$widget]['data']
          ));
        }
      }
    
      do_action('piklist_post_render_widget', $this->widgets[$widget]);
    }
  }
  
  /**
   * register_widgets
   * Register widgets with the appropriate universal widget.
   *
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public function register_widgets()
  {
    if (empty($this->widgets))
    {
      $data = array(
        'title' => 'Title'
        ,'description' => 'Description'
        ,'tags' => 'Tags'
        ,'class' => 'Class'
        ,'height' => 'Height'
        ,'width' => 'Width'
      );
        
      piklist::process_parts('widgets', $data, array(&$this, 'register_widgets_callback'), $this->widgets_path);
      
      do_action('piklist_widgets_post_register');
    }
  }

  /**
   * register_widgets_callback
   * Handle the registration of a widget form.
   *
   * @param $arguments
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public function register_widgets_callback($arguments)
  {
    extract($arguments);

    $this->widgets[$id] = $arguments;
  }
  
  /**
   * part_id
   * Specify the part id.
   *
   * @param $part_id The current id for the part
   * @param $part_data comment block data
   *
   * @return
   *
   * @access public
   * @static
   * @since 1.0
   */
  public function part_id($part_id, $add_on, $part, $part_data)
  {
    return piklist::slug($add_on . ' ' . str_replace('-form.php', '.php', strtolower($part)));
  }
}