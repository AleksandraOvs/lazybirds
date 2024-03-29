<?php // phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols

require_once(dirname(dirname(dirname(__FILE__))) . "/vendor/autoload.php");
//require_once(dirname(dirname(dirname(__FILE__)))."/src/autoload.php");

if (!function_exists('is_countable')) {
    function is_countable($value)
    {
        return is_array($value) || $value instanceof Countable;
    }
}

define('TEST_ACTOR_ID', 'test');
define('TEST_ACTOR_TOKEN', '#K!-k(D7x[Ro_y40[|-X');
define('TEST_CASHBOX_WITHOUT_AGENT', '1');
define('TEST_CASHBOX_WITH_AGENT', '2');
