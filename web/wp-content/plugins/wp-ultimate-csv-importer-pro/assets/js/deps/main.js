var fileData = {};

jQuery(document).ready(function() {
    //selectpicker global jquery
    //jQuery('select').selectpicker();
    //tooltip
    // tippy('[data-toggle="tooltip"]');
    //Datepicker Jquery
    // jQuery('#from-date').datepicker({
    //     dateFormat: 'yy-mm-dd',
    // }).on('change', function(){
    //     $('.datepicker').hide();
    // });
    // jQuery('#to-date').datepicker({
    //     dateFormat: 'yy-mm-dd',
    // }).on('change', function(){
    //     $('.datepicker').hide();
    // });
    jQuery('input[data-type="date"]')
        .datepicker({
            dateFormat: 'dd/mm/yyyy'
        })
        .on('change', function() {
            $('.datepicker').hide();
        });

    //Icheck Jquery
    // jQuery(
    //     'input[type=radio]:not(".noicheck"), input[type=checkbox]:not(".noicheck,.ios-switch")'
    // ).iCheck({
    //     checkboxClass: 'icheckbox_square-green',
    //     radioClass: 'iradio_square-green',
    //     increaseArea: '20%' // optional
    // });

    // Setting Page Slide Menu jQuery
    jQuery('.setting-tab-list').click(function() {
        jQuery(this)
            .siblings()
            .removeClass('active');
        jQuery(this).addClass('active');
        var data = jQuery(this).data('setting');
        jQuery('.' + data)
            .siblings()
            .removeClass('active');
        jQuery('.' + data).addClass('active');
    });
    jQuery('.custom-fields-tab-list').click(function() {
        jQuery(this)
            .siblings()
            .removeClass('active');
        jQuery(this).addClass('active');
        var data = jQuery(this).data('tab');
        jQuery('.' + data)
            .siblings()
            .removeClass('active');
        jQuery('.' + data).addClass('active');
    });
    jQuery('.browse-btn').click(function() {
        alert('hai');
        jQuery('.drop_file').trigger('click');
    });

    // jQuery('.advanced-filter input[type="checkbox"]').on('change', function(){
    //     alert('');
    // });

    jQuery('.advanced-filter input[type="checkbox"]').on(
        'ifChecked',
        function() {
            jQuery(this)
                .parent()
                .parent()
                .siblings('.row')
                .slideDown();
        }
    );
    jQuery('.advanced-filter input[type="checkbox"]').on(
        'ifUnchecked',
        function() {
            jQuery(this)
                .parent()
                .parent()
                .siblings('.row')
                .slideUp();
        }
    );
    jQuery('.split-record').on('ifChecked', function() {
        jQuery(this)
            .parent()
            .parent()
            .siblings('input')
            .show();
    });
    jQuery('.split-record').on('ifUnchecked', function() {
        jQuery(this)
            .parent()
            .parent()
            .siblings('input')
            .hide();
    });

    jQuery('.custom-size input[type="checkbox"]')
        .on('ifChecked', function() {
            jQuery('.custom-image-sizes').slideDown();
        })
        .on('ifUnchecked', function() {
            jQuery('.custom-image-sizes').slideUp();
        });

    jQuery('.btn-add-size').on('click', function() {
        var clone_row = jQuery(
            'table.media-handle-image-size tbody tr#original-row'
        ).clone();
        jQuery(clone_row).removeAttr('id');
        jQuery(clone_row)
            .children()
            .children('.form-control')
            .removeAttr('value');
        jQuery(clone_row).appendTo('table.media-handle-image-size tbody');

        jQuery('table.media-handle-image-size tbody tr td.delete').on(
            'click',
            function() {
                var row_length = jQuery(
                    'table.media-handle-image-size tbody tr'
                ).length;
                if (row_length > 1) {
                    jQuery(this)
                        .parent()
                        .remove();
                } else {
                    return;
                }
            }
        );
    });

    jQuery('#media-handle').on('change', function() {
        if (jQuery(this).is(':checked')) {
            jQuery('.media-fields').addClass('active');
        } else {
            jQuery('.media-fields').removeClass('active');
        }
    });

    jQuery('.table-mapping .action-icon').on('click', function() {
        jQuery('.manipulation-screen').removeClass('active');
        jQuery(this)
            .children('.manipulation-screen')
            .addClass('active');
        // jQuery(this).children('.manipulation-screen').show();
    });

    jQuery('.manipulation-screen .close').on('click', function() {
        // console.log('clicked');
        jQuery(this)
            .parent()
            .removeClass('active');
        // console.log('here');
    });

    // open calender when click icon
    jQuery('.input-icon').on('click', function() {
        jQuery(this)
            .siblings('.form-control')
            .focus();
    });

    dragableDroppable();
});

// mapping accordon jQuery
function toggle_func(id) {
    jQuery('#' + id + '-body').slideToggle('slow');
    //jQuery('#icon'+id).toggleClass("icon-circle-down").toggleClass("icon-circle-up");
    jQuery('#' + id).toggleClass('bg-white active');
    jQuery('#' + id + ' span').toggleClass('active');
}

// Dragable JS  (Advance Mapping Page)

var dragableDroppable = function() {
    jQuery('.draggable').draggable({
        //revert: true,
        helper: 'clone',
        containment: 'document',
        helper: function() {
            return jQuery(this)
                .clone()
                .appendTo('body')
                .css({
                    zIndex: 5
                });
        },
        start: function(event, ui) {
            jQuery(this).fadeTo('fast', 0.5);
        },
        stop: function(event, ui) {
            jQuery(this).fadeTo(0, 1);
        }
    });
    jQuery('.droppable').droppable({
        hoverClass: 'active',
        drop: function(event, ui) {
            this.value += '{' + jQuery(ui.draggable).text() + '}';
        }
    });
};

function getFileTree(value) {
    var siteurl = window.location.origin + ajaxurl;
    var test = document.location;

    jQuery('#file_tree').fileTree(
        {
            root: '/',
            script: ajaxurl + '?action=get_server',
            expandSpeed: 750,
            collapseSpeed: 750,
            multiFolder: false
        },
        function(file) {
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'get_server',
                    dir: file
                },
                xhr: function() {
                    //upload Progress
                    var xhr = jQuery.ajaxSettings.xhr();
                    if (xhr.upload) {
                        xhr.upload.addEventListener(
                            'progress',
                            function(event) {
                                var percent = 0;
                                var position = event.loaded || event.position;
                                //var position = event.position;
                                var total = event.total;
                                if (event.lengthComputable) {
                                    percent = Math.ceil(
                                        (position / total) * 100
                                    );
                                }
                                //update progressbar
                                jQuery('#progress-div' + ' #progress-bar').css(
                                    'width',
                                    +percent + '%'
                                );
                            },
                            true
                        );
                    }
                    return xhr;
                },
                success: function(data) {
                    data = JSON.parse(data);
                    value(data);
                    jQuery('.jqueryfiletree').hide();
                }
            });
        }
    );
}

// function getFileTree() {
//     // var siteurl = document.getElementById('siteurl').value;
//     var siteurl = 'http://localhost/wordpress';

//      console.log(siteurl);
//     // return;
//     jQuery('#file_tree').fileTree({
//         root: '/',
//         script: siteurl + '/wp-admin/admin-ajax.php?action=get_server',
//         expandSpeed: 750,
//         collapseSpeed: 750,
//         multiFolder: false
//     }, function(file) {
//         var postdata = new Array({'external_file_url':file, 'import_method':'server_import'});
//         jQuery.ajax({
//             type: 'POST',
//             url: ajaxurl,
//             data: {
//                 'action': 'get_server',
//                 'postdata': postdata
//             },
//             xhr: function(){
//                 //upload Progress
//                 var xhr = jQuery.ajaxSettings.xhr();
//                 if (xhr.upload) {
//                     xhr.upload.addEventListener('progress', function(event) {
//                         var percent = 0;
//                         var position = event.loaded || event.position;
//                         //var position = event.position;
//                         var total = event.total;
//                         if (event.lengthComputable) {
//                             percent = Math.ceil(position / total * 100);
//                         }
//                         //update progressbar
//                         jQuery("#progress-div" + " #progress-bar").css("width", + percent +"%");
//                     }, true);
//                 }
//                 return xhr;
//             },
//             success: function (data) {
//                 data = JSON.parse(data);
//                 if(data['Success'] == 'Success!') {
//                     document.getElementById('file_name').value = '';
//                     document.getElementById('file_name').value = data['filename'];
//                     var get_file_extension = data['filename'].split('/');
//                     var get_file_name = get_file_extension[get_file_extension.length-1];
//                     var get_file = get_file_name.split('.');
//                     var fileextn = get_file[get_file.length-1];
//         if(data['isutf8'] == 'No'){
//                         document.getElementById('wp_notice').style.display = '';
//                         document.getElementById('wp_notice').innerHTML = '<p>Your csv file has invalid UTF-8 character. please check your csv</p>';
//                 }
//                     if(fileextn == 'zip'){
//                         jQuery.ajax({
//                             type: 'POST',
//                             url: ajaxurl,
//                             data: {
//                                 'action': 'upload_zipfile_handler',
//                                 'eventkey': data['eventkey'],
//                                 'import_method':'server'
//                             },
//                             success: function (data) {
//                                 data = JSON.parse(data);
//                                 document.getElementById('choose_file').innerHTML =data['data'];
//                                 jQuery('#modal_zip').modal('show');
//                             }
//                         });
//                     } else {
//                         document.getElementById('file_version').value=data['version'];
//                         document.getElementById('uploaded_name').value=data['filename'];
//                         document.getElementById('file_extension').value = data['extension'];
//                         var get_current_action = jQuery( '#form_import_file' ).attr( 'action' );
//                         document.getElementById('displaysection').style.display = "";
//                         document.getElementById('division4').style.display = "none";
//                         jQuery("#filenamedisplay").empty();
//                         jQuery('<label/>').text((data['filename']) + ' - ' + data['filesize']).appendTo('#filenamedisplay');
//                     }
//                     if(fileextn != 'zip'){
//                         jQuery.ajax({
//                             type: 'POST',
//                             url: ajaxurl,
//                             data: {
//                                 'action': 'set_post_types',
//                                 'filekey': data['eventkey'],
//                                 'uploadedname': data['filename']
//                             },
//                             success: function (result) {
//                                 var result = JSON.parse(result);
//                                 if(result != '') {
//                                     if(result['is_template'] == 'yes'){
//                                         var action = get_current_action + '&eventkey=' + data['eventkey'];
//                                     } else {
//                                         var splitaction = get_current_action.split("&");
//                                         var action = splitaction[0] + '&step=mapping_config&istemplate=no&eventkey=' + data['eventkey'];
//                                     }
//                                     jQuery('.selectpicker').selectpicker('val', result['type']);
//                                 } else {
//                                     var splitaction = get_current_action.split("&");
//                                     var action = splitaction[0] + '&step=mapping_config&istemplate=no&eventkey=' + data['eventkey'];
//                                 }
//                                 jQuery('#form_import_file').attr('action', action);
//                                 jQuery('.continue-btn').attr('disabled', false);
//                             }
//                         });
//                     }
//                     document.getElementById('server_dwn_file').disabled = false;
//                 } else {
//                     var warning = data['Failure'];
//                     notice_warning(warning);
//                     document.getElementById('upload_file').value="";
//                     return false;
//                 }
//             }
//         });
//     });
// }

// function getCloseEvent(){
//     jQuery(window).bind("beforeunload", function() {
//         return confirm("Do you really want to close?");
//     });
// }

// window.addEventListener("beforeunload", function (e) {
//     var confirmationMessage = "\o/";

//     (e || window.event).returnValue = confirmationMessage; //Gecko + IE
//     return confirmationMessage;                            //Webkit, Safari, Chrome
//   });
