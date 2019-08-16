<?php

if (!defined('AREA')) {
    die('Access denied');
}

function fn_additional_field_order_manage_getGroup($user_id)
{
    $query = db_get_row("SELECT usergroup FROM ?:usergroup_links
                         INNER JOIN
                         ?:users ON
                         ?:usergroup_links.user_id = ?:users.user_id
                         INNER JOIN
                         ?:usergroup_descriptions
                         ON
                         ?:usergroup_descriptions.usergroup_id = ?:usergroup_links.usergroup_id
                         WHERE
                         ?:users.user_id = ?i
                         AND
                         ?:usergroup_descriptions.lang_code = 'th'
                         AND
                         ?:usergroup_links.status = 'A'", $user_id);
    return $query['usergroup'];
}

//This function assign usergroup data to order page
function fn_additional_field_order_manage_get_orders_post($params, &$orders)
{
    foreach ($orders as &$o) {
        $user_id = $o['user_id'];
        $group_name = db_get_row("SELECT usergroup FROM ?:usergroup_links
                                  INNER JOIN
                                  ?:users ON
                                  ?:usergroup_links.user_id = ?:users.user_id
                                  INNER JOIN
                                  ?:usergroup_descriptions
                                  ON
                                  ?:usergroup_descriptions.usergroup_id = ?:usergroup_links.usergroup_id
                                  WHERE
                                  ?:users.user_id = ?i
                                 AND
                                  ?:usergroup_descriptions.lang_code = 'th'
                                 AND
                                 ?:usergroup_links.status = 'A'", $user_id);
        $o['usergroup'] = $group_name;
    }
}

function fn_additional_field_order_manage_getGroupByOrder($order_id)
{
    $query = db_get_row("SELECT ?:usergroup_descriptions.usergroup FROM ?:orders
                         LEFT JOIN
                         ?:usergroup_links
                         ON
                         ?:orders.user_id = ?:usergroup_links.user_id
                         LEFT JOIN
                         ?:usergroup_descriptions
                         ON
                         ?:usergroup_links.usergroup_id = ?:usergroup_descriptions.usergroup_id
                         WHERE
                         order_id = ?i 
                         AND
                         ?:usergroup_links.status = 'A'
                         AND
                         ?:usergroup_descriptions.lang_code = 'th'", $order_id);
    return $query['usergroup'];

}
