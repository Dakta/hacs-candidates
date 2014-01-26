<?php
/**
 * The template for listing content on index/archive/search.
 *
 * Used for index/archive/search, not single.
 *
 */
?>

    <li class="candidate clearfix"><article id="post-<?php the_ID(); ?>" <?php post_class("clearfix"); ?>>
        <div class="three columns">
            <?php the_post_thumbnail(); ?>
        </div>
        <div class="thirteen columns">
            <header>
                <h1><a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
                <?php
                    // Find connected political party
                    $connected = new WP_Query( array(
                        'connected_type' => 'candidate_to_party',
                        'connected_items' => get_the_id(),
                        'nopaging' => true,
                    ) );
                    
                    // Display connected political party
                    if ( $connected->have_posts() ) :
                ?>
                <?php while ( $connected->have_posts() ) : $connected->the_post(); ?>
                <span style="color: <?php the_field('party_color'); ?>; "  class="<?php echo strtolower(get_field("party_term")); ?> party"><a href="<?php the_permalink(); ?>"><?php the_field("member_term"); ?></a></span>
                <?php endwhile;
                    // Prevent weirdness
                    wp_reset_postdata();
                    endif;
                ?>
                <span class="status <?php $terms = get_terms( 'running_status', array('number' => 1) ); echo $terms[0]->slug; ?>"><?php echo get_the_term_list( $post->ID, 'running_status', '', ', ', '' ); ?></span>
                <?php edit_post_link( __( 'Edit', 'candidates' ), '<span class="edit-link">', '</span>' ); ?>
            </header>
            <blockquote cite="<?php echo esc_url( get_permalink() ); ?>">
                <?php the_excerpt(); ?>
                <a href="<?php the_permalink() ?>">Read more...</a>
            </blockquote>
        </div>
    </article></li>



<?php

/* Unused, kept for reference */
/*
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

*/

?>
