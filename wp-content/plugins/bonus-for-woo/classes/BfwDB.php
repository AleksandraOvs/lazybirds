<?php
/**
 * Class BfwDb
 *
 * @since 5.0.0
 * @version 5.2.0
 */
class BfwDB
{

    /**
     * Проверяем версию базы данных
     *
     * @since 5.0.0
     * @version 5.2.0
     */
    public static function checkDb(): void
    {
        $vdb = get_option('bfw_version_db');
        if(isset($vdb)){
            /*Сверяем версию version_db с BONUS_COMPUTY_VERSION_DB*/
            if($vdb<BONUS_COMPUTY_VERSION_DB){
                BfwDB::getUpdateDb();
            }

            /* На всякий случай проверим главную таблицу на существование*/
            global $wpdb;
            $table = $wpdb->prefix . 'bfw_computy';
            if ( $wpdb->get_var("show tables like '".$table."'") != $table ) {
                BfwDB::getUpdateDb();
            }

        }else{
            BfwDB::getUpdateDb();
        }
    }

    /**
     * Обновление базы данных до актуальной версии. Срабатывает при активации и обновлении плагина
     *
     * @since 5.0.0
     * @version 5.0.0
     */
    public static function getUpdateDb(): void
    {

        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $bfw_computy = $wpdb->prefix . 'bfw_computy';
        $sql1 = "CREATE TABLE $bfw_computy (
		id mediumint NOT NULL AUTO_INCREMENT,
		name varchar(255) DEFAULT '' NOT NULL,
		slug varchar(50) DEFAULT '' NOT NULL,
		percent varchar(50) DEFAULT '' NOT NULL,
		summa_start varchar(50)  NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

$bfw_history_computy =$wpdb->prefix . 'bfw_history_computy';
          $sql2 = "CREATE TABLE $bfw_history_computy (
		id mediumint NOT NULL AUTO_INCREMENT,
		user int(9) NOT NULL,
		date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		symbol varchar(10) NOT NULL,
		points decimal(19,4)  NOT NULL,
		orderz int(10)  NOT NULL,
		comment_admin VARCHAR(255)  DEFAULT '' NOT NULL,
		status varchar(10)  DEFAULT '' NOT NULL,
		PRIMARY KEY (id)
	) $charset_collate;";


$bfw_coupons_computy = $wpdb->prefix . 'bfw_coupons_computy';
            $sql3 = "CREATE TABLE $bfw_coupons_computy (
		id mediumint NOT NULL AUTO_INCREMENT,
		code varchar(250) NOT NULL,
		sum decimal(19,4) NOT NULL,
		status varchar(10) DEFAULT '' NOT NULL,
		created datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		date_use datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		user int(10) NOT NULL,
		comment_admin varchar(255)  DEFAULT '' NOT NULL,
		PRIMARY KEY (id)
                   ) $charset_collate;";

        dbDelta($sql1);
        dbDelta($sql2);
        dbDelta($sql3);
            /*
$bfw_time_points_computy = $wpdb->prefix . 'bfw_time_points_computy';
        $sql4 = "CREATE TABLE  $bfw_time_points_computy (
		id mediumint(10) NOT NULL AUTO_INCREMENT,
		user int(10) NOT NULL,
		created_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		off_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		points decimal(19,4) NOT NULL,
		status varchar(10) DEFAULT '' NOT NULL,
		PRIMARY KEY (id)
	) $charset_collate;";
*/

       // dbDelta($sql4);

        update_option('bfw_version_db', 2);
    }



}