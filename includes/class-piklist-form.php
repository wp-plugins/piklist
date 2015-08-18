<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Piklist_Form
 * Insert description here
 *
 * @package     Piklist
 * @subpackage  Form
 * @copyright   Copyright (c) 2012-2015, Piklist, LLC.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
class Piklist_Form
{
  private static $templates = array();

  private static $template_shortcodes = array(
    'field_wrapper'
    ,'field_label'
    ,'field'
    ,'field_description_wrapper'
    ,'field_description'
  );
  
  private static $scopes = array(
    'post' => array(
      'ID'
      ,'menu_order'
      ,'comment_status'
      ,'ping_status'
      ,'pinged'
      ,'post_author'
      ,'post_category'
      ,'post_content'
      ,'post_date'
      ,'post_date_gmt'
      ,'post_excerpt'
      ,'post_name'
      ,'post_parent'
      ,'post_password'
      ,'post_status'
      ,'post_title'
      ,'post_type'
      ,'tags_input'
      ,'to_ping'
      ,'tax_input'
    )
    ,'post_meta' => array()
    ,'comment' => array(
      'comment_post_ID'
      ,'comment_author'
      ,'comment_author_email'
      ,'comment_author_url'
      ,'comment_content'
      ,'comment_type'
      ,'comment_parent'
      ,'user_id'
      ,'comment_author_IP'
      ,'comment_agent'
      ,'comment_date'
      ,'comment_approved'
    )
    ,'comment_meta' => array()
    ,'user' => array(
      'ID'
      ,'user_pass'
      ,'user_login'
      ,'user_nicename'
      ,'user_url'
      ,'user_email'
      ,'display_name'
      ,'nickname'
      ,'first_name'
      ,'last_name'
      ,'description'
      ,'rich_editing'
      ,'user_registered'
      ,'role'
      ,'user_role'
      ,'jabber'
      ,'aim'
      ,'yim'
    )
    ,'user_meta' => array()
    ,'taxonomy' => array()
    ,'term_meta' => array()
  );
  
  public static $field_list_types = array(
    'multiple_fields' => array(
      'checkbox'
      ,'radio'
      ,'add-ons'
      ,'file'
      ,'add-ons'
    )
    ,'multiple_value' => array(
      'checkbox'
      ,'file'
      ,'select'
      ,'add-ons'
    )
  );
  
  private static $field_alias = array(
    'datepicker' => 'text'
    ,'timepicker' => 'text'
    ,'colorpicker' => 'text'
    ,'password' => 'text'
    ,'color' => 'text'
    ,'date' => 'text'
    ,'datetime' => 'text'
    ,'datetime-local' => 'text'
    ,'email' => 'text'
    ,'month' => 'text'
    ,'range' => 'text'
    ,'search' => 'text'
    ,'tel' => 'text'
    ,'time' => 'text'
    ,'url' => 'text'
    ,'week' => 'text'
    ,'submit' => 'button'
    ,'reset' => 'button'
  );
  
  private static $field_assets = array(
    'colorpicker' => array(
      'callback' => array('piklist_form', 'render_field_custom_assets')
    )
    ,'datepicker' => array(
      'scripts' => array(
        'jquery-ui-datepicker'
      )
    )
    ,'editor' => array(
      'styles' => array(
        'editor-buttons'
      )
    )
  );
  
  private static $fields = null;

  private static $field_ids = array();
  
  private static $fields_defaults = array();
  
  private static $fields_rendered = array();
    
  private static $field_rendering = null;
  
  private static $field_types_rendered = array();
  
  private static $field_wrapper_ids = array();

  private static $form_id = null;
    
  private static $current_form_id = null;
    
  private static $form_saved = false;
  
  private static $forms = array();
  
  private static $nonce = false;
  
  public static $field_editor_settings = array(
    'tiny_mce' => ''
    ,'quicktags' => ''
  );
    
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
    add_action('wp_loaded', array('piklist_form', 'wp_loaded'), 100);
    add_action('post_edit_form_tag', array('piklist_form', 'add_enctype'));
    add_action('user_edit_form_tag', array('piklist_form', 'add_enctype'));
    add_action('init', array('piklist_form', 'save_fields_actions'), 100);
    add_action('wp_ajax_piklist_form', array('piklist_form', 'ajax'));
    add_action('wp_ajax_nopriv_piklist_form', array('piklist_form', 'ajax'));
    add_action('admin_footer', array('piklist_form', 'render_field_assets'));
    add_action('wp_footer', array('piklist_form', 'render_field_assets'));
    add_action('customize_controls_print_footer_scripts', array('piklist_form', 'render_field_assets'));

    add_action('piklist_notices', array('piklist_form', 'admin_notices'));

    if (is_admin())
    {
      add_action('admin_enqueue_scripts', 'wp_enqueue_media');
    }
    
    add_filter('teeny_mce_before_init', array('piklist_form', 'tiny_mce_settings'), 100, 2);
    add_filter('tiny_mce_before_init', array('piklist_form', 'tiny_mce_settings'), 100, 2);
    add_filter('quicktags_settings', array('piklist_form', 'quicktags_settings'), 100, 2);
    add_filter('piklist_field_templates', array('piklist_form', 'field_templates'), 0);
  }
  
  /**
   * wp_loaded
   * Insert description here
   *
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function wp_loaded()
  {
    global $pagenow;

    /**
     * piklist_field_templates
     * Add custom field templates.
     *
     * 
     * @since 1.0
     */
    self::$templates = apply_filters('piklist_field_templates', self::$templates);
    
    foreach (self::$template_shortcodes as $template_shortcode)
    {
      add_shortcode($template_shortcode, array('piklist_form', 'template_shortcode'));
    }
    
    if (in_array($pagenow, array('widgets.php', 'customize.php')))
    {
      if (!class_exists('_WP_Editors'))
      {
        require(ABSPATH . WPINC . '/class-wp-editor.php');
      }
      
      add_action('admin_print_footer_scripts', array('_WP_Editors', 'editor_js'), 50);
      add_action('admin_footer', array('piklist_form', 'editor_proxy'));
    }
    
    self::check_nonce();
    
    self::process_form();
  }
  
  /**
   * get
   * Insert description here
   *
   * @param $variable
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function get($variable)
  {
    return isset(self::$$variable) ? self::$$variable : false;
  }
  
  /**
   * valid
   * Insert description here
   *
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function valid()
  {
    return self::$nonce;
  }
  
  /**
   * check_nonce
   * Insert description here
   *
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function check_nonce()
  {
    if (isset($_REQUEST[piklist::$prefix]['nonce']))
    {
      self::$nonce = wp_verify_nonce($_REQUEST[piklist::$prefix]['nonce'], 'piklist-' . $_REQUEST[piklist::$prefix]['fields_id']);
    }
  }
  
  /**
   * editor_proxy
   * Insert description here
   *
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function editor_proxy()
  {
    piklist::render('shared/editor-proxy');
  }
  
  /**
   * field_templates
   * Define field layouts for each section.
   *
   * @param $templates
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function field_templates($templates)
  {
    return array(
      'field'  => array(
        'name' => __('Field only', 'piklist')
        ,'description' => __('Displays field with no label.', 'piklist')
        ,'template' => '[field]'
      )
      ,'default' => array(
        'name' => __('Default', 'piklist')
        ,'description' => __('Default field layout.', 'piklist')
        ,'template' => '[field]
                        [field_description_wrapper]
                          <p class="description">[field_description]</p>
                        [/field_description_wrapper]'
      )
      ,'widget' => array(
        'name' => __('Widget', 'piklist')
        ,'description' => __('Default layout for Widget fields.', 'piklist')
        ,'template' => '[field_wrapper]
                          <div id="%1$s" class="%2$s piklist-field-container">
                            <div class="piklist-field-container-row">
                              <div class="piklist-label-container">
                                [field_label]
                              </div>
                              <div class="piklist-field">
                                [field]
                                [field_description_wrapper]
                                  <span class="piklist-field-description description">[field_description]</span>
                                [/field_description_wrapper]
                              </div>
                            </div>
                          </div>
                        [/field_wrapper]'
      )
      ,'widget_classic' => array(
        'name' => __('Widget (classic)', 'piklist')
        ,'description' => __('Classic layout for Widget fields.', 'piklist')
        ,'template' => '[field_wrapper]
                          <p id="%1$s" class="%2$s">
                            [field_label]
                            [field]
                            [field_description_wrapper]
                              <small>[field_description]</small>
                            [/field_description_wrapper]
                          </p>
                        [/field_wrapper]'
      )
      ,'post_meta' => array(
        'name' => __('Post', 'piklist')
        ,'description' => __('Default layout for Post fields.', 'piklist')
        ,'template' => '[field_wrapper]
                        <div id="%1$s" class="%2$s piklist-field-container">
                          <div class="piklist-label-container">
                            [field_label]
                            [field_description_wrapper]
                              <p class="piklist-field-description description">[field_description]</p>
                            [/field_description_wrapper]
                          </div>
                          <div class="piklist-field">
                            [field]
                          </div>
                        </div>
                       [/field_wrapper]'
      )
      ,'term_meta' => array(
        'name' => __('Terms', 'piklist')
        ,'description' => __('Default layout for Term fields.', 'piklist')
        ,'template' => '<table class="form-table">
                          [field_wrapper]
                          <tr>
                            <th scope="row" class="left">
                              [field_label]
                            </th>
                            <td>
                              [field]
                              [field_description_wrapper]
                                <p class="piklist-field-description description">[field_description]</p>
                              [/field_description_wrapper]
                            </td>
                          </tr>
                          [/field_wrapper]
                        </table>'
      )
      ,'user_meta' => array(
        'name' => __('User', 'piklist')
        ,'description' => __('Default layout for User fields.', 'piklist')
        ,'template' => '<table class="form-table">
                          [field_wrapper]
                          <tr>
                            <th scope="row">
                              [field_label]
                            </th>
                            <td>
                              [field]
                              [field_description_wrapper]
                                <p class="piklist-field-description description">[field_description]</p>
                              [/field_description_wrapper]
                            </td>
                          </tr>
                          [/field_wrapper]
                        </table>'
      )
      ,'shortcode' => array(
        'name' => __('Shortcode', 'piklist')
        ,'description' => __('Default layout for Shortcode fields.', 'piklist')
        ,'template' => '[field_wrapper]
                        <div id="%1$s" class="%2$s piklist-field-container">
                          <div class="piklist-label-container">
                            [field_label]
                            [field_description_wrapper]
                              <p class="piklist-field-description description">[field_description]</p>
                            [/field_description_wrapper]
                          </div>
                          <div class="piklist-field">
                            [field]
                          </div>
                        </div>
                       [/field_wrapper]'
      )
      ,'media_meta' => array(
        'name' => __('Media', 'piklist')
        ,'description' => __('Default layout for Media fields.', 'piklist')
        ,'template' => '</td></tr>
                          [field_wrapper]
                          <tr>
                             <th valign="top" scope="row" class="label">
                             [field_label]
                            </th>
                            <td>
                              [field]
                              [field_description_wrapper]
                                <p class="piklist-field-description description">[field_description]</p>
                              [/field_description_wrapper]
                            </td>
                          </tr>
                          [/field_wrapper]'
      )
      ,'theme' => array(
        'name' => __('Theme', 'piklist')
        ,'description' => __('Default layout for frontend fields.', 'piklist')
        ,'template' => '[field_wrapper]
                          <div id="%1$s" class="%2$s piklist-theme-field-container">
                            <div class="piklist-theme-label">
                              [field_label]
                            </div>
                            <div class="piklist-theme-field">
                              [field]
                              [field_description_wrapper]
                                <p class="piklist-field-description description">[field_description]</p>
                              [/field_description_wrapper]
                            </div>
                          </div>
                        [/field_wrapper]'
      )
      ,'admin_notice' => array(
        'name' => __('Admin Notice', 'piklist')
        ,'description' => __('Default layout for Admin Notices.', 'piklist')
        ,'template' => '[field_wrapper]
                          <div id="%1$s" class="%2$s piklist-field-container piklist-admin-notice">
                            <p>
                              [field]
                            </p>
                          </div>
                        [/field_wrapper]'
      )
      ,'admin_notice_error' => array(
        'name' => __('Admin Error Notice', 'piklist')
        ,'description' => __('Default layout for Admin Error Notices.', 'piklist')
        ,'template' => '[field_wrapper]
                          <div id="%1$s" class="%2$s piklist-field-container piklist-admin-notice piklist-admin-notice-error">
                            <p>
                              [field]
                            </p>
                          </div>
                        [/field_wrapper]'
      )
    );
  }
  
  /**
   * get_field_id
   * Insert description here
   *
   * @param $field
   * @param $scope
   * @param $index
   * @param $prefix
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function get_field_id($field, $scope = false, $index = false, $prefix = true)
  {
    if (!$field)
    {
      return false;
    }

    $prefix = $scope && $prefix ? piklist::$prefix : null;
    
    if (piklist_admin::is_widget() && (!$scope || ($scope && ($scope != piklist::$prefix && $field != 'fields_id'))) && piklist_widget::widget())
    {
      $id = $prefix . piklist_widget::widget()->get_field_id(str_replace(':', '_', $field));
    }
    else
    {
      $id = $prefix . ($scope && $scope != piklist::$prefix ? $scope . '_' : null) . str_replace(':', '_', $field) . (is_numeric($index) ? '_' . $index : null);
    }
    
    if (isset(self::$fields_rendered[$scope][$field]))
    {
      self::$fields_rendered[$scope][$field]['id'] = $id;
    }

    return $id;
  }
  
  /**
   * get_field_name
   * Insert description here
   *
   * @param $field
   * @param $scope
   * @param $index
   * @param $prefix
   * @param $multiple
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function get_field_name($field, $scope, $index = false, $prefix = true, $multiple = false)
  {
    if (!$field)
    {
      return false;
    }

    $prefix = !in_array($scope, array(piklist::$prefix, false)) && $prefix ? piklist::$prefix : null;
    
    if (piklist_admin::is_widget() && (!$scope || ($scope && ($scope != piklist::$prefix && $field != 'fields_id'))) && piklist_widget::widget())
    {
      $name = $prefix . piklist_widget::widget()->get_field_name(str_replace(':', '][', $field)) . ($multiple && is_numeric($index) ? '[' . $index . ']' : null) . '[]';
    }
    else
    {
      $name = $prefix . ($scope ? $scope . (piklist_admin::is_media() && isset($GLOBALS['piklist_attachment']) ? '_' . $GLOBALS['piklist_attachment']->ID : '') . '[' : null) . str_replace(':', '][', $field) . ($scope ? ']' : null) . ($multiple && is_numeric($index) ? '[' . $index . ']' : null) . ($multiple || ($scope && $scope != piklist::$prefix) ? '[]' : null); 
    }
    
    if (isset(self::$fields_rendered[$scope][$field]))
    {
      self::$fields_rendered[$scope][$field]['name'] = $name;
    }
    
    return $name;
  }
  
  /**
   * get_field_object_id
   * Insert description here
   *
   * @param $field
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function get_field_object_id($field)
  {
    global $post, $tag_ID, $current_user, $wp_taxonomies, $pagenow, $user_id;
    
    $id = null;
    
    switch ($field['scope'])
    {
      case 'comment':
      case 'post_meta':
      case 'taxonomy':
      
        if (isset($wp_taxonomies[$field['field']]) && isset($wp_taxonomies[$field['field']]->object_type) && $wp_taxonomies[$field['field']]->object_type[0] == 'user')
        {
          if (isset($_REQUEST[piklist::$prefix . 'user']['ID']) && self::valid())
          {
            $id = (int) $_REQUEST[piklist::$prefix . 'user']['ID'][0];
          }
          elseif ($pagenow == 'user-edit.php')
          {
            $id = $user_id;
          }
          elseif (is_user_logged_in())
          {
            $id = $current_user->ID;
          }
        }
        else
        {        
          if (isset($GLOBALS['piklist_attachment']))
          {
            $id = $GLOBALS['piklist_attachment']->ID;
          }
          else
          {
            if (isset($_REQUEST[piklist::$prefix . 'post']['ID']))
            {
              $id = (int) $_REQUEST[piklist::$prefix . 'post']['ID'];
            }
            elseif (is_admin() && $post)
            {
              $id = $post->ID;
            }
          }
        }

      break;

      case 'term_meta':

        $id = $tag_ID;
        
      break;
    
      case 'user_meta':
            
        if (isset($_REQUEST[piklist::$prefix . 'user']['ID']))
        {
          $id = (int) $_REQUEST[piklist::$prefix . 'user']['ID'];
        }
        elseif ($pagenow == 'user-edit.php')
        {
          $id = $user_id;
        }
        elseif (is_user_logged_in())
        {
          $id = $current_user->ID;
        }
      
      break;

      case 'post':
        
        if ($field['field'] == 'ID' && !empty($field['value']))
        {
          $id = $field['value'];
        }
      
        if (isset($_REQUEST[piklist::$prefix . 'post']['ID']))
        {
          $id = (int) $_REQUEST[piklist::$prefix . 'post']['ID'];
        }
        elseif (is_admin() && $post)
        {
          $id = $post->ID;
        }
      
      break;
      
      case 'user':
        
        if ($field['field'] == 'ID' && !empty($field['value']))
        {
          $id = $field['value'];
        }
        
        if (isset($_REQUEST[piklist::$prefix . 'user']['ID']))
        {
          $id = (int) $_REQUEST[piklist::$prefix . 'user']['ID'];
        }
        elseif ($pagenow == 'user-edit.php')
        {
          $id = $user_id;
        }
        elseif (is_user_logged_in())
        {
          $id = $current_user->ID;
        }

      break;
    }
    
    if (!isset(self::$field_ids[$field['scope']]))
    {
      self::$field_ids[$field['scope']] = array();
    }
    
    if (!in_array($id, self::$field_ids[$field['scope']]))
    {
      array_push(self::$field_ids[$field['scope']], $id);
    }

    return $id;
  }
  
  /**
   * get_field_value
   * Insert description here
   *
   * @param $scope
   * @param $field
   * @param $type
   * @param $id
   * @param $unique
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function get_field_value($scope, $field, $type = 'option', $id = false, $unique = false)
  {
    global $wpdb;
    
    $key = isset($field['field']) ? $field['field'] : null;
    $prefix = !in_array($scope, array(piklist::$prefix, false)) ? piklist::$prefix : null;
    $type = isset(self::$scopes[$type]) ? $type : 'option';
    
    if (!$id)
    {
      if (isset($_REQUEST[piklist::$prefix . $key]))
      {
        return $_REQUEST[piklist::$prefix . $key];
      }
      elseif (isset($_REQUEST[piklist::$prefix . $scope][$key]))
      {
        return $_REQUEST[piklist::$prefix . $scope][$key];
      }
      elseif (isset($_REQUEST[$scope][$key]))
      {
        return $_REQUEST[$scope][$key];
      }
    }
    
    if ($id || $type == 'option')
    {
      switch ($type)
      {
        case 'post':
        
          $object = get_post($id);

          if (!is_wp_error($object) && is_object($object) && $object->post_status != 'auto-draft')
          {
            $attribute = property_exists($object, $field['field']) ? $object->$field['field'] : (isset($field['value']) ? $field['value'] : false);
            
            return $attribute;
          }
      
        break;
        
        case 'user':
        
          $object = get_userdata($id);

          if (!is_wp_error($object))
          {
            $attribute = property_exists($object->data, $field['field']) ? $object->data->$field['field'] : (isset($field['value']) ? $field['value'] : false);
          
            return $attribute;
          }
          
        break;
      
        case 'option':
    
          $options = get_option($scope);
        
          $keys = stristr($key, ':') ? explode(':', $key) : false;
        
          if (stristr($key, ':'))
          {
            $value = piklist::array_path_get($options, explode(':', $key));
          }
          else
          {
            $value = isset($options[$key]) ? $options[$key] : (isset($field['value']) ? $field['value'] : false);
          }
        
          return $value;
        
        break;
    
        case 'taxonomy':

          $key = is_string($field['save_as']) ? $field['save_as'] : $key;
          
          /**
           * piklist_taxonomy_value_key
           * Insert description here
           *
           * 
           * @since 1.0
           */
          $terms = piklist(wp_get_object_terms($id, $key), apply_filters('piklist_taxonomy_value_key', 'term_id', $key));

          /**
           * piklist_taxonomy_value
           * Insert description here
           *
           * 
           * @since 1.0
           */
          $terms = apply_filters('piklist_taxonomy_value', $terms, $id, $key, $field);

          return !empty($terms) ? $terms : false;
      
        break;
          
        case 'post_meta':
        case 'term_meta': 
        case 'user_meta': 

          $meta_type = substr($type, 0, strpos($type, '_'));
          
          if ($key)
          {
            $meta_key = strstr($key, ':') ? substr($key, 0, strpos($key, ':')) : $key;
            $meta_key = isset($field['save_as']) && is_string($field['save_as']) ? $field['save_as'] : $meta_key;
          }
          else
          {
            $meta_key = $scope;
          }

          if (isset($field['multiple']) && $field['multiple'])
          {
            switch ($type)
            {
              case 'post_meta':
            
                $meta_table = $wpdb->postmeta;
                $meta_id_field = 'meta_id';
                $meta_id = 'post_id';
            
              break;

              case 'term_meta': 
            
                $meta_table = $wpdb->termmeta;
                $meta_id_field = 'meta_id';
                $meta_id = 'term_id';
            
              break;

              case 'user_meta':
            
                $meta_table = $wpdb->usermeta;
                $meta_id_field = 'umeta_id';
                $meta_id = 'user_id';
            
              break;
            }

            $keys = $wpdb->get_results($wpdb->prepare("SELECT {$meta_id_field} FROM $meta_table WHERE meta_key = %s AND $meta_id = %d", $meta_key, $id));
            $unique = count($keys) == 1 ? true : $unique;
          }
        
          $meta = get_metadata($meta_type, $id, $meta_key, $unique);

          if (strstr($key, ':'))
          {
            $meta = isset($meta[$meta_key]) ? $meta[$meta_key] : null;
          }
        
          if ($meta != 0)
          {
            if (metadata_exists($meta_type, $id, $meta_key) && !$meta)
            {
              $meta = array();
            }
            elseif (!metadata_exists($meta_type, $id, $meta_key))
            {
              if (isset($field['value']))
              {
                $meta = $field['value'];
              }
              else
              {
                $meta = null;
              }
            }
          }
          
          return $meta;
      
        break;
      }
    }

    return isset($field['value']) ? $field['value'] : null;
  }
  
  /**
   * get_field_wrapper_id
   * Insert description here
   *
   * @param $field
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function get_field_wrapper_id($field)
  {
    $index = null;
    
    do {
      
      $id = piklist::$prefix . $field['field'] . ($index === null ? '' : '_' . $index);
      
      $index = $index === null ? 0 : $index + 1;
      
    } while (in_array($id, self::$field_wrapper_ids));
    
    array_push(self::$field_wrapper_ids, $id);
    
    return $id;
  }
  
  /**
   * get_field_template
   * Insert description here
   *
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function get_field_template($scope = null)
  {
    global $pagenow;

    if (!is_admin())
    {
      $wrapper = 'theme';
    }
    elseif (isset(self::$templates[$scope]))
    {
      $wrapper = $scope;
    }
    else
    {
      if (piklist_admin::is_post())
      {
        $wrapper = 'post_meta';
      }
      elseif (piklist_admin::is_media())
      {
        $wrapper = 'media_meta';
      }
      elseif (piklist_admin::is_widget())
      {
        $wrapper = 'widget';
      }
      elseif ($type = piklist_admin::is_term())
      {
        $wrapper = 'term_meta' . ($type == 'new' ? '_new' : '');
      }
      elseif (piklist_admin::is_user())
      {
        $wrapper = 'user_meta';
      }
      else
      {
        $wrapper = 'default';
      }
    }
    
    return $wrapper;
  }
  
  /**
   * get_field_scope
   * Insert description here
   *
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function get_field_scope()
  {
    global $pagenow;
    
    $scope = null;
    
    if (piklist_admin::is_post())
    {
      $scope = 'post_meta';
    }
    elseif (piklist_admin::is_media())
    {
      $scope = 'post_meta';
    }
    elseif (piklist_admin::is_term())
    {
      $scope = 'term_meta';
    }
    elseif (piklist_admin::is_user())
    {
      $scope = 'user_meta';
    }
    elseif ($pagenow == 'admin.php' && isset($_REQUEST['page']) && $_REQUEST['page'] == 'shortcode_editor')
    {
      $scope = 'shortcode';
    }
    
    return $scope;
  }
  
  /**
   * get_field_show_value
   * Insert description here
   *
   * @param $field
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function get_field_show_value($field)
  {
    extract($field);
    
    if (isset($value) && !empty($value))
    {
      switch ($type)
      {
        case 'radio':
        case 'checkbox':   
        case 'select':   
        
          $value = is_array($value) ? $value : array($value);
          $_value = array();
          foreach ($value as $v)
          {
            if (piklist::is_flat($value))
            {
              if (isset($choices[$v]))
              {
                array_push($_value, $choices[$v]);
              }
            }
            else
            {
              foreach ($v as $_v)
              {
                if (isset($choices[$_v]))
                {
                  array_push($_value, $choices[$_v]);
                }
              }
              array_push($_value, '');
            }
          }
          $value = $_value;

        break;
      }
    }
        
    return $value;
  }

  /**
   * add_enctype
   * Adds enctype to forms.
   *
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function add_enctype()
  {
    echo ' enctype="multipart/form-data" ';
  }
  
  /**
   * ajax
   * Insert description here
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
    switch ($_REQUEST['method'])
    {
      case 'field':
        
        if (isset($_REQUEST['field']))
        {
          $field = $_REQUEST['field'];

          array_walk_recursive($field, array('piklist', 'array_values_cast'));

          echo json_encode(array(
            'field' => self::render_field($field, true)
            ,'data' => $field
            ,'tiny_mce' => self::$field_editor_settings['tiny_mce']
            ,'quicktags' => self::$field_editor_settings['quicktags']  
          ));
        }
      
      break;
    }

    die;
  }
  
  /**
   * render_field
   * Insert description here
   *
   * @param $field
   * @param $return
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function render_field($field, $return = false)
  {
    $field = wp_parse_args($field, array(
      'field' => false
      ,'scope' => self::get_field_scope()
      ,'type' => 'text'
      ,'label' => false
      ,'description' => false
      ,'prefix' => true
      ,'value' => null
      ,'object_id' => null
      ,'relate' => false
      ,'capability' => null
      ,'role' => null
      ,'logged_in' => false
      ,'add_more' => false
      ,'sortable' => isset($field['sortable']) ? $field['sortable'] : (isset($field['add_more']) && is_bool($field['add_more']) && $field['add_more'] ? true : false)
      ,'choices' => false
      ,'list' => true
      ,'position' => false
      ,'template' => null
      ,'wrapper' => false
      ,'columns' => null
      ,'embed' => false
      ,'child_field' => false
      ,'label_position' => 'before'  
      ,'conditions' => array()
      ,'options' => false
      ,'on_post_status' => array()
      ,'display' => false
      ,'group_field' => false
      ,'required' => false
      ,'save_as' => false
      ,'index' => null
      ,'multiple' => false
      ,'errors' => false
      ,'validate' => array()
      ,'sanitize' => array()
      ,'attributes' => array(
        'class' => array()
      )
      ,'query' => array()
      ,'tax_query' => array()
      ,'meta_query' => array()
    ));

    /**
     * piklist_request_field
     * Filter the request field
     *
     * @param array $field 
     *
     * @since 1.0
     */
    $field = apply_filters('piklist_request_field', $field);

    /**
     * piklist_request_field_$scope_$field
     * Filter a specific request field
     *
     * The dynamic portions of the hook name, `$field['scope']` and `$field['field']`,
     * refer to the 'scope' and 'field' parameters, of an individual field.
     *
     * @param array $field 
     *
     * @since 1.0
     */
    $field = apply_filters('piklist_request_field_' . $field['scope'] . '_' . $field['field'], $field);
    
    // Validate field
    if (!self::validate_field($field) || ($field['embed'] && !$return) || empty($field))
    {
      return false;
    }
    
    // Set Object ID
    if (is_null($field['object_id']))
    {
      $field['object_id'] = self::get_field_object_id($field);
    }  
    
    // Set Template
    if (!$field['template'])
    {
      $field['template'] = self::get_field_template($field['scope']);
    }

    if (!in_array($field['type'], self::$field_types_rendered))
    {
      array_push(self::$field_types_rendered, $field['type']);
    }
    
    // Set Defaults
    array_push(self::$fields_defaults, $field);
    
    // Determine if its a multiple type field
    if (in_array($field['type'], self::$field_list_types['multiple_fields']) || (is_array($field['attributes']) && in_array('multiple', $field['attributes'])))
    {
      $field['multiple'] = true;
    }
    
    if ($field['type'] == 'html' && !isset($field['field']))
    {
      $field['field'] = piklist::unique_id();
    }
    
    // Manage Classes
    if (isset($field['attributes']['class']))
    {
      $field['attributes']['class'] = !is_array($field['attributes']['class']) ? explode(' ', $field['attributes']['class']) : $field['attributes']['class'];
    }
    else
    {
      $field['attributes']['class'] = array();
    }
    
    array_push($field['attributes']['class'], self::get_field_id($field['field'], $field['scope'], false, $field['prefix']));
    
    // Set Wrapper
    $wrapper = array(
      'id' => self::get_field_wrapper_id($field)
      ,'class' => array()
    );
    
    // Set Columns
    if (is_numeric($field['columns']) && !$field['child_field'])
    {
      array_push($wrapper['class'], 'piklist-field-type-group piklist-field-column-' . $field['columns']);
    }
    
    if (isset($field['attributes']['columns']) && is_numeric($field['attributes']['columns']))
    {
      array_push($field['attributes']['class'], 'piklist-field-column-' . $field['attributes']['columns']);
      unset($field['attributes']['columns']);
    }

    if (isset($field['attributes']['wrapper_class']))
    {
      array_push($wrapper['class'], $field['attributes']['wrapper_class']);
    }
    
    // Check Statuses - Legacy, these get mapped to conditions post_status_hide, post_status_value
    if (!empty($field['on_post_status']))
    {
      $object = !is_null($field['object_id']) ? get_post($field['object_id'], ARRAY_A) : (isset($GLOBALS['post']) ? (array) $GLOBALS['post'] : null);
      
      if ($object)
      {
        $status_list = isset($object['post_type']) ? piklist_cpt::get_post_statuses($object['post_type']) : array();
        foreach (array('value', 'hide') as $status_display)
        {
          if (isset($field['on_post_status'][$status_display]))
          {
            $field['on_post_status'][$status_display] = is_array($field['on_post_status'][$status_display]) ? $field['on_post_status'][$status_display] : array($field['on_post_status'][$status_display]);
            foreach ($field['on_post_status'][$status_display] as $_status)
            {
              if (strstr($_status, '--'))
              {
                $status_range = explode('--', $_status);
                $status_range_start = array_search($status_range[0], $status_list);
                $status_range_end = array_search($status_range[1], $status_list);

                if (is_numeric($status_range_start) && is_numeric($status_range_end))
                {
                  $status_slice = array();
                  for ($i = $status_range_start; $i <= $status_range_end; $i++)
                  {
                    array_push($status_slice, $status_list[$i]);
                  }
                            
                  array_splice($field['on_post_status'][$status_display], array_search($_status, $field['on_post_status'][$status_display]), 1, $status_slice);
                }
              }
            }
          }
        }
      }

      foreach ($field['on_post_status'] as $status_display => $statuses)
      {
        array_push($field['conditions'], array(
          'type' => 'post_status_' . $status_display
          ,'value' => $statuses
        )); 
      }
      
      unset($field['on_post_status']);
    }
          
    // Get errors
    $field['errors'] = piklist_validate::get_errors($field['field'], $field['scope']);

    // Get field value
    if (!$field['group_field'] && $field['value'] !== false && !in_array($field['type'], array('button', 'submit', 'reset')))
    {
      if (piklist_admin::is_widget())
      {
        $stored_value = isset(piklist_widget::widget()->instance[$field['field']]) ? maybe_unserialize(piklist_widget::widget()->instance[$field['field']]) : $field['value'];
      }
      else
      {
        $stored_value = self::get_field_value($field['scope'], $field, $field['scope'], $field['object_id'], false);    
      }
      
      if (piklist_validate::errors()) 
      {
        $stored_value = piklist_validate::get_request_value($field['field'], $field['scope']);
      }
      
      if (!isset($stored_value) && !isset($field['attributes']['placeholder']) && !$field['multiple'])
      {
        $field['attributes']['placeholder'] = htmlspecialchars($field['value']);
      }
      elseif (isset($stored_value) || (is_array($stored_value) && empty($stored_value)))
      {
        $field['value'] = $stored_value;
      }
    }
    
    // Check for nested fields
    if ($field['description'])
    {
      $field['description'] = self::render_nested_field($field, $field['description']);
    }
    
    if (is_array($field['choices']) && !in_array($field['type'], array('select', 'multiselect')))
    {
      foreach ($field['choices'] as &$choice)
      {
        $choice = self::render_nested_field($field, $choice);
      }
    }

    if (!empty($field['conditions']))
    {
      foreach ($field['conditions'] as &$condition)
      {
        if (is_array($condition))
        {          
          if (!isset($condition['type']) || empty($condition['type']))
          {
            $condition['type'] = 'toggle';
          }
          elseif (piklist_admin::is_post())
          {
            global $post;
            
            $condition['value'] = is_array($condition['value']) ? $condition['value'] : array($condition['value']);
            
            if (substr($condition['type'], 0, 12) == 'post_status_' && in_array($post->post_status, $condition['value']))
            {
              if ($condition['type'] == 'post_status_hide')
              {
                return false;
              }
              elseif ($condition['type'] == 'post_status_value')
              {
                $field['display'] = true;
              }
            }
          }

          if (isset($condition['field']))
          {
            $condition['scope'] = isset($condition['scope']) ? $condition['scope'] : $field['scope'];
            $condition['id'] = self::get_field_id($condition['field'], $condition['scope'], false, $field['prefix']);
            $condition['name'] = self::get_field_name($condition['field'], $condition['scope'], false, $field['prefix']);
            $condition['reset'] = isset($condition['reset']) ? $condition['reset'] : true;
                  
            if (!in_array('piklist-field-condition', $field['attributes']['class']))
            {
              if (!in_array('piklist-field-condition', $wrapper['class']))
              {
                array_push($wrapper['class'], 'piklist-field-condition');
              }
        
              if (!in_array('piklist-field-condition-' . $condition['type'], $wrapper['class']))
              {
                array_push($wrapper['class'], 'piklist-field-condition-' . $condition['type']);
              }
            }
          }
        }
      }
    }

    // Set the field template 
    if ($field['group_field'] && self::get_field_template($field['scope']) == $field['template'] && (strstr(self::$templates[$field['template']]['template'], '</tr>') || $field['template'] == 'default'))
    {
      $field['child_field'] = true;
      $field['template'] = 'field';
    }
    elseif ($field['type'] == 'hidden' || $field['embed'])
    {
      $field['template'] = 'field';
    }
    
    $field['wrapper'] = preg_replace(
      array(
        '/ {2,}/'
        ,'/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s'
      )
      ,array(
        ' '
        ,''
      )
      ,sprintf(self::$templates[$field['template']]['template'], $wrapper['id'], implode(' ', $wrapper['class']))
    );

    /**
     * piklist_pre_render_field
     * Filter the request field before it renders
     *
     * @param array $field 
     *
     * @since 1.0
     */
    $field = apply_filters('piklist_pre_render_field', $field);

    /**
     * piklist_pre_render_field_$scope_$field
     * Filter a specific request field before it renders
     *
     * The dynamic portions of the hook name, `$field['scope']` and `$field['field']`,
     * refer to the 'scope' and 'field' parameters, of an individual field.
     *
     * @param array $field 
     *
     * @since 1.0
     */
    $field = apply_filters('piklist_pre_render_field_' . $field['scope'] . '_' . $field['field'], $field);

    // Bail from rendering the field if its a display with no value or its already been rendered in this form
    if (!$field['group_field'] && (($field['display'] && empty($field['value']) && $field['type'] != 'group')))
    {
      return false;
    }
    
    self::$field_rendering = $field;
    
    self::$fields_rendered[$field['scope']][$field['field']] = $field;
    
    $field_to_render = self::template_tag_fetch('field_wrapper', $field['wrapper']);

    $rendered_field = do_shortcode($field_to_render);
    
    switch ($field['position'])
    {
      case 'start':
    
        $rendered_field = self::template_tag_fetch('field_wrapper', $field['wrapper'], 'start') . $rendered_field;
        
      break;
      
      case 'end':
      
        $rendered_field .= self::template_tag_fetch('field_wrapper', $field['wrapper'], 'end');
      
      break;
      
      case 'wrap':
      
        $rendered_field = self::template_tag_fetch('field_wrapper', $field['wrapper'], 'start') . $rendered_field . self::template_tag_fetch('field_wrapper', $field['wrapper'], 'end');
      
      break;
    }

    /**
     * piklist_post_render_field
     * Filter the request field after it renders
     *
     * @param array $field
     *
     * @return $rendered_field
     *
     * @since 1.0
     */
    $rendered_field = apply_filters('piklist_post_render_field', $rendered_field, $field);

    /**
     * piklist_post_render_field_$scope_$field
     * Filter a specific request field after it renders
     *
     * The dynamic portions of the hook name, `$field['scope']` and `$field['field']`,
     * refer to the 'scope' and 'field' parameters, of an individual field.
     *
     * @param array $field
     *
     * @return $rendered_field
     *
     * @since 1.0
     */
    $rendered_field = apply_filters('piklist_post_render_field_' . $field['scope'] . '_' . $field['field'], $rendered_field, $field);
      
    self::$field_rendering = null;

    // Return the field as requested
    if ($return)
    {
      return preg_replace('/[ \t]+/', ' ', preg_replace('/[\r\n]+/', '', $rendered_field));
    }
    else
    {
      echo $rendered_field;
    }
  }
  
  /**
   * validate_field
   * Check to see if a field should be rendered.
   *
   * @param array $parts Comment block data at the top of the view.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function validate_field($field)
  {
    foreach ($field as $parameter => $value)
    {
      if (!empty($value) && !is_null($value))
      {
        if (!self::validate_field_parameter($parameter, $value))
        {
          return false;
        }
      }
    }
    
    return true;
  }

  /**
   * validate_field_parameter
   * Check to see if the field parameter passes validation.
   *
   * @param string $parts The parameter name.
   * @param mixes $parts The parameter value.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function validate_field_parameter($parameter, $value)
  {
    global $post, $pagenow, $current_screen;

    switch ($parameter)
    {
      case 'capability':

        return piklist_user::current_user_can($value);

      break;

      case 'role':
      
        return piklist_user::current_user_role($value);

      break;

      case 'logged_in':

        return $value == 'true' ? is_user_logged_in() : true;

      break;

      default:

        /**
         * piklist_validate_field_parameter
         * Add custom part parameters to check.
         *
         * @param $parameter Parameter to check.
         * @param $value Value to compare.
         * 
         * @since 1.0
         */
        return apply_filters('piklist_validate_field_parameter', true, $parameter, $value);

      break;
    }
  }
  
  /**
   * save_fields
   * Insert description here
   *
   * @param $object
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function save_fields($object = null)
  {
    if (!empty(self::$fields_rendered))
    {
      $fields_id = md5(serialize(self::$fields_defaults));
      
      if (false === ($fields = get_transient(piklist::$prefix . $fields_id))) 
      {
        set_transient(piklist::$prefix . $fields_id, self::$fields_rendered, 60 * 60 * 24);
      }
      
      piklist::render('fields/fields', array(
        'nonce' => wp_create_nonce('piklist-' . $fields_id)
        ,'fields_id' => $fields_id
        ,'fields' => self::$fields_rendered
      ));
      
      self::$fields_defaults = self::$fields_rendered = array();
    }
  }

  /**
   * save_fields_actions
   * Insert description here
   *
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function save_fields_actions()
  {
    $actions = array(
      'dbx_post_sidebar'
      ,'show_user_profile'
      ,'edit_user_profile'
      ,'piklist_settings_form'
      ,'media_meta'
    );

    foreach ($actions as $action) 
    {
      add_action($action, array('piklist_form', 'save_fields'), 101);
    }
    
    $taxonomies = get_taxonomies('', 'names'); 
    foreach ($taxonomies as $taxonomy) 
    {
      add_action($taxonomy . '_add_form', array('piklist_form', 'save_fields'), 101);
      add_action($taxonomy . '_edit_form', array('piklist_form', 'save_fields'), 101);
    }
  }

  /**
   * render_nested_field
   * Insert description here
   *
   * @param $field
   * @param $content
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function render_nested_field($field, $content)
  {
    preg_match_all("#\[field=(.*?)\]#i", $content, $matches);

    if (!empty($matches[1]))
    {
      for ($i = 0; $i < count($matches[1]); $i++)
      {
        $nested_field = false;

        foreach ($field['fields'] as $f)
        {
          if ($f['field'] == $matches[1][$i])
          {
            $nested_field = $f;
            break;
          }
        }
      
        if ($nested_field)
        {
          $field['child_field'] = true;
          
          $content = str_replace(
            $matches[0][$i]
            ,self::render_field(
              wp_parse_args(array(
                  'scope' => $field['scope']
                  ,'field' => $nested_field['field']
                  ,'embed' => true
                  ,'prefix' => $field['prefix']
                  ,'value' => self::get_field_value($field['scope'], $nested_field, isset(self::$scopes[$field['scope']]) ? $field['scope'] : 'option')
                )
                ,$nested_field
              )
              ,true
            )
            ,$content
          );
        }
      }
    }
    
    return $content;
  }
  
  /**
   * render_field_assets
   * Insert description here
   *
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function render_field_assets()
  {
    global $wp_scripts, $pagenow;
    
    /**
     * piklist_field_assets
     * Register and Enqueue assets for fields.
     *
     * 
     * @since 1.0
     */   
    $field_assets = apply_filters('piklist_field_assets', self::$field_assets);

    $field_types_rendered = piklist_admin::is_widget() ? array_keys($field_assets) : self::$field_types_rendered;

    if (!empty($field_types_rendered))
    {
      $jquery_ui_core = $wp_scripts->query('jquery-ui-core');
      
      wp_register_style('jquery-ui-core', piklist::$urls['piklist'] . '/parts/css/jquery-ui/jquery-ui.css', false, $jquery_ui_core->ver);
      wp_register_style('jquery-ui-core-piklist', piklist::$urls['piklist'] . '/parts/css/jquery-ui.piklist.css', false, piklist::$version);
     
      wp_enqueue_style('jquery-ui-core');
      wp_enqueue_style('jquery-ui-core-piklist');

      foreach ($field_types_rendered as $type)
      {
        if (isset($field_assets[$type]))
        {
          if (isset($field_assets[$type]['callback']))
          {
            call_user_func_array($field_assets[$type]['callback'], array($type));
          }
          else
          {
            if (isset($field_assets[$type]))
            {
              if (isset($field_assets[$type]['scripts']))
              {
                foreach ($field_assets[$type]['scripts'] as $script)
                {
                  wp_enqueue_script($script);
                }
              }
      
              if (isset($field_assets[$type]['styles']))
              {
                foreach ($field_assets[$type]['styles'] as $style)
                {
                  wp_enqueue_style($style);
                }
              }
            }
          }
        }
      }
    }
  }
  
  /**
   * render_field_custom_assets
   * Insert description here
   *
   * @param $type
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function render_field_custom_assets($type)
  {
    switch ($type)
    {
      case 'colorpicker':
      
        wp_enqueue_style('wp-color-picker');
  
        wp_enqueue_script('iris', admin_url('js/iris.min.js'), array('jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch'), false, 1);
        wp_enqueue_script('wp-color-picker', admin_url('js/color-picker.min.js'), array('iris'), false, 1);

        wp_localize_script('wp-color-picker', 'wpColorPickerL10n', array(
          'clear' => __('Clear')
          ,'defaultString' => __('Default')
          ,'pick' => __('Select Color')
        ));
      
      break;
      
      default:

        /**
         * piklist_validate_part_parameter
         * Allow custom assets for fields
         *
         * @param $type Field type.
         * 
         * @since 1.0
         */
        do_action('piklist_render_field_custom_assets', $type);
      
      break;
    }
  }
  
  /**
   * template_tag_fetch
   * Insert description here
   *
   * @param $template_tag
   * @param $template
   * @param $wrapper
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function template_tag_fetch($template_tag, $template, $wrapper = false)
  {
    if (!strstr('[', $template) && isset(self::$templates[$template]['template']))
    {
      $template = self::$templates[$template]['template'];
    }
    
    if ($wrapper == 'start')
    {
      $output = substr($template, 0, strpos($template, '[' . $template_tag));
    }
    elseif ($wrapper == 'end')
    {
      $output = substr($template, strpos($template, '[/' . $template_tag . ']') + strlen('[/' . $template_tag . ']'));
    }
    else
    {
      $output = strstr($template, '[' . $template_tag) ? substr($template, strpos($template, '[' . $template_tag), strpos($template, '[/' . $template_tag . ']') + strlen('[/' . $template_tag . ']') - strpos($template, '[' . $template_tag)) : $template;
    }
    
    return $output;
  }
  
  /**
   * template_shortcode
   * Insert description here
   *
   * @param $attributes
   * @param $content
   * @param $tag
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function template_shortcode($attributes, $content = '', $tag)
  {
    extract(shortcode_atts(array(
      'class' => array()
    ), $attributes));

    $content = do_shortcode($content);
    $type = isset(self::$field_alias[self::$field_rendering['type']]) ? self::$field_alias[self::$field_rendering['type']] : self::$field_rendering['type'];

    switch ($tag)
    {
      case 'field_label':
      
        $content = self::template_label($type, self::$field_rendering);
        
      break;

      case 'field_description_wrapper':
      
        $content = isset(self::$field_rendering['description']) && !empty(self::$field_rendering['description']) ? $content : '';
      
      break;
      
      case 'field_description':

        $content = self::$field_rendering['display'] ? '' : self::$field_rendering['description'];
      
      break;
      
      case 'field':
      
        $content = '';
        
        if ((self::$field_rendering['add_more'] || self::$field_rendering['sortable']) && !self::$field_rendering['display'])
        {
          self::$field_rendering['attributes']['data-piklist-field-addmore'] = self::$field_rendering['add_more'] ? 'true' : 'false';
          self::$field_rendering['attributes']['data-piklist-field-sortable'] = self::$field_rendering['sortable'] ? 'true' : 'false';

          if (self::$field_rendering['sortable'] && !self::$field_rendering['add_more'])
          {
            self::$field_rendering['attributes']['data-piklist-field-addmore-actions'] = 'false';
          }
        }

        if (is_numeric(self::$field_rendering['columns']))
        {
          self::$field_rendering['attributes']['data-piklist-field-columns'] = self::$field_rendering['columns'];
        }

        if (self::$field_rendering['display'])
        {
          self::$field_rendering['value'] = is_array(self::$field_rendering['value']) && count(self::$field_rendering['value']) == 1 ? current(self::$field_rendering['value']) : self::$field_rendering['value'];
          self::$field_rendering['value'] = self::get_field_show_value(self::$field_rendering);
          
          $content = self::template_field('show', self::$field_rendering);
        }
        else
        {   
          if ((is_array(self::$field_rendering['value']) && isset(self::$field_rendering['value'][0]) && !self::$field_rendering['multiple']) 
              || (self::$field_rendering['multiple'] && !piklist::is_flat(self::$field_rendering['value']))
              || (in_array(self::$field_rendering['type'], self::$field_list_types['multiple_fields']) && !in_array(self::$field_rendering['type'], self::$field_list_types['multiple_value']) && count(self::$field_rendering['value']) > 1)
             )
          {
            $values = self::$field_rendering['value'];
          }
          else
          {
            $values = array(self::$field_rendering['value']);
          }
          
          $clone = self::$field_rendering;

          for ($index = 0; $index < count($values); $index++)
          {
            if (!stristr($clone['field'], ':') && !$clone['group_field'])
            {
              $clone['index'] = $index;
            }

            if (isset($clone['errors'][$clone['index']]))
            {
              array_push($clone['attributes']['class'], 'piklist-error');
            }
            
            $clone['value'] = $values[$index];

            $content .= self::template_field($type, $clone);
          }
        }
        
      break;
    }
    
    return $content;
  }
  
  /**
   * template_label
   * Insert description here
   *
   * @param $type
   * @param $field
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function template_label($type, $field)
  {
    if (empty($field['label']))
    {
      return '';
    }
    
    if (isset(self::$templates[$field['template']]['label']) && self::$templates[$field['template']]['label'] === false)
    {
      return self::field_label($field);
    }
    
    $attributes = array(
      'for' => self::get_field_name($field['field'], $field['scope'], $field['index'], $field['prefix'], $field['multiple'])
      ,'class' => 'piklist-field-part piklist' . ($field['child_field'] ? '-child' : '') . '-label piklist-label-position-' . $field['label_position'] . (isset($field['attributes']['label_class']) ? ' ' . $field['attributes']['label_class'] : '')
    );
    
    $label_tag = !$field['multiple'] || in_array('multiple', $field['attributes']) ? 'label' : 'span';    
    
    return '<' . $label_tag . ' ' . self::attributes_to_string($attributes) . '>' . self::field_label($field) . '</' . $label_tag . '>';
  }
 
  /**
   * template_field
   * Insert description here
   *
   * @param $type
   * @param $field
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function template_field($type, $field)
  {
    $content = '';
    
    if ($field['child_field'])
    {
      if ($field['label_position'] == 'before' && $field['template'] == 'field')
      {
        $content .= self::template_label($type, $field);
      }
      
      $content .= piklist::render('fields/' . $type, $field, true);

      if ($field['label_position'] == 'after' && $field['template'] == 'field')
      {
        $content .= self::template_label($type, $field);
      }
    }
    else
    {
      $content .= piklist::render('fields/' . $type, $field, true);
    }
    
    return $content;
  }
  
  /**
   * field_label
  * Generates the proper markup for 'required' and 'help' paramaters
   *
   * @param $field
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function field_label($field)
  {
    $label = '';

    $label .= $field['label'];
    $label .= !empty($field['required']) ? '<span class="piklist-required">*</span>' : null;
    $label .= isset($field['help']) ? piklist::render('shared/tooltip-help', array('message' => $field['help']), true) : null;
  
    return $label;

  }
  
  /**
   * render_form
   * Insert description here
   *
   * @param $attributes
   * @param $content
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function render_form($form, $add_on = 'piklist') 
  {
    if ($form)
    { 
      if ($add_on && isset(piklist::$paths[$add_on]))
      {
        $paths[$add_on] = piklist::$paths[$add_on];
      }
      else
      {
        $paths = piklist::$paths;
      }
      
      if (empty($paths))
      {
        return false;
      }

      foreach ($paths as $display => $path)
      {   
        if (in_array($form . '.php', piklist::get_directory_list($path . '/parts/forms')))
        {
          /**
           * piklist_get_file_data
           * Insert description here
           *
           * 
           * @since 1.0
           */
          $data = piklist::get_file_data($path . '/parts/forms/' . $form . '.php', apply_filters('piklist_get_file_data', array(
                    'class' => 'Class'
                    ,'title' => 'Title'
                    ,'description' => 'Description'
                    ,'method' => 'Method'
                    ,'action' => 'Action'
                    ,'filter' => 'Filter'
                    ,'redirect' => 'Redirect'
                    ,'message' => 'Message'
                    ,'capability' => 'Capability'
                    ,'logged_in' => 'Logged In'
                  ), 'form'));

         /**
           * piklist_add_part
           * Insert description here
           *
           * 
           * @since 1.0
           */
          $data = apply_filters('piklist_add_part', $data, 'form');
                  
          if (!$data['logged_in'] || ((isset($data['logged_in']) && $data['logged_in'] == 'true') && is_user_logged_in()))
          {
            if (!$data['capability'] || ($data['capability'] && piklist_user::current_user_can($data['capability'])))
            {
              $data['form'] = $path . '/parts/forms/' . $form;
              $data['form_id'] = piklist::slug($add_on . ' ' . $form);
              $data['filter'] = strtolower($data['filter']) == 'true' ? true : false;
              $data['hide_admin_ui'] = piklist_admin::hide_ui();
              
              self::$forms[$data['form_id']] = $data;
              
              self::$current_form_id = $data['form_id'];
              
              return piklist::render('fields/form', $data, true);
            }
          }
          else
          {
            _e('You must be logged in to view this form.', 'piklist');
          }
        }
      }
    }
    
    return null;
  }
  
  /**
   * process_form
   * Insert description here
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function process_form()
  {
    if (self::valid())
    {
      $form_id = isset($_REQUEST[piklist::$prefix]['form_id']) ? $_REQUEST[piklist::$prefix]['form_id'] : false;
      
      if ($form_id)
      {
        self::$form_id = $form_id;
      }

      if ((self::$form_saved = self::save()) === true)
      {
        $redirect = isset($_REQUEST[piklist::$prefix]['redirect']) ? $_REQUEST[piklist::$prefix]['redirect'] : false;

        if ($redirect)
        {
          $redirect = preg_replace('/#.*/', '', $redirect);

          wp_redirect($redirect);
        }
      }
    }
  }
  
  /**
   * save
   * Insert description here
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function save()
  {
    global $wpdb, $wp_post_types, $wp_taxonomies;

    if (!isset($_REQUEST[piklist::$prefix]['fields_id']) || isset($_REQUEST[piklist::$prefix]['filter']) || (false === ($field_data = piklist_validate::check())))
    {
      return false;
    }
    
    foreach ($field_data as $scope => $fields)
    {
      switch ($scope)
      {
        case 'post_meta':
        case 'term_meta':
        case 'user_meta':

          $meta_type = substr($scope, 0, strpos($scope, '_'));
          
          foreach ($fields as $field)
          {
            if (isset($field['display']) && !$field['display'])
            {
              $path = array_merge(array(
                        piklist::$prefix . $scope
                        ,'name'
                      ), strstr($field['field'], ':') ? explode(':', $field['field']) : array($field['field']));
              
              $uploads = piklist::array_filter_recursive(piklist::array_path_get($_FILES, $path));
              
              if (!empty($uploads) && $field['type'] == 'file')
              {
                $field['request_value'] = self::save_upload($path, $field['request_value'], true);

                $path = explode(':', $field['field']);
                $parent_field = $path[0];

                unset($path[0]);
                
                piklist::array_path_set($field_data[$scope][$parent_field]['request_value'], $path, $field['request_value']);
              }
            }
          }
          
        break;
      }
    }

    foreach ($field_data as $scope => $fields)
    {
      switch ($scope)
      {
        case 'post':
        case 'comment':
        case 'user':

          $belongs_to = false;
          
          if (isset($fields[piklist::$prefix]))
          {
            foreach ($fields[piklist::$prefix] as $field)
            {
              if ($field['field'] == $scope . '_id')
              {
                $belongs_to = $field['request_value'];
              }
            }
          }
                      
          $object = array();
          
          foreach ($fields as $field)
          {
            if (isset($field['request_value']) && !$field['display'])
            {
              $object[$field['field']] = is_array($field['request_value']) ? current($field['request_value']) : $field['request_value'];
            }
          }

          if (!empty($object))
          {
            if (isset($field['object_id']))
            {
              $object_id = ($scope == 'comment' ? $scope . '_' : null) . 'ID';
              $object[$object_id] = $field['object_id'];
            }
            
            $field['object_id'] = self::save_object($scope, $object, $belongs_to);
          }
      
        break;
      
        case 'post_meta':
        case 'term_meta':
        case 'user_meta':

          $meta_type = substr($scope, 0, strpos($scope, '_'));

          foreach ($fields as $field)
          {
            if (isset($field['object_id']))
            {
              $save_as = is_string($field['save_as']) ? $field['save_as'] : $field['field'];
              $grouped = isset($field['type']) && in_array($field['type'], self::$field_list_types['multiple_value']) && ($field['add_more'] || $field['group_field']);
              
              if (isset($field['display']) && !$field['display'])
              {
                delete_metadata($meta_type, $field['object_id'], $save_as);
            
                if ($grouped)
                {
                  delete_metadata($meta_type, $field['object_id'], '_' . piklist::$prefix . $save_as);
                }

                if (isset($field['request_value']) && !strstr($field['field'], ':'))
                {
                  if (!piklist::is_flat($field['request_value']) && !isset($field['request_value'][0]))
                  {
                    add_metadata($meta_type, $field['object_id'], $save_as, $field['request_value']);
                  }
                  else
                  {
                    foreach ($field['request_value'] as $values)
                    {
                      if (is_array($values) && $field['type'] != 'group')
                      {
                        $meta_ids = array();
                    
                        foreach ($values as $value)
                        {
                          if ($meta_id = add_metadata($meta_type, $field['object_id'], $save_as, $value))
                          {
                            array_push($meta_ids, $meta_id);
                          }
                        }

                        if ($grouped)
                        {
                          add_metadata($meta_type, $field['object_id'], '_' . piklist::$prefix . $save_as, $meta_ids);
                        }
                      }
                      else
                      {
                        if (is_array($values) && count($values) == 1)
                        {
                          $values = current($values);
                        }

                        add_metadata($meta_type, $field['object_id'], $save_as, $values);
                      }
                    }
                  }
                }
              }
            }
          }
                  
        break;
     
        case 'taxonomy':

          $taxonomies = array();

          foreach ($fields as $field)
          {
            if (isset($field['display']) && !$field['display'])
            {
              $taxonomy = is_string($field['save_as']) ? $field['save_as'] : $field['field'];
            
              if (!isset($taxonomies[$taxonomy]))
              {
                $taxonomies[$taxonomy] = array();
              }
              
              if (isset($field['request_value']))
              {
                foreach ($field['request_value'] as $terms)
                {                    
                  if (!empty($terms))
                  {
                    $terms = !is_array($terms) ? array($terms) : $terms;
                    
                    foreach ($terms as $term)
                    {
                      if (!in_array($term, $taxonomies[$taxonomy]))
                      {
                        array_push($taxonomies[$taxonomy], is_numeric($term) ? (int) $term : $term);
                      }
                    }
                  }
                }
              }
            }
          }

          foreach ($taxonomies as $taxonomy => $terms)
          {
            if (isset($wp_taxonomies[$taxonomy]->object_type[0]))
            {
              switch ($wp_taxonomies[$taxonomy]->object_type[0])
              {
                case 'user':

                  if (current_user_can('edit_user', $field['object_id']) && current_user_can($wp_taxonomies[$taxonomy]->cap->assign_terms))
                  {
                    $id = $field['object_id'];
                  }

                break;

                default:

                  $id = $field['object_id'];

                break;
              }
            }
          
            if (isset($id))
            {
              wp_set_object_terms($id, $terms, $taxonomy, false);
              
              clean_object_term_cache($id, $taxonomy);
            }
          }

        break;
      }

      do_action('piklist_save_field', $scope, $fields);

      do_action("piklist_save_field-{$scope}", $fields);

    }

    return true;
  }
  
  /**
   * save_upload
   * Insert description here
   *
   * @param $path
   * @param $storage
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function save_upload($path, $storage = array(), $return = false)
  {
    $files = $_FILES;

    if (!function_exists('media_handle_sideload'))
    {
      require_once(ABSPATH . 'wp-admin/includes/image.php');
      require_once(ABSPATH . 'wp-admin/includes/file.php');
      require_once(ABSPATH . 'wp-admin/includes/media.php');
    }
    
    $paths = array();
    $paths['name'] = $path;
    $path[1] = 'size';
    $paths['size'] = $path;
    $path[1] = 'tmp_name';
    $paths['tmp_name'] = $path;
    $path[1] = 'error';
    $paths['error'] = $path;
    
    $codes = piklist::array_path_get($files, $paths['error']);
    $names = piklist::array_path_get($files, $paths['name']);
    $sizes = piklist::array_path_get($files, $paths['size']);
    $tmp_names = piklist::array_path_get($files, $paths['tmp_name']);
    
    foreach ($codes as $set => $code_set)
    {
      $_storage = array();

      foreach ($code_set as $index => $code)
      {
        if (in_array($code, array(UPLOAD_ERR_OK, 0), true))
        {
          $attach_id = media_handle_sideload(
                          array(
                            'name' => $names[$set][$index]
                            ,'size' => $sizes[$set][$index]
                            ,'tmp_name' => $tmp_names[$set][$index]
                          )
                          ,0
                        );

          if (!is_wp_error($attach_id))
          {
            $_storage[$set] = $attach_id;
          }
        }
      }

      if (isset($_storage[$set]))
      {
        if (!isset($storage[$set]))
        {
          $storage[$set] = array();
        }

        $storage[$set] = array_merge($storage[$set], $_storage);
    
        if ($return && isset($storage[$set]) && is_array($storage[$set]) && count($storage[$set]) > 1)
        {
          $storage[$set] = array_values(array_filter($storage[$set]));
        }
      }
    }
    
    ksort($storage);

    return $storage;
  }
  
  /**
   * fields_diff
   * Insert description here
   *
   * @param $rendered
   * @param $request
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function fields_diff($rendered, $request = array())
  {
    if (!is_array($rendered))
    {
      return array();
    }

    foreach($rendered as $key => $field) 
    {
      if (isset($field['display']))
      {
        unset($rendered[$key]);
      }
    }
    
    return array_filter(is_array($request) ? array_diff(array_keys($rendered), array_keys($request)) : array_keys($rendered), create_function('$a', 'return !strstr($a, ":");'));
  }
  
  /**
   * save_object
   * Insert description here
   *
   * @param $type
   * @param $data
   * @param $belongs_to
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function save_object($type, $data, $belongs_to = false)
  {
    global $wpdb;
    
    $object = array();
    
    foreach (self::$scopes[$type] as $allowed)
    {
      if (isset($data[$allowed]) && !empty($data[$allowed]))
      {
        $object[$allowed] = is_array($data[$allowed]) && count($data[$allowed]) == 1 ? current($data[$allowed]) : $data[$allowed];
      }
    }
    
    switch ($type)
    {
      case 'post':

        $id = isset($object['ID']) ? wp_update_post($object) : wp_insert_post($object);
        
      break;
      
      case 'comment':
        
        if (!empty($object['comment_content']))
        {
          $id = isset($object['ID']) ? wp_update_comment($object) : wp_insert_comment($object);
        }
        
      break;
      
      case 'user':

        $re_auth_cookie = false;
        
        if (isset($object['user_pass']) && empty($object['user_pass']))
        {
          unset($object['user_pass']);
        }
        
        if (isset($object['ID']) && isset($object['user_login']) && !empty($object['user_login']))
        {
          $user_login = $object['user_login'];
          $increment = 0;
        	
          $user_login_check = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->users WHERE user_login = %s LIMIT 1" , $user_login, $user_login));
          
          if ($user_login_check != $object['ID'])
          {
            while ($user_login_check)
            {
              $user_login = $object['user_login'] . '-' . ++$increment;
            	$user_login_check = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->users WHERE user_login = %s LIMIT 1" , $user_login, $user_login));
            }
          
            $result = $wpdb->query($wpdb->prepare("UPDATE $wpdb->users SET user_login = %s WHERE ID = %d ", $user_login, $object['ID']));
          
            unset($object['user_login']);
          
            if (!isset($object['user_nicename']))
            {
              $object['user_nicename'] = $user_login;
            }
          
            $re_auth_cookie = true;
          }
        }
        
        if (isset($object['ID']))
        {
          $id = wp_update_user($object);
        }
        elseif (isset($object['user_pass']) && isset($object['user_login']))
        {
          $id = wp_insert_user($object);
        }
        
        if (isset($id) && !is_wp_error($id))
        {
          if ($re_auth_cookie)
          {
            wp_set_auth_cookie($id);
          }
        
          if (isset($object['user_role']))
          {
            piklist_user::multiple_roles($id, $object['user_role']);
          }
        }

      break;
    }

    if ($belongs_to && $id)
    {
      self::relate($belongs_to, $id);
    }
    
    return isset($id) ? $id : false;
  }
  
  /**
   * relate
   * Insert description here
   *
   * @param $post_id
   * @param $has_post_id
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function relate($post_id, $has_post_id)
  {
    global $wpdb;
    
    $has_post_id = is_array($has_post_id) ? $has_post_id : array($has_post_id);
    
    foreach ($has_post_id as $has)
    {
      $found = $wpdb->get_col($wpdb->prepare('SELECT relate_id FROM ' . $wpdb->prefix . 'post_relationships WHERE post_id = %d AND has_post_id = %d', $post_id, $has));
      if (empty($found))
      {
        $wpdb->insert( 
          $wpdb->prefix . 'post_relationships'
          ,array(
            'post_id' => $post_id
            ,'has_post_id' => $has 
          ) 
          ,array( 
            '%d'
            ,'%d' 
          ) 
        );
      }
    }
  }
  
  /**
   * attributes_to_string
   * Insert description here
   *
   * @param $attributes
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function attributes_to_string($attributes = array(), $exclude = array('id', 'name', 'value', 'wrapper_class'))
  {
    $attribute_string = '';

    if (!is_array($attributes))
    {
      return $attribute_string;
    }

    foreach ($attributes as $key => $value)
    {

      if (isset($value) && ($value !== '') && $value)
      {
        if (is_numeric($key) && !in_array($value, $exclude))
        {
          $attribute_string .= esc_attr($value) . ' ';
        }
        else if (!in_array($key, $exclude))
        {
          $attribute_string .= $key . '="' . esc_attr(is_array($value) ? implode(' ', $value) : $value) .'" '; 
        }
      }
    }

    return $attribute_string;
  }

  /**
   * tiny_mce_settings
   * Insert description here
   *
   * @param $settings
   * @param $editor_id
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function tiny_mce_settings($settings, $editor_id)
  {
    self::set_editor_settings('tiny_mce', $settings, $editor_id);
    
    return $settings;
  }
  
  /**
   * quicktags_settings
   * Insert description here
   *
   * @param $settings
   * @param $editor_id
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function quicktags_settings($settings, $editor_id)
  {
    self::set_editor_settings('quicktags', $settings, $editor_id);
  
    return $settings;
  }

  /**
   * set_editor_settings
   * Insert description here
   *
   * @param $type
   * @param $settings
   * @param $editor_id
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function set_editor_settings($type, $settings, $editor_id)
  {
    if (!empty($settings)) 
    {
      $_settings = self::get_editor_settings($settings);
      
      $settings = array();
      $settings[$editor_id] = $_settings;
    }
    else 
    {
      $settings = array();
    }
        
    self::$field_editor_settings[$type] = $settings;
  }


  /**
   * get_editor_settings
   * Insert description here
   *
   * @param $settings
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function get_editor_settings($settings) 
  {
    $objects = array(
      'formats'
    );
    
    $new_settings = array();
    
    foreach ($settings as $key => $value) 
    {
      if (is_bool($value)) 
      {
        $new_settings[$key] = $value ? true : false;
        continue;
      }
      elseif (!empty($value) && is_string($value) && (('{' == $value{0} && '}' == $value{strlen($value) - 1}) || ('[' == $value{0} && ']' == $value{strlen($value) - 1}) || preg_match('/^\(?function ?\(/', $value))) 
      {
        $new_settings[$key] = $value;
        continue;
      }
      
      $new_settings[$key] = $value;
    }
    
    foreach ($objects as $object)
    {
      if (isset($new_settings[$object]))
      {
        $new_settings[$object] = preg_replace('/(\w+)\s{0,1}:/', '"\1":', str_replace(array("\r\n", "\r", "\n", "\t"), '', str_replace("'", '"', $new_settings[$object])));
        $new_settings[$object] = json_decode($new_settings[$object]);
      }
    }
    
    return $new_settings;
  }
  
  /**
   * render_assets
   * Insert description here
   *
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function render_assets()
  {
    return empty(self::$field_types_rendered) ? false : true;
  }
  
  /**
   * admin_notices
   * Insert description here
   *
   * @param $form_id
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function admin_notices($form_id = null)
  {
    if (self::$form_saved
        && self::$form_id 
        && self::$form_id == $form_id 
        && isset(self::$forms[self::$form_id]) 
        && !empty(self::$forms[self::$form_id]['message'])
      )
    {
      piklist::render('shared/admin-notice', array(
        'id' => 'piklist_form_admin_notice'
        ,'data' => array(
          'notice_type' => 'update'
          ,'dismiss' => false
        )
        ,'content' => self::$forms[self::$form_id]['message']
      ));
    }
  }
}