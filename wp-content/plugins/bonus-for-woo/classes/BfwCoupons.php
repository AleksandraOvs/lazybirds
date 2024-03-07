<?php
/**
 * Class BfwCoupons
 *
 * @since 4.1.0
 * @version 4.1.0
 */

class BfwCoupons
{

    /**
     * Добавление купона
     *
     * @param $code // Код купона
     * @param float $sum //сумма
     * @param  $comment_admin
     * @param $status
     * @version 4.1.0
     */
    public static function addCoupon($code, float $sum, $comment_admin, $status): void
    {
        if ($code!=''){
            global $wpdb;
            $wpdb->insert(
                $wpdb->prefix . 'bfw_coupons_computy', // указываем таблицу
                array(
                    'code' => $code,
                    'created' => date('Y-m-d H:i:s'),
                    'sum' => $sum,
                    'comment_admin' => $comment_admin,
                    'status' => $status,
                ),
                array(
                    '%s', // %s - значит строка
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s'
                )
            );}
    }



/**
 * Показ всех купонов
 *
 *  @version 4.1.0
 */
public static function getListCoupons(): void
{
    global $wpdb;
    $table_bfw = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "bfw_coupons_computy ORDER BY id DESC" );
    if ($table_bfw) {
        ?>
        <table class="table-bfw table-bfw-history-points" id='table-coupons'>
            <thead><tr>
                <th>№</th>
                <th><?php echo __('Coupon code', 'bonus-for-woo'); ?></th>
                <th><?php echo __('Sum', 'bonus-for-woo'); ?></th>
                <th><?php echo __('Create date', 'bonus-for-woo'); ?></th>
                <th><?php echo __('Comment admin', 'bonus-for-woo'); ?></th>
                <th><?php echo __('Client', 'bonus-for-woo'); ?></th>
                <th><?php echo __('Date of use', 'bonus-for-woo'); ?></th>
                <th><?php echo __('Status', 'bonus-for-woo'); ?></th>
                <th><?php echo __('Action', 'bonus-for-woo'); ?></th>
            </tr></thead>
            <tbody>
            <?php
            $i = 1;
            foreach ($table_bfw as $bfw) {
                if($bfw->status == 'active'){$bgtr='background:#fff;';}
                elseif($bfw->status == 'noactive'){$bgtr = 'background:#ff9a9a;';}
                else{$bgtr = 'background:#89f784;';}
               echo '<tr style="'.$bgtr.'"><td>'.$i++.'</td>
<td><b>' . $bfw->code. '</b></td>
<td><b>' . (new BfwPoints())->roundPoints($bfw->sum). '</b></td>
<td>' . date_format(date_create($bfw->created), 'd.m.Y H:i') . '</td>
<td>'   . $bfw->comment_admin. '</td>';
               if($bfw->user!=0){
                $user   = get_userdata($bfw->user);

                if(!empty($user->first_name)){
                    $nameuser = $user->first_name.' '.$user->last_name;
                }else{
                    $nameuser = $user->user_login;
                }
                echo '<td><a href="/wp-admin/user-edit.php?user_id='.$bfw->user.'" target="_blank">'.$nameuser.'</a></td> ';
                    }else{ echo '<td>-</td> ';}
                if($bfw->date_use!='0000-00-00 00:00:00'){
                    echo '<td>'.$bfw->date_use.'</td>';
                }else{echo '<td>-</td> ';}
                $statustext ='';
                if($bfw->status == 'active'){$statustext =  __('Active','bonus-for-woo');}
                elseif($bfw->status == 'noactive'){$statustext =  __('Not active','bonus-for-woo');}
                elseif($bfw->status == 'used'){$statustext =  __('Used','bonus-for-woo');}
                echo '<td>'.$statustext.'</td>';

                    echo '<td style="display: flex;justify-content: space-between;">';
 if($bfw->status == 'active'){
     echo '<form method="post" action="" class="list_role_computy">
                <input type="hidden" name="status_coupon" value="active" >
                  <input type="hidden" name="bfw_edit_status_coupon" value="' . $bfw->id . '" >
                  <input type="submit" value="' . __('Deactivate', 'bonus-for-woo') . '" class="button_activated_coupon" title="' . __('Deactivate', 'bonus-for-woo') . '"  >
                  </form>';
 }elseif($bfw->status == 'noactive'){
     echo '<form method="post" action="" class="list_role_computy">
                    <input type="hidden" name="status_coupon" value="noactive" >
                  <input type="hidden" name="bfw_edit_status_coupon" value="' . $bfw->id . '" >
                  <input type="submit" value="' . __('Activate', 'bonus-for-woo') . '" class="button_activated_coupon" title="' . __('Activate', 'bonus-for-woo') . '">
                  </form>';
 }else{echo '<span></span>';}
        echo '<form method="post" action="" class="list_role_computy">
                  <input type="hidden" name="bfw_delete_coupon" value="' . $bfw->id . '" >
                  <input type="submit" value="+" class="delete_role-bfw" title="' . __('Delete',
                            'bonus-for-woo') . '" onclick="return window.confirm(\' ' . __('Are you sure you want to delete this coupon?', 'bonus-for-woo') . ' \');">
                  </form> 
                  
                  </td>';

                echo '</tr>';
            }
            ?>
            </tbody>
        </table>
        <?php
    }
}


    /**
     * Удаление купона
     *
     *  @version 4.1.0
     */
public static function deleteCoupon($id): void
{
        global $wpdb;
        $table_name = $wpdb->prefix . 'bfw_coupons_computy';
        $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE `id` = %d",$id) );
    }


    /**
     * Изменение статуса купона
     *
     *  @version 4.1.0
     */

 public static function  editStatusCoupon($id,$status): void
 {

     global $wpdb;
     $table_name = $wpdb->prefix . 'bfw_coupons_computy';
     $wpdb->query($wpdb->prepare("UPDATE  $table_name SET `status`=%s WHERE  `id` = %d",$status,$id) );
            }


    /**
     * Вывод одного купона
     *
     *  @version 4.1.0
     */
    public static function getCoupon($code){
        global $wpdb;
        $coupon = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "bfw_coupons_computy WHERE code='$code'");
        return $coupon[0];


    }


    /**
     * Применение купона клиентом
     *
     *  @version 4.1.0
     */
    public static function  enterCoupon($userid,$code_coupon): string
    {
       $coupon = (new BfwCoupons())->getCoupon($code_coupon);

       //Проверяем существует ли купон
        if(isset($coupon->code)){
                    if($coupon->status=='active'){
            //Сделать данный купон использованным
            global $wpdb;
            $table_name = $wpdb->prefix . 'bfw_coupons_computy';
            $wpdb->query($wpdb->prepare("UPDATE  $table_name SET `status`=%s, `user`=%d, `date_use`=%s WHERE  `id` = %d",'used',$userid,date('Y-m-d H:i:s'),$coupon->id) );

            $old_points = (new BfwPoints())->getPoints($userid);
            $coupon_sum = $coupon->sum;
            $new_ball = $old_points+$coupon_sum;
            (new BfwPoints())->updatePoints($userid,$new_ball);
            $pricina = __('Coupon usage', 'bonus-for-woo');
            (new BfwHistory)->add_history($userid, '+', $coupon_sum, '0', $pricina);
            //Отправить почту админу и клиенту уведомление(пока не реализовано)

            return 'good';
                    }else{
                        return 'not_coupon';
                    }
        }else{
            return 'not_coupon';
        }

    }

    }