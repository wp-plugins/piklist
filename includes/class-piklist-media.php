<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Piklist_Media
 * Controls media modifications and features.
 *
 * @package     Piklist
 * @subpackage  Media
 * @copyright   Copyright (c) 2012-2015, Piklist, LLC.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
class Piklist_Media
{
  private static $meta_boxes = array();
    
  /**
   * _construct
   * Insert description here
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
    add_action('init', array('piklist_media', 'init'));
    
    add_filter('attachment_fields_to_edit', array('piklist_media', 'attachment_fields_to_edit'), 100, 2);
  }

  /**
   * attachment_fields_to_edit
   * Checks if there are meta boxes to render.
   *
   * @param $form_fields
   * @param $post
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function attachment_fields_to_edit($form_fields, $post)
  {
    global $typenow;
    
    if ($typenow =='attachment')
    {
      if ($meta_boxes = self::meta_box($post))
      {
        $form_fields['_final'] = $meta_boxes . '<tr class="final"><td colspan="2">' . (isset($form_fields['_final']) ? $form_fields['_final'] : '');
      }
    }
    
    return $form_fields;
  }
  
  /**
   * init
   * Initializes system.
   *
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function init()
  {   
    self::register_meta_boxes();
  }

  /**
   * register_meta_boxes
   * register meta boxes.
   *
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function register_meta_boxes()
  {
    $data = array(
              'title' => 'Title'
              ,'description' => 'Description'
              ,'capability' => 'Capability'
              ,'role' => 'Role'
              ,'order' => 'Order'
              ,'new' => 'New'
              ,'id' => 'ID'
            );
            
    piklist::process_parts('media', $data, array('piklist_media', 'register_meta_boxes_callback'));
  }

  /**
   * register_meta_boxes_callback
   * Handle the registration of a meta box for media.
   *
   * @param $arguments
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function register_meta_boxes_callback($arguments)
  {
    global $pagenow;
    
    extract($arguments);
    
    if (!$data['new'] || ($data['new'] && !in_array($pagenow, array('async-upload.php', 'media-new.php'))))
    {    
      foreach (self::$meta_boxes as $key => $meta_box)
      {
        if ($id == $meta_box['id'])
        {
          unset(self::$meta_boxes[$key]);
        }
      }
      
      if (isset($order))
      {
        self::$meta_boxes[$order] = $arguments;
      }
      else
      {
        array_push(self::$meta_boxes, $arguments);
      }
    }
  }

  /**
   * meta_box
   * Render the meta box.
   *
   * @param $post
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function meta_box($post)
  {
    if (!empty(self::$meta_boxes))
    {
      ob_start();
      
      $GLOBALS['piklist_attachment'] = $post;
      
      foreach (self::$meta_boxes as $meta_box)
      {
        piklist::render('shared/meta-box-start', array(
          'meta_box' => $meta_box
          ,'wrapper' => 'media_meta'
        ), false);
        
        do_action('piklist_pre_render_media_meta_box', $post, $meta_box);
        
        foreach ($meta_box['render'] as $render)
        {
          piklist::render($render, array(
            'data' => $meta_box['data']
          ), false);
        }
                
        do_action('piklist_post_render_media_meta_box', $post, $meta_box);
                
        piklist::render('shared/meta-box-end', array(
          'meta_box' => $meta_box
          ,'wrapper' => 'media_meta'
        ), false);
      }
      
      unset($GLOBALS['piklist_attachment']);
      
      $output = ob_get_contents();
      
      ob_end_clean();
      
      return $output;
    }
    
    return null;
  }
}