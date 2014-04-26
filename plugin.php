<?php
/**
 * Plugin Name: Shortcode Attachments
 * Plugin URI:  http://www.adamko.info
 * Description: Inserts markdown (shortcode) attachmets to a post
 * Version:     v0.1.0
 * Author:      @martin_adamko
 * Author URI:  http://twitter.com/martin_adamko
 * License:     MIT
 */

/**
 * Modify inserted attachment short code
 *
 * TODO: http://fotd.werdswords.com/media_send_to_editor/
 */
function media_send_to_editor_as_shortcode($html, $send_id, $attachment)
{
    trigger_error(json_encode(func_get_args(), JSON_PRETTY_PRINT));

    if (isset($attachment['image_alt'])) {
        // Image
        return '[image id="'.$send_id.'" size="'.$attachment['image-size'].'"]';
    }

    return '[file id="'.$send_id.'"]';
}

/**
 * Handle shortcode
 *
 * @see http://codex.wordpress.org/Function_Reference/wp_get_attachment_link
 *
 */
function shortcode_file_shortcode_handler($args)
{
    // TODO: add attr class
    $defaults = array(
        'size'      => apply_filters('shortcode_file_size_default', 'thumbnail'),
        'permalink' => apply_filters('shortcode_file_permalink_default', false),
        'icon'      => apply_filters('shortcode_file_icon_default', false),
        'text'      => false,
    );

    $args = wp_parse_args($args, $defaults);

    return preg_replace('|(<a .+?)>|', '$1 target="_blank">', wp_get_attachment_link($args['id'], $args['size'], $args['permalink'], $args['icon'], $args['text']));
}

/**
 * Handle shortcode
 *
 * @see http://codex.wordpress.org/Function_Reference/wp_get_attachment_image
 *
 */
function shortcode_image_shortcode_handler($args)
{
    $defaults = array(
        'size' => apply_filters('shortcode_image_size_default', 'thumbnail'),
        'icon' => apply_filters('shortcode_image_icon_default', false),
    );

    $args = wp_parse_args($args, $defaults);

    return wp_get_attachment_image($args['id'], $args['size'], $args['icon']);
}

/**
 * enqueue script
 *
 * @see http://wordpress.stackexchange.com/questions/22643/how-set-defaults-on-wplink
 *
 */
function wpse22643_overwrite_wplinks( $hook ) {

    // register is important, that other plugins will change or deactivate this
    wp_register_script(
        'overwrite-wplinks',
        plugins_url('/js/wpLink.js', __FILE__),
        array( 'wplink' ),
        '',
        TRUE
    );

    wp_enqueue_script( 'overwrite-wplinks' );
}
add_shortcode('image', 'shortcode_image_shortcode_handler' );
add_shortcode('file',  'shortcode_file_shortcode_handler' );

add_filter( 'media_send_to_editor', 'media_send_to_editor_as_shortcode', 10, 3);

add_action( 'admin_print_scripts-post.php',     'wpse22643_overwrite_wplinks', 0 );
add_action( 'admin_print_scripts-post-new.php', 'wpse22643_overwrite_wplinks', 0 );

add_filter('wp_link_query', function($results) {
    foreach ($results as &$post) {
        $post['permalink'] = $post['ID'];
    }

    return $results;
}, 10, 1);

function replace_links_with_id($matches)
{
    $link = array(
        'href'   => get_permalink($matches['ID']),
        'title'  => null,
        'text'   => $matches['text'],
        'target' => null
    );

    if (strlen(trim($matches['title'])) > 0) {
        $link['title'] = str_replace(array('&#8220;', '&#8243;'), array('', ''), trim(trim($matches['title']), "“″"));
    } else {
        $link['title'] = get_the_title($matches['ID']);
    }

    if (strlen(trim($matches[4])) > 0) {
        $link['target'] = trim($matches['target']);
    }

    return '<a href="'.$link['href'].'" title="'.$link['title'].'"'. (! empty($link['target']) ? ' target="'.$link['target'].'"' : '').'>'.$link['text'].'</a>';
}

add_filter('the_content', function($the_content) {
    $the_content = preg_replace_callback('|<a href='.
        '"(?<ID>\d+)"'.
        '( title="(?<title>.+?)")?'.
        '>'.
        '(?<text>.+?)'.
        '</a>|ms', 'replace_links_with_id', $the_content);
    $the_content = preg_replace_callback('|\['.
        '(?<text>.+?)'.
        '\]'.
        '\('.
        '(?<ID>\d+)'.
        '(?<title> ["“(&#8220;)].+?["″(&#8220;)])?'.
        '(?<target> _blank)?\)|', 'replace_links_with_id', $the_content);

    return $the_content;
}, 99);
