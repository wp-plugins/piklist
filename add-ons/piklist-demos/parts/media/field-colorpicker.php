<?php
/*
Title: ColorPicker Fields
Order: 60
Tab: Basic
Flow: Demo Workflow
*/
?>

<p class="piklist-demo-highlight">
  <?php _e('WordPress ColorPicker fields are super simple to create. Piklist handles all the Javascript.', 'piklist-demo');?>
</p>

<?php
    
  piklist('field', array(
    'type' => 'colorpicker'
    ,'field' => 'color'
    ,'label' => __('Color Picker', 'piklist-demo')
  ));

  piklist('field', array(
    'type' => 'colorpicker'
    ,'add_more' => true
    ,'field' => 'color_add_more'
    ,'label' => __('Color Picker', 'piklist-demo')
  ));
  
  piklist('shared/code-locater', array(
    'location' => __FILE__
    ,'type' => 'Media Section'
  ));