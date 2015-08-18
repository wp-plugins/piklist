
<form 
  method="<?php echo strtolower($method); ?>" 
  action="<?php echo $filter ? home_url() . $action : null; ?>" 
  enctype="multipart/form-data"
  id="<?php echo $form_id; ?>"
  autocomplete="off"
  class="piklist-form <?php echo is_admin() ? 'hidden' : null; ?>"
>
  <?php
    do_action('piklist_notices', $form_id);
  
    piklist::render($form);
  
    piklist('field', array(
      'type' => 'hidden'
      ,'scope' => piklist::$prefix
      ,'field' => 'form_id'
      ,'value' => $form_id
    ));
    
    if ($filter):

      piklist('field', array(
        'type' => 'hidden'
        ,'scope' => piklist::$prefix
        ,'field' => 'filter'
        ,'value' => 'true'
      ));

    endif;
    
    if (!empty($redirect)):

      piklist('field', array(
        'type' => 'hidden'
        ,'scope' => piklist::$prefix
        ,'field' => 'redirect'
        ,'value' => $redirect
      ));

    endif;
    
    if ($hide_admin_ui):

      piklist('field', array(
        'type' => 'hidden'
        ,'scope' => piklist::$prefix
        ,'field' => 'admin_hide_ui'
        ,'value' => 'true'
      ));

    endif;
  
    piklist_form::save_fields(); 
  ?>  
</form>