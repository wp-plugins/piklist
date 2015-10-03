<?php
/*
Flow: Demo Workflow
Page: post.php, post-new.php, post-edit.php, profile.php, user-edit.php, edit-tags.php, piklist_demo_fields
Post Type: piklist_demo, attachment
Taxonomy: piklist_demo_type
Header: true
Position: title
*/

// NOTE: Because of the way the some of the admin forms work we just can't remove the 
//       fields or the form will throw errors, so instead we hide them when necessary
?>

  <?php 
    if (in_array($pagenow, array('profile.php', 'user-edit.php'))): 
  ?>
   
    <div class="wrap">
      <h2>
        <?php if ($pagenow == 'profile.php'): ?>
          <?php _e('Edit Your Profile'); ?>
        <?php
          else:
            $user = get_user_by('id', (int) $_REQUEST['user_id']); 
        ?>
          <?php _e('Edit User: ' . $user->user_login); ?> <a href="user-new.php" class="page-title-action"><?php _e('Add New'); ?></a>
        <?php endif;?>
      </h2>
    </div>

    <style type="text/css">
    
    
      <?php if (isset($_REQUEST[piklist::$prefix]['flow_page']) && !in_array($_REQUEST[piklist::$prefix]['flow_page'], array('common', 'common_profile'))): ?>
  
        body.piklist-workflow-active.user-edit-php #your-profile .piklist-meta-box-title,
        body.piklist-workflow-active.profile-php #your-profile .piklist-meta-box-title,
        body.piklist-workflow-active.user-edit-php #your-profile .piklist-form-table,
        body.piklist-workflow-active.profile-php #your-profile .piklist-form-table,
        body.piklist-workflow-active.user-edit-php #your-profile p.submit,
        body.piklist-workflow-active.profile-php #your-profile p.submit {
          display: block;
        }

          body.piklist-workflow-active.user-edit-php #your-profile > *,
          body.piklist-workflow-active.profile-php #your-profile > * {
            display: none;
          }
  
      <?php endif;?>
      
      body.piklist-workflow-active.user-edit-php #profile-page h1,
      body.piklist-workflow-active.profile-php #profile-page h1 {
        display: none;
      }

    </style>
    
  <?php 
    elseif ($pagenow == 'edit-tags.php'): 
      $taxonomy = get_taxonomy($taxnow);
  ?>
    
    <div class="wrap">
      <h2><?php echo $taxonomy->labels->edit_item; ?></h2>
    </div>

    <style type="text/css">
  
      <?php if (isset($_REQUEST[piklist::$prefix]['flow_page']) && !in_array($_REQUEST[piklist::$prefix]['flow_page'], array('common', 'common_term'))): ?>

        body.piklist-workflow-active.edit-tags-php .term-name-wrap,
        body.piklist-workflow-active.edit-tags-php .term-slug-wrap,
        body.piklist-workflow-active.edit-tags-php .term-parent-wrap,
        body.piklist-workflow-active.edit-tags-php .term-description-wrap {
          display: none;
        }

      <?php endif;?>
  
      body.piklist-workflow-active.edit-tags-php h1 {
        display: none;
      }

    </style>
      
  <?php endif;