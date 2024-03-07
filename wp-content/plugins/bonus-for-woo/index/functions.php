<?php

/**
 * Показ всплывающих подсказок.
 *
 * @param string $text       Help tip text.
 * @param  string   $event  danger | faq | info
 * @return string
 * @since  5.0.0
 *
 */
function bfw_help_tip(string $text, string $event='faq' ): string
{
    $text = esc_attr( $text );

    return '<span class="bfw-help-tip ' . $event . '" data-tip="' . $text . '"></span>';
}


/*-------Сортировка массива По возрастанию-------*/
function array_multisort_value()
{
    $args = func_get_args();
    $data = array_shift($args);
    foreach ($args as $n => $field) {
        if (is_string($field)) {
            $tmp = array();
            foreach ($data as $key => $row) {
                $tmp[$key] = $row[$field];
            }
            $args[$n] = $tmp;
        }
    }
    $args[] = &$data;
    call_user_func_array('array_multisort', $args);
    return array_pop($args);
}

add_action('computy_copyright', array('BfwFunctions', 'computy_copyright'), 25);


/*Меняем статус поста\товара*/
if( ! function_exists( 'set_post_status_bfw' ) ) {

    function set_post_status_bfw( $post_status, $post = null ): bool
    {

        $post = get_post( $post );

        if ( ! is_object( $post ) ) {
            return false;
        }

        $post_id = wp_update_post( array(
            'ID' => $post->ID,
            'post_status' => $post_status
        ) );

        if( $post_id ) {
            return true;
        } else {
            return false;
        }
    }
}

function json_encode_cyr($str) {
    $arr_replace_utf = array('\u0410', '\u0430','\u0411','\u0431','\u0412','\u0432',
        '\u0413','\u0433','\u0414','\u0434','\u0415','\u0435','\u0401','\u0451','\u0416',
        '\u0436','\u0417','\u0437','\u0418','\u0438','\u0419','\u0439','\u041a','\u043a',
        '\u041b','\u043b','\u041c','\u043c','\u041d','\u043d','\u041e','\u043e','\u041f',
        '\u043f','\u0420','\u0440','\u0421','\u0441','\u0422','\u0442','\u0423','\u0443',
        '\u0424','\u0444','\u0425','\u0445','\u0426','\u0446','\u0427','\u0447','\u0428',
        '\u0448','\u0429','\u0449','\u042a','\u044a','\u042b','\u044b','\u042c','\u044c',
        '\u042d','\u044d','\u042e','\u044e','\u042f','\u044f');
    $arr_replace_cyr = array('А', 'а', 'Б', 'б', 'В', 'в', 'Г', 'г', 'Д', 'д', 'Е', 'е',
        'Ё', 'ё', 'Ж','ж','З','з','И','и','Й','й','К','к','Л','л','М','м','Н','н','О','о',
        'П','п','Р','р','С','с','Т','т','У','у','Ф','ф','Х','х','Ц','ц','Ч','ч','Ш','ш',
        'Щ','щ','Ъ','ъ','Ы','ы','Ь','ь','Э','э','Ю','ю','Я','я');
    //$str1 = json_encode($str);
    return str_replace($arr_replace_utf,$arr_replace_cyr,$str);
}
