function getCleanValue(value) {
  if (value == null) {
    value = "";
  }
  return value.replace(/^\s*|\s*jQuery/g,'');
}

function populate_course_selector(library, course, style) {
  var hideThis = "#course-spinner-" + library + "-" + style;
  var populateThis = "#edit-courselist-" + library + "-" + style;
  jQuery(populateThis).append('<option value="0" selected="selected">---- Select a course ----</option>');
  jQuery.getJSON(my_ajax_obj.ajax_url, {_ajax_nonce: my_ajax_obj.nonce,     // nonce
                                        action: "cu_ares_get_courses",      // action
                                        library: library,
                                  }, function(data) {
    if (data.courseList) {
      data.courseList.sort(sort_courses);
      jQuery.each(data.courseList, function(key, value) {
        var displayName = value.displayCourseNumber + ': ' + value.courseName;
        if (value.displayCourseNumber == value.courseName) {
          displayName = value.displayCourseNumber;
        }
        var classCode = getCleanValue(value.classCode);
        if (classCode != '') {
          displayName += ' (' + classCode + ')';
        }
        var instructor = getCleanValue(value.instructor);
        if (instructor != '') {
          displayName += ' - ' + instructor;
        }
        jQuery(populateThis).append(jQuery("<option></option>").attr("value", value.id).text(displayName));
      });

      jQuery(populateThis).change(function () {
        if (style == 'inline') {
          ajax_class_selection(library, style);
        } else {
          jQuery("#courselist-form-" + library + "-" + style).submit();
        }
      });

      // now the course selection control can be displayed
      jQuery(hideThis).attr('style', 'display: none');
      jQuery(populateThis).removeAttr('style');

      // trigger the control if a course has already been selected in another request
      if (course) {
        select_course(library, course, style);
        jQuery(populateThis).change();
      }

    } else {
      jQuery(hideThis).attr('style', 'display: none');
      populateThis.replaceWith("An error occured trying to display the list of courses.");
    }
  });
}

function sort_courses(a,b) {
  var astring = a.displayCourseNumber.toLowerCase().split(' ').join('') + getCleanValue(a.classCode).toLowerCase().split(' ').join('');
  var bstring = b.displayCourseNumber.toLowerCase().split(' ').join('') + getCleanValue(b.classCode).toLowerCase().split(' ').join('');
  if (astring < bstring) {return -1}
  if (astring > bstring) {return 1}
  return 0;
}

function select_course(library, course, style) {
  var selector = "#edit-courselist-" + library + "-" + style + " option";
  jQuery(selector + ":selected").removeAttr("selected");
  jQuery(selector).each( function() {
    if (jQuery(this).val() == course) {
        jQuery(this).attr('selected', 'selected');
        return false;
    }
  });
}

function ajax_class_selection(library, style) {
  var selector = "#edit-courselist-" + library + "-" + style;
  if (jQuery(selector + " option:selected").val() != '0') {

    // hide previous display
    var items = "#reserve-items-" + library + "-" + style;
    var spinner = "#items-spinner-" + library + "-" + style;
    var body = "#course-reserves-" + library + "-" + style + " tbody";

    jQuery(items).attr('style', 'display: none');
    jQuery(spinner).removeAttr('style');

    // get new display
    jQuery(selector + " option:selected").each(function () {
      var course = jQuery(this).val();
      jQuery(items + " h3").empty();

      // only need information already in select list, so do away with AJAX call
      jQuery(items + " h3").text(jQuery(selector + " option:selected").text());
      /*jQuery.getJSON(my_ajax_obj.ajax_url, {_ajax_nonce: my_ajax_obj.nonce,     // nonce
                                            action: "cu_ares_get_course_summary",      // action
                                            library: library,
                                            course: course,
                                            }, function(data) {
        jQuery(items + " h3").text(data["value"]);
      });*/

      jQuery.getJSON(my_ajax_obj.ajax_url, {_ajax_nonce: my_ajax_obj.nonce,     // nonce
                                            action: "cu_ares_get_items",      // action
                                            library: library,
                                            course: course,
                                            }, function(data) {
          jQuery(body).empty();
          jQuery.each(data.reserveItemList, function(i, reserve) {
            var odd_even = 'odd';
            ((i+1)%2) == 0  ? odd_even = 'even' : odd_even = 'odd';

            var output = '<tr class="' + odd_even + '">';

            // TITLE AND PAGES
            output += '   <td class="ares-title">';
            if (reserve.articleTitle != '' && reserve.articleTitle != '?') {
              output +=  '<p class="article-title">' + reserve.articleTitle + '</p>';
              output +=  '<p class="title">' + reserve.title + '</p>';
            } else {
              output +=  '<p class="title">' + reserve.title + '</p>';
            }
            if (reserve.pages != '' && reserve.pages != '?') {
              output +=  '<p class="pages">pp. ' + reserve.pages + '</p>';
            }
            output += '</td>';

            // AUTHOR
            output += '   <td class="ares-author"><p>' + reserve.author + '</p></td>';

            // BLACKBOARD LINK OR LIBRARY AND CALLNUMBER
            output += '   <td class="ares-location-complete">';
            if (reserve.status.toUpperCase().indexOf("ELECTRONIC") != -1) {
              output += '<p class="electronic">Electronic Access: <a href="https://canvas.cornell.edu/">Click here to find electronic reserve readings in Canvas</a></p>';
            } else {
              if (reserve.location != '' && reserve.location != '?') {
                output += '<p class="ares-location">' + reserve.location + '&nbsp;</p>';
              }
              output += '<p class="ares-callnumber">' + reserve.callnumber + '</p></td>';
            }
            output += '</td>';

            // DUE DATE
            if (reserve.status.toUpperCase().indexOf("ELECTRONIC") == -1) {
              output += '   <td class="ares-status"><span class="available">' + reserve.dueDate + '</span></td>';
            } else {
              output += '   <td class="ares-status"></td>';
            }

            output += '</tr>';
            jQuery(body).append(output);
          });

        jQuery(spinner).attr('style', 'display: none');
        jQuery(items).removeAttr('style');
        jQuery("#course-reserves-" + library + "-" + style).trigger("update");
      });
    });
  }
  jQuery(document.body).trigger('post-load');
}

