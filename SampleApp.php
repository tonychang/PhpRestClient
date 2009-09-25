<?php
    require_once("SimpleRestClient.php");
    $xml=null;
    $restclient=null;
    $result=null;
    $cert_file = "";//Path to cert file 
    $key_file = "";//Path to private key
    $user_agent = "PHP Sample Rest Client";
    $url = "https://ws.admin.washington.edu/student/v4/public/course/2009,summer,info,344/a";
    $restclient = new SimpleRestClient($cert_file, $key_file, $user_agent);
    $restclient->makeWebRequest($url, TRUE); //FALSE means you are requesting a resource that requires X509 cert authentication and you MUST set the $cert_file & $key_file variables above
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
    <span><b>Raw Output: </b></span>
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
            if (!is_null($xml) && $restclient->getStatusCode() == 200)
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
