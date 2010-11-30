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

  <?php $children = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_parent = ".$post->ID."   AND post_type = 'page' ORDER BY menu_order", 'OBJECT'); ?>
  <?php if ( $children ) : ?>
  <ul id="sub-teams" class="groups">
    <?php foreach ( $children as $child ) : setup_postdata( $child ); ?>
    <li class="team">
      <h2 class="entry-title"><a href="<?php echo get_permalink($child->ID); ?>" rel="bookmark" title="<?php echo $child->post_title; ?>"><?php echo $child->post_title; ?></a></h2>
      <?php the_content(); ?>
    </li>
    <?php endforeach; ?>
  </ul>
  <?php endif; ?>
      
</article>
<?php else : ?>
  <h2><?php bp_group_name(); ?></h2>
  <p><?php bp_group_member_count(); ?> <?php bp_group_join_button(); ?></p>
<?php endif; ?>
