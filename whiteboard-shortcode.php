<?php function foun_whiteboard_shortcode()
{
    $whiteboard_posts = get_field('whiteboard_post', 'option');
    //var_dump($whiteboard_posts);
    $output = '';
    if (count($whiteboard_posts) > 0)
    {
        $output = '<div class="foun-whiteboard">';
        $compiled_whiteboard_posts = [];
        foreach ($whiteboard_posts as $post)
        {
            if (empty($compiled_whiteboard_posts[$post["whiteboard_post_area"]]))
            {
                $compiled_whiteboard_posts[$post["whiteboard_post_area"]] = '<div class="post-title">' . $post["whiteboard_post_area"] . '</div>';
            }
            if ($post["date_time"])
            {
                $compiled_whiteboard_posts[$post["whiteboard_post_area"]] .= '<div class="date-time">' . foun_date_time_string($post['date_time']) . '</div>';
            }
            if ($post["link"])
            {
                $compiled_whiteboard_posts[$post["whiteboard_post_area"]] .= '<div class="read-more">' . sprintf('<a href="%s" target="%s">%s</a>', $post['link']['url'], $post['link']['target'], __('Læs mere', 'foun-theme')) . '</div>';
            }
            $compiled_whiteboard_posts[$post["whiteboard_post_area"]] .= '<div class="post-header">' . $post["title"] . '</div>';
            $compiled_whiteboard_posts[$post["whiteboard_post_area"]] .= '<div class="post-description">' . $post["description"] . '</div>';
        }
        foreach ($compiled_whiteboard_posts as $post)
        {
            $output .= '<div class="post-main">' . $post . "</div>";
        } 
        //Old whiteboard 
        // foreach ($whiteboard_posts as $post){ 
        //     $output .= sprintf('<div class="whiteboard-post %s">', preg_replace('/\W+/','', strtolower(strip_tags($post['whiteboard_post_area'])))); 
        //     $output .= '<div class="post-main">'; 

        //     if($post['whiteboard_post_area_text']){ 
        //         $output .= '<div class="post-header">'; 
        //         $output .= sprintf('<span class="area">%s</span>', $post['whiteboard_post_area_text']); 
        //         $output .= '</div>'; 
        //     } else if(strlen($post['whiteboard_post_area']) > 0) { 
        //         if($post['whiteboard_post_area'] != 'Blank'){ 
        //             $output .= '<div class="post-header">'; 
        //             $output .= sprintf('<span class="area">%s</span>', $post['whiteboard_post_area']); 
        //             $output .= '</div>'; 
        //         } 
        //     } 
            
        //     $output .= '<div class="post-body">'; 
        //     $output .= sprintf('<h3>%s</h3>', $post['title']); 
            
        //     if(strlen(trim($post['description'])) > 0) {
        //         $output .= sprintf('<p>%s</p>', $post['description']); 
        //     }
        //     $output .= '</div>'; 
        //     $output .= '</div>'; 
            
        //     if ($post['date_time'] || $post['link']) { 
        //         $output .= '<div class="post-footer">'; 
        //         if ($post['date_time']) { 
        //             $output .= '<div class="date-time">'; 
        //             $output .= foun_date_time_string($post['date_time']); 
        //             $output .= '</div>'; 
        //         } 
                
        //         if ($post['link']) { 
        //             $output .= '<div class="read-more">'; 
        //             $output .= sprintf('<a href="%s" target="%s">%s</a>', $post['link']['url'], $post['link']['target'], __('Læs mere', 'foun-theme')); 
        //             $output .= '</div>'; 
        //         } 

        //         $output .= '</div>'; 
        //     } 
            
        //     $output .= '</div>'; 
        // } 
        $output .= '</div>'; 
    } 
    return $output; 
} 
add_shortcode('foun_whiteboard', 'foun_whiteboard_shortcode');