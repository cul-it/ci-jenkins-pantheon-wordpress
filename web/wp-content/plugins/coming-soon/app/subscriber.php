<?php

/*
 * subscribers Datatable
 */
function seedprod_lite_subscribers_datatable()
{
    if (check_ajax_referer('seedprod_nonce')) {
        $data = array('');
        $current_page = 1;
        if (!empty(absint($_GET['current_page']))) {
            $current_page = absint($_GET['current_page']);
        }
        $per_page=100;

        $filter = null;
        if (!empty($_GET['filter'])) {
            $filter = sanitize_text_field($_GET['filter']);
            if ($filter == 'all') {
                $filter = null;
            }
        }

        if (!empty($_GET['s'])) {
            $filter = null;
        }


        global $wpdb;
        $tablename = $wpdb->prefix . 'csp3_subscribers';

        // Get records

        $sql = "SELECT *
             FROM $tablename 
             ";
     
        if(!empty($_GET['id'])){
            $sql .= ' WHERE page_uuid = "'.esc_sql($_GET['id']). '"';;
        }else{
            $sql .= ' WHERE 1 =1 ';
        }
     
        if (!empty($_GET['s'])) {
            $sql .= ' AND email LIKE "%'. esc_sql(trim(sanitize_text_field($_GET['s']))).'%"';
        }
     
        if (! empty($_GET['orderby'])) {
            // $orderby = $_GET['orderby'];
            // if ($_GET['orderby'] == 'entries') {
            //     $orderby  = 'entries_count';
            // }
            // $sql .= ' ORDER BY ' . esc_sql(sanitize_text_field($orderby));
            // if(sanitize_text_field($_GET['order']) === 'desc'){
            //     $order = 'DESC';
            // }else{
            //     $order = 'ASC';
            // }
            // $sql .=  ' ' . $order;
        } else {
            $sql .= ' ORDER BY created DESC';
        }
     
        $sql .= " LIMIT $per_page";
        if (empty($_GET['s'])) {
            $sql .= ' OFFSET ' . ($current_page - 1) * $per_page;
        }
      
        $results = $wpdb->get_results($sql);
        //var_dump($results);
        $data = array();
        foreach ($results as $v) {
     
                // Format Date
        $created_at = date(get_option('date_format').' '.get_option('time_format'), strtotime($v->created));


            // Load Data
            $data[] = array(
                     'id' => $v->id,
                     'email' => $v->email,
                     'name' => $v->fname.' '.$v->lname,
                     'created_at' => $created_at,
                     'page_uuid' => $v->page_uuid,
                     );
        }

        $totalitems = seedprod_lite_subscribers_get_data_total($filter);
        $views = seedprod_lite_subscribers_get_views($filter);

        // Get recent subscriber data
        $chart_timeframe = 7;
        if(!empty($_GET['interval'])){
            $chart_timeframe = absint($_GET['interval']);
        }
        
        if (empty($_GET['id'])) {
            $tablename = $wpdb->prefix . 'csp3_subscribers';
            $sql = 'SELECT count(id) as count,DATE_FORMAT(created,"%Y-%m-%d") as created FROM '.$tablename.' ';
            $sql .= ' WHERE created >= DATE(NOW()) - INTERVAL '.esc_sql($chart_timeframe).' DAY GROUP BY DAY(created)';
            $recent_subscribers  = $wpdb->get_results($sql);


        } else {

            $tablename = $wpdb->prefix . 'csp3_subscribers';
            $sql = 'SELECT count(id) as count,DATE_FORMAT(created,"%Y-%m-%d") as created FROM '.$tablename.' ';
            $sql .= ' WHERE page_uuid = "'.esc_sql($_GET['id']). '"';
            $sql .= ' AND created >= DATE(NOW()) - INTERVAL '.esc_sql($chart_timeframe).' DAY GROUP BY DAY(created)';
            $recent_subscribers  = $wpdb->get_results($sql);
        }
        

        $now = new \DateTime("$chart_timeframe days ago", new \DateTimeZone('America/New_York'));
        $interval = new \DateInterval('P1D'); // 1 Day interval
        $period = new \DatePeriod($now, $interval, $chart_timeframe); // 7 Days

        $recent_subscribers_data = array(
            array("Year","Subscribers"),
        );
        foreach ($period as $day) {
            $key = $day->format('Y-m-d');
            $display_key = $day->format('M j');
            $no_val = true;
            foreach ($recent_subscribers as $v) {
                if ($key == $v->created) {
                    $recent_subscribers_data[] = array($display_key,absint($v->count));
                    $no_val = false;
                }
            }
            if ($no_val) {
                $recent_subscribers_data[] = array($display_key,0);
            }
        }
     
        $response = array(
                 'recent_subscribers' => $recent_subscribers_data,
                 'rows' => $data,
                 'lpage_name' => '',
                 'totalitems' => $totalitems,
                 'totalpages' => ceil($totalitems/$per_page),
                 'currentpage'=> $current_page,
                 'views'=>$views,
             );
     
        wp_send_json($response);
    }
}

function seedprod_lite_subscribers_get_data_total($filter = null)
{
    global $wpdb;

    $tablename = $wpdb->prefix . 'csp3_subscribers';

    $sql = "SELECT count(id) FROM $tablename";

    if(!empty($_GET['id'])){
        $sql .= ' WHERE page_uuid = '.esc_sql($_GET['id']);
    }else{
        $sql .= ' WHERE 1 =1 ';
    }

    if (!empty($_GET['s'])) {
        $sql .= ' AND email LIKE "%'. esc_sql(trim(sanitize_text_field($_GET['s']))).'%"';
    }

    $results = $wpdb->get_var($sql);
    return $results;
}

function seedprod_lite_subscribers_get_views($filter = null)
{
    $views = array();
    $current = (!empty($filter) ? $filter : 'all');

    global $wpdb;
    $tablename = $wpdb->prefix . 'csp3_subscribers';

    //All link
    $sql = "SELECT count(id) FROM $tablename";

    if(!empty($_GET['id'])){
        $sql .= ' WHERE lpage_id = '.esc_sql($_GET['id']);
    }else{
        $sql .= ' WHERE 1 =1 ';
    }

    $results = $wpdb->get_var($sql);
    $class = ($current == 'all' ? ' class="current"' :'');
    $all_url = remove_query_arg('filter');
    $views['all'] = $results;

    return $views;
}


/*
* Update Subscriber
*/
function seedprod_lite_update_subscriber_count()
{
    if (check_ajax_referer('seedprod_lite_update_subscriber_count')) {
        update_option('seedprod_subscriber_count', 1);
    } 

}


