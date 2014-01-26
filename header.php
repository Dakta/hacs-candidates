<?php
/**
 * The Header for our theme
 *
 * Displays all of the <head> section and everything up till <section><div class="container">
 *
 */
?><!doctype html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8) ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?php wp_title( '|', true, 'right' ); ?></title>
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/js/html5shiv.js"></script>
	<![endif]-->
	<?php wp_head(); ?>

</head>

<body ontouchstart="" <?php body_class(); ?>>
<div id="page-container"><!-- for the layered footer aesthetic -->
	<header id="masthead" class="site-header" role="banner">
    	<?php if ( get_header_image() ) : ?>
    	<div id="site-header">
    		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
    			<img src="<?php header_image(); ?>" width="<?php echo get_custom_header()->width; ?>" height="<?php echo get_custom_header()->height; ?>" alt="">
    		</a>
    	</div>
    	<?php endif; ?>
    
        <div class="container"><h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1></div>
        <nav><div class="container clearfix">
			<h1 class="visuallyhidden">Primary Menu</h1>
            <a class="screen-reader-text skip-link visuallyhidden" href="#content">Skip to content</a>
			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu clearfix' ) ); ?>
        </div></nav>
    </header>
    
    <section><div class="container">
