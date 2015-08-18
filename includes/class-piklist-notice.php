<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!is_admin()) return;

/**
 * Piklist_Notice
 * Manages the admin notices.
 *
 * @package     Piklist
 * @subpackage  Notice
 * @copyright   Copyright (c) 2012-2015, Piklist, LLC.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
class Piklist_Notice
{
  private static $notices = array();

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
      add_action('current_screen', array('piklist_notice', 'register_notice'));
      add_action('admin_notices', array('piklist_notice', 'admin_notice'));
      add_action('wp_ajax_piklist_notice', array('piklist_notice', 'ajax'));
      add_action('wp_ajax_nopriv_piklist_notice', array('piklist_notice', 'ajax'));
    }
  }
   
  /**
   * register_notice
   * Register any admin notices available.
   *
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function register_notice()
  {    
    $data = array(
              'notice_type' => 'Notice Type' // error, updated, update-nag
              ,'notice_id' => 'Notice ID'
              ,'capability' => 'Capability'
              ,'role' => 'Role'
              ,'page' => 'Page'
              ,'dismiss' => 'Dismiss'
            );
            
    piklist::process_parts('notices', $data, array('piklist_notice', 'register_notice_callback'));
  }

  /**
   * register_notice_callback
   * Handle and render a registered admin notice.
   *
   * @param $arguments
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function register_notice_callback($arguments)
  {
    extract($arguments);
    
    $content = '';
    $dismissed = get_user_meta(get_current_user_id(), piklist::$prefix . 'dismissed_notices', true);
    
    if (!empty($dismissed[0]) && in_array($data['notice_id'], $dismissed))
    {
      return false;
    }
    
    foreach ($render as $file)
    {
      $content .= piklist::render($file, array(
        'data' => $data
      ), true);
    }

    array_push(self::$notices, array_merge($arguments, array(
      'content' => $content
    )));
  }
  
  /**
   * admin_notice
   * Render the admin notice.
   *
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function admin_notice()
  {

    foreach (self::$notices as $notices => $notice)
    {
      piklist::render('shared/admin-notice', array(
        'type' => $notice['data']['notice_type']
        ,'content' => $notice['content']
        ,'notice_id' => $notice['data']['notice_id']
        ,'notice_type' => $notice['data']['notice_type']
        ,'dismiss' => $notice['data']['dismiss']
      ));

    }
  }

  /**
   * ajax
   * Updates the user meta field 'piklist_notice_dismissed' with the notice_id
   * Only triggered if user dismisses notice.
   *
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function ajax()
  {
    if (isset($_REQUEST['id']))
    {
      $user_id = get_current_user_id();
      
      $dismissed = get_user_meta($user_id, piklist::$prefix . 'dismissed_notices', true);
      $dismissed = !$dismissed ? array() : $dismissed;
      
      array_push($dismissed, esc_attr($_REQUEST['id']));
      
      update_user_meta($user_id, piklist::$prefix . 'dismissed_notices', $dismissed);
    }
  }
}