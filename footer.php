    </div></section>
</div><!-- end #page layer -->


<footer><div class="container">
    <section class="copyright three columns">
        <h1>Copyright</h1>
        <small>©2013 [Name]</small>
    </section>


    <nav class="container six columns">
        <h1 class="visuallyhidden">Site Navigation</h1>

        <?php hacs_wrapped_nav_menu($wrap_element='section', $wrap_attributes=array('class' => 'three columns first'), $name_element='h1', $name_attributes=array(), $args=array('theme_location' => 'secondary')); ?>

        <?php hacs_wrapped_nav_menu($wrap_element='section', $wrap_attributes=array('class' => 'three columns last'), $name_element='h1', $name_attributes=array(), $args=array('theme_location' => 'meta')); ?>
    </nav>
</div></footer>

<?php wp_footer(); ?>

</body>
</html>