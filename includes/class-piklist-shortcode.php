<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Piklist_Dashboard
 * Controls admin dashboard widgets and features.
 *
 * @package     Piklist
 * @subpackage  Shortcode
 * @copyright   Copyright (c) 2012-2015, Piklist, LLC.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
class Piklist_Shortcode
{
  /**
   * @var array Registered shortcodes.
   * @access public
   */
  public static $shortcodes = array();
  
  /**
   * @var array Registered editors that use shortcodes.
   * @access private
   */
  private static $shortcode_editors = array('content');

  /**
   * @var array Blacklisted shortcodes to not have the UI applied to.
   * @access private
   */
  private static $shortcodes_blacklist = array(
    'wp_caption'
    ,'caption'
    ,'gallery'
    ,'playlist'
    ,'embed'
    ,'video'
    ,'audio'
    ,'field_wrapper'
    ,'field'
    ,'field_label'
    ,'field_description_wrapper'
    ,'field_description'
  );

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
    add_action('init', array('piklist_shortcode', 'register_shortcodes'));
    add_action('admin_init', array('piklist_shortcode', 'editor'));
    add_action('media_buttons', array('piklist_shortcode', 'media_buttons'), 100);
    add_action('print_media_templates', array('piklist_shortcode', 'print_media_templates'));

    add_filter('piklist_part_id-shortcodes', array('piklist_shortcode', 'part_id'), 10, 4);
    add_filter('piklist_part_process-shortcodes', array('piklist_shortcode', 'part_process'), 10, 2);
    add_filter('piklist_admin_pages', array('piklist_shortcode', 'admin_pages'));
    add_filter('piklist_field_templates', array('piklist_shortcode', 'field_templates'));
    add_filter('piklist_assets_localize', array('piklist_shortcode', 'assets_localize'));
    add_action('wp_editor_settings', array('piklist_shortcode', 'wp_editor_settings'), 10, 2);
    add_action('wp_ajax_piklist_shortcode', array('piklist_shortcode', 'ajax'));
    add_action('wp_ajax_nopriv_piklist_shortcode', array('piklist_shortcode', 'ajax'));
  }
  
  /**
   * admin_pages
   * Insert description here
   *
   * @param $pages
   *
   * @return
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function admin_pages($pages) 
  {
    $pages[] = array(
      'page_title' => __('Shortcode Editor', 'piklist')
      ,'menu_title' =>__('Shortcode Editor', 'piklist')
      ,'capability' => 'read'
      ,'sub_menu' => ''
      ,'menu_slug' => 'shortcode_editor'
    );

    return $pages;
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
  public static function part_id($part_id, $add_on, $part, $part_data)
  {
    return !empty($part_data['shortcode']) ? $part_data['shortcode'] : $part_id;
  }
  
  /**
   * part_process
   * Show Shortcode settings if non-Piklist shortcodes are active.
   *
   * @param $part_data comment block data
   *
   * @return
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function part_process($part)
  {
    $shortcodes = piklist_shortcode::get_shortcodes();
    
    return empty($shortcodes) && $part['part'] == 'shortcodes.php' ? null : $part;
  }
   
  /**
   * register_shortcodes
   * Regsiter shortcodes to be added to the system.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function register_shortcodes()
  {
    $data = array(
              'name' => 'Name'
              ,'description' => 'Description'
              ,'shortcode' => 'Shortcode'
              ,'icon' => 'Icon'
              ,'preview' => 'Preview'
            );

    piklist::process_parts('shortcodes', $data, array('piklist_shortcode', 'register_shortcodes_callback'));
  }

  /**
   * register_shortcodes_callback
   * Handle shortcodes that have been registered.
   *
   * @param array $arguments The shortcode configuration.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function register_shortcodes_callback($arguments)
  {
    extract($arguments);

    if (!empty($data['shortcode']))
    {
      if (!empty($data['icon']) && substr($data['icon'], 0, strlen('dashicons-')) != 'dashicons-')
      {
        $data['icon'] = piklist::$urls[$add_on] . '/' . $data['icon'];
      }
      
      self::$shortcodes[$data['shortcode']] = $arguments;
      
      foreach ($render as $file)
      {
        if (!strstr($file, '-form.php'))
        {
          add_shortcode($data['shortcode'], array('piklist_shortcode', 'shortcode'));
          
          break;
        }
      }
    }
  }
  
  /**
   * shortcode
   * Execute piklist enabled shortcodes.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function shortcode($attributes, $content = '', $tag)
  {
    if (self::$shortcodes[$tag])
    {
      ob_start();

      do_action('piklist_pre_render_shortcode', $attributes, self::$shortcodes[$tag]);
    
      if (self::$shortcodes[$tag]['render'])
      {
        foreach (self::$shortcodes[$tag]['render'] as $render)
        {
          if (is_array($render))
          {
            call_user_func($render['callback'], $attributes, $render['args']);
          }
          elseif (!strstr($render, '-form.php'))
          {
            piklist::render($render, $attributes);
          }
        }
      }
    
      do_action('piklist_post_render_shortcode', $attributes, self::$shortcodes[$tag]);
      
      $output = ob_get_contents();
      
      ob_end_clean();
      
      return $output;
    }
    
    return $content;
  }
  
  /**
   * media_buttons
   * Adds a shortcode media button to editors.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function media_buttons($editor_id)
  {
    if (in_array($editor_id, self::$shortcode_editors))
    {
      echo '<a href="#" id="piklist-shortcode-button" class="button piklist-shortcode-button" title="' . __('Add Shortcode', 'piklist') . '"><span class="wp-media-buttons-icon"></span> ' . __('Add Shortcode', 'piklist') . '</a>';    
    }
  }
  
  /**
   * wp_editor_settings
   * Insert description here
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function wp_editor_settings($settings, $editor_id)
  {
    if (isset($settings['shortcode_buttons']) && $settings['shortcode_buttons'])
    {
      array_push(self::$shortcode_editors, $editor_id);
    }
    
    return $settings;
  }
    
  /**
   * editor
   * Adds hooks to enable functionality for tinymce.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function editor()
  {
    add_filter('mce_buttons', array('piklist_shortcode', 'mce_buttons'));
    add_filter('tiny_mce_before_init', array('piklist_shortcode', 'tiny_mce_before_init'));
    add_filter('mce_external_plugins', array('piklist_shortcode', 'mce_external_plugins'));
  }
  
  /**
   * mce_buttons
   * Adds hooks to enable functionality for tinymce.
   *
   * @param array $buttons Tinymce button configuration.
   *
   * @return array Tinymce button configuration.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function mce_buttons($buttons) 
  {
    array_push($buttons, 'separator', 'piklist_shortcode');
    
    return $buttons;
  }
  
  /**
    * mce_external_plugins
    * Adds the Piklist shortcode plugin.
    *
    * @param array $plugins Tinymce plugins.
    *
    * @return array Registered plugins.
    *
    * @access public
    * @static
    * @since 1.0
    */
   public static function mce_external_plugins($plugins) 
   {
     $plugins['piklist_shortcode'] = piklist::$urls['piklist'] . '/parts/js/tinymce-shortcode.js';
    
     return $plugins;
   }

  /**
   * tiny_mce_before_init
   * Adds tinymce content css.
   *
   * @param array $mce_init Tinymce configuration.
   *
   * @return array Tinymce configuration.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function tiny_mce_before_init($mce_init) 
  {
    $content_css = piklist::$urls['piklist'] . '/parts/css/tinymce-shortcode.css';

    if (isset($mce_init['content_css']))
    {
      $content_css .= ',' . $mce_init['content_css'];
    }
    
    $mce_init['content_css'] = $content_css;

    return $mce_init;
  }
  
  /**
   * get_shortcodes
   * Gets a list of shortcodes for the Shortcode UI setting.
   *
   * @return array Shortcode list.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function get_shortcodes()
  {
    global $shortcode_tags;
    
    /**
     * piklist_shortcodes_blacklist
     * Insert description here
     *
     * 
     * @since 1.0
     */
    self::$shortcodes_blacklist = apply_filters('piklist_shortcodes_blacklist', self::$shortcodes_blacklist);
    
    $shortcodes = array();
    
    foreach ($shortcode_tags as $tag => $function)
    {
      if (!in_array($tag, self::$shortcodes_blacklist) && !isset(self::$shortcodes[$tag]))
      {
        $shortcodes[$tag] = piklist::humanize($tag);
      }
    }
    
    ksort($shortcodes);
    
    return $shortcodes;
  }
  
  /**
   * assets_localize
   * Add data to the local piklist variable
   *
   * @return array Current data.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function assets_localize($localize)
  {
    $localize['shortcodes'] = array();
    
    foreach (self::$shortcodes as $shortcode => $data)
    {
      unset($data['extend'], $data['extend_method'], $data['part']);

      $localize['shortcodes'][$shortcode] = $data['data'];
    }
    
    $shortcodes = piklist::get_settings('piklist_core', 'shortocde_ui');
    $shortcodes = is_array($shortcodes) ? $shortcodes : array($shortcodes);
    $shortcodes = array_filter($shortcodes);
    
    // TODO: Modify after settings are updated
    foreach ($shortcodes as $shortcode)
    {
      $localize['shortcodes'][$shortcode] = array(
        'name' => piklist::humanize($shortcode)
        ,'description' => __('Click on this box to edit the shortcode properties.')
        ,'shortcode' => $shortcode
        ,'icon' => 'dashicons-cloud'
        ,'preview' => false
      );
    }
    
    return $localize;
  }
  
  /**
   * field_templates
   * Insert description here
   *
   * @param $templates
   *
   * @return
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function field_templates($templates)
  {
    $templates['shortcode_data'] = $templates['shortcode'];
    
    return $templates;
  }
  
  /**
   * print_media_templates
   * Add javascript templates.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function print_media_templates() 
  {
    piklist::render('shared/template-piklist-shortcode');
  }
  
  /**
   * ajax
   * Handles shortcode preview
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function ajax() 
  {
    if (!isset($_REQUEST['shortcode']) || !isset($_REQUEST['preview_id']))
    {
      echo esc_html__('No shortcode specified.');
      exit;
    }
    
    $shortcode = stripslashes($_REQUEST['shortcode']);
    $post_id = isset($_REQUEST['post_id']) ? (int) $_REQUEST['post_id'] : null;
    
    if ($post_id)
    {
      global $post;
      
      $post = get_post($post_id);
      setup_postdata($post);
    }

    ob_start();

    do_action('piklist_pre_render_ajax_shortcode', $post_id, $shortcode);

    echo do_shortcode($shortcode);

    do_action('piklist_post_render_ajax_shortcode', $post_id, $shortcode);
    
    $output = ob_get_contents();
    
    ob_end_clean();
    
    wp_send_json_success(array(
      'preview_id' => esc_attr($_REQUEST['preview_id'])
      ,'html' => $output
    ));
  }
}