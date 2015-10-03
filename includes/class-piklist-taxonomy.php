<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Piklist_Taxonomy
 * Controls taxonomy modifications and features.
 *
 * @package     Piklist
 * @subpackage  Taxonomy
 * @copyright   Copyright (c) 2012-2015, Piklist, LLC.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
class Piklist_Taxonomy
{
  private static $meta_boxes;
    
  private static $taxonomies = array();
  
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
    add_action('piklist_activate', array('piklist_taxonomy', 'activate'));

    add_action('init', array('piklist_taxonomy', 'init'));
    add_action('registered_taxonomy',  array('piklist_taxonomy', 'registered_taxonomy'), 10, 3);
    add_action('admin_menu', array('piklist_taxonomy', 'admin_menu'));
    
    add_filter('terms_clauses', array('piklist_taxonomy', 'terms_clauses'), 10, 3);
    add_filter('get_terms_args', array('piklist_taxonomy', 'get_terms_args'), 0);
    add_filter('parent_file', array('piklist_taxonomy', 'parent_file'));
    add_filter('sanitize_user', array('piklist_taxonomy', 'restrict_username'));
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
    self::register_tables();
    self::register_meta_boxes();
  }
  
  /**
   * register_tables
   * Insert description here
   *
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function register_tables()
  {
    global $wpdb;
    
    $termmeta_table = $wpdb->prefix . 'termmeta';
    
    if ($wpdb->get_var("SHOW TABLES LIKE '{$termmeta_table}'") == $termmeta_table)
    {
      array_push($wpdb->tables, 'termmeta');
    
      $wpdb->termmeta = $wpdb->prefix . 'termmeta';      
    }
  }
  
  /**
   * terms_clauses
   * Insert description here
   *
   * @param array $pieces The pieces of the sql query
   * @param array $taxonomies The taxonomies for the query
   * @param array $arguments The arguments for the query
   *
   * @return array $pieces
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function terms_clauses($pieces = array(), $taxonomies = array(), $arguments = array()) 
  {
    if (!empty($arguments['meta_query'])) 
    {
      $query = new WP_Meta_Query($arguments['meta_query']);
      $query->parse_query_vars($arguments);

      if (!empty($query->queries)) 
      {
        $clauses = $query->get_sql('term', 'tt', 'term_id', $taxonomies);
        
        $pieces['join'] .= $clauses['join'];
        $pieces['where'] .= $clauses['where'];
      }
    }
    
    return $pieces;
  }
  
  /**
   * get_terms_args
   * Insert description here
   *
   * @param array $arguments The arguments for the query
   *
   * @return array $arguments
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function get_terms_args($arguments = array()) 
  {
    return wp_parse_args($arguments, array(
      'meta_query' => ''
    ));
  }
  
  /**
   * register_meta_boxes
   * Register term sections.
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
              ,'taxonomy' => 'Taxonomy'
              ,'new' => 'New'
            );
            
    piklist::process_parts('terms', $data, array('piklist_taxonomy', 'register_meta_boxes_callback'));
  }

  /**
   * register_meta_boxes_callback
   * Handle the registration of a term section.
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
    extract($arguments);
    
    $taxonomies = empty($data['taxonomy']) ? get_taxonomies() : $data['taxonomy'];

    foreach ($taxonomies as $taxonomy)
    {
      $data['taxonomy'] = trim($taxonomy);

      if (!isset(self::$meta_boxes[$data['taxonomy']]))
      {
        self::$meta_boxes[$data['taxonomy']] = array();

        add_action($data['taxonomy'] . '_edit_form_fields', array('piklist_taxonomy', 'meta_box'), 10, 2);
      }
    
      foreach (self::$meta_boxes[$data['taxonomy']] as $key => $meta_box)
      {
        if ($id == $meta_box['id'])
        {
          unset(self::$meta_boxes[$data['taxonomy']][$key]);
        }
      }
      
      if (isset($order))
      {
        self::$meta_boxes[$data['taxonomy']][$order] = $arguments;
      }
      else
      {
        array_push(self::$meta_boxes[$data['taxonomy']], $arguments);
      }
    }
  }
  
  /**
   * meta_box_add
   * Insert description here
   *
   * @param $taxonomy
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function meta_box_add($taxonomy)
  {
    self::meta_box(null, $taxonomy);
  }
  
  /**
   * meta_box
   * Insert description here
   *
   * @param $tag
   * @param $taxonomy
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function meta_box($tag = null, $taxonomy)
  {
    if ($taxonomy)
    {
      foreach (self::$meta_boxes[$taxonomy] as $taxonomy => $meta_box)
      {
        piklist::render('shared/meta-box-seperator', array(
          'meta_box' => $meta_box
          ,'wrapper' => 'term_meta'
        ), false);
                
        foreach ($meta_box['render'] as $render)
        {
          piklist::render($render, array(
            'taxonomy' => $taxonomy
            ,'data' => $meta_box['data']
          ), false);
        }
      }
    }
  }
  
  /**
   * activate
   * Creates custom tables
   *
   * @param $network_wide
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function activate($network_wide)
  {
    $table = piklist::create_table(
      'termmeta'
      ,'meta_id bigint(20) unsigned NOT NULL auto_increment
        ,term_id bigint(20) unsigned NOT NULL default "0"
        ,meta_key varchar(255) default NULL
        ,meta_value longtext
        ,PRIMARY KEY (meta_id)
        ,KEY term_id (term_id)
        ,KEY meta_key (meta_key)'
      ,$network_wide
   );
  }
  
  /**
   * registered_taxonomy
   * Insert description here
   *
   * @param $taxonomy
   * @param $object_type
   * @param $arguments
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function registered_taxonomy($taxonomy, $object_type, $arguments) 
  {
    global $wp_taxonomies;
    
    if ($object_type == 'user')
    {
      $arguments  = (object) $arguments;

      add_filter("manage_edit-{$taxonomy}_columns",  array('piklist_taxonomy', 'user_taxonomy_column'));
      
      add_action("manage_{$taxonomy}_custom_column",  array('piklist_taxonomy', 'user_taxonomy_column_value'), 10, 3);

      if (empty($arguments->update_count_callback)) 
      {
        $arguments->update_count_callback  = array('piklist_taxonomy', 'user_update_count');
      }

      $wp_taxonomies[$taxonomy]  = $arguments;
      self::$taxonomies[$taxonomy] = $arguments;
    }
  }
  
  /**
   * user_update_count
   * Insert description here
   *
   * @param $terms
   * @param $taxonomy
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function user_update_count($terms, $taxonomy) 
  {
    global $wpdb;
    
    foreach ($terms as $term) 
    {
      $count  = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d", $term));
      
      do_action('edit_term_taxonomy', $term, $taxonomy);
      
      $wpdb->update($wpdb->term_taxonomy, compact('count'), array(
        'term_taxonomy_id' => $term
      ));
      
      do_action('edited_term_taxonomy', $term, $taxonomy);
    }
  }
  
  /**
   * admin_menu
   * Insert description here
   *
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function admin_menu() 
  {
    $taxonomies  = self::$taxonomies;
  
    ksort(self::$taxonomies);
    
    foreach (self::$taxonomies as $slug => $taxonomy)
    {
      add_users_page($taxonomy->labels->menu_name, $taxonomy->labels->menu_name, $taxonomy->cap->manage_terms, 'edit-tags.php?taxonomy=' . $slug);
    }
  }

  /**
   * parent_file
   * Insert description here
   *
   * @param $file
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function parent_file($file = '') 
  {
    global $pagenow;
    
    if (!empty($_REQUEST['taxonomy']) && isset(self::$taxonomies[$_REQUEST['taxonomy']]) && $pagenow == 'edit-tags.php') 
    {
      return 'users.php';
    }
    
    return $file;
  }
  
  /**
   * user_taxonomy_column
   * Add a 'Users' column header to all user taxonomy edit pages.
   *
   * @param $columns
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function user_taxonomy_column($columns) 
  {
    $columns['users']  = __('Users', 'piklist');

    unset($columns['posts']);
  
    return $columns;
  }
  
  /**
   * user_taxonomy_column_value
   * Adds term data to 'Users' column on all user taxonomy edit pages.
   *
   * @param $display
   * @param $column
   * @param $term_id
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function user_taxonomy_column_value($display, $column, $term_id) 
  {
    switch ($column)
    {
      case 'users':
      
        $term  = get_term($term_id, $_REQUEST['taxonomy']);
        
        echo $term->count;
        
      break;
    }
  }
  
  /**
   * restrict_username
   * Insert description here
   *
   * @param $username
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function restrict_username($username) 
  {
    if (isset(self::$taxonomies[$username]))
    {
      return '';
    }
    
    return $username;
  }  

  /**
   * redirect
   * Insert description here
   *
   * @param $location
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function redirect($location)
  {
    $url = parse_url($location);
    parse_str($url['query'], $url_defaults);
    
    if (stristr($url['path'], 'edit-tags.php') && isset($url_defaults['taxonomy']) && isset($url_defaults['message']))
    {
      $location .= '&action=edit&tag_ID=' . (int) $_POST['tag_ID'];
    }

    return $location;
  }
}

if (!function_exists('add_term_meta'))
{
  /**
   * Add meta data field to a term.
   *
   * post meta data is called "Custom Fields" on the Administration Screen.
   *
   * @param int $term_id post ID.
   * @param string $meta_key Metadata name.
   * @param mixed $meta_value Metadata value.
   * @param bool $unique Optional, default is false. Whether the same key should not be added.
   * @return bool False for failure. True for success.
   */
  function add_term_meta($term_id, $meta_key, $meta_value, $unique = false) 
  {
    return add_metadata('term', $term_id, $meta_key, $meta_value, $unique);
  }
}

if (!function_exists('delete_term_meta'))
{
  /**
   * Remove metadata matching criteria from a term.
   *
   * You can match based on the key, or key and value. Removing based on key and
   * value, will keep from removing duplicate metadata with the same key. It also
   * allows removing all metadata matching key, if needed.
   *
   * @param int $term_id term ID
   * @param string $meta_key Metadata name.
   * @param mixed $meta_value Optional. Metadata value.
   * @return bool False for failure. True for success.
   */
  function delete_term_meta($term_id, $meta_key, $meta_value = '') 
  {
    return delete_metadata('term', $term_id, $meta_key, $meta_value);
  }
}  

if (!function_exists('get_term_meta'))
{
  /**
   * Retrieve term meta field for a term.
   *
   * @param int $term_id post ID.
   * @param string $key Optional. The meta key to retrieve. By default, returns data for all keys.
   * @param bool $single Whether to return a single value.
   * @return mixed Will be an array if $single is false. Will be value of meta data field if $single is true.
   */
  function get_term_meta($term_id, $key = '', $single = false) 
  {
    return get_metadata('term', $term_id, $key, $single);
  }
}

if (!function_exists('update_term_meta'))
{
  /**
   * Update term meta field based on term ID.
   *
   * Use the $prev_value parameter to differentiate between meta fields with the
   * same key and term ID.
   *
   * If the meta field for the term does not exist, it will be added.
   *
   * @param int $term_id post ID.
   * @param string $meta_key Metadata key.
   * @param mixed $meta_value Metadata value.
   * @param mixed $prev_value Optional. Previous value to check before removing.
   * @return bool False on failure, true if success.
   */
  function update_term_meta($term_id, $meta_key, $meta_value, $prev_value = '') 
  {
    return update_metadata('term', $term_id, $meta_key, $meta_value, $prev_value);
  }
}

if (!function_exists('delete_term_meta_by_key'))
{
  /**
   * Delete everything from term meta matching meta key.
   *
   * @param string $term_meta_key Key to search for when deleting.
   * @return bool Whether the term meta key was deleted from the database
   */
  function delete_term_meta_by_key($term_meta_key) 
  {
    return delete_metadata('term', null, $term_meta_key, '', true);
  }
}

if (!function_exists('get_term_custom'))
{
  /**
   * Retrieve all term meta fields, based on term ID.
   *
   * The term meta fields are retrieved from the cache where possible,
   * so the function is optimized to be called more than once.
   *
   * @param int $term_id post ID.
   * @return array
   */
  function get_term_custom($term_id = 0) 
  {
    $term_id = absint($term_id);

    return !$term_id ? null : get_term_meta($term_id);
  }
}

if (!function_exists('get_term_custom_keys'))
{
  /**
   * Retrieve meta field names for a term.
   *
   * If there are no meta fields, then nothing (null) will be returned.
   *
   * @param int $term_id term ID
   * @return array|null Either array of the keys, or null if keys could not be retrieved.
   */
  function get_term_custom_keys($term_id = 0) 
  {
    $custom = get_term_custom($term_id);

    if (!is_array($custom))
    {
      return;
    }
  
    if ($keys = array_keys($custom))
    {
      return $keys;
    }
  }
}

if (!function_exists('get_term_custom_values'))
{
  /**
   * Retrieve values for a custom term field.
   *
   * The parameters must not be considered optional. All of the term meta fields
   * will be retrieved and only the meta field key values returned.
   *
   * @param string $key Meta field key.
   * @param int $term_id post ID
   * @return array Meta field values.
   */
  function get_term_custom_values($key = '', $term_id = 0) 
  {
    if (!$key)
    {
      return null;
    }
  
    $custom = get_term_custom($term_id);

    return isset($custom[$key]) ? $custom[$key] : null;
  }
}