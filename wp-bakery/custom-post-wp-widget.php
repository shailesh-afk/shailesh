<?php
class MyTheme_Post_Box {

    public function __construct() {
        add_action( 'vc_before_init', array( $this, 'register_vc_element' ) );
        add_shortcode( 'mytheme_post_box', array( $this, 'render_shortcode' ) );
    }

    /**
     * Register WPBakery element
     */
    public function register_vc_element() {
        $post_types = get_post_types(
            array( 'public' => true ),
            'objects'
        );

        $post_type_options = array();
        foreach ( $post_types as $post_type ) {
            if($post_type->name == 'page' || $post_type->name == 'attachment'){
                continue;
            }
            $post_type_options[ $post_type->labels->singular_name ] = $post_type->name;
        }

        vc_map( array(
            'name'     => __( 'Post Box', 'mytheme' ),
            'base'     => 'mytheme_post_box',
            'category' => __( 'My Theme Elements', 'mytheme' ),
            'icon'     => 'icon-wpb-ui-post',
            'params'   => array(
                array(
                    'type'       => 'dropdown',
                    'heading'    => __( 'Select Post Type', 'mytheme' ),
                    'param_name' => 'post_type',
                    'value'      => $post_type_options,
                    'std'        => 'post',
                ),
                array(
                    'type'       => 'textfield',
                    'heading'    => __( 'Button Text', 'mytheme' ),
                    'param_name' => 'post-button-text',
                ),
                array(
                    'type'       => 'colorpicker',
                    'heading'    => __( 'Background Color', 'mytheme' ),
                    'param_name' => 'post_bg_color',
                ),
            ),
        ) );
    }

    /**
     * Shortcode output
     */
    public function render_shortcode( $atts ) {

        $atts = shortcode_atts( array(
            'post_type'     => 'post',
            'post_bg_color' => '',
            'post-button-text' => '',
        ), $atts, 'mytheme_post_box' );

        $style = '';
        if ( ! empty( $atts['post_bg_color'] ) ) {
            $style = 'style="background-color:' . esc_attr( $atts['post_bg_color'] ) . ';"';
        }

        $query = new WP_Query( array(
            'post_type'      => $atts['post_type'],
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ) );

        ob_start();
        ?>
        <div class="mytheme-accordion-box" <?php echo $style; ?>>
            <?php if ( $query->have_posts() ) : ?>
                <div class="mytheme-accordion">
                    <?php 
                    while ( $query->have_posts() ) : 
                        $query->the_post(); 
                    ?>
                        <div class="mytheme-accordion__item">
                            <h4 class="mytheme-accordion__title">
                                <?php the_title(); ?>
                            </h4>
                            <?php if ( has_post_thumbnail() ) : ?>
                                <div class="mytheme-accordion-box__image">
                                    <?php the_post_thumbnail( 'full' ); ?>
                                </div>
                            <?php endif; ?>
                            <div class="mytheme-accordion__content">
                                <?php 
                                $content = get_the_content();
                                $content = wp_strip_all_tags( $content );
                                echo wp_trim_words( $content, 20, '...<a href="'.get_permalink().'">'.$atts['post-button-text'].'</a>' ); 
                                ?>
                            </div>
                        </div>
                    <?php 
                    endwhile; 
                    ?>
                </div>
            <?php 
                endif; 
                wp_reset_postdata(); 
            ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
new MyTheme_Post_Box();
