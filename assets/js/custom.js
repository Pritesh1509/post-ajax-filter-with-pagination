jQuery(document).ready(function () {
    var chkboxs = []; // Initialize an empty array
    var currentPage = 1;
    function checkbox_change_func(){
        chkboxs = []; // Reset the array
        // Get all checked checkboxes and push their values to the array
        jQuery("input[name='chkbox']:checked").each(function () {
            chkboxs.push(jQuery(this).val());
        });
        jQuery.ajax({
            type: 'POST',
            url: ajax_object.ajax_url,
            data: {
                'action': 'get_datas',
                 chkboxs: chkboxs,
                 page: currentPage,
            },
            success: function (response) {
                jQuery('.posts-main').hide();
                jQuery('#ajax_resposne').html(response);
            }
        });
    }

    function onchage_post_ajax(){

        var post_ddp_val = jQuery('#post_ddp').val() ? jQuery('#post_ddp').val() : 6;
        chkboxs = []; // Reset the array
        // Get all checked checkboxes and push their values to the array
        jQuery("input[name='chkbox']:checked").each(function () {
            chkboxs.push(jQuery(this).val());
        });
        jQuery.ajax({
            type: 'POST',
            url: ajax_object.ajax_url,
            data: {
                'action': 'get_datas',
                 chkboxs: chkboxs,
                 post_ddp_val:post_ddp_val,
                 page: currentPage,
                 

            },
            success: function (response) {
                jQuery('.posts-main').hide();
                jQuery('#ajax_resposne').html(response);
            }
        });
    }

    jQuery('#post_ddp').change(function(){
        currentPage = 1;
        onchage_post_ajax();
    });

    jQuery("input[name='chkbox']").change(function () {
        currentPage = 1;
        checkbox_change_func();
    });

    jQuery(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        currentPage = jQuery(this).text();
        onchage_post_ajax();
        checkbox_change_func();
    });

    onchage_post_ajax();
    checkbox_change_func();

});
