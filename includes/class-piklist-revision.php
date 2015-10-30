<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Piklist_Revision
 * Manages and enhances post revisions.
 *
 * @package     Piklist
 * @subpackage  Revision
 * @copyright   Copyright (c) 2012-2015, Piklist, LLC.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
class Piklist_Revision
{
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
    add_action('save_post', array('piklist_revision', 'save_post'), -1, 2);
    add_action('wp_restore_post_revision', array('piklist_revision', 'restore_revision'), 10, 2);
  
    add_filter('_wp_post_revision_fields', array('piklist_revision', '_wp_post_revision_fields'));
  }
  
  /**
   * save_post
   * Make sure metadata is saved on post revisions
   *
   * @param int $post_id The post id.
   * @param object $post The post object.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function save_post($post_id, $post) 
  {
    if (($parent_id = wp_is_post_revision($post_id)) && !wp_is_post_autosave($post_id)) 
    {
      if ($meta = piklist('post_custom', $parent_id))
      {
        foreach ($meta as $key => $value)
        {
          add_metadata('post', $post_id, $key, maybe_serialize($value));
        }
      }
    }
  }
  
  /**
   * restore_revision
   * Restores a revision to the current post.
   *
   * @param int $post_id The post id.
   * @param int $revision_id The post revision id.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function restore_revision($post_id, $revision_id)
  {
    if ($meta = piklist('post_custom', $revision_id))
    {
      foreach ($meta as $key => $value)
      {
        update_metadata('post', $post_id, $key, $value);   
      }
    }
  } 
  
  /**
   * _wp_post_revision_fields
   * Adds a custom field for metadata to the revision ui.
   *
   * @param array $fields The current set of fields for the ui.
   *
   * @return array Updated fields.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function _wp_post_revision_fields($fields) 
  {
    global $wpdb;
    
    $meta_keys = $wpdb->get_col("SELECT DISTINCT meta_key FROM $wpdb->postmeta"); //" WHERE meta_key NOT LIKE '\_%';");
    
    foreach ($meta_keys as $meta_key)
    {
      $label = ucwords(str_replace(array('-', '_'), ' ', $meta_key));
      
      $fields[$meta_key] = __($label, 'piklist');
    
      add_filter('_wp_post_revision_field_' . $meta_key, array('piklist_revision', '_wp_post_revision_field'), 10, 4);
    }

    return $fields;
  }

  /**
   * _wp_post_revision_field_meta
   * Render the metadata in the field.
   *
   * @param int $value The field value.
   * @param int $field The field to retrieve.
   *
   * @return mixed The metadata.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function _wp_post_revision_field($value, $field, $revision, $type) 
  {
    $meta = maybe_unserialize(get_metadata('post', $revision->ID, $field, true));

    return is_array($meta) ? self::array_to_list($meta) : $meta;
  }
  
  /**
   * array_to_list
   * Converts object into a more readable format for the post revision table.
   *
   * @param array $array The field to display.
   * @param int $depth The current depth of the transversal.
   *
   * @return string The formatted array.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function array_to_list(&$array, $depth = 0) 
  {
    $output = '';
    
    foreach ($array as $key => $value) 
    { 
      $output .= "\n"; 
    
      for ($i = 0; $i < $depth; $i++)
      {
        $output .= '-'; 
      }
    
      if ($depth > 0)
      {
        $output .= '> '; 
      }

      $output .= $key .' => '; 

      if (is_array($value)) 
      { 
        $output .= self::array_to_list($value, $depth + 1); 
      } 
      else
      {    
        $output .= $array[$key] . "\n"; 
      } 
    } 

    return $output;
  }
}