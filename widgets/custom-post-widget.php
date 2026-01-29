<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Custom_Post_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'custom_post_widget';
    }

    public function get_title() {
        return __( 'Custom Post Widget', 'custom-elementor-widgets' );
    }

    public function get_icon() {
        return 'eicon-alert';
    }

    public function get_categories() {
        return [ 'basic' ];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'section_content',
            [ 'label' => __( 'Content', 'custom-elementor-widgets' ) ]
        );

        $this->add_control(
            'post_text',
            [
                'label'   => __( 'Post Text', 'custom-elementor-widgets' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => __( 'This is a custom alert box!', 'custom-elementor-widgets' ),
            ]
        );
        $this->add_control(
            'post_button',
            [
                'label'   => __( 'Post Button', 'custom-elementor-widgets' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => __( 'Read More', 'custom-elementor-widgets' ),
            ]
        );
		$post_types = get_post_types(
	        [ 'public' => true ],
	        'objects'
	    );

	    $options = [];
	    foreach ( $post_types as $post_type ) {
	        //echo "<pre>"; print_r($post_type);
	    	if($post_type->name == 'page' || $post_type->name == 'attachment' || $post_type->name == 'e-floating-buttons' || $post_type->name == 'elementor_library'){
	    		continue;
	    	}
	        $options[ $post_type->name ] = $post_type->label;
	    }

	    $this->add_control(
	        'post_type',
	        [
	            'label'   => __( 'Select Post Type', 'plugin-name' ),
	            'type'    => \Elementor\Controls_Manager::SELECT,
	            'options' => $options,
	            'default' => 'post',
	        ]
	    );

	    $this->add_control(
	        'posts_per_page',
	        [
	            'label'   => __( 'Posts Per Page', 'plugin-name' ),
	            'type'    => \Elementor\Controls_Manager::NUMBER,
	            'default' => 5,
	        ]
	    );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        <div>
            <?php echo esc_html( $settings['post_text'] ); ?>
        </div>
        <?php
        $args = [
	        'post_type'      => $settings['post_type'],
	        'posts_per_page' => $settings['posts_per_page'],
	        'post_status'    => 'publish',
	    ];

	    $query = new \WP_Query( $args );

	    if ( $query->have_posts() ) : ?>
	        <div class="elementor-post-list">
	        <?php 
	        while ( $query->have_posts() ) : $query->the_post();
	        	$image_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
	            ?>
	            <div class="post-item">
	            	<?php if ( $image_url ) : ?>
                    <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
                    <?php endif; ?>
	                <h3><?php echo get_the_title(); ?></h3>
	                <div>
	                	<?php 
	                	$content = get_the_content();
						$content = wp_strip_all_tags( $content );
						echo wp_trim_words( $content, 20, '...<a href="'.get_permalink().'">'.$settings['post_button'].'</a>' ); 
						?>
					</div>
	            </div>
	            <?php
	        endwhile;
	        ?>
	        </div>
	        <?php
	        wp_reset_postdata();
	    else :
	        echo '<p>No posts found.</p>';
	    endif;
    }
}
