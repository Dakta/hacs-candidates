<?php


/**
 * Presidential Candidates functions and definitions
 *
 */



add_filter( 'dependency_minification_options', function( $options ) {
	  $options['disable_if_wp_debug'] = false;
	  return ( $options );
  });


/**
 * Display or retrieve page name for all areas of blog.
 *
 * By default, the page title will display the separator before the page title,
 * so that the blog title will be before the page title. This is not good for
 * title display, since the blog title shows up on most tabs and not what is
 * important, which is the page that the user is looking at.
 *
 * There are also SEO benefits to having the blog title after or to the 'right'
 * or the page title. However, it is mostly common sense to have the blog title
 * to the right with most browsers supporting tabs. You can achieve this by
 * using the seplocation parameter and setting the value to 'right'. This change
 * was introduced around 2.5.0, in case backwards compatibility of themes is
 * important.
 *
 * @since 1.0.0
 *
 * @param string $sep Optional, default is '|'. How to separate the various items within the page title.
 * @param bool $display Optional, default is true. Whether to display or retrieve title.
 * @param string $seplocation Optional. Direction to display title, 'right'.
 * @return string|null String on retrieve, null when displaying.
 */
function hacs_page_name( $sep = "|", $display = true, $seplocation = 'right' ) {
    global $wpdb, $wp_locale;

    $m = get_query_var('m');
    $year = get_query_var('year');
    $monthnum = get_query_var('monthnum');
    $day = get_query_var('day');
    $search = get_query_var('s');
    $title = '';

    $t_sep = '%WP_TITILE_SEP%'; // Temporary separator, for accurate flipping, if necessary

    // If there is a post
    if ( is_single() || ( is_home() && !is_front_page() ) || ( is_page() && !is_front_page() ) ) {
        $title = single_post_title( '', false );
    }

    // If there's a post type archive
    if ( is_post_type_archive() ) {
        $post_type = get_query_var( 'post_type' );
        if ( is_array( $post_type ) )
            $post_type = reset( $post_type );
        $post_type_object = get_post_type_object( $post_type );
        if ( ! $post_type_object->has_archive )
            $title = post_type_archive_title( '', false );
    }

    // If there's a category or tag
    if ( is_category() || is_tag() ) {
        $title = single_term_title( '', false );
    }

    // If there's a taxonomy
    if ( is_tax() ) {
        $term = get_queried_object();
        if ( $term ) {
            $tax = get_taxonomy( $term->taxonomy );
            $title = single_term_title( $tax->labels->name . $t_sep, false );
        }
    }

    // If there's an author
    if ( is_author() ) {
        $author = get_queried_object();
        if ( $author )
            $title = $author->display_name;
    }

    // Post type archives with has_archive should override terms.
    if ( is_post_type_archive() && $post_type_object->has_archive )
        $title = post_type_archive_title( '', false );

    // If there's a month
    if ( is_archive() && !empty($m) ) {
        $my_year = substr($m, 0, 4);
        $my_month = $wp_locale->get_month(substr($m, 4, 2));
        $my_day = intval(substr($m, 6, 2));
        $title = $my_year . ( $my_month ? $t_sep . $my_month : '' ) . ( $my_day ? $t_sep . $my_day : '' );
    }

    // If there's a year
    if ( is_archive() && !empty($year) ) {
        $title = $year;
        if ( !empty($monthnum) )
            $title .= $t_sep . $wp_locale->get_month($monthnum);
        if ( !empty($day) )
            $title .= $t_sep . zeroise($day, 2);
    }

    // If it's a search
    if ( is_search() ) {
        /* translators: 1: separator, 2: search phrase */
        $title = sprintf(__('Search Results %1$s %2$s'), $t_sep, strip_tags($search));
    }

    // If it's a 404 page
    if ( is_404() ) {
        $title = __('Page not found');
    }

    // what's this stupidity? we don't need this:    
/*
    $prefix = '';
    if ( !empty($title) )
        $prefix = " $sep ";
*/

    // Determines position of the separator and direction of the breadcrumb
    if ( 'right' == $seplocation ) { // sep on right, so reverse the order
        $title_array = explode( $t_sep, $title );
        $title_array = array_reverse( $title_array );
        // $title = implode( " $sep ", $title_array ) . $prefix;
        $title = implode( " $sep ", $title_array );
    } else {
        $title_array = explode( $t_sep, $title );
        // $title = $prefix . implode( " $sep ", $title_array );
        $title = implode( " $sep ", $title_array );
    }
    
    // we don't need this part, it adds the blog name etc.
    // $title = apply_filters('wp_title', $title, $sep, $seplocation);

    // Send it out
    if ( $display )
        echo $title;
    else
        return $title;

}


/*
function hacs_home_template( $templates = '' ) {
	if(!is_array($templates) && !empty($templates)) {
		$templates=locate_template(array("archive.php",$templates),false);
	} 
	elseif(empty($templates)) {
		$templates=locate_template("author-$role.php",false);
	}
	else {
		$new_template=locate_template(array("author-$role.php"));
		if(!empty($new_template)) array_unshift($templates,$new_template);
	}
	
	return $templates;
}
add_filter( 'home_template', 'hacs_home_template' );
*/



/**
 * Load a template part into a template
 *
 * Makes it easy for a theme to reuse sections of code in a easy to overload way
 * for child themes.
 *
 * Includes the named template part for a theme or if a name is specified then a
 * specialised part will be included. If the theme contains no {slug}.php file
 * then no template will be included.
 *
 * The template is included using require, not require_once, so you may include the
 * same template part multiple times.
 *
 * For the $name parameter, if the file is called "{slug}-special.php" then specify
 * "special".
 *
 * For the $type parameter, if the file is called "{slug}-custom_post_type.php"
 * then specify "custom_post_type".
 *
 * If both $name and $type are specified, the file should be called
 * "{slug}-{type}-{name}.php"
 *
 * @uses locate_template()
 * @since 3.0.0
 * @uses do_action() Calls 'get_template_part_{$slug}' action.
 *
 * @param string $slug The slug name for the generic template.
 * @param string $name The name of the specialised template.
 * @param string $type The post type of the specialised template.
 */
function hacs_get_template_part( $slug, $name = null, $type = null ) {
    do_action( "get_template_part_{$slug}", $slug, $name, $type );

    $templates = array();
    $name = (string) $name;
    $type = (string) $type;
    
    if ( '' !== $type and '' !== $name )
        $templates[] = "{$slug}-{$type}-{$name}.php";
    if ( '' !== $type )
        $templates[] = "{$slug}-{$type}.php";
    if ( '' !== $name )
        $templates[] = "{$slug}-{$name}.php";

    $templates[] = "{$slug}.php";

    locate_template($templates, true, false);
}


/**
 * Function to get register_nav_menu()'s human readable name
 *
 * from: http://www.wearepixel8.com/tips-and-tutorials/get-wordpress-nav-menu-name/
 */
function hacs_get_nav_menu_name( $theme_location ) {
     if ( !has_nav_menu( $theme_location ) ) return false;

     $menus      = get_nav_menu_locations();
     $menu_title = wp_get_nav_menu_object( $menus[$theme_location] )->name;

     return $menu_title;
}

/**
 * Wrap $content in $element with $attributes
 *
 */
function hacs_wrap_with_element($content, $element='div', $attributes=array()) {
    $output = '<'.$element;
    foreach ($attributes as $attr_name => $attr_val) {
        $output .= ' '.$attr_name.'="'.$attr_val.'"';
    }
    $output .= '>';
    $output .= $content;
    $output .= '</'.$element.'>';
        
    return $output;
}


/**
 * Wraps wp_nav_menu()'s output in the specified $wrap_element, outputs menu name in $name_element
 *
 */
function hacs_wrapped_nav_menu($wrap_element='div', $wrap_attributes=array(), $name_element='h1', $name_attributes=array(), $args=array()) {
    $echo = (array_key_exists('echo', $args) ? $args['echo'] : true);
    $args['echo'] = 0;
    
    $output  = hacs_wrap_with_element(hacs_get_nav_menu_name( $args['theme_location'] ), $name_element, $name_attributes);
    $output .= wp_nav_menu($args);
    
    $output  = hacs_wrap_with_element($output, $wrap_element, $wrap_attributes);
        
    if ($echo == true) {
        echo $output;
    } else {
        return $output;
    }
}


/**
 * Candidates only works in WordPress 3.6 or later.
 */
if ( version_compare( $GLOBALS['wp_version'], '3.6', '<' ) ) {
    require get_template_directory() . '/inc/back-compat.php';
}

if ( ! function_exists( 'candidates_setup' ) ) :
/**
 * Candidates setup.
 *
 * Set up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support post thumbnails.
 */
function candidates_setup() {

/*
    // This theme styles the visual editor to resemble the theme style.
    add_editor_style( array( 'css/editor-style.css', candidates_font_url() ) );
*/

    // Add RSS feed links to <head> for posts and comments.
    add_theme_support( 'automatic-feed-links' );

    // Enable support for Post Thumbnails, and declare two sizes.
    add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size( 160, 200, true );
    add_image_size( 'candidate-full', 1038, 576, true );

    // This theme uses wp_nav_menu() in two locations.
    register_nav_menus( array(
        'primary'   => 'Top primary menu',
        'secondary' => 'Primary menu in footer',
        'meta'      => 'Secondary menu in footer',
    ) );

    /*
     * Switch default core markup for search form, comment form, and comments
     * to output valid HTML5.
     */
    add_theme_support( 'html5', array(
        'search-form', 'comment-form', 'comment-list',
    ) );


    /*
     * Enable support for Post Formats.
     * See http://codex.wordpress.org/Post_Formats
     */
/*
    add_theme_support( 'post-formats', array(
        'aside', 'image', 'video', 'audio', 'quote', 'link', 'gallery',
    ) );
*/


    // This theme allows users to set a custom background.
    add_theme_support( 'custom-background', apply_filters( 'candidates_custom_background_args', array(
        'default-color' => 'f5f5f5',
    ) ) );

    // Add support for featured content.
    add_theme_support( 'featured-content', array(
        'featured_content_filter' => 'candidates_get_featured_posts',
        'max_posts' => 6,
    ) );

    // This theme uses its own gallery styles.
    add_filter( 'use_default_gallery_style', '__return_false' );
}
endif; // candidates_setup
add_action( 'after_setup_theme', 'candidates_setup' );


/**
 * Adjust content_width value for image attachment template.
 *
 * @return void
 */
/*
function candidates_content_width() {
    if ( is_attachment() && wp_attachment_is_image() ) {
        $GLOBALS['content_width'] = 810;
    }
}
add_action( 'template_redirect', 'candidates_content_width' );
*/


/**
 * Getter function for Featured Content Plugin.
 *
 * @return array An array of WP_Post objects.
 */
function candidates_get_featured_posts() {
    /**
     * Filter the featured posts to return in Twenty Fourteen.
     *
     * @since Twenty Fourteen 1.0
     *
     * @param array|bool $posts Array of featured posts, otherwise false.
     */
    return apply_filters( 'candidates_get_featured_posts', array() );
}

/**
 * A helper conditional function that returns a boolean value.
 *
 * @return bool Whether there are featured posts.
 */
function candidates_has_featured_posts() {
    return ! is_paged() && (bool) candidates_get_featured_posts();
}


/**
 * Register widget areas.
 *
 * @return void
 */
function candidates_widgets_init() {
    require get_template_directory() . '/inc/widgets.php';
    register_widget( 'Twenty_Fourteen_Ephemera_Widget' );

    register_sidebar( array(
        'name'          => 'Content Sidebar',
        'id'            => 'content-sidebar',
        'description'   => 'Primary content sidebar that appears on the right.',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h1 class="widget-title">',
        'after_title'   => '</h1>',
    ) );
    register_sidebar( array(
        'name'          => 'Political Party Information Page Widget Area',
        'id'            => 'party-single',
        'description'   => 'Widgets in this area will be shown under an individual political party\'s profile on /parties/example-party.',
        'before_widget' => '<div>',
        'after_widget'  => '</div>',
        'before_title'  => '<h2>',
        'after_title'   => '</h2>',
    ) );
    register_sidebar( array(
        'name'          => 'Candidate Information Page Widget Area',
        'id'            => 'candidate-single',
        'description'   => 'Widgets in this area will be shown under an individual candidate\'s profile (e.g. on /candidates/joe-example).',
        'before_widget' => '<div>',
        'after_widget'  => '</div>',
        'before_title'  => '<h2>',
        'after_title'   => '</h2>',
    ) );
}
add_action( 'widgets_init', 'candidates_widgets_init' );

/**
 * Register Merriweather Google font for Candidates.
 *
 * @return string
 */
function candidates_font_url() {
    /*
     * There used to be some translation support in here. Since this theme will likely never be translated, I've stripped it out.
     */
/*     $font_url = add_query_arg( 'family', urlencode( 'Merriweather:400,300,300italic,400italic,700,900,900italic,700italic' ), "//fonts.googleapis.com/css" ); */
    $font_url = add_query_arg( 'family', urlencode( 'Merriweather:400,300,300italic,400italic,700,900,900italic,700italic' ), "http://fonts.googleapis.com/css" );

    return $font_url;
}


/**
 * Enqueue scripts and styles for the front end.
 *
 *
 * @return void
 */
function candidates_scripts() {
    // Add Merriweather font, used in the main stylesheet.
    wp_enqueue_style( 'merriweather', candidates_font_url(), array(), null );

    // Add Genericons font, used in the main stylesheet.
    wp_enqueue_style( 'genericons', get_template_directory_uri() . '/genericons/genericons.css', array(), '3.0.2' );

    // Load normalize.css
    wp_enqueue_style( 'normalize', get_template_directory_uri() . '/css/normalize.css' );
    
    // Load boilerplate-base.css
    wp_enqueue_style( 'boilerplate-base', get_template_directory_uri() . '/css/boilerplate-base.css' );
    
    // Load typography.css
    wp_enqueue_style( 'typography', get_template_directory_uri() . '/css/typography.css' );

    // Load skeleton.css
    wp_enqueue_style( 'skeleton', get_template_directory_uri() . '/css/skeleton.css' );

    // Load our main stylesheet.
    wp_enqueue_style( 'candidates-style', get_stylesheet_uri(), array( 'merriweather', 'genericons', 'normalize', 'boilerplate-base', 'typography', 'skeleton' ) );

    // Load boilerplate-helpers.css
    wp_enqueue_style( 'boilerplate-helpers', get_template_directory_uri() . '/css/boilerplate-helpers.css', array( 'candidates-style' ) );


/*
    // Load the Internet Explorer specific stylesheet.
    wp_enqueue_style( 'candidates-ie', get_template_directory_uri() . '/css/ie.css', array( 'candidates-style', 'genericons' ), '20131205' );
    wp_style_add_data( 'candidates-ie', 'conditional', 'lt IE 9' );
*/

/*
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
*/

/*
    if ( is_singular() && wp_attachment_is_image() ) {
        wp_enqueue_script( 'candidates-keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), '20130402' );
    }
*/

/*
    // http://masonry.desandro.com/
    if ( is_active_sidebar( 'sidebar-3' ) ) {
        wp_enqueue_script( 'jquery-masonry' );
    }
*/

/*
    if ( is_front_page() && 'slider' == get_theme_mod( 'featured_content_layout' ) ) {
        wp_enqueue_script( 'candidates-slider', get_template_directory_uri() . '/js/slider.js', array( 'jquery' ), '20131205', true );
        wp_localize_script( 'candidates-slider', 'featuredSliderDefaults', array(
            'prevText' => 'Previous',
            'nextText' => 'Next',
        ) );
    }
*/

/*  wp_enqueue_script( 'candidates-script', get_template_directory_uri() . '/js/functions.js', array( 'jquery' ), '20131209', true ); */
}
add_action( 'wp_enqueue_scripts', 'candidates_scripts' );


/**
 * Enqueue Google fonts style to admin screen for custom header display.
 *
 * @return void
 */
function candidates_admin_fonts() {
    wp_enqueue_style( 'candidates-merriweather', candidates_font_url(), array(), null );
}
add_action( 'admin_print_scripts-appearance_page_custom-header', 'candidates_admin_fonts' );


if ( ! function_exists( 'candidates_the_attached_image' ) ) :
/**
 * Print the attached image with a link to the next attached image.
 *
 * @return void
 */
function candidates_the_attached_image() {
    $post                = get_post();
    /**
     * Filter the default Twenty Fourteen attachment size.
     *
     * @param array $dimensions {
     *     An array of height and width dimensions.
     *
     *     @type int $height Height of the image in pixels. Default 810.
     *     @type int $width  Width of the image in pixels. Default 810.
     * }
     */
    $attachment_size     = apply_filters( 'candidates_attachment_size', array( 810, 810 ) );
    $next_attachment_url = wp_get_attachment_url();

    /*
     * Grab the IDs of all the image attachments in a gallery so we can get the URL
     * of the next adjacent image in a gallery, or the first image (if we're
     * looking at the last image in a gallery), or, in a gallery of one, just the
     * link to that image file.
     */
    $attachment_ids = get_posts( array(
        'post_parent'    => $post->post_parent,
        'fields'         => 'ids',
        'numberposts'    => -1,
        'post_status'    => 'inherit',
        'post_type'      => 'attachment',
        'post_mime_type' => 'image',
        'order'          => 'ASC',
        'orderby'        => 'menu_order ID',
    ) );

    // If there is more than 1 attachment in a gallery...
    if ( count( $attachment_ids ) > 1 ) {
        foreach ( $attachment_ids as $attachment_id ) {
            if ( $attachment_id == $post->ID ) {
                $next_id = current( $attachment_ids );
                break;
            }
        }

        // get the URL of the next image attachment...
        if ( $next_id ) {
            $next_attachment_url = get_attachment_link( $next_id );
        }

        // or get the URL of the first image attachment.
        else {
            $next_attachment_url = get_attachment_link( array_shift( $attachment_ids ) );
        }
    }

    printf( '<a href="%1$s" rel="attachment">%2$s</a>',
        esc_url( $next_attachment_url ),
        wp_get_attachment_image( $post->ID, $attachment_size )
    );
}
endif;


if ( ! function_exists( 'candidates_list_authors' ) ) :
/**
 * Print a list of all site contributors who published at least one post.
 *
 * @return void
 */
function candidates_list_authors() {
    $contributor_ids = get_users( array(
        'fields'  => 'ID',
        'orderby' => 'post_count',
        'order'   => 'DESC',
        'who'     => 'authors',
    ) );

    foreach ( $contributor_ids as $contributor_id ) :
        $post_count = count_user_posts( $contributor_id );

        // Move on if user has not published a post (yet).
        if ( ! $post_count ) {
            continue;
        }
    ?>

    <div class="contributor">
        <div class="contributor-info">
            <div class="contributor-avatar"><?php echo get_avatar( $contributor_id, 132 ); ?></div>
            <div class="contributor-summary">
                <h2 class="contributor-name"><?php echo get_the_author_meta( 'display_name', $contributor_id ); ?></h2>
                <p class="contributor-bio">
                    <?php echo get_the_author_meta( 'description', $contributor_id ); ?>
                </p>
                <a class="contributor-posts-link" href="<?php echo esc_url( get_author_posts_url( $contributor_id ) ); ?>">
                    <?php printf( _n( '%d Article', '%d Articles', $post_count ), $post_count ); ?>
                </a>
            </div><!-- .contributor-summary -->
        </div><!-- .contributor-info -->
    </div><!-- .contributor -->

    <?php
    endforeach;
}
endif;


/**
 * Extend the default WordPress body classes.
 *
 * Adds body classes to denote:
 * 1. Single or multiple authors.
 * 2. Presence of header image.
 * 3. Index views.
 * 4. Full-width content layout.
 * 5. Presence of footer widgets.
 * 6. Single views.
 * 7. Featured content layout.
 *
 * @param array $classes A list of existing body class values.
 * @return array The filtered body class list.
 */
function candidates_body_classes( $classes ) {
    if ( is_multi_author() ) {
        $classes[] = 'group-blog';
    }

    if ( get_header_image() ) {
        $classes[] = 'header-image';
    } else {
        $classes[] = 'masthead-fixed';
    }

    if ( is_archive() || is_search() || is_home() ) {
        $classes[] = 'list-view';
    }

    if ( ( ! is_active_sidebar( 'sidebar-2' ) )
        || is_page_template( 'page-templates/full-width.php' )
        || is_page_template( 'page-templates/contributors.php' )
        || is_attachment() ) {
        $classes[] = 'full-width';
    }

    if ( is_active_sidebar( 'sidebar-3' ) ) {
        $classes[] = 'footer-widgets';
    }

    if ( is_singular() && ! is_front_page() ) {
        $classes[] = 'singular';
    }

    if ( is_front_page() && 'slider' == get_theme_mod( 'featured_content_layout' ) ) {
        $classes[] = 'slider';
    } elseif ( is_front_page() ) {
        $classes[] = 'grid';
    }

    return $classes;
}
add_filter( 'body_class', 'candidates_body_classes' );


/**
 * Extend the default WordPress post classes.
 *
 * Adds a post class to denote:
 * Non-password protected page with a post thumbnail.
 *
 * @param array $classes A list of existing post class values.
 * @return array The filtered post class list.
 */
function candidates_post_classes( $classes ) {
    if ( ! post_password_required() && has_post_thumbnail() ) {
        $classes[] = 'has-post-thumbnail';
    }

    return $classes;
}
add_filter( 'post_class', 'candidates_post_classes' );


/**
 * Create a nicely formatted and more specific title element text for output
 * in head of document, based on current view.
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string The filtered title.
 */
function candidates_wp_title( $title, $sep ) {
    global $paged, $page;

    if ( is_feed() ) {
        return $title;
    }

    // Add the site name.
    $title .= get_bloginfo( 'name' );

    // Add the site description for the home/front page.
    $site_description = get_bloginfo( 'description', 'display' );
    if ( $site_description && ( is_home() || is_front_page() ) ) {
        $title = "$title $sep $site_description";
    }

    // Add a page number if necessary.
    if ( $paged >= 2 || $page >= 2 ) {
        $title = "$title $sep " . sprintf( 'Page %s', max( $paged, $page ) );
    }

    return $title;
}
add_filter( 'wp_title', 'candidates_wp_title', 10, 2 );


// Implement Custom Header features.
require get_template_directory() . '/inc/custom-header.php';

// Custom template tags for this theme.
require get_template_directory() . '/inc/template-tags.php';

// Add Theme Customizer functionality.
require get_template_directory() . '/inc/customizer.php';


/*
 * Add Featured Content functionality.
 *
 * To overwrite in a plugin, define your own Featured_Content class on or
 * before the 'setup_theme' hook.
 */
if ( ! class_exists( 'Featured_Content' ) && 'plugins.php' !== $GLOBALS['pagenow'] ) {
    require get_template_directory() . '/inc/featured-content.php';
}
