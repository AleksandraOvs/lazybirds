<?php
/**
 * Страница купонов
 *
 * @version 4.1.0
 */

/*Обработчик запроса добавления купона*/
if (isset($_POST['bfw_computy_add_coupon_ajax'])) {
    if ($_POST['bfw_computy_add_coupon_ajax'] == 'bfw_computy_add_coupon_ajax') {
        global $wpdb;
        $code = _sanitize_text_fields($_POST['code']);
        $sum = _sanitize_text_fields($_POST['sum']);
        $comment_admin = _sanitize_text_fields($_POST['comment_admin']);
        $status = _sanitize_text_fields($_POST['status']);


        if ($wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "bfw_coupons_computy  WHERE `code` =  '$code'  ")) {
  echo '<div id="message" class="notice notice-warning is-dismissible">
<p>'.sprintf(__('Coupon <b>%s</b> is already being used. Enter another code or change the existing one.', 'bonus-for-woo'),$code).'
	</p></div>';

        }else{
            (new BfwCoupons)->addCoupon($code,$sum,$comment_admin,$status);
            echo '<div id="message" class="notice notice-success is-dismissible">
	<p>' . __('Coupon', 'bonus-for-woo') . ' <b>' . $code . '</b> ' . __('added', 'bonus-for-woo') . '.</p>
</div>';
        }

    } elseif ($_POST['bfw_computy_ajax'] == 'editrolehidden') {
        global $wpdb;
        $percent_role = sanitize_text_field($_POST['percent_role']);
        $summa_start = sanitize_text_field($_POST['summa_start']);
        $name_role = sanitize_text_field($_POST['name_role']);
        $wpdb->update($wpdb->prefix . "bfw_computy",
            array('percent' => $percent_role, 'summa_start' => $summa_start),
            array('name' => $name_role)
        );
    }
}
/*Обработчик запроса добавления роли*/


/*Обработчик запроса удаления купона*/
if (isset($_POST['bfw_delete_coupon'])) {
    (new BfwCoupons)->deleteCoupon($_POST['bfw_delete_coupon']);
    echo '<div id="message" class="notice notice-warning is-dismissible">
	<p>' . __('deleted', 'bonus-for-woo') . '.</p></div>';
}
/*Обработчик запроса удаления купона*/

/*Обработчик запроса изменения статуса купона*/
if (isset($_POST['bfw_edit_status_coupon'])) {
    if(sanitize_text_field($_POST['status_coupon'])=='active'){$status='noactive';}else{$status='active';}
    (new BfwCoupons)->editStatusCoupon($_POST['bfw_edit_status_coupon'],$status);
    echo '<div id="message" class="notice notice-warning is-dismissible">
	<p>' . __('Coupon status changed', 'bonus-for-woo') . '.</p></div>';
}
/*Обработчик запроса изменения статуса купона*/

if(determine_locale()=='ru_RU'){
    $language =' language: {
                        "sProcessing":   "Подождите...",
                        "sLengthMenu":   "Показать _MENU_ купонов",
                        "sZeroRecords":  "Купоны отсутствуют.",
                        "sInfo":         "Купоны с _START_ до _END_ из _TOTAL_ купонов",
                        "sInfoEmpty":    "Купоны с 0 до 0 из 0 купонов",
                        "sInfoFiltered": "(отфильтровано из _MAX_ купонов)",
                        "sInfoPostFix":  "",
                        "sSearch":       "Поиск:",
                        "sUrl":          "",
                        "oPaginate": {
                            "sFirst": "Первая",
                            "sPrevious": "Предыдущая",
                            "sNext": "Следующая",
                            "sLast": "Последняя"
                        },
                        "oAria": {
                            "sSortAscending":  ": активировать для сортировки столбца по возрастанию",
                            "sSortDescending": ": активировать для сортировки столбцов по убыванию"
                        }}';
}else{
    $language ='';
}
?>
    <script>
        jQuery(document).ready(function() {
            jQuery('#table-coupons').DataTable(
                {
                    <?php  echo $language; ?>
                }
            );
        } );

    </script>
<?php
wp_register_style( 'datatables.min.css', BONUS_COMPUTY_PLUGIN_URL . '_inc/datatables/datatables.min.css', array(), BONUS_COMPUTY_VERSION );
wp_register_script( 'jquery.dataTables.min.js', BONUS_COMPUTY_PLUGIN_URL . '_inc/datatables/jquery.dataTables.min.js', array(), BONUS_COMPUTY_VERSION );

wp_enqueue_style( 'datatables.min.css' );
wp_enqueue_script( 'jquery.dataTables.min.js' );

?>
<div class="wrap bonus-for-woo-admin">
    <?php
    echo '<h1>'.sprintf(__('Coupons for %s ', 'bonus-for-woo'), (new BfwPoints())->pointsLabel(5)).'</h1>';
    ?>
    <hr>
    <div class="bfw_texts_wrap">
        <div class="bfw_text">
            <h3><?php echo __('Add a new coupon', 'bonus-for-woo'); ?></h3>
            <form method="post" action="" id="add_coupon_form">
                <input type="hidden" id="bfw_computy_add_coupon_ajax" name="bfw_computy_add_coupon_ajax" value="bfw_computy_add_coupon_ajax">
                <table class="form">
                    <tbody>
                    <tr>
                        <td class="table-bfw">
                            <label for="add_coupon_form_code"><b><?php echo __('Сode coupon', 'bonus-for-woo'); ?></b></label>
                            <input type="text" id="add_coupon_form_code" name="code" value="" placeholder="" required><br>
                            <label for="add_coupon_form_sum"><b><?php echo __('Number of points', 'bonus-for-woo'); ?></b></label>
                            <input min="1" type="number" id="add_coupon_form_sum" name="sum" value=""  placeholder="300" required>
                        </td>
                        <td class="table-bfw">
                            <label for="add_coupon_form_comment_admin"><b><?php echo __('Comment admin', 'bonus-for-woo'); ?></b></label>
                           <textarea style="width: 300px; height: 93px;" id="add_coupon_form_comment_admin" name="comment_admin" placeholder=""></textarea>
                        </td>
                    </tr>

                    </tbody>
                </table>
 <input type="submit" name="submit"  class="button button-primary" value="<?php echo __('Add coupon', 'bonus-for-woo'); ?>">

                    <select name="status">
                        <option value="noactive"><?php echo __('Not active', 'bonus-for-woo'); ?></option>
                        <option value="active"><?php echo __('Active', 'bonus-for-woo'); ?></option>
                    </select>

            </form>
        </div>

        <div class="bfw_text">
            <h3><?php echo __('Description', 'bonus-for-woo'); ?></h3>
            <p><?php echo __('Create a coupon in the form and it will appear in the list of coupons.', 'bonus-for-woo'); ?><br>
                <?php echo __('The customer can only use active coupons.', 'bonus-for-woo'); ?><br>
                <?php echo __('Coupon can only be used once.', 'bonus-for-woo'); ?><br>
               <?php echo __('Once a coupon has been used, it cannot be activated. Only delete.', 'bonus-for-woo'); ?><br>
            </p>


        </div>
    </div>

    <h2><?php echo __('Coupons list', 'bonus-for-woo'); ?></h2>
    <?php
    (new BfwCoupons)->getListCoupons();
    ?>
</div>