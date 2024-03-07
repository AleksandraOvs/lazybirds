<?php

if(determine_locale()=='ru_RU'){
    $language =' language: {
                        "sProcessing":   "Подождите...",
                        "sLengthMenu":   "Показать _MENU_ записей",
                        "sZeroRecords":  "Записи отсутствуют.",
                        "sInfo":         "Записи с _START_ до _END_ из _TOTAL_ записей",
                        "sInfoEmpty":    "Записи с 0 до 0 из 0 записей",
                        "sInfoFiltered": "(отфильтровано из _MAX_ записей)",
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
            jQuery('#table-history-points').DataTable(
                {      responsive: true,
                    <?php if(!is_admin()){?>
                      'sDom': '"top"i',
                    <?php } ?>
                    <?php  echo $language; ?>
                }
            );
        } );

    </script>
<?php
wp_register_style( 'datatables.min.css', BONUS_COMPUTY_PLUGIN_URL . '_inc/datatables/datatables.min.css', array(), BONUS_COMPUTY_VERSION );
wp_register_script( 'jquery.dataTables.min.js', BONUS_COMPUTY_PLUGIN_URL . '_inc/datatables/jquery.dataTables.min.js', array(), BONUS_COMPUTY_VERSION );
wp_register_script( 'dataTables.responsive.min.js', BONUS_COMPUTY_PLUGIN_URL . '_inc/datatables/Responsive-2.2.5/js/dataTables.responsive.min.js', array(), BONUS_COMPUTY_VERSION );
wp_register_style( 'datatablesres.min.css', BONUS_COMPUTY_PLUGIN_URL . '_inc/datatables/Responsive-2.2.5/css/responsive.dataTables.min.css', array(), BONUS_COMPUTY_VERSION );

wp_enqueue_style( 'datatables.min.css' );
wp_enqueue_script( 'jquery.dataTables.min.js' );
wp_enqueue_script( 'dataTables.responsive.min.js' );
wp_enqueue_style( 'datatablesres.min.css' );