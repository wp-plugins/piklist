<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Piklist
 * Core functionality for Piklist.
 *
 * @package     Piklist
 * @copyright   Copyright (c) 2012-2015, Piklist, LLC.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
class Piklist
{
  /**
   * @var string The current version of Piklist.
   * @access public
   */
  public static $version;

  /**
   * @var array Data for all the areas Piklist can be found.
   * @access public
   */
   public static $add_ons = array();

  /**
   * @var array The urls to all areas Piklist can be found.
   * @access public
   */
  public static $urls = array();

  /**
   * @var array The paths to all areas Piklist can be found.
   * @access public
   */
  public static $paths = array();

  /**
   * @var array Conversions for plurals.
   * @access public
   */
  public static $plurals = array(
    'plural' => array(
      '/(quiz)$/i' => "$1zes"
      ,'/^(ox)$/i' => "$1en"
      ,'/([m|l])ouse$/i' => "$1ice"
      ,'/(matr|vert|ind)ix|ex$/i' => "$1ices"
      ,'/(x|ch|ss|sh)$/i' => "$1es"
      ,'/([^aeiouy]|qu)y$/i' => "$1ies"
      ,'/(hive)$/i' => "$1s"
      ,'/(?:([^f])fe|([lr])f)$/i' => "$1$2ves"
      ,'/(shea|lea|loa|thie)f$/i' => "$1ves"
      ,'/sis$/i' => "ses"
      ,'/([ti])um$/i' => "$1a"
      ,'/(tomat|potat|ech|her|vet)o$/i' => "$1oes"
      ,'/(bu)s$/i' => "$1ses"
      ,'/(alias)$/i' => "$1es"
      ,'/(octop)us$/i' => "$1i"
      ,'/(ax|test)is$/i' => "$1es"
      ,'/(us)$/i' => "$1es"
      ,'/s$/i' => "s"
      ,'/$/' => "s"
    )
    ,'singular' => array(
      '/(quiz)zes$/i' => "$1"
      ,'/(matr)ices$/i' => "$1ix"
      ,'/(vert|ind)ices$/i'  => "$1ex"
      ,'/^(ox)en$/i' => "$1"
      ,'/(alias)es$/i' => "$1"
      ,'/(octop|vir)i$/i' => "$1us"
      ,'/(cris|ax|test)es$/i' => "$1is"
      ,'/(shoe)s$/i' => "$1"
      ,'/(o)es$/i' => "$1"
      ,'/(bus)es$/i' => "$1"
      ,'/([m|l])ice$/i' => "$1ouse"
      ,'/(x|ch|ss|sh)es$/i' => "$1"
      ,'/(m)ovies$/i' => "$1ovie"
      ,'/(s)eries$/i' => "$1eries"
      ,'/([^aeiouy]|qu)ies$/i' => "$1y"
      ,'/([lr])ves$/i' => "$1f"
      ,'/(tive)s$/i' => "$1"
      ,'/(hive)s$/i' => "$1"
      ,'/(li|wi|kni)ves$/i' => "$1fe"
      ,'/(shea|loa|lea|thie)ves$/i' => "$1f"
      ,'/(^analy)ses$/i' => "$1sis"
      ,'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => "$1$2sis"
      ,'/([ti])a$/i' => "$1um"
      ,'/(n)ews$/i' => "$1ews"
      ,'/(h|bl)ouses$/i' => "$1ouse"
      ,'/(corpse)s$/i' => "$1"
      ,'/(us)es$/i' => "$1"
      ,'/ss$/i' => "ss"
      ,'/s$/i' => ""
    )
    ,'irregular' => array(
      'move' => 'moves'
      ,'foot' => 'feet'
      ,'goose' => 'geese'
      ,'sex' => 'sexes'
      ,'child' => 'children'
      ,'man' => 'men'
      ,'tooth' => 'teeth'
      ,'person' => 'people'
    )
    ,'ignore' => array(
      'sheep'
      ,'fish'
      ,'deer'
      ,'series'
      ,'species'
      ,'money'
      ,'rice'
      ,'information'
      ,'equipment'
      ,'media'
      ,'documentation'
    )
  );

  /**
   * @var string The prefix used for all piklist field names and ids.
   * @access public
   */
  public static $prefix = '_';

  /**
   * @var array Holds all processed parts by folder.
   * @access public
   */
  private static $processed_parts = array();

  /**
   * load
   * Load resources, classes and add-ons.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function load()
  {
    self::add_plugin('piklist', dirname(dirname(__FILE__)));

    self::$version = current(self::get_file_data(self::$add_ons['piklist']['path'] . '/piklist.php', array('version' => 'Version')));

    load_plugin_textdomain('piklist', false, 'piklist/languages/');

    register_activation_hook('piklist/piklist.php', array('piklist', 'activate'));

    self::auto_load();

    add_filter('piklist_part_data', array('piklist', 'part_data'), 10, 2);

    add_action('admin_init', array('piklist', 'process_parts_callback'), 1000);
    add_action('admin_head', array('piklist', 'process_parts_callback'), 1000);
    add_action('template_redirect', array('piklist', 'process_parts_callback'), 0);
    add_action('piklist_widgets_post_register', array('piklist', 'process_parts_callback'), 1000);
    
    add_filter('piklist_workflow_part_exclude_folders', array('piklist', 'part_exclude_folders'), 10, 3);
  }

  /**
   * auto_load
   * Auto load all classes in the includes directory of a plugin.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function auto_load($addon = 'piklist')
  {
    if (isset(self::$add_ons[$addon]['path']))
    {
      $includes = self::get_directory_list(self::$add_ons[$addon]['path'] . '/includes');
     
      foreach ($includes as $include)
      {
        $class_name = str_replace(array('.php', 'class_'), array('', ''), self::slug($include));
     
        if ($include != __FILE__)
        {
          if (!class_exists($class_name))
          {
            include_once self::$add_ons[$addon]['path'] . '/includes/' . $include;

            if (method_exists($class_name, '_construct') && !is_subclass_of($class_name, 'WP_Widget'))
            {
              call_user_func(array($class_name, '_construct'));
            }
          }

        }
      }
    }
  }

  /**
   * activate
   * Fire activation hook for Piklist.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function activate()
  {
    piklist::check_network_propagate('do_action', 'piklist_activate');
  }

  /**
   * paths
   * Retrieve all addon urls.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function paths()
  {
    $paths = array();
    foreach(self::$add_ons as $addon => $data)
    {
      $paths[$addon] = $data['path'];
    }
    return $paths;
  }

  /**
   * search_addons
   * Search addon properties and return addon key or data
   *
   * @param string $key Key within addons to search through.
   * @param mixed $value Value to search for.
   * @param bool $return_data Whether to return the addon data or addon key.
   *
   * @return bool|string|array Returns false if not found, otherwise the data or key.
   *
   * @access public
   * @static
   * @since 1.0
   *
   */
  public static function search_addons($key, $value, $return_data = false)
  {
    foreach(self::$add_ons as $addon => $data)
    {
      if (false !== array_search($value, $data[$key]))
      {
        if ($return_data)
        {
          return $addon;
        }
        else
        {
          return $data;
        }
      }
    }

    return false;
  }

  /**
   * add_plugin
   * Add a plugin or add-on to the paths and urls objects.
   *
   * @param string $type Slug for the plugin.
   * @param string $path Path to the plugin.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function add_plugin($type, $path)
  {
    self::$add_ons[$type]['path'] = stristr($path, ':\\') || stristr($path, ':/') ? str_ireplace('/', '\\', $path) : $path;
    self::$paths[$type] = &self::$add_ons[$type]['path'];

    $path = str_replace(chr(92), '/', $path);

    self::$add_ons[$type]['url'] = plugins_url() . substr($path, strrpos($path, '/'));
    self::$urls[$type] = &self::$add_ons[$type]['url'];

    /**
     * piklist_parts_process
     * Signals that parts are in process.
     *
     * @param  array $processed parts so far.
     * @param  var $folder the parts folder where the file is located.
     *
     * @since 1.0
     */
    do_action('piklist_plugin_loaded-' . $type);
  }

  /**
   * wp_globals
   * Returns an array with all the standard wordpress globals, protecting the real globals
   *
   * @return array
   *
   * @access private
   * @static
   * @since 1.0
   */
  private static function wp_globals()
  {
    $globals = array(
      'authordata'
      ,'comment'
      ,'current_screen'
      ,'current_user'
      ,'hook_suffix'
      ,'is_apache'
      ,'is_IIS'
      ,'is_iis7'
      ,'is_iphone'
      ,'is_chrome'
      ,'is_safari'
      ,'is_NS4'
      ,'is_opera'
      ,'is_macIE'
      ,'is_winIE'
      ,'is_gecko'
      ,'is_lynx'
      ,'is_IE'
      ,'l10n'
      ,'locale'
      ,'pagenow'
      ,'typenow'
      ,'post'
      ,'post_id'
      ,'posts'
      ,'profileuser'
      ,'taxnow'
      ,'user_ID'
      ,'wp'
      ,'wp_admin_bar'
      ,'wp_broken_themes'
      ,'wp_db_version'
      ,'wp_did_header'
      ,'wp_did_template_redirect'
      ,'wp_file_description'
      ,'wp_filter'
      ,'wp_importers'
      ,'wp_plugins'
      ,'wp_post_statuses'
      ,'wp_themes'
      ,'wp_object_cache'
      ,'wp_query'
      ,'wp_queries'
      ,'wp_rewrite'
      ,'wp_roles'
      ,'wp_similiesreplace'
      ,'wp_smiliessearch'
      ,'wp_version'
      ,'wpcommentspopupfile'
      ,'wpcommentsjavascript'
      ,'wpdb'
    );

    foreach ($globals as $key => $global)
    {
      global $$global;

      $globals[$global] = $$global;

      unset($globals[$key]);
    }

    return $globals;
  }

  /**
   * render
   * Renders a file from the parts directory with global arguments.
   *
   * @param string $view File to display; relative path for a local lookup or absolute for a specific lookup.
   * @param array $arguments Variables to be passed to the view globally.
   * @param bool $return Whether to print or return the output.
   * @param array $loop An object with data to be iterated over while rendered.
   *
   * @return string Output from the file rendering.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function render($view, $arguments = array(), $return = false, $loop = null)
  {
    $view .= strstr($view, '.php') ? '' : '.php';

    if (($view_is_absolute = self::path_is_absolute($view)) === true)
    {
      $_file = $view;
    }
    else
    {
      $_backtrace = debug_backtrace();
      $_origin = isset($_backtrace[1]['file']) ? $_backtrace[1]['file'] : '';
      $_origin = apply_filters('piklist_render_origin', $_origin, $view, $arguments, $return, $loop);

      if ($_origin && self::path_is_absolute($_origin))
      {
        $_path = substr($_origin, 0, strrpos($_origin, '/'));
        $_theme_paths = array(get_template_directory(), get_stylesheet_directory());

        foreach ($_theme_paths as $_theme_path)
        {
          if ($_path == $_theme_path && file_exists($_theme_path . '/' . $view))
          {
            $_file = "$_theme_path/$view";
          }
        }
      }
    }
    
    if (!isset($_file))
    {
      $_paths = self::paths();

      if ($view_is_absolute && (false !== strpos($view, '/parts/')) && preg_match('~(?<base>.+(?:piklist)?)/parts/(?<view>.+)$~i', $view, $_matches))
      {
        if (false !== ($_add_on = array_search($_matches['base'], $_paths)))
        {
          $view = $_matches['view'];
          $_paths = array($_add_on => $_paths[$_add_on]) + $_paths;
        }
        else
        {
          return $return ? '' : null;
        }
      }

      foreach ($_paths as $_add_on => $_path)
      {
        $_file = (self::path_is_absolute($view) ? null : self::$add_ons[$_add_on]['path'] . '/parts/') . $view . (strstr($view, '.php') ? '' : '.php');

        if (file_exists($_file))
        {
          $view = $_file;
        }
      }
    }
    else
    {
      $view = $_file;
    }

    if ($return)
    {
      ob_start();
    }

    $_wp_globals = self::wp_globals();

    $_arguments = array($_wp_globals['wp_query']->query_vars);

    if (isset($arguments) && !empty($arguments))
    {
      array_push($_arguments, $arguments);
    }


    foreach ($_arguments as $_object)
    {
      foreach ($_object as $_key => $_value)
      {
        if (isset($_wp_globals[$_key]))
        {
          $trigger_error_message = sprintf(__('is a reserved WordPress global variable and cannot be passed as an argument to %s', 'piklist'), 'piklist::render()');

          trigger_error('$' . $_key . " " . $trigger_error_message, E_USER_WARNING);
        }
        else
        {
          $$_key = $_value;
        }
      }
    }

    // Bring WP globals into scope
    extract($_wp_globals);

    /**
     * piklist_render
     *
     * @since 1.0
     */
    $view = apply_filters('piklist_render', $view, $arguments);

    if (file_exists($view))
    {
      if ($loop)
      {
        for ($i = 0; $i < count($arguments[$loop]); $i++)
        {
          $$loop = $arguments[$loop][$i];

          include $view;
        }
      }
      else
      {
        include $view;
      }
    }
    else
    {
      if (dirname($view) != str_replace('/.php', '', $view))
      {
        trigger_error(sprintf(__('File does not exist%s', 'piklist'), ': ' . $view), E_USER_WARNING);
      }
    }

    if ($return)
    {
      $output = ob_get_contents();

      ob_end_clean();

      return trim($output);
    }
  }

  /**
   * process_parts
   * Scan and process all views in a specified folder.
   *
   * @param string $folder Folder name inside the parts folder to inspect.
   * @param array $data Comment block data at the top of the view to filter.
   * @param string|array $callback Function to call when processing the view.
   * @param string $path Path(s) to scan for parts and folder combinations.
   * @param string $prefix Prefix to require of the views.
   * @param string $suffix Suffix to require of the views.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function process_parts($folder, $data = array(), $callback = null, $path = null, $prefix = '', $suffix = '.php')
  {
    if (!isset(self::$processed_parts[$folder]))
    {
      self::$processed_parts[$folder] = array(
        'callback' => $callback
        ,'parts' => array()
      );
    }

    $parts = array();

    $paths = $path ? $path : self::paths();
    $paths = is_array($paths) ? $paths : array($paths);

    /**
     * piklist_part_data
     * Add additional parameters to read from file comment blocks.
     *
     * @param  array $data comment block parameters
     * @param  var $folder the parts folder where the file is located.
     *
     * @since 1.0
     */
    $data = array_merge(apply_filters('piklist_part_data', $data, $folder), $data);

    foreach ($paths as $add_on => $path)
    {
      $files = self::get_directory_list($path . '/parts/' . $folder);

      if (empty($files) && in_array($add_on, array('theme', 'parent-theme')))
      {
        $files = self::get_directory_list($path . '/' . $folder);
      }

      foreach ($files as $part)
      {
        if (strtolower($part) != 'index.php' && substr($part, 0, strlen($prefix)) == $prefix && substr($part, strlen($part) - strlen($suffix)) == $suffix)
        {
          $render = $path . '/parts/' . $folder . '/' . $part;
          $part_data = self::get_file_data($render, $data);

          $_part = array(
            'id' => !empty($part_data['extend']) ? $part_data['extend'] : apply_filters("piklist_part_id-{$folder}", piklist::slug($add_on . ' ' . $part), $add_on, $part, $part_data)
            ,'folder' => $folder
            ,'part' => $part
            ,'data' => $part_data
            ,'prefix' => $prefix
            ,'add_on' => $add_on
            ,'path' => $path
            ,'render' => array(
              $render
            )
          );

          /**
           * piklist_part_process
           * Post-process for a part.
           *
           * @param  array $part being validated
           * @param  var $folder the parts folder where the file is located.
           *
           * @since 1.0
           */
          $_part = apply_filters('piklist_part_process', $_part, $folder);

          /**
           * piklist_part_process-FOLDER
           * Post-process for a part by folder.
           *
           * @param  array $part being validated
           *
           * @since 1.0
           */
          $_part = apply_filters("piklist_part_process-{$folder}", $_part);

          if ($_part)
          {
            array_push(self::$processed_parts[$folder]['parts'], $_part);
          }
        }
      }
    }

    $extensions = array();
    
    // Move extensions to the end of the list
    uasort(self::$processed_parts[$folder]['parts'], array('piklist', 'sort_by_data_extend'));
    self::$processed_parts[$folder]['parts'] = array_values(self::$processed_parts[$folder]['parts']);
    
    foreach (self::$processed_parts[$folder]['parts'] as $current_index => &$part)
    {
      if (isset(self::$processed_parts[$folder]['parts'][$current_index + 1]))
      {
        for ($index = $current_index + 1; $index < count(self::$processed_parts[$folder]['parts']); $index++)
        {
          $extend = self::$processed_parts[$folder]['parts'][$index];

          if ($part['id'] == $extend['id'])
          {
            foreach ($part['data'] as $attribute => &$data)
            {
              if (!in_array($attribute, array('extend', 'extend_method')) && (!empty($extend['data'][$attribute]) || is_bool($extend['data'][$attribute])))
              {
                $data = is_array($extend['data'][$attribute]) && is_array($data) ? array_unique(array_merge($extend['data'][$attribute], $data), SORT_REGULAR) : $extend['data'][$attribute];
              }
            }
            
            array_push($extensions, $index);

            foreach ($extend['render'] as $render)
            {
              if (!in_array($render, $part['render']))
              {
                switch ($extend['data']['extend_method'])
                {
                  case 'before':
                    array_unshift($part['render'], $render);
                  break;

                  case 'replace':
                    $part['render'] = array($render);
                  break;

                  case 'after':
                  default:
                    array_push($part['render'], $render);
                  break;
                }
              }
            }
          }
        }
      }
    }
    
    foreach ($extensions as $index)
    {
      unset(self::$processed_parts[$folder]['parts'][$index]);
    }

    self::$processed_parts[$folder]['parts'] = array_values(self::$processed_parts[$folder]['parts']);

    /**
     * piklist_part_add
     * Add additional parts to processed list.
     *
     * @param  array $processed parts so far.
     * @param  var $folder the parts folder where the file is located.
     *
     * @since 1.0
     */
    self::$processed_parts[$folder]['parts'] = array_merge(apply_filters('piklist_part_add', array(), $folder), self::$processed_parts[$folder]['parts']);

    /**
     * piklist_part_add-FOLDER
     * Add additional parts to processed list.
     *
     * @param  array $processed parts so far.
     *
     * @since 1.0
     */
    self::$processed_parts[$folder]['parts'] = array_merge(apply_filters("piklist_part_add-{$folder}", array()), self::$processed_parts[$folder]['parts']);

    // Move extensions to the end of the list
    uasort(self::$processed_parts[$folder]['parts'], array('piklist', 'sort_by_data_extend'));
    self::$processed_parts[$folder]['parts'] = array_values(self::$processed_parts[$folder]['parts']);
    
    /**
     * piklist_parts_process
     * Signals that parts are in process.
     *
     * @param  array $processed parts so far.
     * @param  var $folder the parts folder where the file is located.
     *
     * @since 1.0
     */
    do_action('piklist_parts_process', $folder);
    
    /**
     * piklist_parts_process-FOLDER
     * Signals that parts are in process.
     *
     * @param  array $processed parts so far.
     * @param  var $folder the parts folder where the file is located.
     *
     * @since 1.0
     */
    do_action("piklist_parts_process-{$folder}");

    if (is_null($callback))
    {
      $parts = array();

      foreach (self::$processed_parts[$folder]['parts'] as $_part)
      {
        if (self::validate_part($_part))
        {
          array_push($parts, $_part);
        }
      }

      return $parts;
    }
  }

  /**
   * get_processed_parts
   * Get the list of processed parts if it has been processed.
   *
   * @param  var $folder the parts folder where the file is located.
   *
   * @since 1.0
   */
  public static function get_processed_parts($folder)
  {
    return isset(self::$processed_parts[$folder]) ? self::$processed_parts[$folder] : null;
  }

  /**
   * process_parts_callback
   * Process any callbacks for processed parts.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function process_parts_callback()
  {
    $processed_parts = array_reverse(self::$processed_parts);

    foreach ($processed_parts as $folder => $processed)
    {
      foreach ($processed['parts'] as $part)
      {
        /**
         * piklist_part_process_callback
         * Post-process for a part.
         *
         * @param  array $part being validated
         * @param  var $folder the parts folder where the file is located.
         *
         * @since 1.0
         */
        $part = apply_filters('piklist_part_process_callback', $part, $folder);

        /**
         * piklist_part_process_callback-FOLDER
         * Post-process for a part by folder.
         *
         * @param  array $part being validated
         *
         * @since 1.0
         */
        $part = apply_filters("piklist_part_process_callback-{$folder}", $part);

        if ($part && self::validate_part($part))
        {
          call_user_func_array($processed['callback'], array($part));
        }
      }

      do_action('piklist_parts_processed', $folder);
    
      do_action("piklist_parts_processed-{$folder}");

      unset(self::$processed_parts[$folder]);
    }
  }

  /**
   * part_data
   * Adds tab to all part types for easy association
   *
   * @param array $data The part object.
   * @param string $folder The folder name.
   *
   * @return array The part object.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function part_data($data, $folder)
  {
    $data['extend'] = 'Extend';
    $data['extend_method'] = 'Extend Method';

    return $data;
  }

  /**
   * get_file_data
   * Get file data and try and cast the values.
   *
   * @param string $file File path to read.
   * @param array $data Attributes to fetch.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function get_file_data($file, $data)
  {
    $data = get_file_data($file, $data);
   
    array_walk_recursive($data, array('piklist', 'array_values_cast'));

    foreach ($data as $parameter => &$value)
    {
      switch ($parameter)
      {
        case 'capability':
        case 'id':
        case 'page':
        case 'post_type':
        case 'role':
        case 'status':
        case 'taxonomy':
        case 'post_format':

          $value = piklist::explode(',', $value, 'strtolower');
          $value = array_filter($value);
          $value = empty($value) ? null : $value;

        break;

        case 'template':

          $value = piklist::explode(',', $value, 'strtolower');
          $value = str_ireplace('.php', '', $value);
          $value = array_filter($value);
          $value = empty($value) ? null : $value;

        break;

        case 'flow':
        case 'flow_page':
        case 'tab':
        case 'sub_tab':

          $value = piklist::explode(',', $value, array('piklist', 'slug'));
          $value = array_filter($value);
          $value = empty($value) ? null : $value;

        break;

        default:

          /**
           * piklist_part_data_parameter
           * Add custom part parameters to check.
           *
           * @param $value Value to compare.
           * @param $parameter Parameter to check.
           *
           * @since 1.0
           */
          $value = apply_filters('piklist_part_data_parameter', $value, $parameter);

        break;
      }
    }

    return $data;
  }

  /**
   * validate_part
   * Check to see if a part should be registered
   *
   * @param array $part Comment block data at the top of the view.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function validate_part($part)
  {
    if (isset($part['data']))
    {
      foreach ($part['data'] as $parameter => $value)
      {
        if (!empty($value))
        {
          if (!self::validate_part_parameter($parameter, $value) && !apply_filters('piklist_validate_part_parameter_skip', false, $parameter, $part))
          {
            return false;
          }
        }
      }
    }

    return true;
  }

  /**
   * validate_part_parameter
   * Check to see if the paramter passes validation.
   *
   * @param string $parts The parameter name.
   * @param mixes $parts The parameter value.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function validate_part_parameter($parameter, $value)
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

      case 'post_type':

        return ($post && in_array($post->post_type, $value)) || !$post;

      break;

      case 'status':
      case 'post_status':

        return ($post && in_array($post->post_status, $value)) || !$post;

      break;

      case 'new':

        return $value == 'true' ? $pagenow == 'post-new.php' : true;

      break;

      case 'page':

        $current = array($pagenow);

        if (!empty($current_screen->id))
        {
          array_push($current, $current_screen->id);
        }

        if (!empty($_REQUEST['page']))
        {
          array_push($current, $_REQUEST['page']);
        }
        
        return array_intersect($value, $current);

      break;

      case 'id':

        return $post && in_array($post->ID, $value);

      break;

      case 'template':

        $page_template = ($post->post_status == 'auto-draft') ? 'default' : strtolower(str_replace('.php', '', get_post_meta($post->ID, '_wp_page_template', true)));

        return in_array($page_template, $value);

      break;

      case 'post_format':

        $format = get_post_format($post->ID);
        $format = empty($format) ? 'standard' : $format;

        return in_array($format, $value);

      break;

      case 'network':

        if (isset($current_screen) && $current_screen->id == 'dashboard-network')
        {
          return $value || $value == 'only';
        }
        elseif (isset($current_screen) && $current_screen->id == 'dashboard')
        {
          return $value === true;
        }

      break;

      default:

        /**
         * piklist_validate_part_parameter
         * Add custom part parameters to check.
         *
         * @param $parameter Parameter to check.
         * @param $value Value to compare.
         *
         * @since 1.0
         */
        return apply_filters('piklist_validate_part_parameter', true, $parameter, $value);

      break;
    }
  }
  
  /**
   * part_exclude_folders
   * Used to exclude core files from being affected by workflows
   *
   * @param mixed $output Information to output.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function part_exclude_folders($folders, $part, $folder)
  {
    if ($part['add_on'] == 'piklist')
    {
      $folders = array_merge($folders, array(
        'dashboard'
        ,'forms'
        ,'help'
        ,'media'
        ,'meta-boxes'
        ,'notices'
        ,'pointers'
        ,'terms'
        ,'users'
      ));
    }

    return $folders;
  }

  /**
   * pre
   * Used for debugging to output information to the screen.
   *
   * @param mixed $output Information to output.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function pre($output = '-')
  {
    $output = $output === '-' ? '--------------------------------------------------' : $output;

    echo "<pre>\r\n";

    print_r($output);

    echo "</pre>\r\n";

    $output = ob_get_contents();

    if (!empty($output))
    {
      @ob_flush();
      @flush();
    }
  }

  /**
   * console
   * Used for debugging to output information to the browser console.
   *
   * @param mixed $output Information to output.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function console($output)
  {
    ?><script>window.console && console.log(<?php echo json_encode($output); ?>);</script><?php
  }

  /**
   * get_directory_list
   * Gets a list of files in a directory.
   *
   * @param string $start Relative path to inspect.
   * @param bool $path Whether or not to include the path.
   * @param bool $extension Whether or not to include the extension.
   *
   * @return array List of files.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function get_directory_list($start = '.', $path = false, $extension = false)
  {
    $files = array();

    if (is_dir($start))
    {
      $file_handle = opendir($start);

      while (($file = readdir($file_handle)) !== false)
      {
        if ($file != '.' && $file != '..' && strlen($file) > 2)
        {
          if (strcmp($file, '.') == 0 || strcmp($file, '..') == 0)
          {
            continue;
          }

          if ($file[0] != '.' && $file[0] != '_')
          {
            $file_parts = explode('.', $file);
            $_file = $extension ? $file : $file_parts[0];
            $file_path = $path ? $start . '/' . $_file : $_file;

            if (is_dir($file_path))
            {
              $files = array_merge($files, self::get_directory_list($file_path, $path, $extension));
            }
            else
            {
              array_push($files, $path ? $file_path : $file);
            }
          }
        }
      }

      closedir($file_handle);
    }
    else
    {
      $files = array();
    }
    
    return $files;
  }

  /**
   * dashes
   * Converts a string to lowercase and spaces to dashes.
   *
   * @param string $string String to dash.
   *
   * @return string Dashed string.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function dashes($string, $encoding = false)
  {
    return str_replace(array('_', ' '), '-', preg_replace('/[^\P{P}\-_]+/u', '', str_replace('.php', '', $encoding ? mb_strtolower($string, $encoding) : strtolower($string))));
  }

  /**
   * slug
   * Converts a string to lowercase and spaces/dashes to underscores.
   *
   * @param string $string String to slug.
   *
   * @return string Slugged string.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function slug($string, $encoding = false)
  {
    return str_replace(array('-', ' '), '_', preg_replace('/[^\P{P}\-_]+/u', '', str_replace('.php', '', $encoding ? mb_strtolower($string, $encoding) : strtolower($string))));
  }

  /**
   * humanize
   * Converts a string to human readable string, concept borrowed from RoR.
   *
   * @param string $string String to humanize.
   *
   * @return string Humanized string.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function humanize($string)
  {
    return implode(' ', array_map('ucwords', explode(' ', preg_replace('/\s+/', ' ', preg_replace('/[^a-z0-9\s+]/', ' ', trim(strtolower($string)))))));
  }

  /**
   * check_network_propagate
   * Propogate function call through network if necessary.
   *
   * @param string|array $callback Function to call.
   * @param mixed $arguments Arguments to pass to callback.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function check_network_propagate($callback, $arguments)
  {
    global $wpdb;

    if (function_exists('is_multisite') && is_multisite())
    {
      if (is_network_admin())
      {
        $core = $wpdb->blogid;
        $ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
        
        foreach ($ids as $id)
        {
          switch_to_blog($id);

          call_user_func($callback, $arguments);
        }
        
        switch_to_blog($core);
      }
      else
      {
        call_user_func($callback, $arguments);
      }
    }
    else
    {
      call_user_func($callback, $arguments);
    }
  }

  /**
   * create_table
   * Create a mySQL table.
   *
   * @param string $table_name Table name.
   * @param array $columns List of columns for the table
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function create_table($table_name, $columns)
  {
    global $wpdb;

    $settings = $wpdb->has_cap('collation') ? (!empty($wpdb->charset) ? 'DEFAULT CHARACTER SET ' . $wpdb->charset : null) . (!empty($wpdb->collate) ? ' COLLATE ' . $wpdb->collate : null) : null;

    $wpdb->query('CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . $table_name . ' (' . $columns . ') ' . $settings . ';');
  }

  /**
   * delete_table
   * Delete a mySQL table.
   *
   * @param string $table_name Table name.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function delete_table($table_name)
  {
    global $wpdb;

    $wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . $table_name);
  }

  /**
   * post_type_labels
   * Create detailed post type labels.
   *
   * @param string $label Singular label.
   *
   * @return array List of all labels accepted by register_post_type.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function post_type_labels($label)
  {
    return array(
      'name' => __(self::singularize($label), 'piklist')
      ,'singular_name' => __(self::singularize($label), 'piklist')
      ,'all_items' => __('All ' . self::pluralize($label), 'piklist')
      ,'add_new' => __('Add New', 'piklist')
      ,'add_new_item' => __('Add New ' . self::singularize($label), 'piklist')
      ,'edit_item' => __('Edit ' . self::singularize($label), 'piklist')
      ,'new_item' => __('Add New ' . self::singularize($label), 'piklist')
      ,'view_item' => __('View ' . self::singularize($label), 'piklist')
      ,'search_items' => __('Search ' . self::pluralize($label), 'piklist')
      ,'not_found' => __('No ' . self::pluralize($label) . ' found', 'piklist')
      ,'not_found_in_trash' => __('No ' . self::pluralize($label) . ' found in trash', 'piklist')
      ,'parent_item_colon' => __('Parent ' . self::pluralize($label) . ':', 'piklist')
      ,'menu_name' => __(self::pluralize($label), 'piklist')
    );
  }

  /**
   * taxonomy_labels
   * Create detailed taxonomy labels.
   *
   * @param string $label Singular label.
   *
   * @return array List of all labels accepted by register_taxonomy.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function taxonomy_labels($label)
  {
    return array(
      'name' => __(self::singularize($label), 'piklist')
      ,'singular_name' => __(self::singularize($label), 'piklist')
      ,'search_items' =>  __('Search ' . self::pluralize($label), 'piklist')
      ,'all_items' => __('All ' . self::pluralize($label), 'piklist')
      ,'parent_item' => __('Parent '  . self::pluralize($label), 'piklist')
      ,'parent_item_colon' => __('Parent ' . self::pluralize($label) . ':', 'piklist')
      ,'edit_item' => __('Edit ' . self::singularize($label), 'piklist')
      ,'update_item' => __('Update ' . self::singularize($label), 'piklist')
      ,'add_new_item' => __('Add New ' . self::singularize($label), 'piklist')
      ,'view_item' => __('View ' . self::singularize($label), 'piklist')
      ,'popular_items' => __('Popular ' . self::pluralize($label), 'piklist')
      ,'new_item_name' => __('New ' . self::singularize($label) . ' Name', 'piklist')
      ,'separate_items_with_commas' => __('Separate ' . self::pluralize($label) . ' with commas', 'piklist')
      ,'add_or_remove_items' => __('Add or remove ' . self::pluralize($label), 'piklist')
      ,'choose_from_most_used' => __('Choose from the most used ' . self::pluralize($label), 'piklist')
      ,'not_found' => __('No ' . self::pluralize($label) . ' found.', 'piklist')
      ,'menu_name' => __(self::pluralize($label), 'piklist')
      ,'name_admin_bar' => $label
    );
  }

  /**
   * pluralize
   * Pluralize a singular word.
   *
   * @param string $string Word to pluralize.
   *
   * @return Pluralized word.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function pluralize($string)
  {
    if ((in_array(strtolower($string), self::$plurals['ignore'])) || (strrpos($string, ' ') && in_array(strtolower(substr($string, strrpos($string, ' ') + 1, strlen($string) - strrpos($string, ' ') + 1)), self::$plurals['ignore'])))
    {
      return $string;
    }

    foreach (self::$plurals['irregular'] as $pattern => $result)
    {
      $pattern = '/' . $pattern . '$/i';
      if (preg_match($pattern, $string))
      {
        return preg_replace($pattern, $result, $string);
      }
    }

    foreach (self::$plurals['plural'] as $pattern => $result)
    {
      if (preg_match($pattern, $string))
      {
        return preg_replace($pattern, $result, $string);
      }
    }

    return $string;
  }

  /**
   * singularize
   * Singularize a plural word.
   *
   * @param string $string Word to singularize.
   *
   * @return Singularized word.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function singularize($string)
  {
    if (in_array(strtolower($string), self::$plurals['ignore']))
    {
      return $string;
    }

    foreach (self::$plurals['irregular'] as $pattern => $result)
    {
      $pattern = '/' . $pattern . '$/i';
      if (preg_match($pattern, $string))
      {
        return preg_replace($pattern, $result, $string);
      }
    }

    foreach (self::$plurals['singular'] as $pattern => $result)
    {
      if (preg_match($pattern, $string))
      {
        return preg_replace($pattern, $result, $string);
      }
    }

    return $string;
  }

  /**
   * add_admin_menu_separator
   * Insert a seperator in the admin menu at a specified position.
   *
   * @param int $position Position to insert the seperator.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function add_admin_menu_separator($position)
  {
    global $menu;

    if (isset($menu) && !empty($menu))
    {
      $index = 0;

      foreach ($menu as $offset => $section)
      {
        if (substr($section[2], 0, 9) == 'separator')
        {
          $index++;
        }

        if ($offset >= $position)
        {
          $menu[$position] = array(
            ''
            ,'read'
            ,'separator' . $index
            ,''
            ,'wp-menu-separator'
          );

          ksort($menu);

          break;
        }
      }
    }
  }

  /**
   * array_paths
   * Get the array paths in an object
   *
   * @param array $array Array to search.
   * @param array $path Path searching.
   * @param string $delimiter Delimeter for path keys.
   *
   * @return array Map of paths
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function array_paths($array, $path = array(), $delimiter = ':') 
  {
    $map = array();
   
    if (!empty($array))
    {
      foreach ($array as $key => $value)
      {
        $current_path = array_merge($path, array($key));
      
        if (is_array($value)) 
        {
          $map = array_merge($map, self::array_paths($value, $current_path, $delimiter));
        } 
        else 
        {
          $map[] = join($delimiter, $current_path);
        }
      }
    }
    
    return $map;
  }

  /**
   * array_path_get
   * Get value from array given key path.
   *
   * @param array $array Array to get value from.
   * @param string|array $path Path to get.
   *
   * @return array Found value.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function array_path_get($array, $path)
  {
    if (!$path)
    {
      return false;
    }

    $map = is_array($path) ? $path : explode('/', $path);
    $found =& $array;

    foreach ($map as $part)
    {
      if (!array_key_exists($part, $found))
      {
        return null;
      }

      $found = $found[$part];
    }

    return $found;
  }

  /**
   * array_path_set
   * Set value from key path.
   *
   * @param array $array Array to set value to.
   * @param string|array $path Path to set.
   * @param string|array $value Value to set.
   *
   * @return bool Whether the array was updated.
   *
   * @access
   * @static
   * @since 1.0
   */
  public static function array_path_set(&$array, $path, $value)
  {
    if (is_array($path) && empty($path))
    {
      $array = $value;

      return false;
    }
    elseif (!$path)
    {
      return false;
    }

    $map = is_array($path) ? $path : explode('/', $path);
    $found =& $array;

    foreach ($map as $part)
    {
      if (!isset($found[$part]))
      {
        $found[$part] = array();
      }

      $found =& $found[$part];
    }

    $found = $value;

    return true;
  }

  /**
   * array_values_cast
   * Automatically cast array values.
   *
   * @param string $value Value to cast based on what it is.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function array_values_cast(&$value)
  {
    if (is_numeric($value))
    {
      $value = $value + 0;
    }
    elseif (in_array(strtolower($value), array('true', 'false')))
    {
      $value = strtolower($value) == 'true' ? true : false;
    }
  }
  
  /**
   * array_values_strip_all_tags
   * Remove all tags from an array
   *
   * @param string $value Value to strip
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function array_values_strip_all_tags(&$value)
  {
    if (is_string($value))
    {
      $value = wp_strip_all_tags($value);
    }
  }

  /**
   * xml_to_array
   * Convert an XML string to an array.
   *
   * @param string $xml XMl string.
   *
   * @return array Converted XML string.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function xml_to_array($xml)
  {
    libxml_use_internal_errors(true);

    $xml_document = new DOMDocument();
    $xml_document->loadXML($xml);

    return self::dom_node_to_array($xml_document->documentElement);
  }

  /**
   * dom_node_to_array
   * Convert an XML dom to an array.
   *
   * @param object $node XML document.
   *
   * @return array Converted XML document.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function dom_node_to_array($node)
  {
    $output = array();
    switch ($node->nodeType)
    {
      case XML_CDATA_SECTION_NODE:
      case XML_TEXT_NODE:
        $output = trim($node->textContent);
      break;

      case XML_ELEMENT_NODE:
      for ($x = 0, $y = $node->childNodes->length; $x < $y; $x++)
      {
        $child = $node->childNodes->item($x);

        $value = self::dom_node_to_array($child);

        if (isset($child->tagName))
        {
          $tag = $child->tagName;
          if (!isset($output[$tag]))
          {
            $output[$tag] = array();
          }
          $output[$tag][] = $value;
        }
        elseif ($value)
        {
          $output = (string) $value;
        }
      }

      if (is_array($output))
      {
        if ($node->attributes->length)
        {
          $attributes = array();
          foreach($node->attributes as $key => $attribute_node)
          {
            $attributes[$key] = (string) $attribute_node->value;
          }
          $output['@attributes'] = $attributes;
        }

        foreach ($output as $key => $value)
        {
          if (is_array($value) && count($value) == 1 && $key != '@attributes')
          {
            $output[$key] = $value[0];
          }
        }
      }

      break;
    }

    return $output;
  }

  /**
   * has_block_level_tags
   * Checks if html string contains block level tags
   *
   * @param string $string The html string to check.
   *
   * @return bool Whether the string contains block level elements
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function has_block_level_tags($string)
  {
    $block_level_tags = array(
      'address'
      ,'article'
      ,'aside'
      ,'blockquote'
      ,'canvas'
      ,'dd'
      ,'div'
      ,'dl'
      ,'fieldset'
      ,'figcaption'
      ,'figure'
      ,'footer'
      ,'form'
      ,'h1'
      ,'h2'
      ,'h3'
      ,'h4'
      ,'h5'
      ,'h6'
      ,'header'
      ,'hgroup'
      ,'hr'
      ,'main'
      ,'nav'
      ,'noscript'
      ,'ol'
      ,'output'
      ,'p'
      ,'pre'
      ,'section'
      ,'table'
      ,'tfoot'
      ,'ul'
      ,'video'
    );
    
    preg_match_all('~<([^/][^>]*?)>~', $string, $matches, PREG_PATTERN_ORDER); 
    
    if (isset($matches[1]) && !empty($matches[1]))
    {
      $found = array_intersect($block_level_tags, array_unique($matches[1]));

      return !empty($found);
    }
    
    return false;
  }
  
  /**
   * directory_empty
   * Check if a directory is empty.
   *
   * @param string $path Directory path to check.
   *
   * @return bool Whether the directory is empty.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function directory_empty($path)
  {
    if (is_dir($path))
    {
      $files = @scandir($path);
      return count($files) > 2 ? false : true;
    }

    return true;
  }

  /**
   * unique_id
   * Generates a unique id from an objects structure or a random number.
   *
   * @param array|object $object Object to use for the unique id generation.
   *
   * @return string A unique 7 digit md5 string based on an objects structure.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function unique_id($object = null)
  {
    return substr(md5(is_object($object) || is_array($object) ? serialize($object) : rand()), 0, 7);
  }

  /**
   * object_to_array
   * Converts an object to an array.
   *
   * @param object $object Object to convert to an array.
   *
   * @return array Converted object.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function object_to_array($object)
  {
    if (!is_array($object) && !is_object($object))
    {
      return $object;
    }

    if (is_object($object))
    {
      $object = get_object_vars($object);
    }

    return array_map(array('piklist', 'object_to_array'), $object);
  }

  /**
   * is_associative
   * Check if an array is associative.
   *
   * @param array $array Array to check.
   *
   * @return bool Status of comparison.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function is_associative($array)
  {
    return array_keys($array) !== range(0, count($array) - 1);
  }

  /**
   * get_settings
   * Retrieves a setting by key.
   *
   * @param string $option Option to pull setting from.
   * @param string $setting Setting to pull from the options.
   *
   * @return string The setting requested.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function get_settings($option, $setting)
  {
    $options = get_option($option);

    return isset($options[$setting]) ? $options[$setting] : null;
  }

  /**
   * sort_by_order
   * Sort an array by the order key.
   *
   * @param array $a First array.
   * @param array $b Second array.
   *
   * @return bool Status of comparison.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function sort_by_order($a, $b)
  {
    return $a['order'] - $b['order'];
  }

  /**
   * sort_by_name_order
   * Sort an array by the name key.
   *
   * @param array $a First array.
   * @param array $b Second array.
   *
   * @return bool Status of comparison.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function sort_by_name_order($a, $b)
  {
    return $a['name'] - $b['name'];
  }

  /**
   * sort_by_tab_order
   * Sort an array by the tab key.
   *
   * @param array $a First array.
   * @param array $b Second array.
   *
   * @return bool Status of comparison.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function sort_by_tab_order($a, $b)
  {
    return $a['tab_order'] - $b['tab_order'];
  }

  /**
   * sort_by_args_order
   * Sort an array by the args|order key.
   *
   * @param array $a First array.
   * @param array $b Second array.
   *
   * @return bool Status of comparison.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function sort_by_args_order($a, $b)
  {
    if (!isset($a['args']['order']) && !isset($b['args']['order']))
    {
      return 1;
    }

    $a['args']['order'] = !empty($a['args']['order']) ? $a['args']['order'] : 0;
    $b['args']['order'] = !empty($b['args']['order']) ? $b['args']['order'] : 0;

    return $a['args']['order'] - $b['args']['order'];
  }

  /**
   * sort_by_data_order
   * Sort an array by the data|order key.
   *
   * @param array $a First array.
   * @param array $b Second array.
   *
   * @return bool Status of comparison.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function sort_by_data_order($a, $b)
  {
    return $a['data']['order'] - $b['data']['order'];
  }

  /**
   * sort_by_data_extend
   * Sort an array by the data|order key.
   *
   * @param array $a First array.
   * @param array $b Second array.
   *
   * @return bool Status of comparison.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function sort_by_data_extend($a, $b)
  {
    return empty($b['data']['extend']);
  }

  /**
   * array_filter_recursive
   * Custom filter to remove empty values from a multidimensional array.
   *
   * @param array $array
   *
   * @return array Filtered array.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function array_filter_recursive($array)
  {
    if (!is_array($array))
    {
      return $array;
    }

    foreach ($array as &$value)
    {
      if (is_array($value))
      {
        $value = self::array_filter_recursive($value);
      }
    }

    return array_filter($array);
  }
   
  /**
   * array_column
   * Returns an array of values representing a single column from the input array.
   *
   * @param array $array A multi-dimensional array from which to pull a column of values.
   * @param string mixed $columnKey The column of values to return. This value may
   *                                be the integer key of the column you wish to retrieve, or it may be
   *                                the string key name for an associative array. It may also be NULL to
   *                                return complete arrays (useful together with index_key to reindex
   *                                the array).
   * @param mixed $index The column to use as the index/keys for the
   *                     returned array. This value may be the integer key of the column, or
   *                     it may be the string key name.
   *
   * @return array
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function array_column($array, $column, $index = null)
  {
    if (function_exists('array_column'))
    {
      return array_column($array, $column, $index);
    }
    
    $result = array();

    foreach ($array as $item) 
    {
      if (!is_array($item)) 
      {
        continue;
      } 
      elseif (is_null($index) && array_key_exists($column, $item)) 
      {
        $result[] = $item[$column];
      } 
      elseif (array_key_exists($index, $item)) 
      {
        if (is_null($column)) 
        {
          $result[$item[$index]] = $item;
        } 
        elseif (array_key_exists($column, $item)) 
        {
          $result[$item[$index]] = $item[$column];
        }
      }
    }
      
    return $result;
  }

  /**
   * object
   * Get meta or an option and nicely format the object.
   *
   * @param string $type Type of object to fetch.
   * @param mixed $id Identifier for the object.
   *
   * @return array Object data.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function object($type, $id)
  {
    $data = $type == 'option' ? get_option($id) : get_metadata($type, $id);

    if (!empty($data))
    {
      foreach ($data as $key => $value)
      {
        $data[$key] = self::object_value(maybe_unserialize($value));
      }
    }

    return $data;
  }

  /**
   * object_format
   * Format a stored object like a grouped or add-more field.
   *
   * @param object|array $object Object to format.
   *
   * @return object|array Formatted object.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function object_format($object = array())
  {
    $keys = array_keys($object);

    if (empty($keys) || empty($object))
    {
      return array();
    }

    $formatted = $values = array();

    for ($i = 0; $i < count($object[$keys[0]]); $i++)
    {
      foreach ($keys as $key)
      {
        $value = isset($object[$key][$i]) ? $object[$key][$i] : null;

        if (is_array($value) && !isset($value[0][0]))
        {
          $values[$key] = self::object_format($value);
        }
        else
        {
          $values[$key] = $value;
        }
      }

      $formatted[] = $values;
    }

    return $formatted;
  }

  /**
   * object_value
   * Set the value for the value found in the object method.
   *
   * @param array $object Object to format the values of.
   *
   * @return array Formatting object.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function object_value($object)
  {
    if (is_array($object) && count($object) == 1 && self::is_flat($object))
    {
      return maybe_unserialize(current($object));
    }
    elseif (is_array($object))
    {
      foreach ($object as $key => $value)
      {
        $value = maybe_unserialize($value);

        if (is_array($value) && is_numeric($key) && count($value) == 1 && self::is_flat($object))
        {
          $object = current($value);
        }
        elseif (is_array($value) && is_array($object))
        {
          $object[$key] = self::object_value($value);
        }
      }
    }

    return maybe_unserialize($object);
  }

  /**
   * is_flat
   * Check if an object is multi-dimensional.
   *
   * @param object|array $object Object to check.
   *
   * @return bool Status of comparison.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function is_flat($object)
  {
    if (is_array($object) || is_object($object))
    {
      foreach ($object as $index => $value)
      {
        if (is_array($value) || is_object($value))
        {
          return false;
        }
      }
    }

    return true;
  }

  /**
   * path_is_absolute
   * Determine whether a path is relative or absolute
   *
   * @param string $path path to check
   *
   * @return bool
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function path_is_absolute($path)
  {
    return ( path_is_absolute($path) || ( 1 === preg_match('~^[a-z]+://~i', $path) ) );
  }

  /**
   * explode
   * Explode and trim a string into an array.
   *
   * @param string $delimiter Delimeter to explode string by.
   * @param string $string String to explode.
   * @param string $map Additional function to map array with.
   *
   * @return array Converted string.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function explode($delimiter, $string, $map = false)
  {
    $output = array_map('trim', explode($delimiter, $string));

    if ($map)
    {
      $output = array_map($map, $output);
    }

    return $output;
  }
  
  /**
   * pluck
   * Pluck values out of an object and return a key => value paired object
   *
   * @param object $object The object to pluck
   * @param mixed $arguments The keys to pluck from the object
   *
   * @return object Plucked object
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function pluck($object, $arguments)
  {
    $list = array();
    $arguments = is_array($arguments) ? $arguments : array($arguments);

    foreach ($object as $key => $value)
    {
      if (count($arguments) > 1)
      {
        if (in_array('_key', $arguments))
        {
          $_value = $arguments[1];
          $list[$key] = is_object($value) ? $value->$_value : $value[$_value];
        }
        else
        {
          $__key = $arguments[0];
          $_key = is_object($value) ? $value->$__key : (isset($value[$__key]) ? $value[$__key] : null);

          $_value = $arguments[1];
          $list[$_key] = is_object($value) ? $value->$_value : (isset($value[$_value]) ? $value[$_value] : null);
        }
      }
      else
      {
        $_value = $arguments[0];
        array_push($list, $_value ? (is_object($value) && isset($value->$_value) ? $value->$_value : (isset($value[$_value]) ? $value[$_value] : null)) : null);
      }
    }
    
    return $list;
  }

  /**
   * get_ip_address
   * Get the IP address of the visitor.
   *
   * @return string IP address.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function get_ip_address()
  {
    if (!empty($_SERVER['HTTP_CLIENT_IP']))
    {
      $ip_address = $_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
      $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip_address = $_SERVER['REMOTE_ADDR'];
    }

    return $ip_address;
  }

  /**
   * performance
   * Removes what php limits are possible to remove to allow a process to run as long as needed.
   *
   * @access public
   * @static
   * @since 1.0
   */
  public static function performance()
  {
    if (!ini_get('safe_mode'))
    {
      ini_set('max_execution_time', -1);
      ini_set('memory_limit', -1);
    }
  }
}

/**
 * piklist
 * The core helper function for the Piklist framework.
 *
 * @since 1.0
 */
function piklist($option, $arguments = array())
{
  if (!is_array($arguments) && strstr($arguments, '='))
  {
    parse_str($arguments, $arguments);
  }

  if (is_array($option) || is_object($option))
  {
    return empty($arguments) ? piklist::object_format($option) : piklist::pluck($option, $arguments);
  }
  else
  {
    switch ($option)
    {
      case 'field':

        if (piklist_setting::get('active_section'))
        {
          piklist_setting::register_setting($arguments);
        }
        else
        {
          return piklist_form::render_field($arguments, isset($arguments['return']) ? $arguments['return'] : false);
        }

      break;
      
      case 'form':
        
        return piklist_form::render_form($arguments['form'], isset($arguments['add_on']) ? $arguments['add_on'] : null);
        
      break;

      case 'list_table':

        piklist_list_table::render($arguments);

      break;

      case 'post_type_labels':

        return piklist::post_type_labels($arguments);

      break;

      case 'taxonomy_labels':

        return piklist::taxonomy_labels($arguments);

      break;

      case 'option':
      case 'post_custom':
      case 'post_meta':
      case 'get_post_custom':
      case 'user_custom':
      case 'user_meta':
      case 'get_user_custom':
      case 'term_custom':
      case 'term_meta':
      case 'get_term_custom':

        switch ($option)
        {
          case 'user_custom':
          case 'user_meta':
          case 'get_user_custom':

            $type = 'user';

          break;

          case 'term_custom':
          case 'term_meta':
          case 'get_term_custom':

            $type = 'term';

          break;

          case 'post_custom':
          case 'post_meta':
          case 'get_post_custom':

            $type = 'post';

          break;

          default:

            $type = 'option';

          break;
        }

        return piklist::object($type, $arguments);

      break;

      case 'dashes':

        return piklist::dashes($arguments);

      break;

      case 'slug':

        return piklist::slug($arguments);

      break;

      case 'humanize':

        return piklist::humanize($arguments);

      break;

      case 'performance':

        piklist::performance();

      break;

      case 'comments_template':

        $file = isset($arguments[0]) ? $arguments[0] : '/comments.php';
        $seperate_comments = isset($arguments[1]) ? $arguments[1] : false;

        piklist_comments::comments_template($file, $seperate_comments);

      break;

      case 'include_meta_boxes':

        piklist::render('shared/notice', array(
          'content' => sprintf(__('This page is using the old Piklist WorkFlow system. Please update your code to the %snew WorkFlow system%s.', 'piklist'), '<a href="https://piklist.com/user-guide/docs/building-workflows-piklist-v0-9-9/" target="_blank">', '</a>')
          ,'notice_type' => 'error'
        ));

      break;
      
      case 'prefix':
        
        return piklist::$prefix;
      
      break;
      
      case 'url':
        
        return isset(piklist::$add_ons[$arguments]) ? piklist::$add_ons[$arguments]['url'] : null;
      
      break;

      case 'path':
        
        return isset(piklist::$add_ons[$arguments]) ? piklist::$add_ons[$arguments]['path'] : null;
      
      break;
      
      default:

        $return = isset($arguments['return']) ? $arguments['return'] : false;
        $loop = isset($arguments['loop']) ? $arguments['loop'] : null;
        
        unset($arguments['return']);
        unset($arguments['loop']);

        return piklist::render($option, $arguments, $return, $loop);

      break;
    }
  }
}