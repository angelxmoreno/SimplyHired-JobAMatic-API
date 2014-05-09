<?php

class SimplyHiredJobAMaticApi extends AbstractSimplyHiredJobAMaticApi{

    protected $publisher_id;
    protected $domain;
    protected $query;
    protected $location;
    protected $miles;
    protected $sort_by;
    protected $window_size;
    protected $page_number;
    protected $source;
    protected $search_style = '2';
    protected $configuration_flag = 'r';
    protected $client_ip;
    protected $description_fragment = '0';
    protected $end_point = 'http://api.simplyhired.com/a/jobs-api/xml-v2';

    function __construct($publisher_id, $domain = null) {
        if (is_array($publisher_id) && is_null($domain)) {
            $this->setVarsByArray($publisher_id);
        } elseif (is_string($publisher_id) && is_string($domain)) {
            $this->publisher_id = $publisher_id;
            $this->domain = $domain;
        } elseif (!is_string($publisher_id) || !is_string($domain)) {
            throw new Exception('Publisher ID and Job-a-matic Domain are required');
        }
    }

    protected function setVarsByArray(array $settings) {
        $class_vars = get_object_vars($this);
        foreach ($settings as $key => $val) {
            if (array_key_exists($key, $class_vars)) {
                $this->{$key} = $val;
            }
        }
    }

    protected function getRemoteIp() {
        if(!$this->client_ip && (php_sapi_name() == 'cli' || !isset($_SERVER['REMOTE_ADDR']) ) {
            $host = gethostname();
            $this->client_ip = gethostbyname($host);
        } elseif(!$this->client_ip && isset($_SERVER['REMOTE_ADDR'])) {
            $this->client_ip = $_SERVER['REMOTE_ADDR'];
        }
        return $this->client_ip;
    }

    public function buildUrl(array $params = array()) {
        $get_params = array(
            'pshid' => $this->publisher_id,
            'jbd' => $this->domain,
            'ssty' => $this->search_style,
            'cflg' => $this->configuration_flag,
            'clip' => $this->getRemoteIp(),
        );
        $params = array_merge(array(
            'q' => 'title:Senior AND PHP NOT Drupal',
            'ws' => '3',
            'pn' => 3,
        ), $params);

        $url = $this->end_point . $this->_buildPathParams($params) . '?' . http_build_query($get_params);
        return $url;
    }

    public function parseXmlResults($xml_string) {
        $xml = simplexml_load_string($xml_string);
        //check for errors
        $error['type'] = (string) $xml->error['type'];
        $error['code'] = (string) $xml->error['code'];
        if ($error['type'] <> '') {
            return $error;
        }

        return $resultsObj = new SimplyHiredJobAMaticApi_Results($xml);
    }
    
    protected function _buildPathParams(array $params){
        $path = '';
        foreach($params as $key => $val){
            $path .= '/' . $key . '-' . urlencode($val);
        }
        return $path;
    }

}
