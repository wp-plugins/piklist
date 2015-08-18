<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!is_admin()) return;

/**
 * Piklist_Pointer
 * Manages the admin pointers.
 *
 * @package     Piklist
 * @subpackage  Pointer
 * @copyright   Copyright (c) 2012-2015, Piklist, LLC.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
class Piklist_Pointer
{
  private static $pointers = array();

  /**
   * _construct
   * Class constructor.
   *
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function _construct()
  {
    if (is_admin())
    {
      add_filter('piklist_assets_localize', array('piklist_pointer', 'assets_localize'));

      add_action('current_screen', array('piklist_pointer', 'register_pointer'));
      add_action('admin_enqueue_scripts', array('piklist_pointer', 'admin_enqueue_scripts'));
      add_action('wp_ajax_piklist_pointer', array('piklist_notice', 'ajax'));
      add_action('wp_ajax_nopriv_piklist_pointer', array('piklist_notice', 'ajax'));
    }
  }

  /**
   * admin_enqueue_scripts
   * Enqueue neccessary scripts and styles.
   *
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function admin_enqueue_scripts()
  {
    if (!empty(self::$pointers))
    {
      wp_enqueue_script('wp-pointer');
      wp_enqueue_style('wp-pointer');
    }
  }

  /**
   * register_pointer
   * Register any admin pointers available.
   *
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function register_pointer()
  {
    $data = array(
              'title' => 'Title'
              ,'capability' => 'Capability'
              ,'role' => 'Role'
              ,'page' => 'Page'
              ,'anchor_id' => 'Anchor ID'
              ,'edge' => 'Edge'
              ,'align' => 'Align'
            );
            
    piklist::process_parts('pointers', $data, array('piklist_pointer', 'register_pointer_callback'));
  }

  /**
   * register_pointer_callback
   * Handle and render a registered admin pointer.
   *
   * @param $arguments
   *
   * @return
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function register_pointer_callback($arguments)
  {
    extract($arguments);
    
    $content =  '<h3>' . $data['title'] . '</h3>';
    $dismissed = get_user_meta(get_current_user_id(), 'dismissed_wp_pointers', true);
    $dismissed = explode(',', $dismissed);
    
    if (!empty($dismissed[0]) && in_array($id, $dismissed))
    {
      return false;
    }
    
    foreach ($render as $file)
    {
      $content .= piklist::render($file, array(
        'data' => $data
      ), true);
    }

    array_push(self::$pointers, array_merge($arguments, array(
      'content' => $content
    )));
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
    $localize['pointers'] = array();
    
    foreach (self::$pointers as $pointer)
    {
      array_push($localize['pointers'], array(
        'target' => $pointer['data']['anchor_id']
        ,'options' => array(
          'content' => $pointer['content']
          ,'position' => array(
            'edge' => $pointer['data']['edge']
            ,'align' => $pointer['data']['align']
          )
        )
        ,'pointer_id' => $pointer['id'] 
      ));
    }

    return $localize;
  }
}