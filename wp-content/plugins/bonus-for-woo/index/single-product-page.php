<?php
/**
 * Текст под ценой (сколько бонусов получите)
 * @version 5.2.3
 */

$val = get_option('bonus_option_name');

/**
 * @param $url
 * @param $title
 * @param $description
 * @return string
 */
function bfw_social_in_single($url, $title, $description): string
{
    $val = get_option('bonus_option_name');
    $price_width_bonuses  = '<div class="bfw_social_links">';

    if(!empty($val['ref-social-vk'])){
        $price_width_bonuses .= '<a target="_blank" href="https://vk.com/share.php?url='.$url.'&title='.$title.'&description='.$description.'&noparse=true" class="bfw_social_link_item bfw_ref_icon_vk"></a>';
    }
    if(!empty($val['ref-social-fb'])){
        $price_width_bonuses .= '<a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u='.$url.'" class="bfw_social_link_item bfw_ref_icon_fb"></a>';
    }
    if(!empty($val['ref-social-tw'])){
        $price_width_bonuses .= '<a target="_blank" href="https://twitter.com/share?url='.$url.'" class="bfw_social_link_item bfw_ref_icon_tw"></a>';
    }
    if(!empty($val['ref-social-tg'])){
        $price_width_bonuses .= '<a class="bfw_social_link_item bfw_ref_icon_tg" target="_blank" href="https://telegram.me/share/url?url='.$url.'&text='.$title.' '.$description.'"></a>';
    }
    if(!empty($val['ref-social-whatsapp'])){
        $price_width_bonuses .= '<a class="bfw_social_link_item bfw_ref_icon_whatsapp" target="_blank" href="https://api.whatsapp.com/send?text='.urlencode( $url).'" data-action="share/whatsapp/share"></a>';
    }
    if(!empty($val['ref-social-viber'])){
        $price_width_bonuses .= '<a class="bfw_social_link_item bfw_ref_icon_viber" target="_blank" href="viber://forward?text='.$url.'"></a>';
    }
    if(!empty($val['ref-copy'])){
        $price_width_bonuses .= ' <span class="bfw_social_link_item copy_referal_single" data-link="'.$url.'" title="Копировать ссылку" ></span><span class="copy_good"></span>';
    }
    $price_width_bonuses .= '</div>';

    return $price_width_bonuses;
}

function BFWPointsInSinglePage($prize,$percent,$id,$price_width_bonuses,$upto,$product){
    $val = get_option('bonus_option_name');
    $ball =  (float)$prize/100*$percent;
    $ball =  (new BfwPoints())->roundPoints($ball);/*округляем если надо*/

    if (!empty($ball)) {

        if ((new BfwRoles)->is_pro() AND isset($val['exclude-tovar-cashback'])) {
            $exclude_tovar = $val['exclude-tovar-cashback'];
            $tovars = apply_filters('bfw-excluded-products-filter', explode(",", $exclude_tovar), $exclude_tovar);
        }else{$tovars= array();}

        $categoriexs = $val['exclude-category-cashback'] ?? 'not';

        if (in_array($id, $tovars) OR has_term($categoriexs, 'product_cat', $id)) {
            $price_width_bonuses .= '';
            if (!empty($val['addkeshback-exclude'])) {
                $price_width_bonuses .= '<div class="how_mach_bonus">' . sprintf(__('Cashback: %s %s %s', 'bonus-for-woo'), $upto, $ball, (new BfwPoints())->howLabel($upto,$ball)) . '</div>';
            }
        } else {
            $price_width_bonuses .= '<div class="how_mach_bonus">' . sprintf(__('Cashback: %s %s %s', 'bonus-for-woo'), $upto, $ball, (new BfwPoints())->howLabel($upto,$ball)) . '</div>';
        }

    }

    if((new BfwRoles)->is_pro() AND isset($val['ref-links-on-single-page']) AND (new BfwRoles)->is_invalve() AND is_user_logged_in()){

        if($val['ref-links-on-single-page']==1){
            $userid = get_current_user_id();
            //Вставка реферальных ссылок
            $get_referral = get_user_meta( $userid, 'bfw_points_referral', true );
            $url = esc_url(get_permalink( get_the_ID()).'?bfwkey='. $get_referral);
            $description = get_bloginfo('description');
            $title = $product->get_title();

            if(is_product() ){
                $refer =bfw_social_in_single($url,$title,$description);
                $price_width_bonuses .= $refer;

            }
        }
    }
    return $price_width_bonuses;
}
/*
 * На будущее
 * add_action( 'woocommerce_before_single_product_summary', 'wpbl_exmaple_hook', 20); до изображения товара
 * add_action( 'woocommerce_single_product_summary', 'wpbl_exmaple_hook', 5 ); над заголовком
 *add_action( 'woocommerce_single_product_summary', 'wpbl_exmaple_hook', 10 ); после заголовка
 * add_action( 'woocommerce_single_product_summary', 'wpbl_exmaple_hook', 20 );  после цены
 * add_action( 'woocommerce_product_meta_end', 'wpbl_exmaple_hook', 20 ); в мета товара(категории, метки, артикул)
 * function wpbl_exmaple_hook(){
    echo '<div class="wbpl_share">Кешбэк: 45 баллов</div>';
}
 *
 * */


/*Шорт код для вставки в любом месте, но только на странице продукта*/
add_shortcode('bfw_cashback_in_product', 'balls_after_product_price_shortcode', 100, 2);
/*Шорт код для вставки в любом месте, но только на странице продукта*/

add_filter( 'woocommerce_get_price_html', 'balls_after_product_price_all', 100, 2 );
/**
 * @param $price
 * @param $_product
 * @return string
 * @version 5.3.0
 */
function balls_after_product_price_all($price, $_product ): string
{
    global $post;
    global $product;
    $upto = '';
    $val = get_option('bonus_option_name');
    $price_width_bonuses = '';
    $id = $post->ID;/*найти id товара */
    $type = $_product->get_type();
    /*узнаем $prize - сумму товара с которой будем считать кешбэк*/
    if ($type =='simple') {
        if($_product->get_sale_price()){
            // 'простой товар c акцией';
            $prize = $_product->get_sale_price();
        }else{
            // 'простой товар';
            $prize = $_product->get_price();
        }
    } elseif ($type == 'variable') {
        // 'вариативный товар';
        $prize = $_product->get_variation_sale_price('max', true);

        if($_product->get_variation_sale_price('max', true)!=$_product->get_variation_sale_price('min', true)){
           $upto = __('up to', 'bonus-for-woo');
        }

    } else {
        $prize = 0;
    }



    if (is_user_logged_in()) {
        $userid = get_current_user_id();//  id пользователя
        $getRole = (new BfwRoles)->getRole($userid);
        $percent = $getRole['percent'];
            if($percent==0 AND (new BfwRoles)->is_invalve($userid)){
                $percent = (new BfwRoles)->maxPercent();
                $upto = __('up to', 'bonus-for-woo');

            }
    }else{
        //Если не зарегистрирован, то максимальны кешбэк до
        $percent = (new BfwRoles)->maxPercent();
       if(empty($val['bonus-in-price-upto'])){
           $upto = __('up to', 'bonus-for-woo');
       }else{
           $upto = '';
       }
    }

    $price_width_bonuses =  BFWPointsInSinglePage($prize,$percent,$id,$price_width_bonuses,$upto,$_product);



    if (!empty($val['bonus-in-price-loop'])) {
        // Отображать на других страницах, всех кроме страницы товара
        if(!is_product()){
            $price .= $price_width_bonuses;
        }
    }

    if (!empty($val['bonus-in-price'])) {
        if(is_product()){
            $price .= $price_width_bonuses;
        }
    }

     return $price;
}


/**
 * @param $price
 * @param $product
 * @return string|void
 * @version 5.2.3
 */
function balls_after_product_price_shortcode($price, $product ){
    if(!is_admin()){ /*Чтоб работало со всякими сборщиками, типа элементора*/
        global $post;
        global $product;

        $upto = '';
        $val = get_option('bonus_option_name');
        $price_width_bonuses = '';
        $id = $post->ID;/*найти id товара */
        $type = $product->get_type();
        /*узнаем $prize - сумму товара с которой будем считать кешбэк*/
        if ($type =='simple') {
            if($product->get_sale_price()){
                // 'Простой товар c акцией';
                $prize = $product->get_sale_price();
            }else{
                // 'Простой товар';
                $prize = $product->get_price();
            }
        } elseif ($type == 'variable') {
            // 'Вариативный товар';
            $prize = $product->get_variation_sale_price('max', true);

            if($product->get_variation_sale_price('max', true)!=$product->get_variation_sale_price('min', true)){
                if(empty($val['bonus-in-price-upto'])){
                    $upto = __('up to', 'bonus-for-woo');
                }
            }

        } else {
            $prize = 0;
        }



        if (is_user_logged_in()) {

            $userid = get_current_user_id();//  id пользователя
            $getRole = (new BfwRoles)->getRole($userid);
            $percent = $getRole['percent'];
            if($percent==0 AND (new BfwRoles)->is_invalve($userid)){
                $percent = (new BfwRoles)->maxPercent();
                if(empty($val['bonus-in-price-upto'])){
                    $upto = __('up to', 'bonus-for-woo');
                }else{
                    $upto = '';
                }
            }
        }else{
            //Если не зарегистрирован, то максимальны кешбэк до
            $percent = (new BfwRoles)->maxPercent();

            if(empty($val['bonus-in-price-upto'])){
                $upto = __('up to', 'bonus-for-woo');
            }else{
                $upto = '';
            }
        }


        return  BFWPointsInSinglePage($prize,$percent,$id,$price_width_bonuses,$upto,$product);
}
}