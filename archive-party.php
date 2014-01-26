<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme and one
 * of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query,
 * e.g., it puts together the home page when no home.php file exists.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 */

get_header(); ?>

<h1><?php hacs_page_name(); ?></h1>

<?php



if ( get_post_type_description() ) {
    the_post_type_description();
}

?>

    <?php
        if ( have_posts() ) :
    ?>
    <ul class="candidates clearfix">
        <?php
            // Start the Loop.
            while ( have_posts() ) : the_post();
        
                /*
                 * Include the post format-specific template for the content. If you want to
                 * use this in a child theme, then include a file called called content-___.php
                 * (where ___ is the post format) and that will be used instead.
                 */
                // get_template_part( 'listing', get_post_format() );
                hacs_get_template_part( 'listing', get_post_format(), get_post_type() );
        
            endwhile;
        ?>
    </ul>
    <?php
        // Previous/next post navigation.
        candidates_paging_nav();

        else :
        // If no content, include the "No posts found" template.
        get_template_part( 'content', 'none' );
    
        endif;
    ?>

<?php

get_footer();
