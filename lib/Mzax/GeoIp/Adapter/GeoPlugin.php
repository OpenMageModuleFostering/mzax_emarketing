<?php


/**
 * 
 * 
 * 
 * @link http://www.geoplugin.com
 * @link http://www.geoplugin.com/webservices/json
 * 
 * @author Jacob Siefer
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version 0.2.5
 */
class Mzax_GeoIp_Adapter_GeoPlugin extends Mzax_GeoIp_Adapter_Abstract
{
    const API_URI = 'http://www.geoplugin.net/json.gp?ip={IP}';

    public $requestLimit = 80;
    
    
    
    /**
     * Retrieve the name of this adapter
     *
     * @return string
     */
    public function getName()
    {
        return 'geoPlugin.net';
    }
    
    
    public function getCredits()
    {
        return '<a href="http://www.geoplugin.com"><strong>geoPlugin</strong></a> - this product includes GeoLite data created by MaxMind, available from <a href="http://www.maxmind.com">http://www.maxmind.com</a>.';
    }
    
    
    protected function _fetch(Mzax_GeoIp_Request $request)
    {
        $uri = str_replace('{IP}', $request->ip, self::API_URI);
        $client = new Zend_Http_Client($uri);
        
        try {
            $response = $client->request();
            $request->httpResponse = $response;
            
            $data = Zend_Json::decode($response->getBody());
            if(isset($data['geoplugin_city'])) {
                $request->city = html_entity_decode($data['geoplugin_city'], ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
            }
            if(isset($data['geoplugin_region'])) {                
                $request->region = html_entity_decode($data['geoplugin_region'], ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
            }
            if(isset($data['geoplugin_countryCode'])) {
                $request->countryId = $data['geoplugin_countryCode'];
            }
            if(isset($data['geoplugin_countryName'])) {
                $request->country = $data['geoplugin_countryName'];
            }
            if(isset($data['geoplugin_longitude']) && isset($data['geoplugin_latitude'])) {
                $request->loc = array($data['geoplugin_longitude'], $data['geoplugin_latitude']);
            }
            
            // geoplugin_regionCode 01?
        }
        catch(Zend_Json_Exception $e) {
            $this->easeTillNextDay();
            throw $e;
        }
    }
    
    
    
    

}

