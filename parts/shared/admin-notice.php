
<div class="<?php echo esc_attr((is_admin() ? null : 'piklist-notice-') . $type); ?>">

  <?php if (is_array($notices)): ?>
    
    <?php foreach ($notices as $notice): ?>

      <p><?php echo $notice; ?></p>

    <?php endforeach; ?>
  
  <?php else: ?>
    
    <p>
      <?php echo $notices; ?>
    </p>

  <?php endif; ?>

</div>
