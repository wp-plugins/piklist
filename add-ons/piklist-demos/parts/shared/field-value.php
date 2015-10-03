
<a href="#TB_inline?width=600&height=550&inlineId=<?php echo $id; ?>" class="thickbox button button-secondary button-small alignright piklist-demo-field-value-button" title="<?php _e('Getting the field value is easy!', 'piklsit-demo'); ?>"><?php _e('Get value'); ?></a>	

<div id="<?php echo $id; ?>" style="display: none;">
  
  <?php foreach ($codes as $index => $code): ?>
    
    <?php if (isset($code)): ?>
   
      <pre class="piklist-demo-pre">&lt;?php<br>  // <?php _e('Get your data the WordPress Way!','piklist-demo');?><br>  // <?php _e('Use standard WordPress functions like', 'piklist-demo');?><?php echo ' get_' . $type . '_meta()'; ?><br>  <strong><?php echo $code; ?></strong><br><br>  // <?php _e('Output data'); ?><br>  print_r($value);<br>?&gt;</pre>
   
    <?php endif; ?>
  
    <pre class="piklist-demo-pre"><?php print_r($values[$index]); ?></pre>
    
    <hr>
    
  <?php endforeach; ?>

</div>