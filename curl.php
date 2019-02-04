<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$token = genrateToken();

if (!empty($token) && $token != '') {

    $data = array("order_info" =>
        array(
            "rdcustnum" => '3456',
            "order_id" => '000000063',
            "purchase_date" => "2019-01-29 14:32:11",
            "bill_to_name" => 'Tech Team',
            "ship_to_name" => 'Tech Team',
            "grand_total" => '293.48',
            "status" => 'pending',
            "shipping_address" =>
            array(
                "street" => '2967 Reserve St Killaloe',
                "city" => 'Killaloe',
                "province_state" => 'Ontario',
                "postal_zip" => 'K0J 2A0',
            ),
            "country" => 'CA',
            "billing_address" => array
                (
                "street" => '2967 Reserve St Killaloe',
                "city" => 'Killaloe',
                "province_state" => 'Ontario',
                "postal_zip" => 'K0J 2A0',
            ),
            "shipping_method" => 'flatrate_flatrate',
            "cust_email" => 'techteam@imediadesigns.ca',
            "cust_group" => 'General',
            "subtotal" => '174.21',
            "shipping_cost" => '96.62',
            "customer_name" => 'Tech',
            "payment_method" => 'Check / Money order',
        ),
        "order_items" => array(
            "product_name" => 'E924 24"END ALUM.PIPE WRENCH',
            "SKU" => '90127',
            "item_status" => "",
            "original_price" => '147.81',
            "price" => '147.81',
            "qty" => '1.0000',
            "subtotal" => "",
            "tax_amount" => '0.0000',
            "tax_percent" => '0.0000',
            "discount_amount" => '0.0000',
            "row_total" => '147.81'
        )
    );

    $respose = sendRequestToCurl($data, $token);
    echo '<pre>';
    print_r($respose);
    die;
}

function post_to_url($end_point, $data, $type, $header, $pra) {
    $post = curl_init();
    curl_setopt($post, CURLOPT_HTTPHEADER, $header);
    curl_setopt($post, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($post, CURLOPT_FAILONERROR, true);


    if (!empty($data)) {
        curl_setopt($post, CURLOPT_POST, count($data));
        curl_setopt($post, CURLOPT_POSTFIELDS, array(json_encode($data)));
    }

    if ($type == 'POST') {
        curl_setopt($post, CURLOPT_POST, 1);
    }

    curl_setopt($post, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($post, CURLOPT_FRESH_CONNECT, 1);
    curl_setopt($post, CURLOPT_URL, $end_point);
    
    $result = curl_exec($post);

    if ($pra != 1) {
        echo '<pre>';
        print_r($result);
        die;
    }
    curl_close($post);
    return $result;
}

function genrateToken() {
    $header = getAuthorisationHeader();
    $params['p'] = 'connect';
    $params['req'] = 'UserLogin';
    $params['userId'] = "RIDGIDAPI";
    $params['password'] = "Z7I3d3oWrNng";
    $end_point = 'https://testonline.cji.on.ca?' . http_build_query($params);
    $data = '';
    $response = post_to_url($end_point, $data, $type = 'POST', $header, 1);
    $response = json_decode($response);
    if (isset($response->header) && $response->content->success == '1') {
        return $response->header->rlSession;
    } else {
        echo 'Unable to Get Access Toekn, please check error log';
        die;
    }
}

function getAuthorisationHeader($token = '') {
    $headers = array();
    if ($token) {
        $headers = ['rlSession:' . $token];
    }
    return $headers;
}

function sendRequestToCurl($data, $token) {
    $header = getAuthorisationHeader($token);
    $end_point = 'https://testonline.cji.on.ca/ridgidOrderUpd';
    $params['p'] = 'connect';
    $params['rlSession'] = $token;
    $end_point .= '?' . http_build_query($params);
    post_to_url($end_point, $data, $type = '', $header, '');
}

?>