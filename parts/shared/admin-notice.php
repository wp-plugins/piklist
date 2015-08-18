

<div id="<?php echo esc_attr($notice_id); ?>" class="notice <?php echo $dismiss == true || !empty($notice_id) ? 'is-dismissible' : null; ?> <?php echo esc_attr((is_admin() ? ($notice_type == 'update' ? 'updated ' : null) : 'piklist-notice-') . $notice_type); ?>">

  <?php echo wpautop($content); ?>

</div>
