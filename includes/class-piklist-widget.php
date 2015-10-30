<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Piklist_Widget
 * Controls widget modifications and features.
 *
 * @package     Piklist
 * @subpackage  Widget
 * @copyright   Copyright (c) 2012-2015, Piklist, LLC.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
class Piklist_Widget
{
  /**
   * @var array The current widget.
   * @access public
   */
  public static $current_widget = null;

  /**
   * @var array Classes for registered widgets.
   * @access private
   */
  private static $widget_classes = array();

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
    add_action('init', array('piklist_widget', 'init'));
    add_action('widgets_init', array('piklist_widget', 'widgets_init'));

    add_filter('dynamic_sidebar_params', array('piklist_widget', 'dynamic_sidebar_params'));
  }

  /**
   * init
   * Initializes system.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function init()
  {
    self::register_sidebars();
  }

  /**
   * register_sidebars
   * Register sidebars via the piklist_sidebars
   * Sets better defaults than WordPress
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function register_sidebars()
  {
    /**
     * piklist_sidebars
     * Filter register_sidebar()
     *
     * @param array Sidebar parameters.
     *
     * @since 1.0
     */
    $sidebars = apply_filters('piklist_sidebars', array());

    foreach ($sidebars as $sidebar)
    {
      register_sidebar(array_merge(array(
        'name' => $sidebar['name']
        ,'id' => sanitize_title_with_dashes($sidebar['name'])
        ,'description' => isset($sidebar['description']) ? $sidebar['description'] : null
        ,'before_widget' => isset($sidebar['before_widget']) ? $sidebar['before_widget'] : '<div id="%1$s" class="widget-container %2$s">'
        ,'after_widget' => isset($sidebar['after_widget']) ? $sidebar['after_widget'] : '</div>'
        ,'before_title' => isset($sidebar['before_title']) ? $sidebar['before_title'] : '<h3 class="widget-title">'
        ,'after_title' => isset($sidebar['after_title']) ? $sidebar['after_title'] : '</h3>'
     ), $sidebar));
    }
  }

  /**
   * widgets_init
   * Groups widgets for universal widgets.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function widgets_init()
  {
    global $wp_widget_factory, $wp_version;

    $widget_class = 'piklist_universal_widget';

    $addons_paths = piklist::paths();
    
    foreach ($addons_paths as $from => $path)
    {
      if (!piklist::directory_empty($path . '/parts/widgets'))
      {
        $widget_class_name = $widget_class . '_' . piklist::slug($from);

        $suffix = '';
        $title = '';
        $description = '';

        if (isset(piklist_add_on::$available_add_ons[$from]))
        {
          if (stripos(piklist_add_on::$available_add_ons[$from]['Name'], 'widget') === false)
          {
            $suffix = ' ' . __('Widgets', 'piklist');
          }

          $title = piklist_add_on::$available_add_ons[$from]['Name'] . $suffix;

          $description = strip_tags(piklist_add_on::$available_add_ons[$from]['Description']);
        }
        elseif ($from == 'piklist')
        {
          $title = __('Piklist Widgets', 'piklist');
          $description = __('Core Widgets for Piklist.', 'piklist');
        }
        elseif ($from == 'theme')
        {
          $current_theme = wp_get_theme();

          $title = $current_theme . ' ' . __('Widgets', 'piklist');
          $description = sprintf(__('Widgets for the %s Theme', 'piklist'), $current_theme);
        }

        $wp_widget_factory->widgets[$widget_class_name] = new $widget_class($widget_class_name, $title, $description, array($from => $path));
      }
    }
  }

  /**
   * widget
   * Get the current widget object.
   *
   * @return array The current widget object.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function widget()
  {
    global $wp_widget_factory;
    
    return isset($wp_widget_factory->widgets[self::$current_widget]) ? $wp_widget_factory->widgets[self::$current_widget] : null;
  }

  /**
   * dynamic_sidebar_params
   * Add helpful classes to widget areas on frontend of website.
   *
   * @param array $params The widget params.
   *
   * @return array The widget params.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function dynamic_sidebar_params($params)
  {
    $id = $params[0]['id'];

    if (!isset(self::$widget_classes[$id]))
    {
      self::$widget_classes[$id] = 0;
    }
    self::$widget_classes[$id]++;

    $class = 'class="widget-' . self::$widget_classes[$id] . ' ';

    if (self::$widget_classes[$id] % 2 == 0)
    {
      $class .= 'widget-even ';
      $class .= 'widget-alt ';
    }
    else
    {
      $class .= 'widget-odd ';
    }

    $params[0]['before_widget'] = str_replace('class="', $class, $params[0]['before_widget']);

    return $params;
  }
}