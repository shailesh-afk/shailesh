<?php
/**
 * Astra Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra Child
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_ASTRA_CHILD_VERSION', '1.0.0' );

/**
 * Enqueue styles
 */
function child_enqueue_styles() {
	wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_ASTRA_CHILD_VERSION, 'all' );

}
add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );



add_action('woocommerce_before_add_to_cart_button', 'single_product_custom_field_display');
function single_product_custom_field_display() {
    echo '<div class="first-name-field">
    <label for="first-name-product">'.__('First Name:').'</label>
    <input type="text" id="first-name-product" name="first-name-product" />
    </div>';

    echo '<div class="last-name-field">
    <label for="last-name-product">'.__('Last Name:').'</label>
    <input type="text" id="last-name-product" name="last-name-product" />
    </div>';
}

// Save custom fields as custom cart item data
add_filter('woocommerce_add_cart_item_data', 'add_custom_cart_item_data', 20, 2 );
function add_custom_cart_item_data( $cart_item_data, $product_id ) {
    if ( isset($_POST['first-name-product']) ) {
        $cart_item_data['first-name-product'] = sanitize_text_field($_POST['first-name-product']);
    }
    if ( isset($_POST['last-name-product']) ) {
        $cart_item_data['last-name-product'] = sanitize_text_field($_POST['last-name-product']);
    }
    return $cart_item_data;
}

// Display custom fields in Cart and Checkout
add_filter( 'woocommerce_get_item_data', 'display_custom_cart_item_data', 20, 2 );
function display_custom_cart_item_data( $cart_data, $cart_item ) {
    if( isset($cart_item['first-name-product']) ) {
        $cart_data[] = array(
            'key'   => __('First Name'),
            'value' => $cart_item['first-name-product'],
        );
    }
    if( isset($cart_item['last-name-product']) ) {
        $cart_data[] = array(
            'key'   => __('Last Name'),
            'value' => $cart_item['last-name-product'],
        );
    }
    return $cart_data;
}

// Save and display custom fields (custom order item metadata)
add_action( 'woocommerce_checkout_create_order_line_item', 'save_order_item_custom_meta_data6', 10, 4 );
function save_order_item_custom_meta_data6( $item, $cart_item_key, $values, $order ) {
    if( isset($values['first-name-product']) ) {
        $item->update_meta_data('first-name-product', $values['first-name-product']); 
    }
    if( isset($values['last-name-product']) ) {
        $item->update_meta_data('last-name-product', $values['last-name-product']); 
    }
}

// Add readable "meta key" label name replacement
add_filter('woocommerce_order_item_display_meta_key', 'filter_wc_order_item_display_meta_key6', 10, 3 );
function filter_wc_order_item_display_meta_key6( $display_key, $meta, $item ) {
    if( $item->get_type() === 'line_item' ) {
        if( $meta->key === 'first-name-product' ) {
            $display_key = __('First Name');
        }
    }
    if( $item->get_type() === 'line_item' ) {
        if( $meta->key === 'last-name-product' ) {
            $display_key = __('Last Name');
        }
    }
    return $display_key;
}


add_action( 'woocommerce_before_order_notes', 'codeastrology_add_custom_checkout_field' ); 
function codeastrology_add_custom_checkout_field( $checkout ) { 
   $current_user = wp_get_current_user();
   $saved_license_no = $current_user->license_no;
   woocommerce_form_field( 'license_no', array(        
      'type' => 'text',        
      'class' => array( 'form-row-wide' ),        
      'label' => 'License Number',        
      'placeholder' => 'CA12345678',        
      'required' => true,        
      'default' => $saved_license_no,        
   ), $checkout->get_value( 'license_no' ) ); 
}

add_action( 'woocommerce_checkout_update_order_meta', 'codeastrology_save_new_checkout_field' );	
function codeastrology_save_new_checkout_field( $order_id ) { 
    if ( $_POST['license_no'] ) {
    	update_post_meta( $order_id, '_license_no', esc_attr( $_POST['license_no'] ) );
    }
}
  
add_action( 'woocommerce_admin_order_data_after_billing_address', 'codeastrology_show_new_checkout_field_order', 10, 1 );
function codeastrology_show_new_checkout_field_order( $order ) {    
   $order_id = $order->get_id();
   if ( get_post_meta( $order_id, '_license_no', true ) ) {
   		echo '<p><strong>License Number:</strong> ' . get_post_meta( $order_id, '_license_no', true ) . '</p>';
   }
}

add_action( 'woocommerce_email_after_order_table', 'codeastrology_show_new_checkout_field_emails', 20, 4 );
function codeastrology_show_new_checkout_field_emails( $order, $sent_to_admin, $plain_text, $email ) {
    if ( get_post_meta( $order->get_id(), '_license_no', true ) ) 
    {
    	echo '<p><strong>License Number:</strong> ' . get_post_meta( $order->get_id(), '_license_no', true ) . '</p>';	
    }
}

add_action( 'woocommerce_admin_order_data_after_billing_address', 'codeastrology_show_editable_license_no', 10, 1 );
function codeastrology_show_editable_license_no( $order ) {
    $order_id   = $order->get_id();
    $license_no = get_post_meta( $order_id, '_license_no', true );
    ?>
    <p class="form-field form-field-wide">
        <label for="license_no"><?php _e( 'License Number', 'woocommerce' ); ?>:</label>
        <input type="text" id="license_no" name="license_no" value="<?php echo esc_attr( $license_no ); ?>" style="width:100%;">
    </p>
    <?php
}

add_action( 'woocommerce_process_shop_order_meta', 'codeastrology_save_license_no' );
function codeastrology_save_license_no( $order_id ) {
    if ( isset( $_POST['license_no'] ) ) {
        update_post_meta( $order_id, '_license_no', sanitize_text_field( $_POST['license_no'] ) );
    }
}


add_filter( 'woocommerce_billing_fields', 'ts_unrequire_wc_phone_field');
function ts_unrequire_wc_phone_field( $fields ) {
	$fields['billing_phone']['required'] = true;
	return $fields;
}



add_action( 'woocommerce_thankyou', 'show_custom_field_on_thankyou', 20 );
function show_custom_field_on_thankyou( $order_id ) {
    if ( ! $order_id ) return;
    $value = get_post_meta( $order_id, '_license_no', true );
    if ( $value ) {
        echo '<div class="woocommerce-order-custom-field" style="margin-top:20px;">';
        echo '<h3>' . __( 'License Number' ) . '</h3>';
        echo '<p>' . esc_html( $value ) . '</p>';
        echo '</div>';
    }
}








//---- Add buttons to top of post content
/*function ip_post_likes($content) {
    // Check if single post
    if(is_singular('post')) {
        ob_start();
        ?>
        <ul class="likes">
            <li class="likes__item likes__item--like">
                <a href="<?php echo add_query_arg('post_action', 'like'); ?>">
                    Like (<?php echo ip_get_like_count('likes') ?>)
                </a>
            </li>
            <li class="likes__item likes__item--dislike">
                <a href="<?php echo add_query_arg('post_action', 'dislike'); ?>">
                    Dislike (<?php echo ip_get_like_count('dislikes') ?>)
                </a>
            </li>
        </ul>
        <?php
        $output = ob_get_clean();
        return $output . $content;
    }else {
        return $content;
    }
}

add_filter('the_content', 'ip_post_likes');

//---- Get like or dislike count
function ip_get_like_count($type = 'likes') {
    $current_count = get_post_meta(get_the_id(), $type, true);
    return ($current_count ? $current_count : 0);
}

//---- Process like or dislike
function ip_process_like() {
    $processed_like = false;
    $redirect       = false;

    if(is_singular('post')) {
        if(isset($_GET['post_action'])) {
            if($_GET['post_action'] == 'like') {
                // Like
                $like_count = get_post_meta(get_the_id(), 'likes', true);

                if($like_count) {
                    $like_count = $like_count + 1;
                }else {
                    $like_count = 1;
                }

                $processed_like = update_post_meta(get_the_id(), 'likes', $like_count);
            }elseif($_GET['post_action'] == 'dislike') {
                // Dislike
                $dislike_count = get_post_meta(get_the_id(), 'dislikes', true);

                if($dislike_count) {
                    $dislike_count = $dislike_count + 1;
                }else {
                    $dislike_count = 1;
                }

                $processed_like = update_post_meta(get_the_id(), 'dislikes', $dislike_count);
            }

            if($processed_like) {
                $redirect = get_the_permalink();
            }
        }
    }
    if($redirect) {
        wp_redirect($redirect);
        die;
    }
}
add_action('template_redirect', 'ip_process_like'); */

// Function to count post views



function custom_vote_scripts() {
    wp_enqueue_script('custom-script', get_stylesheet_directory_uri() . '/js/custom-script.js', array('jquery'), '1.0', true);
    wp_localize_script('custom-script', 'customscript', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'custom_vote_scripts');

function custom_post_likes_dislikes($content) {
    if (is_singular('post')) {
        ob_start(); 
        $post_id = get_the_ID();
        $likes = get_post_meta($post_id, '_post_likes', true);
        $dislikes = get_post_meta($post_id, '_post_dislikes', true);
        $likes = ($likes) ? $likes : 0;
        $dislikes = ($dislikes) ? $dislikes : 0;
        ?>
        <div class="custom-vote-buttons">
            <a href="javascript:void(0)" class="custom-vote-btn" data-action="like" data-post-id="<?php echo $post_id; ?>">
                Like (<span id="like-count-<?php echo $post_id; ?>"><?php echo $likes; ?></span>)
            </a>
            <a href="javascript:void(0)" class="custom-vote-btn" data-action="dislike" data-post-id="<?php echo $post_id; ?>">
                Dislike (<span id="dislike-count-<?php echo $post_id; ?>"><?php echo $dislikes; ?></span>)
            </a>
        </div>
        <?php
        $output = ob_get_clean();
        return $output . $content;  
    }
    return $content;
}
add_filter('the_content', 'custom_post_likes_dislikes');


function process_post_vote() {
    if (!isset($_POST['post_id']) || !isset($_POST['vote_type'])) {
        wp_send_json_error(array('message' => 'Invalid request data.'));
    }
    $post_id = sanitize_text_field($_POST['post_id']);
    $vote_type = sanitize_text_field($_POST['vote_type']);
    $meta_key = ($vote_type === 'like') ? '_post_likes' : '_post_dislikes';
    $current_count = get_post_meta($post_id, $meta_key, true);
    $new_count = (empty($current_count) || !is_numeric($current_count)) ? 1 : (int)$current_count + 1;
    update_post_meta($post_id, $meta_key, $new_count);
    wp_send_json_success(array('new_count' => $new_count));
}
add_action('wp_ajax_process_post_vote', 'process_post_vote');
add_action('wp_ajax_nopriv_process_post_vote', 'process_post_vote');

/*function like_button( $content ) {
    if ( !is_singular('post') ) return $content;
    $post_id = get_the_ID();
    $likes = get_post_meta($post_id, 'wp_post_likes', true);
    $likes = ($likes == "") ? 0 : $likes;
    /*?>

    <div class="wp-like-wrapper">
        <button class="wp-like-btn" data-postid="<?php echo $post_id; ?>">
            ❤️ Like
        </button>
        <span class="wp-like-count"><?php echo $likes; ?></span>
    </div>
    <?php*/ /*
    global $post;

    $post_id = $post->ID;
    $current_count = get_post_meta( $post_id, 'likes',true);
    $current_count = $current_count ? $current_count : 0;
    
    ob_start();
    ?>
    <div class='aj_likes'>
        <p><a class='aj_thumbsup' href='' data-id='<?php echo $post_id;?>'>
            <i class="far fa-thumbs-up"></i></a> 
            Likes (<span><?php echo $current_count;?></span>)</p>
    </div>
    <?php
    return ob_get_clean();
}
add_filter( 'the_content', 'like_button' );


function wp_post_like() {
    if( isset($_POST['post_id']) ) {
        $post_id = $_POST['post_id'];
        $likes = get_post_meta($post_id, 'wp_post_likes', true);
        $likes = ($likes == "") ? 0 : $likes;
        $likes++;
        update_post_meta($post_id, 'wp_post_likes', $likes);
        echo $likes; 
    }
    wp_die();
}
add_action('wp_ajax_wp_post_like', 'wp_post_like');
add_action('wp_ajax_nopriv_wp_post_like', 'wp_post_like');



add_action( 'wp_ajax_aj_like_post', 'aj_record_like_post' );
add_action( 'wp_ajax_nopriv_aj_like_post', 'aj_record_like_post' ); 
// nopriv allows for nonloggedin users to like as well

function aj_record_like_post(){
    $post_id = sanitize_text_field( $_POST['id']);
    $current_count = get_post_meta( $post_id, 'likes',true);
    $current_count = $current_count ? $current_count : 0;
    $new_count = $current_count + 1; 
    update_post_meta( $post_id, 'likes', $new_count);
    echo json_encode(array(
        'status'=>'good',
        'new_count'=>$new_count,
    )); exit;
}*/

/*add_filter( 'the_content', 'like_it_button_html', 99 );
function like_it_button_html( $content ) {
    global $post;
    $like_text = '';
    if ( is_single() ) {
        $nonce = wp_create_nonce( 'pt_like_it_nonce' );
        $link = admin_url('admin-ajax.php?action=pt_like_it&post_id='.$post->ID.'&nonce='.$nonce);
        $likes = get_post_meta( get_the_ID(), '_pt_likes', true );
        $likes = ( empty( $likes ) ) ? 0 : $likes;
        $like_text = '
                    <div class="pt-like-it">
                        <a class="like-button" href="'.$link.'" data-id="' . get_the_ID() . '" data-nonce="' . $nonce . '">' . 
                        __( 'Like it' ) .
                        '</a>
                        <span id="like-count-'.get_the_ID().'" class="like-count">' . $likes . '</span>
                    </div>';
    }
    return $content . $like_text;
}

add_action( 'wp_ajax_nopriv_pt_like_it', 'pt_like_it' );
add_action( 'wp_ajax_pt_like_it', 'pt_like_it' );
function pt_like_it() {
 
    if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'pt_like_it_nonce' ) || ! isset( $_REQUEST['nonce'] ) ) {
        exit( "No naughty business please" );
    }
 
    $likes = get_post_meta( $_REQUEST['post_id'], '_pt_likes', true );
    $likes = ( empty( $likes ) ) ? 0 : $likes;
    $new_likes = $likes + 1;
 
    update_post_meta( $_REQUEST['post_id'], '_pt_likes', $new_likes );
 
    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
        echo $new_likes;
        die();
    }
    else {
        wp_redirect( get_permalink( $_REQUEST['post_id'] ) );
        exit();
    }
}*/


/*function tr_generate_profile_slug( $user_id, $force = false ) {
    $current_profile_slug = $this->get_profile_slug( $user_id, true );
    error_log($current_profile_slug);

}
add_filter( 'um_custom_meta_permalink_base_generate_user_slug', 'tr_generate_profile_slug');
add_filter( 'um_change_user_profile_slug', 'tr_generate_profile_slug');*/

/*
* Creating a function to create our CPT
*/
function custom_post_type() {
    $labels = array(
        'name'                => _x( 'Movies', 'Post Type General Name', 'twentytwentyone' ),
        'singular_name'       => _x( 'Movie', 'Post Type Singular Name', 'twentytwentyone' ),
        'menu_name'           => __( 'Movies', 'twentytwentyone' ),
        'parent_item_colon'   => __( 'Parent Movie', 'twentytwentyone' ),
        'all_items'           => __( 'All Movies', 'twentytwentyone' ),
        'view_item'           => __( 'View Movie', 'twentytwentyone' ),
        'add_new_item'        => __( 'Add New Movie', 'twentytwentyone' ),
        'add_new'             => __( 'Add New', 'twentytwentyone' ),
        'edit_item'           => __( 'Edit Movie', 'twentytwentyone' ),
        'update_item'         => __( 'Update Movie', 'twentytwentyone' ),
        'search_items'        => __( 'Search Movie', 'twentytwentyone' ),
        'not_found'           => __( 'Not Found', 'twentytwentyone' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'twentytwentyone' ),
    );
      
    $args = array(
        'label'               => __( 'movies', 'twentytwentyone' ),
        'description'         => __( 'Movie news and reviews', 'twentytwentyone' ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
        'taxonomies'          => array( 'genres' ),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
        'show_in_rest' => true,
  
    );
    register_post_type( 'movies', $args );

    register_taxonomy(
        'movie_category',
        array( 'movies' ),
        array(
            'hierarchical'      => true,
            'labels'            => array(
                'name'          => 'Movie Categories',
                'singular_name' => 'Movie Category',
            ),
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array(
                'slug' => 'movie-category',
            ),
        )
    );

    register_taxonomy(
        'movie_tag',
        'movies',
        array(
            'hierarchical'      => false,
            'labels'            => array(
                'name'          => 'Movie Tags',
                'singular_name' => 'Movie Tag',
            ),
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'movie-tag'),
        )
    );

  
} 
/* Hook into the 'init' action so that the function
* Containing our post type registration is not 
* unnecessarily executed. 
*/
  
add_action( 'init', 'custom_post_type', 0 );


// Register the widget.
function register_custom_elementor_widgets( $widgets_manager ) {
   require_once __DIR__ . '/widgets/custom-post-widget.php';
   $widgets_manager->register( new \Custom_Post_Widget() );

   require_once __DIR__ . '/widgets/custom-accordian-widget.php';
   $widgets_manager->register( new \Custom_Accordian_Widget() );
}
add_action( 'elementor/widgets/register', 'register_custom_elementor_widgets' );



if ( is_plugin_active( 'js_composer/js_composer.php' ) ) {
    require_once __DIR__ . '/wp-bakery/custom-text-widget.php';
    require_once __DIR__ . '/wp-bakery/custom-accordian-wp-widget.php';
    require_once __DIR__ . '/wp-bakery/custom-post-wp-widget.php';
    //require_once __DIR__ . '/wp-bakery/custom-text-widget.php';
    //require_once __DIR__ . '/wp-bakery/custom-text-widget.php';
    //require_once __DIR__ . '/wp-bakery/custom-text-widget.php';
    //require_once __DIR__ . '/wp-bakery/custom-text-widget.php';
}




function fb_page_info_shortcode() {
    $page_id = '378672501998936';
    $access_token = 'EAARAGPDAIW0BQe7GFKvLMH1CZARGv5FbsurfuVMKgS3Ia3ixOYRbWxYA58Oo4uOZB7WQjGUpR0oLNAqM1cgpYYsJjNT0aLzAKmGmLWIGkkH4xyOeuv1kdm3TwoG4WtOaZCZBpc5NHgK1CGJy9Pg0n9EqEtnwuml1epAg1vZAbZB4GwXzq3pZCIKLBwlNbXWjdgaRkXcbUzmxPDERpHwUiJMJZCzB0Uh2usxsjma4vpxjqVdxrpltBZCmr5YWO19RIBvRi6OkaPaKmSZBALKvwZD';
    $url = "https://graph.facebook.com/v19.0/{$page_id}?fields=name,fan_count,link&access_token={$access_token}";
    $response = wp_remote_get($url);
    if (is_wp_error($response)) {
        return 'Facebook API request failed.';
    }
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    if (empty($data['name'])) {
        return 'No data returned from Facebook.';
    }

    ob_start();
    ?>
    <div class="fb-page-info">
        <h3><?php echo esc_html($data['name']); ?></h3>
        <p>Followers: <?php echo esc_html($data['fan_count']); ?></p>
        <a href="<?php echo esc_url($data['link']); ?>" target="_blank">
            Visit Facebook Page
        </a>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('fb_page_info', 'fb_page_info_shortcode');




/*--------------------------------------------------
  1. Add Admin Menu
--------------------------------------------------*/
add_action('admin_menu', 'my_custom_admin_menu');
function my_custom_admin_menu() {
    add_menu_page(
        'My Menu Page',        
        'My Menu',          
        'manage_options',   
        'my-menu-slug',      
        'my_menu_page_html', 
        'dashicons-admin-generic',
        20
    );
}

/*--------------------------------------------------
  2. Menu Page HTML
--------------------------------------------------*/
function my_menu_page_html() {
    ?>
    <div class="wrap">
        <h1>My Custom Menu</h1>
        <form method="post" action="options.php">
            <?php
                settings_fields('my_menu_settings_group');
                do_settings_sections('my-menu-slug');
                submit_button();
            ?>
        </form>
    </div>
    <?php
}

/*--------------------------------------------------
  3. Register Settings & Fields
--------------------------------------------------*/
add_action('admin_init', 'my_menu_register_settings');

function my_menu_register_settings() {
    register_setting(
        'my_menu_settings_group',
        'my_menu_name',
        array(
            'sanitize_callback' => 'sanitize_text_field'
        )
    );

    // Description WYSIWYG field
    register_setting(
        'my_menu_settings_group',
        'my_menu_description',
        array(
            'sanitize_callback' => 'wp_kses_post'
        )
    );

    add_settings_section(
        'my_menu_section',
        'Menu Information',
        '__return_false',
        'my-menu-slug'
    );

    add_settings_field(
        'my_menu_name',
        'Name',
        'my_menu_name_callback',
        'my-menu-slug',
        'my_menu_section'
    );

    add_settings_field(
        'my_menu_description',
        'Description',
        'my_menu_description_callback',
        'my-menu-slug',
        'my_menu_section'
    );
}

/*--------------------------------------------------
  4. Field Callbacks
--------------------------------------------------*/
function my_menu_name_callback() {
    $value = get_option('my_menu_name', '');
    ?>
    <input
        type="text"
        name="my_menu_name"
        value="<?php echo esc_attr($value); ?>"
        class="regular-text"
    />
    <?php
}

function my_menu_description_callback() {
    $content = get_option('my_menu_description', '');

    wp_editor(
        $content,
        'my_menu_description',
        array(
            'textarea_name' => 'my_menu_description',
            'media_buttons' => true,
            'textarea_rows' => 8,
            'teeny'         => false,
            'quicktags'     => true,
        )
    );
    ?>
    <p class="description" style="margin-top:8px;">
        <code>#username</code>
        <code>#displayname</code>
        <code>#email</code>
    </p>
    <?php
}



function my_menu_replace_variables( $text ) {

    if ( ! is_user_logged_in() ) {
        return $text;
    }

    $user = wp_get_current_user();

    $replacements = array(
        '#username'     => $user->user_login,
        '#displayname'  => $user->display_name,
        '#email'        => $user->user_email,
    );

    return str_replace(
        array_keys( $replacements ),
        array_values( $replacements ),
        $text
    );
}



add_filter( 'woocommerce_get_item_data', 'display_custom_cart_item_column_data', 10, 2 );
function display_custom_cart_item_column_data( $item_data, $cart_item ) {
    if ( isset( $cart_item['custom_text'] ) ) {
        $item_data[] = array(
            'name'  => 'Extra Info',
            'value' => wc_clean( $cart_item['custom_text'] ),
        );
    }
    return $item_data;
}