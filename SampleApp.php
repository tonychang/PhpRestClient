<?php
    require_once("SimpleRestClient.php");
    $xml=null;
    $restclient=null;
    $result=null;
    
    $cert_file=null;//Path to cert file 
    $key_file=null;//Path to private key
    $key_password=null;//Private key passphrase
    $curl_opts=null;//Array to set additional CURL options or override the default options of the SimpleRestClient
    $post_data=null;//Array or string to set POST data 
    $user_agent = "PHP Sample Rest Client";
    $url = "https://ws.admin.washington.edu/student/v4/public/course/2009,summer,info,344/a";

    $restclient = new SimpleRestClient($cert_file, $key_file, $key_password, $user_agent, $curl_opts);
    
    if (!is_null($post_data))
    {
      $restclient->postWebRequest($url, $post_data); 
    }
    else
    {
      $restclient->getWebRequest($url); 
    }
?>
 <html>
 <head>
    <title>Sample App</title>
 </head>
 <body>
    <span><b>Requested Url: </b><?php echo $url; ?> </span><br />
    <br />
    <span><b>Status Code: </b></span>
    <div id="status_code">
        <?php
            if (!is_null($restclient))
            {
                //Get the Http_Status_Code
                echo 'Http Status Code: ' . $restclient->getStatusCode() . '<br />';
                $response = $restclient->getWebResponse();
                //Get the error message returned from web service
                $xml = simplexml_load_string($response);

                if (!is_null($xml))
                {
                    $result = $xml->xpath('//div[@class="status_description"]');
                    if (!is_null($result) && !empty($result))
                    {
                        echo 'Web Service Error Message: ' . $result[0] . '<br />';;
                    }
                }
            }
        ?>
    </div>
    <br />
    <span><b>Response: </b></span>
    <div id="response">
        <textarea id="response_output" rows="10" cols="150">
            <?php
                if (!is_null($restclient))
                {
                  echo $restclient->getWebResponse();
                }
            ?>
        </textarea>
    </div>
    <br />
    <span><b>Class Detail: </b></span>
    <div id="content">
        <?php
            if (!is_null($xml) && $restclient->getStatusCode() == 200 && is_null($post_data))
            {
                echo 'Title: ' . $xml->head->title . '<br />'; //Get xml data via object drill down created by simplexml
                $result = $xml->xpath('//div/a/span[@class="curriculum_abbreviation"]'); //Get XML data via Xpath queries
                echo "Curr Abbrev: " . $result[0]->asXml() . '<br />';
                $result = $xml->xpath('//div/span[@class="course_branch"]');
                echo "Branch: " . $result[0]->asXml() . '<br />';;
                $result = $xml->xpath('//div/span[@class="section_id"]');
                echo "Section ID: " . $result[0]->asXml() . '<br />';;
                $result = $xml->xpath('//div/span[@class="sln"]');
                echo "SLN: " . $result[0]->asXml();
            }
        ?>
    </div>
 </body>
 </html>
