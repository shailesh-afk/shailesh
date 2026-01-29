<?php
/* Template Name: Text */

get_header();

$name = get_option( 'my_menu_name', '' );
$description = get_option( 'my_menu_description', '' );
?>
<h2><?php echo esc_html( my_menu_replace_variables( $name ) ); ?></h2>
<div class="content">
    <?php echo wp_kses_post( my_menu_replace_variables( $description ) ); ?>
</div>

<?php 
get_footer();
?>