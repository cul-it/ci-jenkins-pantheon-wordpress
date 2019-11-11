<?php
/**
 * Plugin Name: Ares
 * Description: Display Ares information in Wordpress.
 * Version: 1.0
 * Author: Adam Smith
 */

global $nonce;
$nonce = 'cu_ares';

global $cache_expires;
$cache_expires = 60*60;

global $libraries_url;
$libraries_url = 'http://api.library.cornell.edu/LibServices/showLocationInfo.do?output=json';

global $courses_url;
$courses_url = 'http://api.library.cornell.edu/LibServices/showCourseReserveList.do?output=json&library=';

global $reserves_url;
$reserves_url = 'http://api.library.cornell.edu/LibServices/showCourseReserveItemInfo.do?output=json&courseid=';


function cu_ares_enqueue() {
  global $nonce;
  wp_enqueue_script ('cu_ares_js', plugins_url('js/ares.js', __FILE__));
  wp_localize_script('cu_ares_js', 'my_ajax_obj', array(
   'ajax_url' => admin_url('admin-ajax.php'),
   'nonce'    => wp_create_nonce($nonce),
  ) );
  wp_enqueue_script ('cu_ares_tableshorter_js', plugins_url('js/jquery.tablesorter.min.js', __FILE__), array('jquery'));
  wp_enqueue_style('cu_ares_tablesorter_skin', plugins_url('table_skins/cu/ares-table-cu-style.css', __FILE__));
}
add_action( 'wp_enqueue_scripts', 'cu_ares_enqueue' );


// wraps calls to mann services to automatically cache data, handle encoding issues
function cu_ares_get_data($key, $url, $force_refresh=false) {
  if ($force_refresh) {
    wp_cache_delete($key);
  }

  $response = wp_cache_get($key);

  if ($response === false) {
    $response = wp_remote_get($url);
    if (is_wp_error($response)) {
      return '';
    }
    if (is_array($response)) {
      if (isset($response['body'])) {
        $response = $response['body'];
      }
    }
    // code offered by John Fereira to deal with encoding issues with Ares data
    $encoding =  mb_detect_encoding($response, "auto");
    $response = mb_convert_encoding($response, $encoding, "UTF-8");
    global $cache_expires;
    wp_cache_set($key, $response, '', $cache_expires);
  }
  return $response;
}

function output_json($json) {
  @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
  print $json;
  wp_die();
}

function cu_ares_get_courses() {
  //check_ajax_referer($nonce);
  $library = 'all';
  if (isset($_GET['library'])) {
    $library = $_GET['library'];
  }
  global $courses_url;
  $key = 'cu_ares_get_courses_' . $library;
  $url = $courses_url . $library;
  output_json(cu_ares_get_data($key, $url));
}
add_action('wp_ajax_cu_ares_get_courses', 'cu_ares_get_courses');
add_action('wp_ajax_nopriv_cu_ares_get_courses', 'cu_ares_get_courses');

/*
doing this directly in javascript instead
function cu_ares_get_course_summary() {
  //check_ajax_referer($nonce);
  $library = '';
  if (isset($_GET['library'])) {
    $library = $_GET['library'];
  }
  $course = '';
  if (isset($_GET['course'])) {
    $course = $_GET['course'];
  }

  $summary_key = 'cu_ares_course_summary_' . $course;
  $response = wp_cache_get($summary_key);
  if ($response === false) {
    global $courses_url;
    $key = 'cu_ares_get_courses_' . $library;
    $url = $courses_url . $library;
    $courses_json = cu_ares_get_data($key, $url);
    $courses = json_decode($courses_json)->{"courseList"};
    $course_titles = array();
    foreach ($courses as $acourse) {
      $course_title = '';
      if ($acourse->{"id"} == $course) {
        $course_title .= $acourse->{"courseNumber"} . ': ' . $acourse->{"courseName"};
        if (trim($acourse->{"classCode"}) != '') {
          $course_title .= ' (' . $acourse->{"classCode"} . ')';
        }
        if (trim($acourse->{"instructor"}) != '') {
          $course_title .= ' - ' . $acourse->{"instructor"};
        }
      }
      if ($course_title != '') {
        $course_titles[strtoupper(trim($acourse->{"courseNumber"})) . strtoupper(trim($acourse->{"classCode"}))] = $course_title;
      }
    }
    ksort($course_titles);
    $response = '{"value" : "' . join(" | ", $course_titles) . '"}';

    global $cache_expires;
    wp_cache_set($summary_key, $response, '', $cache_expires);
  }
  output_json($response);
}
add_action('wp_ajax_cu_ares_get_course_summary', 'cu_ares_get_course_summary');
add_action('wp_ajax_nopriv_cu_ares_get_course_summary', 'cu_ares_get_course_summary');
*/

function cu_ares_get_items() {
  //check_ajax_referer($nonce);
  $course = '';
  if (isset($_GET['course'])) {
    $course = $_GET['course'];
  }
  global $reserves_url;
  $key = 'cu_ares_get_items_' . $course;
  $url = $reserves_url . $course;
  output_json(cu_ares_get_data($key, $url));
}
add_action('wp_ajax_cu_ares_get_items', 'cu_ares_get_items');
add_action('wp_ajax_nopriv_cu_ares_get_items', 'cu_ares_get_items');


function cu_ares_output_course_select($options) {
  extract($options);
  $output = <<<HERE
<form action="/reserves" accept-charset="UTF-8" method="get"
      id="courselist-form-$library-$style" class="courselist-form">

    <div class="form-item edit-courselist-wrapper" id="edit-courselist-wrapper-$library-$style">
      <label for="edit-courselist-$library-$style">Select a course: </label>
      <div id="course-spinner-$library-$style" class="lds-ring"><span class="spinner-icon"></span> <span class="spinner-text"> Loading...</span></div>
      <input type="hidden" name="library" value="$library">
      <select name="course" class="form-select edit-courselist" id="edit-courselist-$library-$style" style="display: none">
      </select>
    </div>
</form>
HERE;
  return $output;

}


function cu_ares_output_course_items($options) {
  extract($options);
  $output = <<<HERE
    <div id="items-spinner-$library-$style" class="lds-ring" style="display: none">
      <span class="spinner-icon"></span> 
      <span  class="spinner-text"> Loading...</span>
    </div>
    <div id="reserve-items-$library-$style"
         class="reserve-items"
         style="display: none">

        <h3></h3>

        <table id="course-reserves-$library-$style" class="tablesorter standard course-reserves sticky-enabled" border="0" cellpadding="0" cellspacing="0">
          <caption>Delivery slots:</caption>
            <thead>
                <tr>
                    <th scope="col" class="item">Item</th>
                    <th scope="col" class="author">Author</th>
                    <th scope="col" class="callnumber">Call Number</th>
                    <th scope="col" class="status">Due Back</th>
                 </tr>
             </thead>
            <tbody>

            </tbody>
        </table>

    </div>
HERE;
    return $output;
}


function cu_ares_output_unobtrusive_js($options) {
  extract($options);
  $output = <<<HERE
    <script>
      //$(window).load(function () {
      jQuery(document).ready(function () {
        populate_course_selector("$library", "$course", "$style");
        jQuery("#course-reserves-$library-$style").tablesorter();
      });
      jQuery(document.body).trigger('post-load');
    </script>
HERE;
    return $output;
}

function cu_ares_output_interface($atts) {
  $options = shortcode_atts( array(
    'library'    => 'all',
    'course'     => NULL,
    'style'      => 'page',
    'hide_empty' => 'true'
  ), $atts );

  if (isset($_GET['library'])) {
    $options['library'] = $_GET['library'];
  }

  if (isset($_GET['course'])) {
    $options['course'] = $_GET['course'];
  }

  $options['spinner_src'] = plugins_url( 'img/loading.gif', __FILE__ );
  $output = cu_ares_output_course_select($options);
  $output .= cu_ares_output_course_items($options);
  $output .= cu_ares_output_unobtrusive_js($options);
  return $output;
}
add_shortcode('cu_ares_interface', 'cu_ares_output_interface');



// create the reserves page upon activating the plugin and cache all courses
function cu_ares_activation() {
  if (is_admin()) {
    $slug = 'reserves';
    $slug_exists = false;
	  global $wpdb;
	  if($wpdb->get_row("SELECT post_name FROM wp_posts WHERE post_name = '" . $slug . "'", 'ARRAY_A')) {
		  $slug_exists = true;
	  }

    $ares_page_title = 'Reserves';
    $ares_page_content = '[cu_ares_interface library="all" style="inline"]';
    $ares_page_check = get_page_by_title($ares_page_title);
    $ares_page = array ('post_type' => 'page',
                        'post_title' => $ares_page_title,
                        'post_content' => $ares_page_content,
                        'post_status' => 'publish',
                        'post_author' => 1,
                        'post_slug' => $slug
                       );
    if (! isset($ares_page_check->ID) && ! $slug_exists) {
      $ares_page_id = wp_insert_post($ares_page);
    }
  }
  // cache all library courses
  global $courses_url;
  cu_ares_get_data('cu_ares_get_courses_all', $courses_url . $library);
}
register_activation_hook(__FILE__, 'cu_ares_activation');



