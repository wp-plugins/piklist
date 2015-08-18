<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!is_admin()) return;

/**
 * Piklist_Dashboard
 * Controls admin dashboard widgets and features.
 *
 * @package     Piklist
 * @subpackage  Dashboard
 * @copyright   Copyright (c) 2012-2015, Piklist, LLC.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
class Piklist_Dashboard
{
  /**
   * @var array Registered dashboard widgets.
   * @access private
   */
  private static $widgets = array();
  
  /**
   * _construct
   * Class constructor.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function _construct()
  {
    if (is_admin())
    {
      add_filter('piklist_part_process-dashboard', array('piklist_meta', 'part_process'), 10, 2);
      
      add_action('wp_dashboard_setup', array('piklist_dashboard', 'register_dashboard_widgets'));
      add_action('wp_network_dashboard_setup', array('piklist_dashboard', 'register_dashboard_widgets'));
    }
  }

  /**
   * register_dashboard_widgets
   * Register dashboard widgets.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function register_dashboard_widgets()
  {
    $data = array(
              'title' => 'Title'
              ,'capability' => 'Capability'
              ,'role' => 'Role'
              ,'network' => 'Network'
            );
            
    piklist::process_parts('dashboard', $data, array('piklist_dashboard', 'register_dashboard_widgets_callback'));
  }

  /**
   * register_dashboard_widgets_callback
   * Handle the registration of a dashboard widget.
   *
   * @param array $arguments The dashboard widget configuration.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function register_dashboard_widgets_callback($arguments)
  {
    extract($arguments);
    
  	$screen = get_current_screen();
    
    self::$widgets[$id] = $arguments;
    
    if (piklist_meta::update_meta_box($screen, $id))
    {
      piklist_meta::update_meta_box($screen, $id, 'remove');
    }
    
    wp_add_dashboard_widget(
      $id
      ,__($data['title'])
      ,array('piklist_dashboard', 'render_dashboad_widget')
    );
  }
  
  /**
   * render_dashboad_widget
   * Render the dashboad widget
   *
   * @param mixed $null No data.
   * @param array $data Widget configuration.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function render_dashboad_widget($null, $data)
  {
    $widget = self::$widgets[$data['id']];
    
    do_action('piklist_pre_render_dashboard_widget', $null, $widget);
    
    if ($widget['render'])
    {
      foreach ($widget['render'] as $render)
      {
        if (is_array($render))
        {
          call_user_func($render['callback'], $null, $render['args']);
        }
        else
        {
          piklist::render($render, array(
            'data' => $widget['data']
          ));
        }
      }
    }
    
    do_action('piklist_post_render_dashboard_widget', $null, $widget);
  }
}