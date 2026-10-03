<?php function foun_list_shortcode($atts)
{
    $a = shortcode_atts(array(
        'page' => "",
        'posts-per-page' => '3',
        'pagination' => "0",
        'post-type' => "event",
    ) , $atts);

    global $wp_locale, $post;

    //To remove frontend manipulation
    // User can set as many posts as they want, now min = 1 max = 9 $a['posts-per-page'] = max(1, min(9, (int)$a['posts-per-page']));
    // User can set any post type they want, now only the once set below
    $allowedTypes = array(
        'post',
        'event'
    );
    if (!in_array($a['post-type'], $allowedTypes, true)) $a['post-type'] = 'event';
    $post_type = $a["post-type"];
    $page_key = $post_type . '-page';
    $paged = $a['pagination'] && isset($_GET[$page_key]) ? max(1, (int)$_GET[$page_key]) : 1;
    $post_query_args = array(
        'post_type' => $a["post-type"],
        'posts_per_page' => $a['posts-per-page'],
        'paged' => $paged,
        'orderby' => 'date',
        'order' => 'DESC',
    );
    // Events are the ones with a date field, they run upcoming first instead
    if ($post_type === 'event')
    {
        $post_query_args['meta_key'] = 'event_date_times_date';
        $post_query_args['orderby'] = 'meta_value_num';
        $post_query_args['order'] = 'ASC';
        $post_query_args['meta_query'] = array(
            array(
                'key' => 'event_date_times_date',
                'value' => date("Y-m-d") ,
                'compare' => '>=',
                'type' => 'DATE',
            )
        );
    }
    $post_query = new WP_Query($post_query_args);
    $output = '';
    $output .= '<div class="alm-paging-content ' . $post_type . '">';
    foreach ($post_query->posts as $post)
    {
        // ----- Gets info from to use easily later on
        //Date getter
        $event_date_times = $post_type === 'event' ? get_field('event_date_times') : null;
        $date_string = $event_date_times ? $event_date_times['date'] : get_the_date('Y-m-d');
        $date = new DateTime($date_string);

        //Excerpt getter
        $excerpt = has_excerpt() ? get_the_excerpt() : get_the_content();
        $excerpt = wp_trim_words($excerpt, 30, '...');

        //Place getter
        $place = $post_type === 'event' ? get_field('event_place') : '';

        //item Box
        $img_class = !has_post_thumbnail() ? " no-img" : "";
        $output .= '<div class="item' . $img_class . '">';
        // ----- Sets info, can be varied depending on post_type using the

        //Set image
        $output .= '<div class="thumbnail">';
        $output .= has_post_thumbnail() ? '<a href="' . get_the_permalink() . '">' . get_the_post_thumbnail(null, 'medium') . '</a>' : "";
        $output .= '</div>';

        //Info box
        $output .= '<div class="info">';

        //Set Date
        if ($post_type === 'event')
        {
            $output .= sprintf('<p>%s</p>', $date->format('d') . '. ' . $wp_locale->get_month($date->format('m')) . ' ' . $date->format('Y'));
        }

        //Set Heading
        $output .= sprintf('<h3><a href="%s">%s</a></h3>', get_the_permalink() , get_the_title());

        //Set excerpt
        if ($post_type === 'post')
        {
            $output .= sprintf('<p>%s</p>', $excerpt);
        }

        //Set place
        if ($post_type === 'event')
        {
            $output .= sprintf('<p class="place"> <b>Sted:</b> %s</p>', $place);
        }

        //Set link
        $output .= sprintf('<a class="read-more" href="%s">Gå til aktivitet</a>', get_the_permalink());

        //end item Box/Info
        $output .= '</div>';
        $output .= '</div>';
    }
    $output .= '</div>';
    
    if ($a['pagination'] && $post_query->max_num_pages > 1)
    {
        $output .= '<div class="alm-btn-wrap">';
        $output .= sprintf('<ul class="alm-paging inline-pagination-posts" data-page="%s" data-posts-per-page="%s" data-post-type="%s">', esc_attr($a['page']) , esc_attr($a['posts-per-page']) , esc_attr($a['post-type']));
        if ($paged > 1)
        {
            $output .= sprintf('<li class="prev"><a href="%s">%s</a></li>', esc_url(add_query_arg($page_key, $paged - 1)) , __('Forige', 'foun-theme'));
        }
        else
        {
            $output .= sprintf('<li class="prev disabled"><a>%s</a></li>', __('Forige', 'foun-theme'));
        }
        $output .= foun_pagination_numbers($paged, $post_query->max_num_pages, $page_key);
        if ($paged < $post_query->max_num_pages)
        {
            $output .= sprintf('<li class="next"><a href="%s">%s</a></li>', esc_url(add_query_arg($page_key, $paged + 1)) , __('Næste', 'foun-theme'));
        }
        $output .= '</ul>';
        $output .= '</div>';
    }
    return do_shortcode($output);
}
add_shortcode('foun_list', 'foun_list_shortcode');

function foun_list_rest($request)
{
    // The paging links are built from the current url, point them back at the page the list sits on
    $path = $request->get_param('path');
    if ($path)
    {
        $_SERVER['REQUEST_URI'] = '/' . ltrim($path, '/');
    }
    $html = foun_list_shortcode(array(
        'page' => $request->get_param('page') ? : '',
        'posts-per-page' => $request->get_param('posts-per-page') ? : '3',
        'pagination' => $request->get_param('pagination') ? : '0',
        'post-type' => $request->get_param('post-type') ? : 'event',
    ));
    return array(
        'html' => $html
    );
}

/* The same list over https, so js can fetch it without loading the whole page */
function foun_list_rest_route()
{
    register_rest_route('foun/v1', '/posts', array(
        'methods' => 'GET',
        'permission_callback' => '__return_true',
        'callback' => 'foun_list_rest',
    ));
}
add_action('rest_api_init', 'foun_list_rest_route');

