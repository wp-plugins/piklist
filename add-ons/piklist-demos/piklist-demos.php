<?php
/*
Plugin Name: Piklist Demos
Plugin URI: http://piklist.com
Description: Creates a Demo post type, Taxonomy, Settings Page, User fields, Dashbaord widget, Help tabs and Widget, with Field Examples.
Version: 0.3
Author: Piklist
Author URI: http://piklist.com/
Text Domain: piklist-demo
Domain Path: /languages
*/

  if (!defined('ABSPATH'))
  {
    exit;
  }

  add_filter('piklist_post_types', 'piklist_demo_post_types');
  function piklist_demo_post_types($post_types)
  {
    $post_types['piklist_demo'] = array(
      'labels' => piklist('post_type_labels', 'Piklist Demo')
      ,'title' => __('Enter a new Demo Title')
      ,'menu_icon' => piklist::$addons['piklist']['url'] . '/parts/img/piklist-menu-icon.svg'
      ,'page_icon' => piklist::$addons['piklist']['url'] . '/parts/img/piklist-page-icon-32.png'
      ,'supports' => array(
        'title'
        ,'post-formats'
      )
      ,'public' => true
      ,'admin_body_class' => array (
        'piklist-demonstration'
        ,'piklist-sample'
      )
      ,'has_archive' => true
      ,'rewrite' => array(
        'slug' => 'piklist-demo'
      )
      ,'capability_type' => 'post'
      ,'edit_columns' => array(
        'title' => __('Demo')
        ,'author' => __('Assigned to')
      )
      ,'hide_meta_box' => array(
        'slug'
        ,'author'
      )
      ,'status' => array(
        'new' => array(
          'label' => 'New'
          ,'public' => false
        )
        ,'pending' => array(
          'label' => 'Pending Review'
          ,'public' => false
        )
        ,'demo' => array(
          'label' => 'Demo'
          ,'public' => true
          ,'exclude_from_search' => true
          ,'show_in_admin_all_list' => true
          ,'show_in_admin_status_list' => true
       )
        ,'lock' => array(
          'label' => 'Lock'
          ,'public' => true
        )
      )
    );

    $post_types['piklist_lite_demo'] = array(
      'labels' => piklist('post_type_labels', 'Lite Demo')
      ,'title' => __('Enter a new Demo Title')
      ,'menu_icon' => piklist::$addons['piklist']['url'] . '/parts/img/piklist-menu-icon.svg'
      ,'page_icon' => piklist::$addons['piklist']['url'] . '/parts/img/piklist-page-icon-32.png'
      ,'show_in_menu' => 'edit.php?post_type=piklist_demo'
      ,'supports' => array(
        'title'
      )
      ,'public' => true
      ,'has_archive' => true
      ,'capability_type' => 'post'
      ,'edit_columns' => array(
        'title' => __('Title')
      )
      ,'hide_meta_box' => array(
        'slug'
        ,'author'
      )
    );

    return $post_types;
  }
  
  add_filter('piklist_relationships', 'piklist_demo_relationships');
  function piklist_demo_relationships($relationships)
  {
    $relationships[] = array(
      'name' => 'posts_to_posts'
      ,'from' => array(
        'scope' => 'post'
        ,'query' => array(
          'post_type' => 'post'
        )
      )
      ,'to' => array(
        'scope' => 'post'
        ,'query' => array(
          'post_type' => 'post'
        )
      )
    );
    
    return $relationships;
  }
      

  add_filter('piklist_taxonomies', 'piklist_demo_taxonomies');
  function piklist_demo_taxonomies($taxonomies)
  {
    $taxonomies[] = array(
      'post_type' => 'piklist_demo'
      ,'name' => 'piklist_demo_type'
      ,'configuration' => array(
        'hierarchical' => true
        ,'labels' => piklist('taxonomy_labels', 'Demo Taxonomy')
        ,'page_icon' => piklist::$addons['piklist']['url'] . '/parts/img/piklist-page-icon-32.png'
        ,'show_ui' => true
        ,'query_var' => true
        ,'rewrite' => array(
          'slug' => 'demo-type'
        )
        ,'show_admin_column' => true
        ,'list_table_filter' => true
        ,'meta_box_filter' => true
        ,'comments' => true
      )
    );

    $taxonomies[] = array(
      'object_type' => 'user'
      ,'name' => 'piklist_demo_user_type'
      ,'configuration' => array(
        'hierarchical' => true
        ,'labels' => piklist('taxonomy_labels', 'Demo User Type')
        ,'page_icon' => piklist::$addons['piklist']['url'] . '/parts/img/piklist-page-icon-32.png'
        ,'show_ui' => true
        ,'query_var' => true
        ,'rewrite' => array(
          'slug' => 'demo-user-type'
        )
        ,'show_admin_column' => true
        ,'list_table_filter' => true
      )
    );

    return $taxonomies;
  }

  add_filter('piklist_admin_pages', 'piklist_demo_admin_pages');
  function piklist_demo_admin_pages($pages)
  {
    $pages[] = array(
      'page_title' => __('Demo Settings')
      ,'menu_title' => __('Demo Settings', 'piklist-demo')
      ,'sub_menu' => 'edit.php?post_type=piklist_demo'
      ,'capability' => 'manage_options'
      ,'menu_slug' => 'piklist_demo_fields'
      ,'setting' => 'piklist_demo_fields'
      ,'menu_icon' => piklist::$addons['piklist']['url'] . '/parts/img/piklist-icon.png'
      ,'page_icon' => piklist::$addons['piklist']['url'] . '/parts/img/piklist-page-icon-32.png'
      ,'default_tab' => 'Basic'
      // ,'layout' => 'container'
      ,'save_text' => 'Save Demo Settings'
    );

    return $pages;
  }

  add_filter('piklist_field_templates', 'piklist_demo_field_templates');
  function piklist_demo_field_templates($templates)
  {
    $templates['piklist_demo'] = array(
                                'name' => __('User', 'piklist-demo')
                                ,'description' => __('Default layout for User fields from Piklist Demos.', 'piklist-demo')
                                ,'template' => '[field_wrapper]
                                                  <div id="%1$s" class="%2$s">
                                                    [field_label]
                                                    [field]
                                                    [field_description_wrapper]
                                                      <small>[field_description]</small>
                                                    [/field_description_wrapper]
                                                  </div>
                                                [/field_wrapper]'
                              );

    $templates['theme_tight'] = array(
                                  'name' => __('Theme - Tight', 'piklist-demo')
                                  ,'description' => __('A front end form wrapper example from Piklist Demos.', 'piklist-demo')
                                  ,'template' => '[field_wrapper]
                                                    <div id="%1$s" class="%2$s piklist-field-container">
                                                      [field_label]
                                                      <div class="piklist-field">
                                                        [field]
                                                        [field_description_wrapper]
                                                          <span class="piklist-field-description">[field_description]</span>
                                                        [/field_description_wrapper]
                                                      </div>
                                                    </div>
                                                  [/field_wrapper]'
                                );

    return $templates;
  }

  add_filter('piklist_post_submit_meta_box_title', 'piklist_demo_post_submit_meta_box_title', 10, 2);
  function piklist_demo_post_submit_meta_box_title($title, $post)
  {
    switch ($post->post_type)
    {
      case 'piklist_demo':
        $title = __('Create Demo');
      break;
    }

    return $title;
  }

  add_filter('piklist_post_submit_meta_box', 'piklist_demo_post_submit_meta_box', 10, 3);
  function piklist_demo_post_submit_meta_box($show, $section, $post)
  {
    switch ($post->post_type)
    {
      case 'piklist_demo':

        switch ($section)
        {
          case 'minor-publishing-actions':
          //case 'misc-publishing-actions':
          //case 'misc-publishing-actions-status':
          case 'misc-publishing-actions-visibility':
          case 'misc-publishing-actions-published':

            $show = false;

          break;
        }

      break;
    }

    return $show;
  }

  add_action('the_content', 'piklist_demo_meta_field_insert');
  function piklist_demo_meta_field_insert($content)
  {
    if (get_post_type() == 'piklist_demo')
    {
      global $post;

      $meta = piklist('post_custom', $post->ID);

      foreach ($meta as $key => $value)
      {
        if (!empty($value) && substr($key, 0, 1) != '_')
        {
          $content .= '<br /><strong>' . $key . ':</strong> ' . (is_array($value) ? var_export($value, true) : $value);
        }
      }
    }

    return $content;
  }

  add_filter('piklist_assets', 'piklist_demo_assets');
  function piklist_demo_assets($assets)
  {
    array_push($assets['styles'], array(
      'handle' => 'piklist-demos'
      ,'src' => piklist::$addons['piklist']['url'] . '/add-ons/piklist-demos/parts/css/piklist-demo.css'
      ,'media' => 'screen, projection'
      ,'enqueue' => true
      ,'admin' => true
    ));

    return $assets;
  }
  
  $piklist_demo_thickbox_loaded = false;
  
  add_filter('piklist_post_render_field', 'piklist_demo_post_render_field', 10, 2);
  function piklist_demo_post_render_field($rendered_field, $field)
  {
    global $piklist_demo_thickbox_loaded;

    if (!$piklist_demo_thickbox_loaded)
    {
      add_thickbox();

      $piklist_demo_thickbox_loaded = true;
    }

    $codes = $values = array();

    if ($field['type'] != 'html')
    {
      switch ($field['scope'])
      {
        case 'post_meta':
        case 'user_meta':
        case 'term_meta':
        case 'comment_meta':
        
          // Only show this if data is saved.
          if ($field['object_id'])
          {
            $type = str_replace('_meta', '', $field['scope']);
          
            if (!$field['group_field'])
            {
              if ($field['type'] == 'group')
              {
                if ($field['field'])
                {
                  $unique = true;
               
                  array_push($codes, '$value = get_' . $type . '_meta(' . $field['object_id'] . ', \'' . $field['field'] . '\', ' . ($unique ? 'true' : 'false') . ');');
                  array_push($values, get_metadata($type, $field['object_id'], $field['field'], $unique)); 
                }
                else
                {
                  $unique = !$field['add_more'];
                
                  foreach ($field['fields'] as $column)
                  {
                    array_push($codes, '$value = get_' . $type . '_meta(' . $field['object_id'] . ', \'' . $column['field'] . '\', ' . ($unique ? 'true' : 'false') . ');');
                    array_push($values, get_metadata($type, $field['object_id'], $column['field'], $unique));
                  }
                }
              }
              elseif (empty($field['conditions']))
              {
                $unique = !$field['add_more'];
                
                array_push($codes, '$value = get_' . $type . '_meta(' . $field['object_id'] . ', \'' . $field['field'] . '\', ' . ($unique ? 'true' : 'false') . ');');
                array_push($values, get_metadata($type, $field['object_id'], $field['field'], $unique));
              }
            }
          }
       
        break;
      }
    
      if (!empty($values[0]))
      {
        piklist('field', array(
          'type' => 'html'
          ,'attributes' => array(
            'class' => 'piklist-demo-field-value'
          )
          ,'value' => piklist('shared/field-value', array(
                        'id' => piklist::unique_id()
                        ,'codes' => $codes
                        ,'values' => $values
                        ,'type' => $type
                        ,'field' => $field
                        ,'return' => true
                      ))
        ));
      }
    }
    
    return $rendered_field;
  }
