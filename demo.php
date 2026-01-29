<?php
/* Template Name: Example Template */



$page_id = '378672501998936';
$access_token = FB_PAGE_TOKEN;

$url = add_query_arg(
    array(
        'fields' => 'name,fan_count,link',
        'access_token' => $access_token,
    ),
    "https://graph.facebook.com/v19.0/{$page_id}"
);

$response = wp_remote_get($url);

if (!is_wp_error($response)) {
    $data = json_decode(wp_remote_retrieve_body($response), true);
    
    if (isset($data['name'])) {
        ?>
        <div class="fb-page-info">
            <h3><?php echo esc_html($data['name']); ?></h3>
            <p>Followers: <?php echo esc_html($data['fan_count']); ?></p>
            <a href="<?php echo esc_url($data['link']); ?>" target="_blank">
                Visit Facebook Page
            </a>
        </div>
        <?php
    }
}
?>
