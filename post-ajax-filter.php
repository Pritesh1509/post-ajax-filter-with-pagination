<?php 
/*
Plugin Name: Post Ajax Filter 
Description: custom plugin created for Product listing with pagination using ajax
Version: 1.0
Author: Pritesh Rajpura
*/

// Enqueue front-end styles and scripts
function my_custom_plugin_enqueue_scripts() {
    wp_enqueue_style('boostrap-style',plugin_dir_url(__FILE__) . '/assets/css/bootstrap.min.css',array(),'1.0', 'all');
    wp_enqueue_script('boostrap-js', plugin_dir_url(__FILE__) . '/assets/js/bootstrap.bundle.min.js',array('jquery'),'1.0',true  );
    wp_enqueue_script('custom-js', plugin_dir_url(__FILE__) . '/assets/js/custom.js',array('jquery'),'1.0',true );
    wp_localize_script('custom-js', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'my_custom_plugin_enqueue_scripts');


// Our custom post type function
function custom_post_type() {
    // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => _x( 'Products', 'Post Type General Name', 'twentytwentyone' ),
            'singular_name'       => _x( 'Product', 'Post Type Singular Name', 'twentytwentyone' ),
            'menu_name'           => __( 'Products', 'twentytwentyone' ),
            'parent_item_colon'   => __( 'Parent Product', 'twentytwentyone' ),
            'all_items'           => __( 'All product', 'twentytwentyone' ),
            'view_item'           => __( 'View Product', 'twentytwentyone' ),
            'add_new_item'        => __( 'Add New Product', 'twentytwentyone' ),
            'add_new'             => __( 'Add New', 'twentytwentyone' ),
            'edit_item'           => __( 'Edit Product', 'twentytwentyone' ),
            'update_item'         => __( 'Update Product', 'twentytwentyone' ),
            'search_items'        => __( 'Search Product', 'twentytwentyone' ),
            'not_found'           => __( 'Not Found', 'twentytwentyone' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'twentytwentyone' ),
        );
          
    // Set other options for Custom Post Type
          
        $args = array(
            'label'               => __( 'product', 'twentytwentyone' ),
            'description'         => __( 'Product', 'twentytwentyone' ),
            'labels'              => $labels,
            'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
            'taxonomies'          => array( 'genres' ),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 5,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'post',
            'show_in_rest' => true,
      
        );
        register_post_type( 'product', $args );
      
}    
add_action( 'init', 'custom_post_type', 0 );

function produc_taxonomy() {
    register_taxonomy(
        'product_category',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
        'product',             // post type name
        array(
            'hierarchical' => true,
            'label' => 'Product Category', // display name
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array(
                'with_front' => false  // Don't display the category base before
            )
        )
    );
}
add_action( 'init', 'produc_taxonomy');

//Ajax Callback Function 
add_action( 'wp_ajax_nopriv_get_datas', 'get_datas' );
add_action( 'wp_ajax_get_datas', 'get_datas' );
function get_datas() {
    $chkboxs = isset($_REQUEST['chkboxs']) ? $_REQUEST['chkboxs'] : array();
    $post_per_page = isset($_REQUEST['post_ddp_val']) ? $_REQUEST['post_ddp_val'] : 6 ;
    $paged = $_POST['page'] ?? 1;

    $tax_query = array();


    if (!empty($chkboxs)) {
    $tax_query[] = 
        array(
            'taxonomy'=>'product_category',
            'field'=>'slug',
            'terms'=>$chkboxs,
        );
    }

    $args = array(
        'post_type'=>'product',
        'post_status'=>'publish',
        'order'=>'ASC',
        'paged'=> $paged,
        'posts_per_page' => $post_per_page,
        'tax_query'=> $tax_query,
    );

    $query = new Wp_query($args); 
    if($query->have_posts()){ ?>
        <div class="ajax-posts-main">
            <div class="container pt-4">
                <div class="row">
                    <?php while($query->have_posts()){ $query->the_post(); ?>
                    <div class="col-md-4">
                        <div class="product-box">
                            <?php echo '<h2 class="text-center pt-2">'.get_the_title().'</h2>'; ?>
                            <?php echo '<p>'.get_the_excerpt().'</p>'; ?>
                        </div>
                    </div>
                    <?php } 
                    echo '<div class="pagination">';
                    echo paginate_links(array(
                        'total' => $query->max_num_pages,
                        'current' => $paged,
                        'prev_next' => false
                    ));
                    echo '</div>'; ?>
                </div>
            </div>
        </div>
    <?php 

    
} 
wp_reset_postdata();
    wp_die();  
}