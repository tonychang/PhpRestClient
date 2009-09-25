<?php
//Date: 9/24/2009 
//Copyright (c) 2009
//by University of Washington

// Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated 
// documentation files (the "Software"), to deal in the Software without restriction, including without limitation 
// the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and 
// to permit persons to whom the Software is furnished to do so, subject to the following conditions:
//
// The above copyright notice and this permission notice shall be included in all copies or substantial portions 
// of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED 
// TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL 
// THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF 
// CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER 
// DEALINGS IN THE SOFTWARE.
class SimpleRestClient
{
    protected $_cert_file=null; 
    protected $_key_file=null;
    protected $_user_agent="PhpRestClient";
    protected $_http_status=null;
    protected $_response=null;
    
    public function __construct($cert_file=null,$key_file=null,$user_agent="PhpRestClient")
    {
        $this->_cert_file = $cert_file;
        $this->_key_file = $key_file;
        $this->_user_agent = $user_agent;
    } 
    
    public function setCertFile($cert_file)
    {
        $this->_cert_file = $cert_file;
    }
    
    public function setKeyFile($key_file)
    {
        $this->_key_file = $key_file;
    }
    
    public function setUserAgent($user_agent)
    {
        $this->_user_agent = $user_agent;
    }
    
    public function getWebResponse()
    {
        return $this->_response;
    }
    
    public function getStatusCode()
    {
        return $this->_http_status;
    }

    public function makeWebRequest($url, $public=TRUE)
    {
        $_raw_data=null;
        $c = curl_init($url); // $url is the resource we're fetching
        
        //If this is a private protected resource then add the cert and key to the curl request
        if ($public == FALSE && !is_null($this->_cert_file) && !is_null($this->_key_file))
        {
            curl_setopt($c,CURLOPT_SSLCERT, $this->_cert_file);   // Full path to cert
            curl_setopt($c,CURLOPT_SSLKEY,  $this->_key_file);    // Full path to private key   
        }
        curl_setopt($c,CURLOPT_RETURNTRANSFER, true);  // return result instead of echoing
        curl_setopt($c,CURLOPT_SSL_VERIFYPEER, false);  // Make sure CA is known and cert not expired
        curl_setopt($c,CURLOPT_USERAGENT, $this->_user_agent); //Add an user agent so the webservice knows who is making the request
        $_raw_data = curl_exec($c);
                if (curl_errno($c) != 0) {
                   echo ('Aborting. cURL error: ' . curl_error($c));
                   exit(-1);
                }
        $this->_response=str_replace("xmlns=","a=",$_raw_data); //Getting rid of xmlns so that clients can use SimpleXML and XPath without problems otherwise SimpleXML does not recognize the document as an XML document
        $this->_http_status = curl_getinfo($c, CURLINFO_HTTP_CODE); //Capture the status code
        
        curl_close($c);   
    }
}


?>