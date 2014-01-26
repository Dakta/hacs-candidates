<?php
/**
 * The template for listing content on index/archive/search.
 *
 * Used for index/archive/search, not single.
 *
 */
?>


    <li class="candidate clearfix"><article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <div class="three columns">
            <img src="img/Biden_2013.jpg"
                 title="Joe Biden"
                 alt="Joe Biden, official Vice President portrait."
                 width="100%" />
        </div>
        <div class="thirteen columns">
            <header>
                <h1><a href="candidates/joe-biden/">Joe Biden</a></h1>
                <span class="democratic party"><a href="parties/democratic-party/">Democrat</a></span>
                <span class="status undeclared">Undeclared</span>
            </header>
            <blockquote cite="candidates/joe-biden/">
                <p>Joseph Robinette "Joe" Biden, Jr. (/ˈdʒoʊsɨf rɒbɨˈnɛt ˈbaɪdən/; born November 20, 1942) is
                    the 47th and current Vice President of the United States, jointly elected with President
                    Barack Obama. He is a member of the Democratic Party and was a United States Senator from
                    Delaware from January 3, 1973, until his resignation on January 15, 2009, following his
                    election to the Vice Presidency. In 2012, Biden was elected to a second term alongside
                    Obama.</p>
            </blockquote>
        </div>
    </article></li>




<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php the_post_thumbnail(); ?>

	<header class="entry-header">
		<?php if ( in_array( 'category', get_object_taxonomies( get_post_type() ) ) && candidates_categorized_blog() ) : ?>
		<div class="entry-meta">
			<span class="cat-links"><?php echo get_the_category_list( _x( ', ', 'Used between list items, there is a space after the comma.', 'candidates' ) ); ?></span>
		</div>
		<?php
			endif;

			if ( is_single() ) :
				the_title( '<h1 class="entry-title">', '</h1>' );
			else :
				the_title( '<h1 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h1>' );
			endif;
		?>

		<div class="entry-meta">
			<?php
				if ( 'post' == get_post_type() )
					candidates_posted_on();

				if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) :
			?>
			<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'candidates' ), __( '1 Comment', 'candidates' ), __( '% Comments', 'candidates' ) ); ?></span>
			<?php
				endif;

				edit_post_link( __( 'Edit', 'candidates' ), '<span class="edit-link">', '</span>' );
			?>
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->

	<?php if ( is_search() ) : ?>
	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->
	<?php else : ?>
	<div class="entry-content">
		<?php
			the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'candidates' ) );
			wp_link_pages( array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'candidates' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
			) );
		?>
	</div><!-- .entry-content -->
	<?php endif; ?>

	<section class="party-candidates">
        <?php
            // Find connected pages
            $connected = new WP_Query( array(
              'connected_type' => 'candidate_to_party',
              'connected_items' => get_queried_object(),
              'nopaging' => true,
            ) );
            
            // Display connected pages
            if ( $connected->have_posts() ) :
        ?>
        <h1>This Party's Candidates:</h1>
        <ul>
        <?php while ( $connected->have_posts() ) : $connected->the_post(); ?>
            <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
        <?php endwhile; ?>
        </ul>
            
        <?php 
            // Prevent weirdness
            wp_reset_postdata();
            
            endif;
        ?>
	</section>

	<?php the_tags( '<footer class="entry-meta"><span class="tag-links">', '', '</span></footer>' ); ?>
</article><!-- #post-## -->
