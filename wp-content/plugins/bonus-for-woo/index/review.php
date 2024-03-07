<?php

/*Добавление бонусов когда добавлен отзыв о товаре*/
$bonus_option_name = get_option('bonus_option_name');

if (!empty($bonus_option_name['bonus-for-otziv'])) {
          update_option('comment_moderation', '1');/*баллы утверждаются вручную*/
}

//Если одобрен добавляет баллы
add_action('comment_unapproved_to_approved', 'bfwoo_approve_comment_callback');

//Если отклонен удаляет баллы
add_action('comment_approved_to_unapproved', 'bfwoo_unapproved_comment_callback');


/**
 * Если одобрен отзыв
 * @param $comment
 */
function bbloomer_paid_is_paid_status( $statuses ) {
    $val = get_option('bonus_option_name');
    $order_staus = $val['add_points_order_status'] ?? 'completed';

    $statuses[] = $order_staus;
    return $statuses;
}
function bfwoo_approve_comment_callback($comment): void
{
    $val = get_option('bonus_option_name');

    if (!empty($val['bonus-for-otziv'])) {
        $bonusfor_otziv_new = $val['bonus-for-otziv'];
        $computy_user_point = (new BfwPoints)->getPoints($comment->user_id) + $bonusfor_otziv_new;

        if (get_post_type($comment->comment_post_ID) == 'product') {

            add_filter( 'woocommerce_order_is_paid_statuses', 'bbloomer_paid_is_paid_status' );

            if ( wc_customer_bought_product( $comment->comment_author_email,$comment->user_id,$comment->comment_post_ID ) ) {
               // вы уже покупали этот товар ранее.

                global $wpdb;
                $count_comment = $wpdb->get_var('SELECT COUNT(comment_ID) FROM ' . $wpdb->prefix . 'comments WHERE user_id = "' . $comment->user_id . '" AND comment_post_ID = "' . $comment->comment_post_ID . '" AND comment_approved ="1"');
                if ($count_comment == 1) {/*если количество отзывов у этого товара у этого клиента 0, то добавим баллы*/
                    (new BfwPoints)->updatePoints($comment->user_id,$computy_user_point);//добавляем баллы клиенту
                    $cause = __('For review', 'bonus-for-woo');
                    /*В историю*/
                    (new BfwHistory)->add_history($comment->user_id,'+',$bonusfor_otziv_new,'0',$cause);

                    /*email*/
                    $val = get_option( 'bonus_option_name' );

                    /*шаблонизатор письма*/

                    $text_email = $val['email-when-review-text'] ?? '';
                    $title_email = $val['email-when-review-title'] ?? __('Points accrual', 'bonus-for-woo');
                    $user = get_userdata($comment->user_id);
                    $get_referral = get_user_meta( $comment->user_id, 'bfw_points_referral', true );
                    $text_email_array = array('[referral-link]'=> esc_url(site_url().'?bfwkey='. $get_referral),'[user]'=>$user->display_name,'[cause]'=>$cause,'[points]'=>$bonusfor_otziv_new,'[total]'=>$computy_user_point);
                    $message_email = (new BfwEmail)->template($text_email,$text_email_array);
                    /*шаблонизатор письма*/


                    if(!empty($val['email-when-review'])){
                            (new BfwEmail)->getMail($comment->user_id, '', $title_email, $message_email);
                        }
                    /*email*/
                }
            }

        }
    }

}


/**
 * Если одобренный отзыв отклонен, удаляет баллы
 *
 * @param $comment
 */
function bfwoo_unapproved_comment_callback($comment): void
{
    $val = get_option('bonus_option_name');
    $bonusfor_otziv_new = $val['bonus-for-otziv'];
    $computy_user_point = (new BfwPoints)->getPoints($comment->user_id) - $bonusfor_otziv_new;
    if (get_post_type($comment->comment_post_ID) == 'product') {

        global $wpdb;
        $count_comment = $wpdb->get_var('SELECT COUNT(comment_ID) FROM ' . $wpdb->prefix . 'comments WHERE user_id = "' . $comment->user_id . '" AND comment_post_ID = "' . $comment->comment_post_ID . '" AND comment_approved ="1"');
        if ($count_comment == 0) {/*Если количество одобренных у этого товара у этого клиента 1, то удаляем баллы*/

            (new BfwPoints)->updatePoints($comment->user_id,$computy_user_point);//Удаляем баллы клиенту
            $cause = sprintf(__('Return of %s for Product Review', 'bonus-for-woo'), (new BfwPoints())->pointsLabel(5));
            /*В историю*/
            (new BfwHistory)->add_history($comment->user_id,'-',$bonusfor_otziv_new,'0',$cause);

            /*email*/
            $title_email = sprintf(__('Return of %s', 'bonus-for-woo'), (new BfwPoints())->pointsLabel(5));
            $info_email = sprintf(__('Your product review has been rejected. %s %s are canceled.', 'bonus-for-woo'), $bonusfor_otziv_new, (new BfwPoints())->pointsLabel(5));
            $message_email='<p>'.$info_email.'</p>';
            $message_email.='<p>'.__('Cause', 'bonus-for-woo').': '.$cause.'</p>';
            $message_email.='<p>'.sprintf(__('The sum of your %s is now', 'bonus-for-woo'), (new BfwPoints())->pointsLabel(5)).': <b>'.$computy_user_point.' '.(new BfwPoints())->pointsLabel($computy_user_point).'</b></p>';
            $val = get_option( 'bonus_option_name' );

            if(!empty($val['email-when-review'])){
                    (new BfwEmail)->getMail($comment->user_id, '', $title_email, $message_email);
                }
            /*email*/
        }
    }
}