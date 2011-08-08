<?php /* Assemble the main navigation menu with a bunch of conditionals for different tab states. */ 
$events_id = get_cat_ID('events');
global $bp;

$ancestors = get_post_ancestors($post->ID); // Return an array of all ancestors
$root = count($ancestors)-1; // Go to the end of that array
$parent = $ancestors[$root]; // Top parent is the last in the array
?>

<ul id="nav-main" role="navigation">
<?php // Home
  if ( (is_front_page()) && ($paged < 1) ) : ?>
  <li class="current" title="This is the current page"><em>Home</em></li>
<?php elseif (is_front_page()) : ?>
  <li class="current"><a href="<?php echo bloginfo('url'); ?>" rel="home">Home</a></li>
<?php else : ?>
  <li><a href="<?php echo bloginfo('url'); ?>" rel="home">Home</a></li>
<?php endif; ?>

<?php // Teams 
  if ( function_exists('bp_is_active') && bp_is_active( 'groups' ) ) :
    if ( bp_is_page(BP_GROUPS_SLUG) && bp_is_directory() ) : ?>
  <li class="current" title="This is the current page"><em>Teams</em></li>
<?php elseif ( bp_is_groups_component() && bp_is_single_item() ) : ?>
  <li class="current"><a href="<?php echo site_url(); ?>/<?php echo BP_GROUPS_SLUG ?>/">Teams</a></li>
<?php else : ?>
  <li><a href="<?php echo site_url(); ?>/<?php echo BP_GROUPS_SLUG ?>/">Teams</a></li>
<?php endif; 
  endif; ?>

<?php // Community
if ( function_exists('bp_is_active') && bp_is_active( 'activity' ) ) :
  if ( bp_is_page(BP_ACTIVITY_SLUG) && bp_is_directory() ) : ?>  
  <li class="current"><a href="<?php echo site_url(); ?>/<?php echo BP_ACTIVITY_SLUG ?>/">Community</a></li>
  <?php else : ?>
  <li><a href="<?php echo site_url(); ?>/<?php echo BP_ACTIVITY_SLUG ?>/">Community</a></li>
  <?php endif; ?>
<?php else : // If activity is disabled, link to the blog page ?>
  <?php if ( is_page('blog') || is_category('syndicated') || is_category('twitter') ) : ?>
  <li class="current"><a href="<?php echo get_permalink(get_page_by_path('blog')->ID); ?>">Community</a></li>
  <?php else : ?>
  <li><a href="<?php echo get_permalink(get_page_by_path('blog')->ID); ?>">Community</a></li>
  <?php endif; ?>
<?php endif; ?>

<?php // Forums
if ( function_exists('bp_is_active') && bp_is_active( 'forums' ) ) :
  if ( bp_is_page('forums') && bp_is_directory() ) : ?>  
  <li class="current"><a href="<?php echo site_url(); ?>/forums/">Forums</a></li>
  <?php else : ?>
  <li><a href="<?php echo site_url(); ?>/forums/">Forums</a></li>
  <?php endif; ?>
<?php endif; ?>

<?php // Events
  if ( is_term('events','category') ) :
    if ( is_category('events') && ($paged < 1) ) : ?>
  <li class="current" title="This is the current page"><em>Events</em></li>
<?php elseif ( is_category('events') ||
               (is_singular() && in_category('events')) ||
               (is_singular() && fc_in_descendant_category( get_term_by('name', 'events', 'category')) ) ) : ?>
  <li class="current"><a href="<?php echo get_category_link( $events_id ); ?>">Events</a></li>
<?php else : ?>
  <li><a href="<?php echo get_category_link( $events_id ); ?>">Events</a></li>
<?php endif; 
  endif; ?>
  
<?php // Media
  if ( get_page_by_path('media') && (get_post_status( get_page_by_path('media') ) === 'publish') ) :
    if ( is_page('media') ) : ?>
  <li class="current" title="This is the current page"><em>Media</em></li>
<?php elseif ( fc_is_child(get_page_by_path('media')->ID) ) : ?>
  <li class="current"><a href="<?php echo get_permalink(get_page_by_path('media')->ID); ?>">Media</a></li>
<?php else : ?>
  <li><a href="<?php echo get_permalink(get_page_by_path('media')->ID); ?>">Media</a></li>
<?php endif; 
  endif; ?>

<?php // Docs
  if (get_page_by_path('docs')) :
    if ( is_page('docs') ) : ?>
  <li class="last current" title="This is the current page"><em>Docs</em></li>
<?php elseif ( fc_is_child(get_page_by_path('docs')->ID) ) : ?>
  <li class="last current"><a href="<?php echo get_permalink(get_page_by_path('docs')->ID); ?>">Docs</a></li>
<?php else : ?>
  <li class="last"><a href="<?php echo get_permalink(get_page_by_path('docs')->ID); ?>">Docs</a></li>
<?php endif;
  endif; ?>  
</ul>
