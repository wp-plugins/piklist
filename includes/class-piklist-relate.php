<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Piklist_Relate
 * Adds object relates to workdpress.
 *
 * @package     Piklist
 * @subpackage  Relate
 * @copyright   Copyright (c) 2012-2015, Piklist, LLC.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
class Piklist_Relate
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
    add_filter('posts_where', array('piklist_relate', 'posts_where'), 10, 2);
    add_filter('pre_user_query', array('piklist_relate', 'pre_user_query'));
    add_filter('comments_clauses', array('piklist_relate', 'comments_clauses'), 10, 2);
  }
  
  /**
   * pre_user_query
   * Insert description here
   *
   * @param $query
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function pre_user_query($query)
  {
    if (isset($query->query_vars['user_belongs']) || isset($query->query_vars['user_has']))
    {
      if (null !== ($relate_where = self::relate_query($query, 'user')))
      {
        $query->query_where .= ' ' . $relate_where;
      }
    }
  }
  
  /**
   * comments_clauses
   * Insert description here
   *
   * @param $query
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function comments_clauses($clauses, $query)
  {
    if (isset($query->query_vars['comment_belongs']) || isset($query->query_vars['comment_has']))
    {
      if (null !== ($relate_where = self::relate_query($query, 'comment')))
      {
        $clauses['where'] .= ' ' . $relate_where;
      }
    }
    
    return $clauses;
  }
  
  /**
   * posts_where
   * Insert description here
   *
   * @param $where
   * @param $query
   *
   * @return $where
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function posts_where($where, $query)
  {
    if (isset($query->query_vars['post_belongs']) || isset($query->query_vars['post_has']))
    {
      if (null !== ($relate_where = self::relate_query($query, 'post')))
      {
        $where .= ' ' . $relate_where;
      }
    }
    
    return $where;
  }
  
  /**
   * posts_where
   * Insert description here
   *
   * @param $query
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  private static function relate_query($query, $scope = 'post')
  {
    global $wpdb;
    
    $where = null;
    
    switch ($scope)
    {
      case 'post':
      
        $table = $wpdb->posts;
        $column_id = 'ID';
        
      break;

      case 'user':
      
        $table = $wpdb->users;
        $column_id = 'ID';

      break;

      case 'comment':
      
        $table = $wpdb->comments;
        $column_id = 'comment_ID';

      break;
    }

    $belongs = $scope . '_belongs';
    $has = $scope . '_has';
    $relate = $scope . '_relate';
    $relate = isset($query->query_vars[$relate]) ? $query->query_vars[$relate] : $scope;
    
    if (isset($query->query_vars[$belongs]))
    {
      $object_id = $query->query_vars[$belongs];
      $related = self::get_object_ids($object_id, $scope, $relate);
      
      $where = " AND {$table}.{$column_id} IN (" . ($related ? implode(',', array_map('intval', $related)) : -1) . ")";
    }
    elseif (isset($query->query_vars[$has]))
    {
      $object_id = $query->query_vars[$has];
      $relate = isset($query->query_vars[$relate]) ? $query->query_vars[$relate] : $scope;
      $related = get_metadata($scope, $object_id, '_' . piklist::$prefix . 'relate_' . $relate);
      
      $where = " AND {$table}.{$column_id} IN (" . ($related ? implode(',', array_map('intval', $related)) : -1) . ")";
    }

    return $where;
  }
  
  /**
   * get_object_ids
   * Insert description here
   *
   * @param $scope
   * @param $object_id
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function get_object_ids($object_id, $scope, $relate_scope)
  {
    global $wpdb;
    
    if (!$object_id)
    {
      return null;
    }
   
    $meta_key = '_' . piklist::$prefix . 'relate_' . $relate_scope;
    
    switch ($scope)
    {
      case 'post':
      case 'post_meta':

        $query = $wpdb->prepare("SELECT DISTINCT $wpdb->postmeta.post_id FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->postmeta.meta_key = %s AND $wpdb->postmeta.meta_value = %d AND $wpdb->posts.ID = $wpdb->postmeta.post_id AND $wpdb->posts.post_type != %s AND $wpdb->posts.post_type != %s", $meta_key, $object_id, 'revision', 'trash');

      break;

      case 'user':
      case 'user_meta':
      
        $query = $wpdb->prepare("SELECT DISTINCT $wpdb->usermeta.user_id FROM $wpdb->users, $wpdb->usermeta WHERE $wpdb->usermeta.meta_key = %s AND $wpdb->usermeta.meta_value = %d AND $wpdb->users.ID = $wpdb->usermeta.user_id", $meta_key, $object_id);
        
      break;
      
      case 'comment':
      case 'comment_meta':

        $query = $wpdb->prepare("SELECT DISTINCT $wpdb->commentmeta.comment_id FROM $wpdb->comments, $wpdb->commentmeta WHERE $wpdb->commentmeta.meta_key = %s AND $wpdb->commentmeta.meta_value = %d AND $wpdb->comments.comment_ID = $wpdb->commentmeta.comment_id", $meta_key, $object_id);

      break;
    }
    
    if (isset($query))
    {
      $object_ids = $wpdb->get_col($query);
      
      if ($object_ids)
      {
        return $object_ids;
      }
    }
    
    return null;
  }
  
  /**
   * relate_field
   * Insert description here
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function relate_field($field)
  {
    global $wpdb;

    if (!isset($field['relate']) || !isset($field['relate']['scope']))
    {
      return $field;
    }
    
    // Get the id of what we are relating the field to
    if (!isset($field['relate']['field']))
    {
      switch ($field['relate']['scope'])
      {
        case 'post':
        case 'post_meta':

          $field['relate_to'] = piklist_admin::is_post();
          
        break;

        case 'user':
        case 'user_meta':
          
          $field['relate_to'] = piklist_admin::is_user();
          
        break;

        case 'comment':
        case 'comment_meta':
          
          $field['relate_to'] = piklist_admin::is_comment();
          
        break;
      }
    }

    if (in_array($field['scope'], array('post_meta', 'user_meta', 'comment_meta')))
    {
      $field['value'] = self::get_object_ids($field['object_id'], $field['scope'], $field['relate']['scope']);
    }
    else
    {
      $field['object_id'] = self::get_object_ids($field['object_id'], $field['scope'], $field['relate']['scope']);
    }
    
    return $field; 
  }
}
