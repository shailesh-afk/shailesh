<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Custom_Accordian_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'custom_accordian_widget';
    }

    public function get_title() {
        return __( 'Custom Accordian Widget', 'custom-elementor-widgets' );
    }

    public function get_icon() {
        return 'eicon-alert';
    }

    public function get_categories() {
        return [ 'basic' ];
    }

    protected function register_controls(): void {

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'textdomain' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'list_title',
			[
				'label' => esc_html__( 'Title', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'List Title' , 'textdomain' ),
				//'label_block' => true,
			]
		);

		$repeater->add_control(
			'list_content',
			[
				'label' => esc_html__( 'Content', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'List Content' , 'textdomain' ),
				//'show_label' => false,
			]
		);
		$repeater->add_control(
		    'list_image',
		    [
		        'label' => esc_html__( 'Image', 'textdomain' ),
		        'type' => \Elementor\Controls_Manager::MEDIA,
		        'default' => [
		            'url' => \Elementor\Utils::get_placeholder_image_src(),
		        ],
		    ]
		);
		$repeater->add_control(
			'list_color',
			[
				'label' => esc_html__( 'Color', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'color: {{VALUE}}'
				],
			]
		);

		$this->add_control(
			'list',
			[
				'label' => esc_html__( 'Accordian List', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'list_title' => esc_html__( 'Title #1', 'textdomain' ),
						'list_content' => esc_html__( 'Item content. Click the edit button to change this text.', 'textdomain' ),
						'list_image' => esc_html__( 'Item Image.', 'textdomain' ),
					],
					[
						'list_title' => esc_html__( 'Title #2', 'textdomain' ),
						'list_content' => esc_html__( 'Item content. Click the edit button to change this text.', 'textdomain' ),
						'list_image' => esc_html__( 'Item Image.', 'textdomain' ),
					],
				],
				'title_field' => '{{{ list_title }}}',
			]
		);

		$this->end_controls_section();

	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();

		if ( $settings['list'] ) { ?>
			<dl>
			<?php
			foreach (  $settings['list'] as $item ) {
				?>
				<dt class="elementor-repeater-item-<?php echo esc_attr( $item['_id'] ); ?>"> 
					<?php echo $item['list_title']; ?>	
				</dt>
				<?php 
				if ( ! empty( $item['list_image']['url'] ) ) {
				?>
	            <div class="accordion-image">
	                <img src="<?php echo esc_url( $item['list_image']['url'] ); ?>" alt="<?php echo $item['list_title']; ?>">
	            </div>
	            <?php
	            }
	            ?>
				<dd><?php echo $item['list_content']; ?></dd>
				<?php
			}
			?>
			</dl>
			<?php
		}
	}

	protected function content_template(): void {
		?>
		<# if ( settings.list.length ) { #>
			<dl>
			<# _.each( settings.list, function( item ) { #>
				<dt class="elementor-repeater-item-{{ item._id }}">{{{ item.list_title }}}</dt>
				<# if ( item.list_image && item.list_image.url ) { #>
                    <div class="accordion-image">
                        <img src="{{ item.list_image.url }}" alt="">
                    </div>
                <# } #>
				<dd>{{{ item.list_content }}}</dd>
			<# }); #>
			</dl>
		<# } #>
		<?php
	}
}
