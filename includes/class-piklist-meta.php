<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Piklist_Meta
 * Insert description here
 *
 * @package     Piklist
 * @subpackage  Meta
 * @copyright   Copyright (c) 2012-2015, Piklist, LLC.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
class Piklist_Meta
{
  public static $grouped_meta_keys = array(
    'post' => array()
    ,'term' => array()
    ,'comment' => array()
    ,'user' => array()
  );
  
  private static $reset_meta = array(
    'post.php' => array(
      'id' => 'post'
      ,'group' => 'post_meta'
    )
    ,'user-edit.php' => array(
      'id' => 'user_id'
      ,'group' => 'user_meta'
    )
    ,'comment.php' => array(
      'id' => 'comment_id'
      ,'group' => 'comment_meta'
    )
  );
  
  private static $wp_save_post_revision_check = false;
  
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
    add_action('init', array('piklist_meta', 'meta_grouped'), 100);
    add_action('init', array('piklist_meta', 'meta_reset'));
    add_action('query', array('piklist_meta', 'meta_sort'));
    add_action('add_meta_boxes', array('piklist_meta', 'register_meta_boxes'), 1000);
    add_action('do_meta_boxes', array('piklist_meta', 'sort_meta_boxes'), 100, 3);

    add_filter('get_post_metadata', array('piklist_meta', 'get_post_meta'), 100, 4);
    add_filter('get_user_metadata', array('piklist_meta', 'get_user_meta'), 100, 4);
    add_filter('get_term_metadata', array('piklist_meta', 'get_term_meta'), 100, 4);

    add_filter('wp_save_post_revision_check_for_changes', array('piklist_meta', 'wp_save_post_revision_check_for_changes'), -1, 3);
    add_filter('wp_save_post_revision_post_has_changed', array('piklist_meta', 'wp_save_post_revision_post_has_changed'), -1, 3);
    add_filter('get_post_metadata', array('piklist_meta', 'wp_save_post_revision_post_meta_serialize'), 100, 4);

    add_filter('piklist_part_process-meta-boxes', array('piklist_meta', 'part_process'), 10, 2);
  }
  
  /**
   * update_meta_box
   * Insert description here
   *
   * @param $object
   *
   * @access public
   * @static
   * @since 1.0
   */  
  public static function update_meta_box($screen, $id, $action = 'search')
  {
    global $wp_meta_boxes;
    
    $check = false;
    
    if (empty($screen))
    {
      $screen = get_current_screen();
    }
    elseif (is_string($screen))
    {
      $screen = convert_to_screen($screen);
    }
    
    $page = $screen->id;
    
    foreach (array('normal', 'advanced', 'side') as $context)
    {
      foreach (array('high', 'sorted', 'core', 'default', 'low') as $priority)
      {
        if (isset($wp_meta_boxes[$page][$context][$priority]))
        {
          foreach ($wp_meta_boxes[$page][$context][$priority] as $order => $meta_box)
          {
            if ($meta_box['id'] == $id)
            {
              if ($action == 'remove')
              {
                unset($wp_meta_boxes[$page][$context][$priority][$order]);
              }
              
              $check = true;
            }
          }
        }
      }
    }
    
    return $check;
  }
  
  /**
   * register_meta_boxes
   * Insert description here
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
              ,'context' => 'Context'
              ,'description' => 'Description'
              ,'capability' => 'Capability'
              ,'role' => 'Role'
              ,'priority' => 'Priority'
              ,'order' => 'Order'
              ,'post_type' => 'Post Type'
              ,'post_status' => 'Post Status'
              ,'lock' => 'Lock'
              ,'collapse' => 'Collapse'
              ,'status' => 'Status'
              ,'new' => 'New'
              ,'id' => 'ID'
              ,'template' => 'Template'
              ,'meta_box' => 'Meta Box'
            );
    
    piklist::process_parts('meta-boxes', $data, array('piklist_meta', 'register_meta_boxes_callback'));
  }
  
  /**
   * register_meta_boxes_callback
   * Insert description here
   *
   * @param $arguments
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function register_meta_boxes_callback($arguments)
  {
    global $post, $pagenow, $wp_meta_boxes;

    extract($arguments);

    $textdomain = isset(piklist_add_on::$available_add_ons[$add_on]['TextDomain']) ? piklist_add_on::$available_add_ons[$add_on]['TextDomain'] : null;
    $title = !empty($data['title']) ? $data['title'] : $id;
    $title = !empty($textdomain) ? __($title, $textdomain) : $title;
    $types = empty($data['post_type']) ? get_post_types() : $data['post_type'];
    $context = empty($data['context']) ? 'normal' : $data['context'];
    $priority = empty($data['priority']) ? 'low' : $data['priority'];
    
    foreach ($types as $type)
    {
      $type = trim($type);

      if (!empty($data['extend']) && $data['extend_method'] == 'remove')
      {
        self::update_meta_box($type, $data['extend'], 'remove');
      }
      else
      {
        if (empty($data['extend']) || (!empty($data['extend']) && self::update_meta_box($type, $id)))
        {
          self::update_meta_box($type, $id, 'remove');
      
          add_meta_box(
            $id
            ,$title
            ,array('piklist_meta', 'meta_box')
            ,$type
            ,$context
            ,$priority
            ,array(
              'render' => $render
              ,'add_on' => $add_on
              ,'order' => $data['order'] ? $data['order'] : null
              ,'data' => $data
            )
          );
        }
        
        if ($data['meta_box'] === false)
        {
          add_filter("postbox_classes_{$type}_{$id}", array('piklist_meta', 'lock_meta_boxes'));
          add_filter("postbox_classes_{$type}_{$id}", array('piklist_meta', 'no_meta_boxes'));
        }
        else
        {
          if ($data['lock'] === true)
          {
            add_filter("postbox_classes_{$type}_{$id}", array('piklist_meta', 'lock_meta_boxes'));
          }
          
          if ($data['collapse'] === true)
          {
            add_filter("postbox_classes_{$type}_{$id}", array('piklist_meta', 'collapse_meta_boxes'));
          }
        }
    
        if ($title == $id)
        {
          add_filter("postbox_classes_{$type}_{$id}", array('piklist_meta', 'no_title_meta_boxes'));
        }

        add_filter("postbox_classes_{$type}_{$id}", array('piklist_meta', 'default_classes'));
      }
    }
  }

  /**
   * meta_box
   * Insert description here
   *
   * @param $post
   * @param $meta_box
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function meta_box($post, $meta_box)
  {
    do_action('piklist_pre_render_meta_box', $post, $meta_box);
    
    if ($meta_box['args']['render'])
    {
      foreach ($meta_box['args']['render'] as $render)
      {
        if (is_array($render))
        {
          call_user_func($render['callback'], $post, $render['args']);
        }
        else
        {
          piklist::render($render, array(
            'data' => $meta_box['args']['data']
          ));
        }
      }
    }
    
    do_action('piklist_post_render_meta_box', $post, $meta_box);
  }
  
  /**
   * part_process
   * Process part addition.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function part_process($part)
  {
    global $wp_meta_boxes, $current_screen;

    foreach (array('normal', 'advanced', 'side') as $context)
    {
      foreach (array('high', 'sorted', 'core', 'default', 'low') as $priority)
      {
        if (isset($wp_meta_boxes[$current_screen->id][$context][$priority]))
        {
          foreach ($wp_meta_boxes[$current_screen->id][$context][$priority] as $meta_box)
          {
            if ($meta_box['id'] == $part['id'] && $part['id'] != 'submitdiv')
            {
              if (!in_array($meta_box, $part['render']))
              {
                if ($part['data']['extend_method'] == 'before')
                {
                  array_push($part['render'], $meta_box);
                }
                elseif ($part['data']['extend_method'] == 'after')
                {
                  array_unshift($part['render'], $meta_box);
                }
                else
                {
                  unset($wp_meta_boxes[$current_screen->id][$context][$priority][$meta_box['id']]);
                }
              }
            }
          }
        }
      }
    }
    
    if ($part['id'] == 'submitdiv' && empty($part['data']['post_type']))
    {
      $post_types = get_post_types(array(
        '_builtin' => false
      ));

      $part['data']['post_type'] = array_values($post_types);
    }
          
    return $part;
  }
  

  /**
   * sort_meta_boxes
   * Insert description here
   *
   * @param $post_type
   * @param $context
   * @param $post
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function sort_meta_boxes($post_type, $context, $post)
  {
    global $pagenow;

    if (in_array($pagenow, array('edit.php', 'post.php', 'post-new.php')) && post_type_exists(get_post_type()))
    {
      global $wp_meta_boxes;

      foreach (array('high', 'sorted', 'core', 'default', 'low') as $priority)
      {
        if (isset($wp_meta_boxes[$post_type][$context][$priority]))
        {
          uasort($wp_meta_boxes[$post_type][$context][$priority], array('piklist', 'sort_by_args_order'));
        }
      }

      add_filter('get_user_option_meta-box-order_' . $post_type, array('piklist_meta', 'user_sort_meta_boxes'), 100, 3);
    }
  }

  /**
   * user_sort_meta_boxes
   * Insert description here
   *
   * @param $result
   * @param $option
   * @param $user
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function user_sort_meta_boxes($result, $option, $user)
  {
    global $wp_meta_boxes;

    $post_type = str_replace('meta-box-order_', '', $option);

    $order = array(
      'side' => ''
      ,'normal' => ''
      ,'advanced' => ''
    );

    foreach (array('side', 'normal', 'advanced') as $context)
    {
      foreach (array('high', 'sorted', 'core', 'default', 'low') as $priority)
      {
        if (isset($wp_meta_boxes[$post_type][$context][$priority]) && !empty($wp_meta_boxes[$post_type][$context][$priority]))
        {
          $order[$context] .= (!empty($order[$context]) ? ',' : '') . implode(',', array_keys($wp_meta_boxes[$post_type][$context][$priority]));
        }
      }
    }

    return $order;
  }

  /**
   * lock_meta_boxes
   * Returns classes to be used by a metabox, to lock the metabox.
   *
   * @param $classes
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function lock_meta_boxes($classes)
  {
    array_push($classes, 'piklist-meta-box-lock hide-all');
    
    return $classes;
  }

  /**
   * no_title_meta_boxes
   * Returns classes to be used by a metabox, to remove the title.
   *
   * @param $classes
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function no_title_meta_boxes($classes)
  {
    array_push($classes, 'piklist-meta-box-no-title');
    
    return $classes;
  }

  /**
   * no_meta_boxes
   * Returns classes to be used by a metabox, to remove the metabox look.
   *
   * @param $classes
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function no_meta_boxes($classes)
  {
    array_push($classes, 'piklist-meta-box-none');
    
    return $classes;
  }

  /**
   * default_classes
   * Returns classes to be used by a metabox, to identify it as a meta-box created by Piklist.
   *
   * @param $classes
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function default_classes($classes)
  {
    array_push($classes, 'piklist-meta-box');
    
    return $classes;
  }

  /**
   * collapse_meta_boxes
   * Returns classes to be used by a metabox, to collapse it by default.
   *
   * @param $classes
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function collapse_meta_boxes($classes)
  {
    array_push($classes, 'piklist-meta-box-collapse');
    
    return $classes;
  }

  /**
   * default_post_title
   * Sets the default post title to post_type and post_id.
   *
   * @param $id
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function default_post_title($id)
  {
    $post = get_post($id);

    wp_update_post(array(
      'ID' => $id
      ,'post_title' => ucwords(str_replace(array('-', '_'), ' ', $post->post_type)) . ' ' . $id
    ));
  }
  
  /**
   * meta_grouped
   * Insert description here
   *
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function meta_grouped()
  {
    global $wpdb;
    
    foreach (self::$grouped_meta_keys as $meta_type => $meta_keys)
    {
      if (($meta = self::get_meta_properties($meta_type)) !== false)
      {
        $group_key = $wpdb->get_var("SELECT DISTINCT meta_key FROM " . $meta['table'] . " WHERE meta_key LIKE '\_\\" . piklist::$prefix . "%'");
        $key = $wpdb->get_var($wpdb->prepare("SELECT DISTINCT meta_key FROM " . $meta['table'] . " WHERE meta_key = %s", str_replace('_' . piklist::$prefix, '', $group_key)));

        if ($key)
        {
          self::$grouped_meta_keys[$meta_type] = $group_key;
        }
      }
    }
  }
  
  /**
   * meta_reset
   * Insert description here
   *
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function meta_reset()
  {
    global $pagenow;

    /**
     * piklist_reset_meta_admin_pages
     * Insert description here
     *
     * 
     * @since 1.0
     */
    self::$reset_meta = apply_filters('piklist_reset_meta_admin_pages', self::$reset_meta);
    
    if (in_array($pagenow, self::$reset_meta))
    {
      foreach (self::$reset_meta as $page => $data)
      {
        if (isset($_REQUEST[$data['id']]))
        {
          wp_cache_replace($_REQUEST[$data['id']], false, $data['group']);
          
          break;
        }
      }
    }
  }
  
  /**
   * meta_sort
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
  public static function meta_sort($query) 
  {
    global $wpdb;
    
    if (stristr($query, ', meta_key, meta_value FROM'))
    {
      /**
       * piklist_meta_tables
       * Insert description here
       *
       * 
       * @since 1.0
       */
      $meta_tables = apply_filters('piklist_meta_tables', array(
        'post_id' => $wpdb->postmeta
        ,'comment_id' => $wpdb->commentmeta
      ));

      foreach ($meta_tables as $id => $meta_table)
      {
        if (stristr($query, "SELECT {$id}, meta_key, meta_value FROM {$meta_table} WHERE {$id} IN") && !stristr($query, ' ORDER BY '))
        {
          return $query . ' ORDER BY meta_id ASC';
        }
      }
    }
    
    return $query;
  }
  
  /**
   * get_post_meta
   * Insert description here
   *
   * @param $value
   * @param $object_id
   * @param $meta_key
   * @param $single
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function get_post_meta($value, $object_id, $meta_key, $single = false)
  {
    return self::get_metadata($value, 'post', $object_id, $meta_key, $single);
  }
  
  /**
   * get_user_meta
   * Insert description here
   *
   * @param $value
   * @param $object_id
   * @param $meta_key
   * @param $single
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function get_user_meta($value, $object_id, $meta_key, $single = false)
  {
    return self::get_metadata($value, 'user', $object_id, $meta_key, $single);
  }
  
  /**
   * get_term_meta
   * Insert description here
   *
   * @param $value
   * @param $object_id
   * @param $meta_key
   * @param $single
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function get_term_meta($value, $object_id, $meta_key, $single = false)
  {
    return self::get_metadata($value, 'term', $object_id, $meta_key, $single);
  }
  
  /**
   * get_metadata
   * Insert description here
   *
   * @param $value
   * @param $meta_type
   * @param $object_id
   * @param $meta_key
   * @param $single
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function get_metadata($value, $meta_type, $object_id, $meta_key, $single)
  {
    global $wpdb;

    $meta_key = '_' . piklist::$prefix . $meta_key;
    
    if (is_array(self::$grouped_meta_keys[$meta_type]) && in_array($meta_key, self::$grouped_meta_keys[$meta_type]))
    {
      remove_filter('get_post_metadata', array('piklist_meta', 'get_post_meta'), 100);
      remove_filter('get_user_metadata', array('piklist_meta', 'get_user_meta'), 100);
      remove_filter('get_term_metadata', array('piklist_meta', 'get_term_meta'), 100);
      
      if (($meta_ids = get_metadata($meta_type, $object_id, $meta_key)) && ($meta = self::get_meta_properties($meta_type)) !== false)
      {
        foreach ($meta_ids as &$group)
        {
          foreach ($group as &$meta_id)
          {
            $meta_id = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM " . $meta['table'] . " WHERE " . $meta['id_field'] . " = %d", $meta_id));
          }
        }
        
        $value = $meta_ids;
      }
      
      add_filter('get_post_metadata', array('piklist_meta', 'get_post_meta'), 100, 4);
      add_filter('get_user_metadata', array('piklist_meta', 'get_user_meta'), 100, 4);
      add_filter('get_term_metadata', array('piklist_meta', 'get_term_meta'), 100, 4);
    }
    
    return $value;
  }
  
  /**
   * get_meta_properties
   * Insert description here
   *
   * @param $meta_type
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function get_meta_properties($meta_type)
  {
    global $wpdb;
    
    switch ($meta_type)
    {
      case 'post':
        
        $meta = array(
          'table' => $wpdb->postmeta
          ,'id_field' => 'meta_id'
          ,'id' => 'post_id'
        );
      
      break;

      case 'term': 
      
        $meta = !isset($wpdb->termmeta) ? false : array(
          'table' => $wpdb->termmeta
          ,'id_field' => 'meta_id'
          ,'id' => 'term_id'
        );
      
      break;

      case 'user':
        
        $meta = array(
          'table' => $wpdb->usermeta
          ,'id_field' => 'umeta_id'
          ,'id' => 'user_id'
        );
      
      break;
      
      case 'comment':
        
        $meta = array(
          'table' => $wpdb->commentmeta
          ,'id_field' => 'meta_id'
          ,'id' => 'comment_id'
        );
      
      break;
    }
    
    return $meta;
  }
  
  /**
   * wp_save_post_revision_check_for_changes
   * Insert description here
   *
   * @param $meta_type
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function wp_save_post_revision_check_for_changes($check_for_changes, $last_revision, $post)
  {
    self::$wp_save_post_revision_check = true;
    
    return $check_for_changes;
  }

  /**
   * wp_save_post_revision_post_has_changed
   * Insert description here
   *
   * @param $meta_type
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function wp_save_post_revision_post_has_changed($post_has_changed, $last_revision, $post)
  {
    self::$wp_save_post_revision_check = false;

    return $post_has_changed;
  }

  /**
   * wp_save_post_revision_post_meta_serialize
   * Insert description here
   *
   * @param $meta_type
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function wp_save_post_revision_post_meta_serialize($value, $object_id, $meta_key, $single)
  {
    global $wpdb;
    
    if (self::$wp_save_post_revision_check)
    {
      $meta = self::get_meta_properties('post');
        
      $value = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM " . $meta['table'] . " WHERE " . $meta['id'] . " = %d AND meta_key = %s", $object_id, $meta_key));
      $value = maybe_serialize($value);
    }
    
    return $value;
  }
}