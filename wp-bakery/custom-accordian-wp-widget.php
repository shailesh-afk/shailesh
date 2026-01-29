<?php
class MyTheme_Accordian_Box {

    public function __construct() {
        add_action( 'vc_before_init', array( $this, 'register_vc_element' ) );
        add_shortcode( 'mytheme_accordian_box', array( $this, 'render_shortcode' ) );
    }

    /**
     * Register WPBakery element
     */
    public function register_vc_element() {

        vc_map( array(
            'name'     => __( 'Accordion Box', 'mytheme' ),
            'base'     => 'mytheme_accordian_box',
            'category' => __( 'My Theme Elements', 'mytheme' ),
            'icon'     => 'icon-wpb-ui-accordion',

            'params'   => array(
                array(
                    'type'       => 'param_group',
                    'heading'    => __( 'Accordion Items', 'mytheme' ),
                    'param_name' => 'accordions',
                    'params'     => array(
                        array(
                            'type'       => 'textfield',
                            'heading'    => __( 'Title', 'mytheme' ),
                            'param_name' => 'title',
                        ),
                        array(
                            'type'       => 'attach_image',
                            'heading'    => __( 'Image', 'mytheme' ),
                            'param_name' => 'accordian_box_image',
                        ),
                        array(
                            'type'       => 'textarea',
                            'heading'    => __( 'Content', 'mytheme' ),
                            'param_name' => 'content',
                        ),
                    ),
                ),
                array(
                    'type'       => 'colorpicker',
                    'heading'    => __( 'Background Color', 'mytheme' ),
                    'param_name' => 'accordian_bg_color',
                ),
            ),
        ) );
    }

    /**
     * Shortcode output
     */
    public function render_shortcode( $atts ) {

        $atts = shortcode_atts( array(
            'accordions'           => '',
            'accordian_box_image'  => '',
            'accordian_bg_color'   => '',
        ), $atts, 'mytheme_accordian_box' );

        $style = '';
        if ( ! empty( $atts['accordian_bg_color'] ) ) {
            $style = 'style="background-color:' . esc_attr( $atts['accordian_bg_color'] ) . ';"';
        }
        $accordions = vc_param_group_parse_atts( $atts['accordions'] );

        ob_start();
        ?>
        <div class="mytheme-accordion-box" <?php echo $style; ?>>
            <?php if ( ! empty( $accordions ) ) : ?>
                <div class="mytheme-accordion">
                    <?php foreach ( $accordions as $index => $item ) : 
                        $image_url = '';
                        if ( ! empty( $item['accordian_box_image'] ) ) {
                            $image_url = wp_get_attachment_image_url( $item['accordian_box_image'], 'full' );
                        }
                        ?>
                        <div class="mytheme-accordion__item">
                            <h4 class="mytheme-accordion__title">
                                <?php echo esc_html( $item['title'] ); ?>
                            </h4>
                            <?php if ( $image_url ) : ?>
                                <div class="mytheme-accordion-box__image">
                                    <img src="<?php echo $image_url; ?>" alt="">
                                </div>
                            <?php endif; ?>
                            <div class="mytheme-accordion__content">
                                <?php echo wp_kses_post( $item['content'] ); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
new MyTheme_Accordian_Box();
