<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Piklist_Validate
 * Controls validation and sanitization rules.
 *
 * @package     Piklist
 * @subpackage  Validate
 * @copyright   Copyright (c) 2012-2015, Piklist, LLC.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
class Piklist_Validate
{
  private static $errors = array();
  
  private static $request = array();

  private static $submission = array();
  
  private static $fields = array();

  private static $id = false;
  
  private static $parameter = 'piklist_validate';

  private static $validation_rules = array();

  private static $sanitization_rules = array();
    
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
    add_action('init', array('piklist_validate', 'init'));
    add_action('admin_head', array('piklist_validate', 'admin_head'));
    add_action('admin_notices', array('piklist_validate', 'admin_notices'));
    add_action('piklist_notices', array('piklist_validate', 'admin_notices'));

    add_filter('wp_redirect', array('piklist_validate', 'wp_redirect'), 10, 2);
    add_filter('piklist_validation_rules', array('piklist_validate', 'validation_rules'));
    add_filter('piklist_sanitization_rules', array('piklist_validate', 'sanitization_rules'));
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
    /**
     * piklist_validation_rules
     * Add your own validation rules.
     *
     * 
     * @since 1.0
     */
    self::$validation_rules = apply_filters('piklist_validation_rules', self::$validation_rules);

    /**
     * piklist_sanitization_rules
     * Add your own sanitization rules.
     *
     * 
     * @since 1.0
     */
    self::$sanitization_rules = apply_filters('piklist_sanitization_rules', self::$sanitization_rules);
    
    self::get_data();
  }
  
  /**
   * wp_redirect
   * Insert description here
   *
   * @param $location
   * @param $status
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function wp_redirect($location, $status)
  {
    global $pagenow;
    
    if (self::$id && $status == 302)
    {
      if ($pagenow == 'edit-tags.php')
      {
        $location = preg_replace('/&?piklist_validate=[^&]*/', '', $_SERVER['HTTP_REFERER']);
      }

      $location .= (stristr($location, '?') ? (substr($location, -1) == '&' ? '' : '&') : '?') . 'piklist_validate=' . self::$id;
    }
    else
    {
      if ($pagenow == 'edit-tags.php')
      {
        foreach (array('action', 'tag_ID', self::$parameter) as $variable)
        {
          $location = preg_replace('/&?' . $variable . '=[^&]*/', '', $location);
        }
      }
    }

    return $location;
  }
  
  /**
   * admin_head
   * Render admin notices for validation errors.
   *
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function admin_head()
  {
    if (!empty(self::$submission['errors']))
    {
      piklist::render('shared/admin-notice-updated-hide');
    }
  }
  
  /**
   * admin_notices
   * Render notices for each individual field that has errors.
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
    $submitted_form_id = piklist_form::get('form_id');

    if ((($submitted_form_id && $form_id == $submitted_form_id) || !$submitted_form_id) && !empty(self::$submission['errors']))
    {
      $content = '<ol>';
      foreach (self::$submission['errors'] as $type => $fields)
      {
        foreach ($fields as $field => $errors)
        {
          $content .= '<li>' . current($errors) . '</li>';
        }
      }
      $content .= '</ol>';
      
      piklist::render('shared/admin-notice', array(
        'id' => 'piklist_validation_error'
        ,'data' => array(
          'notice_type' => 'error'
        )
        ,'content' => $content
      ));
    }
  }
  
  /**
   * check
   * Insert description here
   *
   * @param $stored_data
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function check(&$stored_data = null)
  {
    if (!isset($_REQUEST[piklist::$prefix]['fields_id']) || !$fields_data = get_transient(piklist::$prefix . $_REQUEST[piklist::$prefix]['fields_id'])) 
    {
      return false;
    }
    
    $fields_id = $_REQUEST[piklist::$prefix]['fields_id'];
    
    foreach ($fields_data as $type => &$fields)
    {
      foreach ($fields as &$field)
      {
        if (!is_null($stored_data))
        {
          $request_data = &$stored_data;
        }
        else
        {
          if (isset($_REQUEST['widget-id']) && isset($_REQUEST['multi_number']) && isset($_REQUEST['widget_number']))
          {
            $widget_index = !empty($_REQUEST['multi_number']) ? $_REQUEST['multi_number'] : $_REQUEST['widget_number'];
            $request_data = &$_REQUEST[piklist::$prefix . $field['scope']][$widget_index];
          }
          elseif (isset($field['scope']) && !empty($field['scope']))
          {
            $request_data = &$_REQUEST[piklist::$prefix . $field['scope']];
          }
          else
          {
            $request_data = &$_REQUEST;
          }
        }
        
        if (isset($request_data) && isset($field['field']))
        {
          $field['request_value'] = !strstr($field['field'], ':') ? (isset($request_data[$field['field']]) ? $request_data[$field['field']] : null) : piklist::array_path_get($request_data, explode(':', $field['field']));
          $field['valid'] = true;
          
          if (stristr($field['field'], ':0:'))
          {
            $_field = $field['field'];
            $value = array();
            $index = 0;
            
            do 
            {
              $_value = piklist::array_path_get($request_data, explode(':', $_field));
              if (isset($_value[$index]) && count($_value[$index]) > 1 && in_array($field['type'], piklist_form::$field_list_types['multiple_value']) && $field['add_more'])
              {
                $_value[$index] = array_values(array_filter($_value[$index]));
              }
              
              if (isset($_value[$index]))
              {
                array_push($value, $_value);
                
                piklist::array_path_set($request_data, explode(':', $_field), $_value);
                
                $_field = strrev(implode(strrev(':' . ($index + 1) . ':'), explode(':' . $index . ':', strrev($_field), 2)));
              }
              else
              {
                break;
              }
              
              $index++;
            } 
            while (isset($_value[$index]));
            
            $field['request_value'] = $_value;
          }
          elseif ($field['type'] == 'group' && empty($field['field']))
          {
            $field['request_value'] = array();
            
            foreach ($field['fields'] as $_field)
            {
              $field['request_value'][$_field['field']] = !strstr($_field['field'], ':') ? (isset($request_data[$_field['field']]) ? $request_data[$_field['field']] : null) : piklist::array_path_get($request_data, explode(':', $_field['field']));              
            }
          }
          else if ($field['type'] != 'html')
          {
            $index = 0;
            
            do 
            {
              if (isset($field['request_value'][$index]) && count($field['request_value'][$index]) > 1 && $field['type'] == 'checkbox')
              {
                $field['request_value'][$index] = array_values(array_filter($field['request_value'][$index]));
              }
              
              $index++;
            } 
            while (isset($field['request_value'][$index]));
          
            piklist::array_path_set($request_data, explode(':', $field['field']), $field['request_value']);
          }

          if (isset($field['sanitize']))
          {
            foreach ($field['sanitize'] as $sanitize)
            {
              if (isset(self::$sanitization_rules[$sanitize['type']]))
              {
                $sanitization = array_merge(self::$sanitization_rules[$sanitize['type']], $sanitize);
                
                if (isset($sanitization['callback']) && isset($field['request_value']))
                {
                  foreach ($field['request_value'] as $index => $request_value)
                  {
                    $request_value = call_user_func_array($sanitization['callback'], array($request_value, $field, isset($sanitize['options']) ? $sanitize['options'] : array()));

                    $_request_value = piklist::array_path_get($request_data, explode(':', $field['field']));
                    $_request_value[$index] = $request_value;
                    
                    $field['request_value'][$index] = $request_value;
                    
                    piklist::array_path_set($request_data, explode(':', $field['field']), $_request_value);
                  }
                }
              }
              else
              {
                $trigger_error = sprintf(__('Sanitization type "%s" is not valid.', 'piklist'), $sanitize['type']);
                
                trigger_error($trigger_error, E_USER_NOTICE);
              }
            }
          }
          
          self::add_request_value($field);
        }
      }
    }
      
    foreach ($fields_data as $type => &$fields)
    {
      foreach ($fields as &$field)
      { 
        if (isset($field['required']) && $field['required'] && isset($field['request_value']))
        {
          for ($index = 0; $index < count($field['request_value']); $index++)
          {
            $request_value = is_array($field['request_value'][$index]) ? array_filter($field['request_value'][$index]) : $field['request_value'][$index];
            
            if (empty($request_value))
            {
              self::add_error($field, $index, __('is a required field.', 'piklist'));
            }
          }
        }
                  
        if (isset($field['validate']))
        {
          foreach ($field['validate'] as $validate)
          {
            if (isset(self::$validation_rules[$validate['type']]))
            {
              $validation = array_merge(self::$validation_rules[$validate['type']], $validate);
              $request_values = isset($field['request_value']) ? $field['request_value'] : array();
              
              if ($field['type'] == 'group')
              {
                $_request_values = array();
                
                if (!empty($request_values))
                {
                  foreach ($request_values as $key => $values)
                  {
                    if (!empty($values))
                    {
                      foreach ($values as $index => $value)
                      {
                        if (!isset($_request_values[$index]))
                        {
                          $_request_values[$index] = array();
                        }
                    
                        $_request_values[$index][$key] = $value;
                      }
                    }
                  }
                }
                
                $request_values = array($_request_values);
              }
              
              if (isset($validation['rule']))
              {
                for ($index = 0; $index < count($request_values); $index++)
                {
                  if (!empty($request_values[$index]) && !preg_match($validation['rule'], $request_values[$index]))
                  {
                    self::add_error($field, $index, $validation['message']);
                  }
                }
              }

              if (isset($validation['callback']))
              {
                for ($index = 0; $index < count($request_values); $index++)
                {
                  if (!empty($request_values[$index]) || ($field['type'] != 'group' && $field['add_more']))
                  {
                    $validation_result = call_user_func_array($validation['callback'], array($index, $request_values[$index], isset($validate['options']) ? $validate['options'] : array(), $field, $fields_data));
      
                    if ($validation_result !== true)
                    {
                      self::add_error($field, $index, !empty($validation['message']) ? $validation['message'] : (is_string($validation_result) ? $validation_result : __('is not valid input', 'piklist')));
                    }
                  }
                }
              }
            }
            else
            {
              $trigger_error = sprintf(__('Validation type "%s" is not valid.', 'piklist'), $validate['type']);
              
              trigger_error($trigger_error, E_USER_NOTICE);
            }
          }
        }
      }
    }

    self::set_data($fields_id);
    
    return !empty(self::$submission['errors']) ? false : $fields_data;
  }
  
  /**
   * add_request_value
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
  private static function add_request_value($field)
  {
    if (!isset(self::$submission['request'][$field['scope']][$field['field']]))
    {
      self::$submission['request'][$field['scope']][$field['field']] = array();
    }
    
    self::$submission['request'][$field['scope']][$field['field']] = $field['request_value'];
  }
  
  /**
   * get_request_value
   * Insert description here
   *
   * @param $field
   * @param $scope
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function get_request_value($field, $scope)
  {
    return isset(self::$submission['request'][$scope][$field]) ? self::$submission['request'][$scope][$field] : null;
  }
  
  /**
   * add_error
   * Insert description here
   *
   * @param $field
   * @param $index
   * @param $message
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  private static function add_error(&$field, $index, $message)
  {
    $field['valid'] = false;
    
    $name = isset($field['label']) && !empty($field['label']) ? $field['label'] : (isset($field['attributes']['placeholder']) ? $field['attributes']['placeholder'] : __(ucwords($field['type'])));
    
    if (!isset(self::$submission['errors'][$field['scope']][$field['field']]))
    {
      self::$submission['errors'][$field['scope']][$field['field']] = array();
    }
    
    self::$submission['errors'][$field['scope']][$field['field']][$index] = '<strong>' . $name . '</strong>' . "&nbsp;" . $message;
  }
  
  /**
   * set_data
   * Insert description here
   *
   * @param $fields_id
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  private static function set_data($fields_id)
  {
    if (!empty(self::$submission['errors']))
    {
      self::$id = substr(md5($fields_id), 0, 10);
      
      $set = set_transient(piklist::$prefix . 'validation_' . self::$id, self::$submission);
    }
  }
  
  /**
   * get_data
   * Insert description here
   *
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function get_data()
  {
    if (isset($_REQUEST[self::$parameter]))
    {
      self::$id = $_REQUEST[self::$parameter];
      
      self::$submission = get_transient(piklist::$prefix . 'validation_' . self::$id);
      
      delete_transient(piklist::$prefix . 'validation_' . self::$id);
    }
  }
  
  /**
   * errors
   * Insert description here
   *
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function errors()
  {
    return empty(self::$submission['errors']) ? false : true;
  } 
  
  /**
   * get_errors
   * Insert description here
   *
   * @param $field
   * @param $scope
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function get_errors($field, $scope)
  {
    return isset(self::$submission['errors'][$scope][$field]) ? self::$submission['errors'][$scope][$field] : false;
  }

  /**
   * Included Validation Callbacks
   */
  
  /**
   * validation_rules
   * Array of included validation rules.
   *
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function validation_rules()
  {
    $validation_rules = array(
      'email' => array(
        'name' => __('Email Address', 'piklist')
        ,'description' => __('Verifies that the input is in the proper format for an email address.', 'piklist')
        ,'callback' => array('piklist_validate', 'validate_email')
      )
      ,'email_domain' => array(
        'name' => __('Email Domain', 'piklist')
        ,'description' => __('Verifies that the email domain entered is a valid domain.', 'piklist')
        ,'callback' => array('piklist_validate', 'validate_email_domain')
      )
      ,'email_exists' => array(
        'name' => __('Email exists?', 'piklist')
        ,'description' => __('Checks that the entered email is not already registered to another user.', 'piklist')
        ,'callback' => array('piklist_validate', 'validate_email_exists')
      )
      ,'file_exists' => array(
        'name' => __('File Exists?', 'piklist')
        ,'description' => __('Verifies that the file path entered leads to an actual file.', 'piklist')
        ,'callback' => array('piklist_validate', 'validate_file_exists')
      )
      ,'hex_color' => array(
        'name' => __('Hex Color', 'piklist')
        ,'description' => __('Verifies that the data entered is a valid hex color.', 'piklist')
        ,'callback' => array('piklist_validate', 'validate_hex_color')
      )
      ,'image' => array(
        'name' => __('Is Image?', 'piklist')
        ,'description' => __('Verifies that the file path entered leads to an image file.', 'piklist')
        ,'callback' => array('piklist_validate', 'validate_image')
      )
      ,'ip_address' => array(
        'name' => __('IP Address', 'piklist')
        ,'description' => __('Verifies that the data entered is a valid IP Address.', 'piklist')
        ,'rule' => "/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/"
        ,'message' => __('is not a valid ip address.', 'piklist')
      )
      ,'limit' => array(
        'name' => __('Entry Limit', 'piklist')
        ,'description' => __('Verifies that the number of items are within the defined limit.', 'piklist')
        ,'callback' => array('piklist_validate', 'validate_limit')
      )
      ,'range' => array(
        'name' => __('Range', 'piklist')
        ,'description' => __('Verifies that the data entered is within the defined range.', 'piklist')
        ,'callback' => array('piklist_validate', 'validate_range')
      )
      ,'safe_text' => array(
        'name' => __('Alphanumeric', 'piklist')
        ,'description' => __('Verifies that the data entered is alphanumeric.', 'piklist')
        ,'rule' => "/^[a-zA-Z0-9 .-]+$/"
        ,'message' => __('contains invalid characters. Must contain only letters and numbers.', 'piklist')
      )
      ,'url' => array(
        'name' => __('URL', 'piklist')
        ,'description' => __('Verifies that the data entered is a valid URL.', 'piklist')
        ,'rule' => "/^(https?:\/\/)?([\da-z\.\-]+)\.([a-z\.]{2,6})([\/\w \.\-@!+]*)*\/?$/"
        ,'message' => __('is not a valid url.', 'piklist')
      )
      ,'username_exists' => array(
        'name' => __('Username exists?', 'piklist')
        ,'description' => __('Checks that the entered username does not already exist.', 'piklist')
        ,'callback' => array('piklist_validate', 'validate_username_exists')
      )
      ,'match' => array(
        'name' => __('Match Fields', 'piklist')
        ,'description' => __('Checks to see if two fields match.', 'piklist')
        ,'callback' => array('piklist_validate', 'validate_match')
      )
    );

    return $validation_rules;
  }

  /**
   * Validate email address
   * @param  $email
   * @param  $field 
   * @param  $options
   * @param  $index
   * @param  $fields
   * @return bool true if string is a valid email address, message otherwise.
   */
  public static function validate_email($index, $value, $options, $field, $fields)
  {
    return is_email($value) ? true : __('does not contain a valid Email Address.', 'piklist');
  }

  /**
   * Validate email address domain
   *
   * When checkdnsrr() returns false, it also returns a php warning.
   * The warning is being suppressed, since it will return a validation message.
   * 
   * @param  $email
   * @param  $field 
   * @param  $options
   * @param  $index
   * @param  $fields
   * @return bool true if string is a valid email domain, message otherwise.
   */
  public static function validate_email_domain($index, $value, $options, $field, $fields)
  {
    return (bool) @checkdnsrr(preg_replace('/^[^@]++@/', '', $value), 'MX') ? true : __('does not contain a valid Email Domain.', 'piklist');
  }

  /**
   * Check if a email is already registered to another user
   *
   * Uses the WordPress function email_exists()
   * 
   * @param  $file
   * @param  $field 
   * @param  $options
   * @param  $index
   * @param  $fields
   * @return bool true if $email is registered to another user generated message, return false otherwise.
   */
  public static function validate_email_exists($index, $value, $options, $field, $fields)
  {
    global $current_user;
    
    return (email_exists($value) && !is_user_logged_in()) || (email_exists($value) && is_user_logged_in() && $value != $current_user->user_email) ? sprintf(__('cannot be "%s". This email is registered to another user.', 'piklist'), $value) : true;
  }

  /**
   * Validate if a file exists
   *
   * When file_get_contents() returns false, it also returns a php warning.
   * The warning is being suppressed, since it will return a validation message.
   * 
   * @param  $file
   * @param  $field 
   * @param  $options
   * @param  $index
   * @param  $fields
   * @return bool true if $file exists, message otherwise.
   */
  public static function validate_file_exists($index, $value, $options, $field, $fields)
  {
    $field_value = is_array($value) ? $value : array($value);

    foreach ($field_value as $value)
    {
      if($field['type'] == 'file' && is_numeric($value))
      {
        $value = wp_get_attachment_url($value);
      }

      if(!@file_get_contents($value))
      {
        return __('contains a file that does not exist.', 'piklist');
      }
    }

    return true;
  }

  /**
   * Validate if a value is a valid hex color
   *
   * Uses the WordPress function sanitize_hex_color to sanitize the value and compare.
   * 
   * @param  $file
   * @param  $field 
   * @param  $options
   * @param  $index
   * @param  $fields
   * @return bool true if $file exists, message otherwise.
   */
  public static function validate_hex_color($index, $value, $options, $field, $fields)
  {
    $hex = self::sanitize_hex_color($value);

    if($hex === $value)
    {
      return true;
    }

    return false;
  }

  /**
   * Validate if an image file exists
   *
   * When exif_imagetype() returns false, it also returns a php warning.
   * The warning is being suppressed, since it will return a validation message.
   * 
   * @param  $file
   * @param  $field 
   * @param  $options
   * @param  $index
   * @param  $fields
   * @return bool true if string is an image file, message otherwise.
   */
  public static function validate_image($index, $value, $options, $field, $fields)
  {
    $field_value = is_array($value) ? $value : array($value);

    foreach ($field_value as $value)
    {
      if($field['type'] == 'file' && is_numeric($value))
      {
        $value = wp_get_attachment_url($value);
      }


      if(!@exif_imagetype($value))
      {
        return __('contains a file that is not an image.', 'piklist');
      }
    }

    return true;
  }

  /**
   * Validate how many items are in request value
   *
   * Request value can be any Piklist field.
   * 
   * @param  $value
   * @param  $field 
   * @param  $options
   * @param  $index
   * @param  $fields
   * @return bool true if value is within limit, message otherwise.
   */
  public static function validate_limit($index, $value, $options = null, $field, $fields)
  {
    $options = wp_parse_args($options, array(
      'min' => 1
      ,'max' => INF
      ,'count' => false
    ));
    
    extract($options);

    switch ($count)
    {
      case 'words':

        $grammar = __('words', 'piklist');
        $words = preg_split('#\PL+#u', $value, -1, PREG_SPLIT_NO_EMPTY);
        $total = count($words);

      break;

      case 'characters':

        $grammar = __('characters', 'piklist');
        $total = strlen($value);

      break;
      
      default:

        $grammar = $field['type'] == 'file' || $field['add_more'] ? __('items added', 'piklist') : __('items selected', 'piklist');
        $total = $field['type'] != 'group' && !$field['multiple'] ? count($field['request_value']) : count($value);

      break;
    }
    
    if ($total < $min || $total > $max)
    {
      if ($min == $max)
      {
        return sprintf(__('must have exactly %1$s %2$s.', 'piklist'), $min, $grammar);
      }
      else
      {
        return sprintf(__('must have between %1$s and %2$s %3$s.', 'piklist'), $min, $max, $grammar);
      }
    }

    return true;
  }


  /**
   * Validate if a numbered value is within a range.
   * 
   * @param  $value
   * @param  $field 
   * @param  $options
   * @param  $index
   * @param  $fields
   * @return bool true if value is within range, message otherwise.
   */
  public static function validate_range($index, $value, $options = null, $field, $fields)
  {
    extract($options);

    $min = isset($options['min']) ? $options['min'] : 1;
    $max = isset($options['max']) ? $options['max'] : 10;

    if (($field['request_value'][0] >= $min) && ($field['request_value'][0] <= $max))
    {
      return true;
    }
    else
    {
      return sprintf(__('contains a value that is not between %s and %s', 'piklist'), $min, $max);
    }
  }

  /**
   * Check if a username already exists
   *
   * Uses the WordPress function username_exists()
   * 
   * @param  $file
   * @param  $field 
   * @param  $options
   * @param  $index
   * @param  $fields
   * @return bool true if $username does not exist, message otherwise.
   */
  public static function validate_username_exists($index, $value, $options, $field, $fields)
  {
    global $current_user;

    get_currentuserinfo();

    if ($current_user->user_login == $value)
    {
      return true;
    }
    elseif (username_exists($value))
    {
      return sprintf(__('cannot be "%s". This username already exists.', 'piklist'), $value);
    }
    else
    {
      return true;
    }
  }
  
  /**
   * Check if two fields match
   * 
   * @param  $file
   * @param  $field 
   * @param  $options
   * @param  $index
   * @param  $fields
   * @return bool true if fields match, message otherwise.
   */
  public static function validate_match($index, $value, $options, $field, $fields)
  {
    if (isset($options['field']))
    {
      $scope = is_array($options['field']) && isset($options['field']['scope']) ? $options['field']['scope'] : $field['scope'];
    
      if (isset($options['field']) && isset($fields[$scope][$options['field']]))
      {
        if (isset($fields[$scope][$options['field']]['request_value'][$index]) && $fields[$scope][$options['field']]['request_value'][$index] === $value)
        {
          return true;
        }
        else
        {
          return sprintf(__('must match <strong>%s</strong>', 'piklist'), isset($fields[$scope][$options['field']]['label']) ? $fields[$scope][$options['field']]['label'] : $fields[$scope][$options['field']]['field'], $value);
        }
      }
    }

    return true;
  }
  
  /**
   * Included Sanitization Callbacks
   */

  /**
   * sanitization_rules
   * Array of included sanitization rules.
   *
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function sanitization_rules()
  {
    $sanitization_rules = array(
      'email' => array(
        'name' => __('Email address', 'piklist')
        ,'description' => __('Strips out all characters that are not allowable in an email address.', 'piklist')
        ,'callback' => array('piklist_validate', 'sanitize_email')
      )
      ,'file_name' => array(
        'name' => __('File name', 'piklist')
        ,'description' => __('Removes or replaces special characters that are illegal in filenames.', 'piklist')
        ,'callback' => array('piklist_validate', 'sanitize_file_name')
      )
      ,'html_class' => array(
        'name' => __('HTML class', 'piklist')
        ,'description' => __('Removes all characters that are not allowable in an HTML classname.', 'piklist')
        ,'callback' => array('piklist_validate', 'sanitize_html_class')
      )
      ,'text_field' => array(
        'name' => __('Text field', 'piklist')
        ,'description' => __('Removes all HTML markup, as well as extra whitespace, leaving only plain text.', 'piklist')
        ,'callback' => array('piklist_validate', 'sanitize_text_field')
      )
      ,'title' => array(
        'name' => __('Post title', 'piklist')
        ,'description' => __('Removes all HTML and PHP tags, returning a title that is suitable for a url', 'piklist')
        ,'callback' => array('piklist_validate', 'sanitize_title')
      )
      ,'user' => array(
        'name' => __('Username', 'piklist')
        ,'description' => __('Removes all unsafe characters for a username.', 'piklist')
        ,'callback' => array('piklist_validate', 'sanitize_user')
      )
      ,'wp_kses' => array(
        'name' => __('wp_kses', 'piklist')
        ,'description' => __('Makes sure that only the allowed HTML element names, attribute names and attribute values plus only sane HTML entities are accepted.', 'piklist')
        ,'callback' => array('piklist_validate', 'sanitize_wp_kses')
      )
      ,'wp_filter_kses' => array(
        'name' => __('wp_filter_kses', 'piklist')
        ,'description' => __('Makes sure only default HTML elements are accepted.', 'piklist')
        ,'callback' => array('piklist_validate', 'sanitize_wp_filter_kses')
      )
      ,'wp_kses_post' => array(
        'name' => __('wp_kses_post', 'piklist')
        ,'description' => __('Makes sure only appropriate HTML elements for post content are accepted.', 'piklist')
        ,'callback' => array('piklist_validate', 'sanitize_wp_kses_post')
      )
      ,'wp_strip_all_tags' => array(
        'name' => __('wp_strip_all_tags', 'piklist')
        ,'description' => __('Properly strip all HTML tags including script and style.', 'piklist')
        ,'callback' => array('piklist_validate', 'sanitize_wp_strip_all_tags')
      )
    );

    return $sanitization_rules;
  }

  /**
   * sanitize_email
   * Strips out all characters that are not allowable in an email address.
   * Uses the WordPress function sanitize_email().
   *
   * @param $value
   * @param $field
   * @param $options
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function sanitize_email($value, $field, $options)
  {
    return sanitize_email($value);
  }

  /**
   * sanitize_file_name
   * Sanitizes a filename
   * -Removes special characters that are illegal in filenames on certain operating systems
   * -Removes special characters requiring special escaping to manipulate at the command line.
   * -Replaces spaces and consecutive dashes with a single dash. 
   * -Trims period, dash and underscore from beginning and end of filename
   * Uses the WordPress function sanitize_file_name()
   *
   * @param $value
   * @param $field
   * @param $options
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function sanitize_file_name($value, $field, $options)
  {
    return sanitize_file_name($value);
  }

  /**
   * sanitize_html_class
   * Sanitizes a html classname to ensure it only contains valid characters.
   * Uses the WordPress function sanitize_html_class()
   *
   * @param $value
   * @param $field
   * @param $options
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function sanitize_html_class($value, $field, $options = null)
  {
    $options = wp_parse_args($options, array(
      array()
    ));

    extract($options);

    return sanitize_html_class($value, isset($fallback) ? $fallback : null);
  }
  
  /**
   * sanitize_text_field
   * Sanitize a string from user input.
   * Uses the WordPress function sanitize_text_field()
   *
   * @param $value
   * @param $field
   * @param $options
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function sanitize_text_field($value, $field, $options)
  {
    return sanitize_text_field($value);
  }

  /**
   * sanitize_title
   * -HTML and PHP tags are stripped
   * -Accents are removed (accented characters are replaced with non-accented equivalents).
   * Uses the WordPress function sanitize_title();
   *
   * @param $value
   * @param $field
   * @param $options
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function sanitize_title($value, $field, $options = null)
  {
    $options = wp_parse_args($options, array(
      array()
    ));

    extract($options);

    return sanitize_title($value, isset($fallback) ? $fallback : null, isset($context) ? $context : null);
  }

  /**
   * sanitize_user
   * Sanitize username stripping out unsafe characters.
   * Uses WordPress function sanitize_user()
   *
   * @param $value
   * @param $field
   * @param $options
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function sanitize_user($value, $field, $options = null)
  {
    $options = wp_parse_args($options, array(
      array()
    ));

    extract($options);
    
    return sanitize_user($value, isset($strict) ? $strict : null);
  }

  /**
   * sanitize_wp_kses
   * Makes sure that only the allowed HTML element names, attribute names and attribute values plus only sane HTML entities will occur in $string. 
   * Uses the WordPress function wp_kses()
   * 
   * accepts
   * array allowed_html
   * array allowed_protocols
   * 
   * @param $value
   * @param $field
   * @param $options
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function sanitize_wp_kses($value, $field, $options = null)
  {
    $options = wp_parse_args($options, array(
      array()
    ));

    extract($options);
    
    return wp_kses($value, isset($allowed_html) ? $allowed_html : null, isset($allowed_protocols) ? $allowed_protocols : null);
  }

  /**
   * sanitize_wp_kses_post
   * Sanitize content for allowed HTML tags for post content.
   * Uses the WordPress function wp_kses_post()
   *
   * @param $value
   * @param $field
   * @param $options
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function sanitize_wp_kses_post($value, $field, $options)
  {
    return wp_kses_post($value);
  }

  /**
   * sanitize_wp_filter_kses
   * Sanitize content with allowed HTML Kses rules.
   * Uses the WordPress function wp_kses_data()
   *
   * @param $value
   * @param $field
   * @param $options
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function sanitize_wp_filter_kses($value, $field, $options)
  {
    return wp_kses_data($value);
  }

  /**
   * sanitize_wp_strip_all_tags
   * Properly strip all HTML tags including script and style.
   * Uses the WordPress function wp_strip_all_tags()
   *
   * @param $value
   * @param $field
   * @param $options
   *
   * @return
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function sanitize_wp_strip_all_tags($value, $field, $options = null)
  {
    $options = wp_parse_args($options, array(
      array()
    ));

    extract($options);

    return wp_strip_all_tags($value, isset($remove_breaks) ? $remove_breaks : null);
  }

  /**
   * Sanitizes a hex color.
   *
   * Returns either '', a 3 or 6 digit hex color (with #), or null.
   *
   * @since 1.0
   * based on WordPress: wp-includes/class-wp-customize-manager.php
   *
   * @param string $color
   * @return string|null
   */
  public static function sanitize_hex_color($color)
  {
    if ('' === $color)
    {
      return '';
    }

    // 3 or 6 hex digits, or the empty string.
    if(preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color))
    {
      return $color;
    }

    return null;
  }

}