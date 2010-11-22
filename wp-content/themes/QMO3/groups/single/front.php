<?php 
  global $bp, $forum_template;
  $page_slug = bp_get_group_slug();
  $page_id = get_page_by_path($page_slug)->ID;
  fc_get_post($page_id);
?>
<?php if ($page_id != '') : ?>
<article class="team-general single hentry">
  <h1 class="entry-title"><?php the_title(); ?></h1>
  <div class="entry-content">
  <?php the_content(); ?>
  </div>
</article>
<?php else : ?>
  <h2><?php bp_group_name(); ?></h2>
  <p><?php bp_group_member_count(); ?> <?php bp_group_join_button(); ?></p>
<?php endif; ?>