<?php 
  $page_slug = bp_get_group_slug();
  $page_id = get_page_by_path($page_slug)->ID;
  fc_get_post($page_id);
?>
<?php if ($page_id != '') : ?>

  <div class="tabbed team-general">
    <h1 class="team-title"><?php the_title(); ?></h1>
    <div class="entry-content">
    <?php the_content(); ?>
    </div>

  <?php $children = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_parent = ".$page_id." AND post_type = 'page' ORDER BY menu_order", 'OBJECT'); ?>
  <?php if ( $children ) : ?>
    <?php foreach ( $children as $child ) : setup_postdata( $child ); ?>
      <h1 class="team-title"><?php echo $child->post_title; ?></h1>
      <div class="entry-content">
      <?php the_content(); ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

  </div>

  <?php if ( $children ) : ?>
    <script src="<?php bloginfo('stylesheet_directory'); ?>/js/TabInterface.js" type="text/javascript"></script>
    <script type="text/javascript">
    // <![CDATA[
      jQuery(document).ready(function(){
        var cabinets = Array();
        var collection = document.getElementsByTagName( '*' );
        var cLen = collection.length;
        for( var i=0; i<cLen; i++ ){
          if( collection[i] &&
              /\s*tabbed\s*/.test( collection[i].className ) ){
            cabinets.push( new TabInterface( collection[i], i ) );
          }
        }
      });
    // ]]>
    </script>
  <?php endif; ?>

<?php else : ?>

  <h2><?php bp_group_name(); ?></h2>
  <p><?php bp_group_member_count(); ?> <?php bp_group_join_button(); ?></p>

<?php endif; ?>
