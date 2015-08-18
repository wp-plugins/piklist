<?php
/*  
Title: Shortcode
Method: post
Message: Shortcode Updated.
*/

  $shortcode = isset($_REQUEST[piklist::$prefix . 'shortcode']) ? current(piklist($_REQUEST[piklist::$prefix . 'shortcode'])) : null;
  $shortcode_data = isset($_REQUEST[piklist::$prefix . 'shortcode_data']) ? current(piklist($_REQUEST[piklist::$prefix . 'shortcode_data'])) : null;
  $shortcodes = piklist_shortcode::$shortcodes;
  
  ksort($shortcodes);
  
  $action = isset($shortcode_data['action']) ? $shortcode_data['action'] : false;
  $name = isset($shortcode_data['name']) ? $shortcode_data['name'] : false;

  if (in_array($action, array('insert', 'update'))):
    
    $forms = array();
    foreach ($shortcodes[$name]['render'] as $render):
      if (strstr($render, '-form.php')):
        array_push($forms, $render);
      endif;
    endforeach;

    if (!empty($forms)):
      
      foreach ($forms as $form):
        piklist::render($form);
      endforeach;
      
    else:
        
      foreach ($shortcode as $attribute => $value):
        piklist('field', array(
          'type' => 'text'
          ,'scope' => 'shortcode'
          ,'field' => $attribute
          ,'label' => piklist::humanize($attribute)
          ,'attributes' => array(
            'class' => 'large-text'
          )
        ));
      endforeach;

    endif;
    
    foreach ($shortcode_data as $attribute => $value):

      switch ($attribute):

        case 'content':

          piklist('field', array(
            'type' => 'editor'
            ,'scope' => 'shortcode_data'
            ,'field' => $attribute
            ,'label' => __('Shortcode Content', 'piklist')
            ,'description' => __('This is the content that the shortcode is wrapped around.', 'piklist')
          ));

        break;
  
        default:

          piklist('field', array(
            'type' => 'hidden'
            ,'scope' => 'shortcode_data'
            ,'field' => $attribute
          ));

        break;

      endswitch;

    endforeach;
?>    
  
  <div id="piklist-thickbox-actions">
    
    <?php
      piklist('field', array(
        'type' => 'submit'
        ,'field' => 'action'
        ,'template' => 'field'
        ,'value' => __($action == 'insert' ? 'Insert into editor' : 'Update')
        ,'attributes' => array(
          'class' => 'button button-primary'
        )
      ));
    ?>
    
  </div>

<?php    
  else:
?>

  <ul class="attachments piklist-shortcodes">
    
    <?php foreach ($shortcodes as $shortcode => $data): ?>
    
      <li class="attachment" title="<?php echo esc_attr($data['data']['description']); ?>" data-piklist-shortcode="<?php echo esc_attr($data['data']['shortcode']); ?>">
        <div class="attachment-preview landscape">
          <div class="thumbnail">
            <div class="centered">
              <?php if (substr($data['data']['icon'], 0, strlen('dashicons-')) == 'dashicons-'): ?>
                <div class="dashicons <?php echo esc_attr($data['data']['icon']); ?>"></div>
              <?php elseif (file_exists($data['data']['icon'])): ?>
                <img src="<?php echo esc_attr($data['data']['icon']); ?>" class="icon" />
              <?php else: ?>
                <div class="dashicons dashicons-media-code"></div>
              <?php endif; ?>
            </div>
            <div class="filename">
              <div><?php echo esc_attr($data['data']['name']); ?></div>
            </div>
          </div>
        </div>
      </li>
    
    <?php endforeach; ?>
  
  </ul>
  
<?php      
      
    piklist('field', array(
      'type' => 'hidden'
      ,'scope' => 'shortcode_data'
      ,'field' => 'name'
    ));

    piklist('field', array(
      'type' => 'hidden'
      ,'scope' => 'shortcode_data'
      ,'field' => 'action'
      ,'value' => 'insert'
    ));

  endif; 