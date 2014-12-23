<?php
include_once("SimpleRestClient.php");


$user = 'your user'; #input here your user
$token = 'token'; #input here your account token
$suite = 'suite'; #input here your suite


$method="Report.QueueRanked";

#in my case I have in the evar8 the order_id information, then in my product report I have the hierarchy of 10 reports and I want the sku which is level1 in hierarchy table
function data_preparation($date, $div){

    return '{
    "reportDescription":{
        "reportSuiteID":"'.$suite.'",
        "dateFrom":"'.$date.'",
        "dateTo":"'.$date.'",
        "metrics":[ {"id":"orders"}, {"id":"revenue"}],
        "elements":[{"id":"evar8", "top":"1"},{"id":"product", "level":"1","top":"10"}]}}';
}




function GetAPIData($method, $data, $user, $token) {
    $username = $user;
    $secret = $token;
    $nonce = md5(uniqid(php_uname('n'), true));
    $nonce_ts = date('c');
    $digest = base64_encode(sha1($nonce.$nonce_ts.$secret));

    $server = "https://api.omniture.com";
    $path = "/admin/1.3/rest/";

    $rc=new SimpleRestClient();
    $rc->setOption(CURLOPT_HTTPHEADER, array("X-WSSE: UsernameToken Username=\"$username\", PasswordDigest=\"$digest\", Nonce=\"$nonce\", Created=\"$nonce_ts\""));

    $rc->postWebRequest($server.$path.'?method='.$method, $data);

    return $rc;
}




function load_data($date, $method, $token, $user){
    $data = data_preparation($date, $dp_no);
    $rc=GetAPIData($method, $data, $user, $token);
    $done = '';
    $error = '';

    if ($rc->getStatusCode()==200) {
        $response=$rc->getWebResponse();
        $json=json_decode($response);
        if ($json->status=='queued') {
            $reportID=$json->reportID;
        }
        else {
            $error=true;
            echo "not queued - ";
        }
    } else {
        $error=true;
        echo "something went really wrong\n";
        var_dump($rc->getInfo());
        echo "\n".$rc->getWebResponse();
    }

    while (!$done && !$error) {
        sleep(5);

        $method="Report.GetStatus";
        $data='{"reportID":"'.$reportID.'"}';

        $rc=GetAPIData($method, $data, $user, $token);

        if ($rc->getStatusCode()==200) {
            $response=$rc->getWebResponse();
            $json=json_decode($response);

            if ($json->status=="done") {
                $done=true;
            }
            else if ($json->status=="failed" || strstr($json->status, "error")>0) {
                $error=true;
            }
        } else {
            $done=true;
            $error=true;
            echo "something went really wrong\n";
            var_dump($rc->getInfo());
            echo "\n".$rc->getWebResponse();
        }
    }

    if ($error) {
        echo "report failed:\n";
        echo $response;
    }
    else {
        $method="Report.GetReport";
        $data='{"reportID":"'.$reportID.'"}';

        $rc=GetAPIData($method, $data, $user, $token);
        if ($rc->getStatusCode()==200) {
            $response=$rc->getWebResponse();
            $json=json_decode($response);
            

            
            foreach ($json->report->data as $el) {
               
                $order_id = $el->name;
                #for each order_id we have the top 10 sku bought in there             
                foreach($el->breakdown as $level2){
                    $sku = $level2->name;
                    $orders = $level2->counts[0];
                    $revenue = $level2->counts[1];
                    echo $order_id.$sku.$orders.$revenue.PHP_EOL;


                }
               


            }
        } else {
            echo "something went really wrong\n";
            var_dump($rc->getInfo());
            echo "\n".$rc->getWebResponse();
        }
    }    
}




$date_touse = '2014-12-20';
$date_touse = DateTime::createFromFormat('Y-m-d', $date_touse);






while(strtotime($date_touse->format('Y-m-d'))<=strtotime('2014-12-20')){
    load_data($date_touse->format('Y-m-d'), $method, $token, $user);    
    date_add($date_touse, date_interval_create_from_date_string("1 day"));
}




?>