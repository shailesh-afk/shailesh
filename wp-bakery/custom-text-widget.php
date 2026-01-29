<?php 
class MyTheme_Custom_Box {

    public function __construct() {
        add_action( 'vc_before_init', array( $this, 'register_vc_element' ) );
        add_shortcode( 'mytheme_custom_box', array( $this, 'render_shortcode' ) );
    }

    /**
     * Register WPBakery element
     */
    public function register_vc_element() {
        vc_map( 
            array(
                'name'     => __( 'Custom Box', 'mytheme' ),
                'base'     => 'mytheme_custom_box',
                'category' => __( 'My Theme Elements', 'mytheme' ),
                'icon'     => 'icon-wpb-ui-custom_heading',
                'params'   => array(
                    array(
                        'type'       => 'textfield',
                        'heading'    => __( 'Title', 'mytheme' ),
                        'param_name' => 'custom-title-one',
                    ),
                    array(
                        'type'       => 'textarea',
                        'heading'    => __( 'Content', 'mytheme' ),
                        'param_name' => 'custom-content-one',
                    ),
                    array(
                        'type'       => 'attach_image',
                        'heading'    => __( 'Image', 'mytheme' ),
                        'param_name' => 'custom_box_image',
                    ),
                    array(
                        'type'       => 'colorpicker',
                        'heading'    => __( 'Background Color', 'mytheme' ),
                        'param_name' => 'bg_color',
                    ),
                ),
            ) 
        );
    }

    /**
     * Shortcode output
     */
    public function render_shortcode( $atts ) {

        $atts = shortcode_atts( array(
            'custom-title-one'    => '',
            'custom-content-one'  => '',
            'custom_box_image'    => '',
            'bg_color'            => '',
        ), $atts, 'mytheme_custom_box' );

        $style = '';
        if ( ! empty( $atts['bg_color'] ) ) {
            $style = 'style="background-color:' . esc_attr( $atts['bg_color'] ) . ';"';
        }

        $image_url = '';
        if ( ! empty( $atts['custom_box_image'] ) ) {
            $image_url = wp_get_attachment_image_url( $atts['custom_box_image'], 'full' );
        }
        ob_start();
        ?>
        <div class="mytheme-custom-box" <?php echo $style; ?>>
            <?php if ( ! empty( $atts['custom-title-one'] ) ) : ?>
                <h3 class="mytheme-custom-box__title">
                    <?php echo $atts['custom-title-one']; ?>
                </h3>
            <?php endif; ?>
            <?php if ( $image_url ) : ?>
                <div class="mytheme-custom-box__image">
                    <img src="<?php echo esc_url( $image_url ); ?>" alt="">
                </div>
            <?php endif; ?>
            <?php if ( ! empty( $atts['custom-content-one'] ) ) : ?>
                <div class="mytheme-custom-box__content">
                    <?php echo $atts['custom-content-one']; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
new MyTheme_Custom_Box();
?>