function houzez_breadcrumbs($options = array())
    {
        global $post;
        $allowed_html_array = array(
            'i' => array(
                'class' => array()
            )
        );
        $text['home']     = esc_html__('Home', 'houzez'); // text for the 'Home' link
        $text['category'] = esc_html__('%s', 'houzez'); // text for a category page
        $text['tax']      = esc_html__('%s', 'houzez'); // text for a taxonomy page
        $text['search']   = esc_html__('Search Results for "%s" Query', 'houzez'); // text for a search results page
        $text['tag']      = esc_html__('%s', 'houzez'); // text for a tag page
        $text['author']   = esc_html__('%s', 'houzez'); // text for an author page
        $text['404']      = esc_html__('Error 404', 'houzez'); // text for the 404 page
        $defaults = array(
            'show_current' => 1, // 1 - show current post/page title in breadcrumbs, 0 - don't show
            'show_on_home' => 0, // 1 - show breadcrumbs on the homepage, 0 - don't show
            'delimiter' => '',
            'before' => '<li class="active">',
            'after' => '</li>',
            'home_before' => '',
            'home_after' => '',
            'home_link' => home_url() . '/',
            'link_before' => '<li itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb">',
            'link_after'  => '</li>',
            'link_attr'   => '',
            'link_in_before' => '',
            'link_in_after'  => ''
        );
        extract($defaults);
        $link = '<a itemprop="url" href="%1$s"><span itemprop="title">' . $link_in_before . '%2$s' . $link_in_after . '</span></a>';
        // form whole link option
        $link = $link_before . $link . $link_after;
        if (isset($options['text'])) {
            $options['text'] = array_merge($text, (array) $options['text']);
        }
        // override defaults
        extract($options);
        // regex replacement
        $replace = $link_before . '<a' .="" esc_attr(="" $link_attr="" )="" '\\1="">' . $link_in_before . '\\2' . $link_in_after . '' . $link_after;
        /*
         * Use bbPress's breadcrumbs when available
         */
        if (function_exists('bbp_breadcrumb') && is_bbpress()) {
            $bbp_crumbs =
                bbp_get_breadcrumb(array(
                    'home_text' => $text['home'],
                    'sep' => '',
                    'sep_before' => '',
                    'sep_after'  => '',
                    'pad_sep' => 0,
                    'before' => $home_before,
                    'after' => $home_after,
                    'current_before' => $before,
                    'current_after'  => $after,
                ));
            if ($bbp_crumbs) {
                echo '<ul class="breadcrumb favethemes_bbpress_breadcrumb">' .$bbp_crumbs. '</ul>';
                return;
            }
        }
        // normal breadcrumbs
        if ((is_home() || is_front_page())) {
            if ($show_on_home == 1) {
                echo '<li>'. esc_attr( $home_before ) . '<a href="' . esc_url( $home_link ) . '">' . esc_attr( $text['home'] ) . '</a>'. esc_attr( $home_after ) .'</li>';
            }
        } else {
            echo '<ol class="breadcrumb">' .$home_before . sprintf($link, $home_link, $text['home']) . $home_after . $delimiter;
            if (is_category() || is_tax())
            {
                $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
                if( $term ) {
                    $taxonomy_object = get_taxonomy( get_query_var( 'taxonomy' ) );
                    //echo '<li><a>'.$taxonomy_object->rewrite['slug'].'</a></li>';
                    $parent = $term->parent;
                    while ($parent):
                        $parents[] = $parent;
                        $new_parent = get_term_by( 'id', $parent, get_query_var( 'taxonomy' ));
                        $parent = $new_parent->parent;
                    endwhile;
                    if(!empty($parents)):
                        $parents = array_reverse($parents);
                        // For each parent, create a breadcrumb item
                        foreach ($parents as $parent):
                            $item = get_term_by( 'id', $parent, get_query_var( 'taxonomy' ));
                            $term_link = get_term_link( $item );
                            if ( is_wp_error( $term_link ) ) {
                                continue;
                            }
                            echo '<li><a href="'.$term_link.'">'.$item->name.'</a></li>';
                        endforeach;
                    endif;
                    // Display the current term in the breadcrumb
                    echo '<li>'.$term->name.'</li>';
                } else {
                    $the_cat = get_category(get_query_var('cat'), false);
                    // have parents?
                    if ($the_cat->parent != 0) {
                        $cats = get_category_parents($the_cat->parent, true, $delimiter);
                        $cats = preg_replace('#<a([^>]+)>([^<]+)#', $replace, $cats);
                        echo $cats;
                    }
                    // print category
                    echo $before . sprintf((is_category() ? $text['category'] : $text['tax']), single_cat_title('', false)) . $after;
                } // end terms else
            }
            else if (is_search()) {
                echo $before . sprintf($text['search'], get_search_query()) . $after;
            }
            else if (is_day()) {
                echo  sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter
                    . sprintf($link, get_month_link(get_the_time('Y'),get_the_time('m')), get_the_time('F')) . $delimiter
                    . $before . get_the_time('d') . $after;
            }
            else if (is_month()) {
                echo  sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter
                    . $before . get_the_time('F') . $after;
            }
            else if (is_year()) {
                echo $before . get_the_time('Y') . $after;
            }
            // single post or page
            else if (is_single() && !is_attachment()) {
                // custom post type
                if (get_post_type() != 'post' && get_post_type() != 'property' ) {
                    $post_type = get_post_type_object(get_post_type());
                    //printf($link, get_post_type_archive_link(get_post_type()), $post_type->labels->name);
                    if ($show_current == 1) {
                        echo esc_attr($delimiter) . $before . get_the_title() . $after;
                    }
                }
                elseif( get_post_type() == 'property' ){
                    $terms = get_the_terms( get_the_ID(), 'property_city' );
                    if( !empty($terms) ) {
                        foreach ($terms as $term) {
                            $term_link = get_term_link($term);
                            // If there was an error, continue to the next term.
                            if (is_wp_error($term_link)) {
                                continue;
                            }
                            echo '<li itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb"><a itemprop="url" href="' . esc_url($term_link) . '"> <span itemprop="title">' . esc_attr( $term->name ). '</span></a></li>';
                        }
                    }
                    if ($show_current == 1) {
                        echo esc_attr($delimiter) . $before . get_the_title() . $after;
                    }
                }
                else {
                    $cat = get_the_category();
                    $cats = get_category_parents($cat[0], true, esc_attr($delimiter));
                    if ($show_current == 0) {
                        $cats = preg_replace("#^(.+)esc_attr($delimiter)$#", "$1", $cats);
                    }
                    $cats = preg_replace('#<a([^>]+)>([^<]+)#', $replace, $cats);
                    echo $cats;
                    if ($show_current == 1) {
                        echo $before . get_the_title() . $after;
                    }
                } // end else
            }
            elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404() && !is_author() ) {
                $post_type = get_post_type_object(get_post_type());
                echo $before . $post_type->labels->name . $after;
            }
            elseif (is_attachment()) {
                $parent = get_post($post->post_parent);
                $cat = current(get_the_category($parent->ID));
                $cats = get_category_parents($cat, true, esc_attr($delimiter));
                if (!is_wp_error($cats)) {
                    $cats = preg_replace('#<a([^>]+)>([^<]+)#', $replace, $cats);
                    echo $cats;
                }
                printf($link, get_permalink($parent), $parent->post_title);
                if ($show_current == 1) {
                    echo esc_attr($delimiter) . $before . get_the_title() . $after;
                }
            }
            elseif (is_page() && !$post->post_parent && $show_current == 1) {
                echo $before . get_the_title() . $after;
            }
            elseif (is_page() && $post->post_parent) {
                $parent_id  = $post->post_parent;
                $breadcrumbs = array();
                while ($parent_id) {
                    $page = get_post($parent_id);
                    $breadcrumbs[] = sprintf($link, get_permalink($page->ID), get_the_title($page->ID));
                    $parent_id  = $page->post_parent;
                }
                $breadcrumbs = array_reverse($breadcrumbs);
                for ($i = 0; $i < count($breadcrumbs); $i++) {
                    echo ( $breadcrumbs[$i] );
                    if ($i != count($breadcrumbs)-1) {
                        echo esc_attr($delimiter);
                    }
                }
                if ($show_current == 1) {
                    echo esc_attr($delimiter) . $before . get_the_title() . $after;
                }
            }
            elseif (is_tag()) {
                echo $before . sprintf($text['tag'], single_tag_title('', false)) . $after;
            }
            elseif (is_author()) {
                global $author;
                $userdata = get_userdata($author);
                echo $before . sprintf($text['author'], $userdata->display_name) . $after;
            }
            elseif (is_404()) {
                echo $before . esc_attr( $text['404'] ). $after;
            }
            // have pages?
            if (get_query_var('paged')) {
                if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author()) {
                    echo ' (' . esc_html__('Page', 'houzez') . ' ' . get_query_var('paged') . ')';
                }
            }
            echo '</a([^></a([^></a([^></ol>';
        }
    }</a'>
