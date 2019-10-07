<?php
/**
 *
 * Thank you for your purchase! You are the best!
 *
 * @copyright    (C) 2018 Hungryweb
 * @website      https://www.hungryweb.net/
 * @support      support@hungryweb.net
 * @license      https://www.hungryweb.net/license-agreement.html
 *
 * ---------------------------------------------------------------------------------
 * This is a commercial software, only users who have purchased a valid license
 * and accepts the terms of the License Agreement can install and use this program.
 * ---------------------------------------------------------------------------------
 *
 */

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

use Tygh\Registry;
use Tygh\Http;
use Tygh\Settings;
use Tygh\Mailer;

function fn_settings_variants_addons_hw_auto_group_usergroup_id()
{
    $data = array(
        '0' => ' --- '
    );
    $usergroups = fn_get_usergroups('C');
    foreach ($usergroups as $k => $uc) {
        $data[$k] = $uc['usergroup'];
    }
    return $data;
}

function fn_hw_auto_group_update_profile($action, $user_data, $current_user_data)
{
    if ($action == 'add' && $user_data['user_type'] == 'C') {
        if (!fn_allowed_for('ULTIMATE:FREE')) {
            $usergroup_id = (int)Registry::get('addons.hw_auto_group.usergroup_id');
            if (!empty($usergroup_id)) {
                $notify = array();
                if (Registry::get('addons.hw_auto_group.notify') == 'Y') {
                    $notify['C'] = true;
                }
                fn_change_usergroup_status('A', $user_data['user_id'], $usergroup_id, $notify);
            }
        }
    }
}

#Hungryweb License
//if (!function_exists('fn_hw_aiden_license_info')) {
//    function fn_hw_aiden_license_info()
//    {
//        $html = '';
//        $html .= '<div class="control-group setting-wide"><label class="control-label">&nbsp;</label><div class="controls"><span><a href="https://www.hungryweb.net/generate-license.html" target="_blank">' . __('hw_license_generator') . '</a></span></div></div>';
//        return $html;
//    }
//}

#Hungryweb actions
//function fn_hw_auto_group_install()
//{
//    fn_hw_aiden_action('auto_group', 'install');
//}
//
//function fn_hw_auto_group_uninstall()
//{
//    fn_hw_aiden_action('auto_group', 'uninstall');
//}
//
//if (!function_exists('fn_hw_aiden_action')) {
//    function fn_hw_aiden_action($addon, $a)
//    {
//        $request = array('addon' => $addon, 'host' => Registry::get('config.http_host'), 'path' => Registry::get('config.http_path'), 'version' => PRODUCT_VERSION, 'edition' => PRODUCT_EDITION, 'lang' => strtoupper(CART_LANGUAGE), 'a' => $a, 'love' => 'aiden');
//        Http::post('https://www.hwebcs.com/ws/addons', $request);
//    }
//}
