<?php
/**
 * Страница истории всех начислений баллов клиентов
 *
 * @version 2.5.1
 */

require_once ('datatable.php');
/*Обработчик удаления записи истории начисления баллов*/
if (isset($_POST['bfw_delete_post_history_points'])) {
    (new BfwHistory)->deleteHistoryId(sanitize_text_field($_POST['bfw_delete_post_history_points']));
    echo '<div id="message" class="notice notice-warning is-dismissible">
	<p>' . __('deleted', 'bonus-for-woo') . '.</p></div>';
}
/*Обработчик удаления записи истории начисления баллов*/

echo '<div class="wrap bonus-for-woo-admin">';
echo '<h1>'.sprintf(__('History of %s for all customers', 'bonus-for-woo'), (new BfwPoints())->pointsLabel(5)).'</h1>';
echo '<p></p>';

$date_start = $_GET['date_start'] ?? false;
$date_finish = $_GET['date_finish'] ?? date("Y-m-d");
?>
<form>
    <input type="hidden" name="page" value="bonus-for-woo/index/list_history.php">
    <label><?php echo   __('From','bonus-for-woo'); ?>
    <input type="date" id="date_start" name="date_start" value="<?php echo $date_start; ?>" max="<?php echo date("Y-m-d"); ?>" onchange="bfwchangestart()"></label>
    <label><?php echo   __('to','bonus-for-woo'); ?>
        <input type="date" id="date_finish" name="date_finish" value="<?php echo $date_finish; ?>" max="<?php echo date("Y-m-d"); ?>" <?php if(!$date_start){echo 'disabled';} ?> ></label>
    <input class="button" type="submit" value="<?php echo __('Search','bonus-for-woo');?>">
</form>
<script>
    function bfwchangestart() {
        let start =   document.getElementById('date_start');
        let finish =  document.getElementById('date_finish');
        console.log(start.value.length);
        if(start.value.length===10){
            finish.disabled = false;
            finish.min = start.value;
        }else{
            finish.disabled = true;
        }

    }
</script>
<br>
<?php
if(empty($_GET['date_start'])){echo '<b style="color: red">'.__('The last 500 entries are displayed.','bonus-for-woo').'</b>';}
(new BfwHistory)->getListHistory($date_start,$date_finish);

echo '</div>';