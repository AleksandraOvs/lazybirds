<?php
wp_register_script ('js-ajax', get_stylesheet_directory_uri() . '/assets/js/ajax.js', array('jquery'), time(), true);
wp_localize_script('js-ajax', 'loadmore', array(
    'ajax_url' => admin_url('admin-ajax.php')
));
wp_enqueue_script('js-ajax');


add_action ('wp_ajax_loadmore', 'true_ajax');
add_action('wp_ajax_nopriv_loadmore', 'true_ajax');

function true_ajax(){

    echo 'test';
    $limit = 3; 
    $page = intval($_POST['page']);
    $page = (empty($page)) ? 1 : $page;	
    $start = ($page != 1) ? $page * $limit - $limit : 0;
    $page++;

    $args = array (
        'taxonomy' => 'product_cat',
       // 'posts_per_page' => 3,
        'hide_empty' => true,
        'parent' => 46
    );

    $product_categories = get_terms ($args);
    $total = count($product_categories);
    $amt = ceil ($total / 3);
    ?>
    <div class="row" style="display: flex; flex-wrap: wrap;">
    <?php
    
    foreach ($product_categories as $product_category){
        ?>
       
        <?php
      
        $image_id = get_term_meta( $product_category->term_id, 'thumbnail_id', true );
        //print_r ($image_id);
        ?>
        <a style="width: 32%;" class="card-collection" href="<?php echo get_term_link($product_category); ?>">
            <picture>
                <?= wp_get_attachment_image( $image_id, 'full' ); ?>
            </picture>
            <div class="btn-abs"><?php echo $product_category->name; ?></div>
        </a>
        <?php
    }
    ?>
    </div>
    <?php
    die;
}
?>