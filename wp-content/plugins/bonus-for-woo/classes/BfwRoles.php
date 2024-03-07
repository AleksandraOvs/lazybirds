<?php
/**
 * Класс ролей(статусов)
 *
 * Class Roles
 *
 * @version 2.5.1
 */

class BfwRoles
{

    /**
     * Вывод роли (new BfwRoles)->getRole($customer_user)['percent']
     *
     * @param int $userId
     * @return array
     * @version 3.4.1
     */

    public static function getRole(int $userId): array
    {

       // self::updateRole($userId);

        $val = get_option('bonus_option_name');
        $exclude_role = $val['exclude-role'] ?? '';
        global $wpdb;

        $user_data = get_userdata($userId);
        $roles = $roles ?? $user_data->roles ?? [];

        $status_id = get_user_meta($userId, 'bfw_status', true);
        $role = array_shift($roles);/*роль пользователя*/

if ($role == 'administrator' || in_array( $role , (array)$exclude_role)) {
            global $wp_roles;
            $roles = array();
            $user = new WP_User( $userId );
            if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
                foreach ( $user->roles as $role )
                    $return['name'] = translate_user_role($wp_roles->roles[$role]['name']);
            }
             $return['percent'] = '0';
             $return['slug'] =$role;
        }
        else {
            $you_role = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "bfw_computy WHERE id='$status_id'");
            $return['name'] = $you_role[0]->name ?? __('Client', 'bonus-for-woo');
            $return['percent'] = $you_role[0]->percent ?? 0;
            $return['slug'] = $you_role[0]->slug ?? 'client';
        }

        return $return;
    }


    /**
     * Метод обновления роли
     * (только для участников бонусной программы(клиентов и подписчиков))
     *
     * @param int $userId
     * @version 2.5.6
     */
    public static function updateRole(int $userId): void
    {

        $val = get_option('bonus_option_name');
        $exclude_role = $val['exclude-role'] ?? '';
        global $wpdb;
        $user_data = get_userdata($userId);

        $roles = $roles ?? $user_data->roles ?? [];
        $role = array_shift($roles);/*роль пользователя*/

        if($role != 'administrator' AND !in_array( $role , (array)$exclude_role)){

            /*сумма всех покупок клиента за все время*/
            $total_all = (new BfwPoints)->getSumUserOrders($userId);

            $allrole = $wpdb->get_results("SELECT summa_start FROM " . $wpdb->prefix . "bfw_computy");
            if(!empty($allrole)){
            $allrole = json_decode(json_encode($allrole), true);/* преобразование в ассоциативный массив*/
            $allrole = array_multisort_value($allrole, 'summa_start', SORT_ASC);

            foreach ($allrole as $bfw) {

                if ($total_all >= $bfw['summa_start']) {
                    $summa = $bfw['summa_start']; //находим первую сумму системы, которая больше суммы всех заказов
                    //break;
                }
            }

                if(isset($summa)){
                    $you_role = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "bfw_computy WHERE summa_start='$summa'");
                    $status_id = get_user_meta($userId, 'bfw_status', true);
                    if ($status_id != $you_role[0]->id) {
                       // $wp_update = wp_update_user(['ID' => $userId, 'role' => $you_role[0]->slug]);
                        $wp_update = update_user_meta($userId, 'bfw_status', $you_role[0]->id);
                        if (!is_wp_error($wp_update)){

                            $val = get_option( 'bonus_option_name' );
                            if(!empty($val['email-when-status-chenge'])) {
                                /*email*/
                                /*шаблонизатор письма*/

                                    $text_email = $val['email-when-status-chenge-text'] ?? '';

                                $title_email = $val['email-when-status-chenge-title'] ?? __('Changing your status', 'bonus-for-woo');
                                $user = get_userdata($userId);
                                $get_referral  = get_user_meta( $userId, 'bfw_points_referral', true );

                                $text_email_array = array('[referral-link]'=> esc_url(site_url().'?bfwkey='. $get_referral),'[user]'=>$user->display_name,'[role]'=>$you_role[0]->name,'[cashback]'=>$you_role[0]->percent);
                                $message_email = (new BfwEmail)->template($text_email,$text_email_array);
                                /*шаблонизатор письма*/

                                (new BfwEmail)->getMail($userId, '', $title_email, $message_email);
                                /*email*/
                            }


                        }
                    }
                }
                else {
                    if($total_all<=$allrole[0]['summa_start'] AND $role!='customer'){
                        //если сумма заказов меньше самого низкого статуса, то понижаем до клиента
                        //wp_update_user(['ID' => $userId, 'role' => 'customer']);
                        if(!empty($val['email-when-status-chenge'])) {
                            /*email*/
                            /*шаблонизатор письма*/

                                $text_email = $val['email-when-status-chenge-text'] ?? '';

                            $title_email = $val['email-when-status-chenge-title'] ?? __('Changing your status', 'bonus-for-woo');
                            $user = get_userdata($userId);
                            $get_referral = get_user_meta( $userId, 'bfw_points_referral', true );
                            $text_email_array = array('[referral-link]'=> esc_url(site_url().'?bfwkey='. $get_referral),'[user]'=>$user->display_name,'[role]'=>__('Client','bonus-for-woo'),'[cashback]'=>0);
                            $message_email = (new BfwEmail)->template($text_email,$text_email_array);
                            /*шаблонизатор письма*/

                            (new BfwEmail)->getMail($userId, '', $title_email, $message_email);
                            /*email*/
                        }
                    }
                }
            }
        }

    }


    /**
     * Сколько надо потратить денег до следующей роли сумма, роль
     *
     * @param int $userId
     * @return array
     * @version 2.5.6
     */
    public static function getNextRole(int $userId): array
    {

        $val = get_option('bonus_option_name');
        $exclude_role = $val['exclude-role'] ?? '';
        global $wpdb;
        $user_data = get_userdata($userId);
        $roles = $roles ?? $user_data->roles ?? [];
        $role = array_shift($roles);/*роль пользователя*/

        if($role == 'administrator' || in_array( $role , (array)$exclude_role)){
            $return['status'] ='admin';
        }else{
            if($wpdb->get_var("SELECT COUNT(*) FROM " . $wpdb->prefix . "bfw_computy")>0) {

                $table_bfw = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "bfw_computy");

                $table_bfw = json_decode(json_encode($table_bfw), true);/* преобразование в ассоциативный массив*/

                $table_bfw = array_multisort_value($table_bfw, 'summa_start', SORT_ASC);


                /*сумма всех покупок клиента за все время*/
                $total_all = (new BfwPoints)->getSumUserOrders($userId);
                if($total_all==0){$total_all=0.01;}
                $summa = 0; $next_status = '';

                foreach ($table_bfw as $bfw) {

                    if ($total_all < $bfw['summa_start']) {
                        $next_cash = $bfw['percent'];
                        $next_status = $bfw['name'];
                        $summa = $bfw['summa_start'];
                        break;//найдена следующая для увеличения кешбэка сумма
                    }
                }

                if (isset($next_cash)) {
                   if($total_all==0.01){$total_all=0;}
                    (float)$ostatok = $summa - $total_all;
                    $return['percent-zarabotannogo'] = 100*$total_all/$summa;
                    $return['sum'] = $ostatok;
                    $return['name'] = $next_status;
                    $return['percent'] = $next_cash;
                    $return['status'] ='next';

                } else {
                    $return['status'] ='max';
                }

            }else{
                /*когда не создано ни одного статуса*/
                $return['status'] ='no';
            }
        }

        return $return;
}


    /**
     * Проверка участвует ли пользователь в бонусной системе
     *
     * @param int|null $userId
     * @return bool
     * @version 3.1.0
     */
    public static function is_invalve(int $userId = null): bool{
        if(empty($userId)){
            $userId = get_current_user_id();
        }
        $role = (new BfwRoles)->getRole($userId)['slug'];//  роль пользователя

        $val = get_option('bonus_option_name');
        $exclude_role = $val['exclude-role'] ?? '';

        if($role == 'administrator' || in_array($role, (array)$exclude_role)){
            return FALSE;
        }else{
            return TRUE;
        }
    }


    /**
     * Проверка на про
     *
     *
     * @return bool
     * @version 4.8.2
     */
    public static function is_pro(): bool
    {
        $pro = get_option( 'bonus-for-woo-pro' );
        if($pro == 'active'){
            return true;
        }else{
            return false;
        }

    }



    /**
     * Находим максимальный процент статусов
     *
     *
     * @return float
     * @version 5.0.1
     */
    public static function maxPercent(): float{
        global $wpdb;
        $table_bfw = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "bfw_computy");
        $table_bfw = json_decode(json_encode($table_bfw), true);/* преобразование в ассоциативный массив*/

        if(!empty(array_column($table_bfw, 'percent'))){
            $maximalpercent = max(array_column($table_bfw, 'percent'));/*находим максимальный процент*/
        }else{
            $maximalpercent=0;
        }
        return $maximalpercent;
    }
}