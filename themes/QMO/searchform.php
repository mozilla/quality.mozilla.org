<form class="fm-search" method="get" action="<?php bloginfo('url'); ?>/" role="search">
<fieldset>
<?php if ( is_search() ) { // If this is a search page ?>
  <legend>Search again?</legend>
<?php } else { ?>
  <legend>Search</legend>
<?php } ?>
  <p><input type="text" value="<?php the_search_query(); ?>" name="s" id="s" placeholder="<?php _e( 'Search QMO', 'qmo' ); ?>"> <button type="submit">Search</button></p>
</fieldset>
</form>
