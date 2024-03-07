<?php
/**
 * Class BfwEmail
 *
 * @version 2.2.0
 */
class BfwEmail
{

    /**
     * Метод отправки сообщения о баллах
     *
     * @param int $user_id
     * @param string $subject
     * @param string $title
     * @param string $message
     * @since 2.5.1
     * @version 5.2.0
     */
    function getMail(int $user_id, string $subject, string $title, string $message ): void
    {
        $messages ='';
        if(get_option( 'woocommerce_email_from_name' )==''){
            $from = get_bloginfo('name');
        }else{
            $from = get_option( 'woocommerce_email_from_name' );
        }

        if(get_option( 'woocommerce_email_from_address' )==''){
            $frome = get_option('admin_email');
        }else{
            $frome = get_option( 'woocommerce_email_from_address' );
        }

        $headers = array(
            'From: '.$from.' <'.$frome.'>',
            'content-type: text/html',
        );

        if($subject==''){$subject=sprintf(__('Reward %s notification', 'bonus-for-woo'), (new BfwPoints())->pointsLabel(5)).' '.$from;}
        if($title==''){$title=$subject;}


        /*header mail*/
        $messages.=self::getHeaderMail($title);

        $user = get_userdata($user_id);
        $messages .= $message;

        /*footer mail*/
        $messages.= self::getFooterMail();


   $val = get_option('bonus_option_name');
  if (empty($val['email-my-methode'])){
      if(!empty($user->user_email)){
          wp_mail( $user->user_email, $subject, $messages, $headers );
      }

  }else{
      do_action( 'bfw_my_methode_get_mail', $message, $user_id );
  }



    }


    /**
     * @param string $title
     * @return string
     * @version 2.5.1
     */
    public static function getHeaderMail(string $title): string
    {
        $background_color = get_option("woocommerce_email_background_color");
        $webcolor = get_option("woocommerce_email_base_color");
        $body_bg_color = get_option("woocommerce_email_body_background_color");
        $text_color = get_option("woocommerce_email_text_color");

       $test='<div class="js-message-body-content mail-Message-Body-Content">
<div style="background-color:{background_color};margin:0;padding:70px 0 70px 0;width:100%"><table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
<tbody><tr><td align="center" valign="top"><div id="template_header_image"></div>
<table  border="0" cellpadding="0" cellspacing="0" width="600" style="background-color:#ffffff;border:1px solid #dedede;border-radius:3px">
<tbody><tr><td align="center" valign="top"><table  border="0" cellpadding="0" cellspacing="0" width="600" style="background-color:{woocommerce_email_base_color};border-bottom-style:none;border-bottom-width:0;border-radius:3px 3px 0 0;color:#ffffff;font-family: sans-serif;font-weight:bold;line-height:100%;vertical-align:middle"><tbody><tr>
<td id="header_wrapper" style="display:block;padding:36px 48px 36px 48px"><h1 style="color:#ffffff;font-family:roboto,arial,sans-serif;font-size:30px;font-weight:300;line-height:150%;margin:0;text-align:left">{title}</h1>
</td></tr></tbody></table></td></tr><tr><td align="center" valign="top">
<table id="template_body" border="0" cellpadding="0" cellspacing="0" width="600"><tbody><tr>
<td id="body_content" valign="top" style="background-color:{woocommerce_email_body_background_color}">
<table border="0" cellpadding="20" cellspacing="0" width="100%"><tbody><tr><td valign="top" style="padding:48px 48px 0 48px">
        <div id="5b5e64daa4b5370bbody_content_inner" style="color:{woocommerce_email_text_color};font-family:roboto,arial,sans-serif;font-size:14px;line-height:150%;text-align:left">';
        return strtr($test, [
            '{background_color}' => $background_color,
            '{woocommerce_email_base_color}' => $webcolor,
            '{woocommerce_email_text_color}' => $text_color,
            '{woocommerce_email_body_background_color}' => $body_bg_color,
            '{title}' => $title]);
    }


    /**
     * @return string
     * @version 2.5.1
     */
    public static function getFooterMail(): string
    {
        if (get_option('woocommerce_email_footer_text') == '') {
            /*Если подвал не указан в настройках woo*/
            $description = get_bloginfo('description');
            ob_start();
            require_once('teamplates/footer.php');
            $get_test = ob_get_contents();
            ob_end_clean();
        } else {
            $footer_text = get_option('woocommerce_email_footer_text');
            $site_title = get_bloginfo('name');
            $site_url = $_SERVER["SERVER_NAME"];
            $description = strtr($footer_text,
                ['{site_title}' => $site_title, ' {site_url}' => $site_url]);

$get_test='</div></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td align="center" valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="600"><tbody><tr><td valign="top" style="padding:0">
<table border="0" cellpadding="10" cellspacing="0" width="100%"><tbody><tr><td colspan="2" valign="middle" style="border:0;color:#c09bb9;font-family:sans-serif;font-size:12px;line-height:125%;padding:0 48px 48px 48px;text-align:center">
 <p>{description}</p> </td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></div></div>';
        }
        return strtr($get_test, ['{description}' => $description]);
    }


    /**
     * Метод формирования шаблона письма
     *
     * @param string $text_email Шаблон настроек
     * @param array $text_email_array
     * @return string
     * @todo для версии PHP > 7.4: return preg_replace_callback('~\[(\w+)\]~', fn($m) => $text_email_array[$m[1]] ?? $m[0], wpautop($text_email));
     * @version 2.5.1
     */
    public static function template(string $text_email, array $text_email_array): string
    {
        ob_start();
        echo  wpautop($text_email);
        $get_contents = ob_get_contents();
        ob_end_clean();
        return strtr($get_contents, $text_email_array);
    }


}