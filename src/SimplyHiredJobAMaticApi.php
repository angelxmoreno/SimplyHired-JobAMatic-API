<?php

class SimplyHiredJobAMaticApi {

    protected $publisher_id;
    protected $domain;
    protected $http;

    protected $search_style = '2';
    protected $configuration_flag = 'r';
    protected $client_ip;
    protected $description_fragment = '0';

    protected $search_settings_map = array(
        'query' => 'q',
        'location' => 'l',
        'miles' => 'mi',
        'sort' => 'sb',
        'window_size' => 'ws',
        'page_number' => 'pn',
        'job_source_type' => 'fsr',
    );

    public $search_settings = array();
    protected $sort_options = array(
        'rd',// relevance descending (default)
        'ra',// relevance ascending
        'dd',// last seen date descending
        'da',// last seen date ascending
        'td',// title descending
        'ta',// title ascending
        'cd',// company descending
        'ca',// company ascending
        'ld',// location descending
        'la',// location ascending
    );
    protected $job_source_type_options = array(
        'primary',// exclude job boards
        'job_board',// job boards only
    );


    protected $end_point = 'http://api.simplyhired.com/a/jobs-api/xml-v2';

    function __construct($publisher_id, $domain, InterfaceSimplyHiredJobAMaticApiHttp $http) {
        if(trim($publisher_id) == '' || is_null($publisher_id)){
            throw new Exception('The Publisher ID is required');
        } else {
            $this->publisher_id = $publisher_id;
        }

        if(trim($domain) == '' || is_null($domain)){
            throw new Exception('The Job-a-matic Domain is required');
        } else {
            $this->domain = $domain;
        }
        $this->http = $http;
    }

    public function setQuery($query){
        $this->search_settings['query'] = $query;
        return $this;
    }

    public function setLocation($location){
        $this->search_settings['location'] = $location;
        return $this;
    }

    public function setLocationExact(){
        $this->search_settings['miles'] = 'mi-exact';
        return $this;
    }

    public function setMiles($miles){
        $miles = ((int)$miles <= 100 && (int)$miles >= 1) ? (int)$miles : 25;
        $this->search_settings['miles'] = $miles;
        return $this;
    }

    public function setSort($sort){
        $sort = in_array($sort, $this->sort_options) ? $sort : 'rd';
        $this->search_settings['sort'] = $sort;
        return $this;
    }

    public function setSortByRelevance($direction = 'desc'){
        $sort = ($direction == 'asc') ? 'ra' : 'rd';
        $this->setSort($sort);
        return $this;
    }

    public function setSortByLastSeenDate($direction = 'desc'){
        $sort = ($direction == 'asc') ? 'da' : 'dd';
        $this->setSort($sort);
        return $this;
    }

    public function setSortByTitle ($direction = 'desc'){
        $sort = ($direction == 'asc') ? 'ta' : 'td';
        $this->setSort($sort);
        return $this;
    }

    public function setSortByCompany($direction = 'desc'){
        $sort = ($direction == 'asc') ? 'ca' : 'cd';
        $this->setSort($sort);
        return $this;
    }

    public function setSortByLocation($direction = 'desc'){
        $sort = ($direction == 'asc') ? 'la' : 'ld';
        $this->setSort($sort);
        return $this;
    }

    public function setWindowSize($window_size){
        $window_size = ((int)$window_size <= 100 && (int)$window_size >= 1) ? (int)$window_size : 10;
        $this->search_settings['window_size'] = $window_size;
        return $this;
    }

    public function setPageNumber($page_number){
        $this->search_settings['page_number'] = (int)$page_number;
        return $this;
    }

    public function setJobSourceType($job_source_type){
        $job_source_type = in_array($job_source_type, $this->job_source_type_options) ? $job_source_type : null;
        $this->search_settings['job_source_type'] = $job_source_type;
        return $this;
    }

    public function setFragmentDescription($clip_description = 0){
        $this->description_fragment = (int)$clip_description;
        return $this;
    }

    public function request(){
        $url = $this->_buildUrl();
        $raw_response = $this->http->get($url);
        $response = $this->_parseXmlResults($raw_response);

        if($response instanceof SimplyHiredJobAMaticApi_Results) {
            return $response;
        } elseif(is_array($response)){
            throw new Exception('API Error code: "' . $response['code'] . '" with message:"'. $response['type'].'"');
        } else {
            throw new Exception('XML Error');
        }
    }

    public function toArray(){
        $properties = array(
            'publisher_id' => $this->publisher_id,
            'domain' => $this->domain,
            'search_style' => $this->search_style,
            'configuration_flag' => $this->configuration_flag,
            'client_ip' => $this->client_ip,
        );
        return array_merge($this->search_settings, $properties);
    }

    protected function _buildUrl(array $params = array()) {
        $get_params = array(
            'pshid' => $this->publisher_id,
            'jbd' => $this->domain,
            'ssty' => $this->search_style,
            'cflg' => $this->configuration_flag,
            'clip' => $this->_getRemoteIp(),
        );

        $url = $this->end_point . $this->_buildSearchPath() . '?' . http_build_query($get_params);
        return $url;
    }

    protected function _parseXmlResults($xml_string) {
        $xml = simplexml_load_string($xml_string);
        //check for errors
        $error['type'] = (string) $xml->error['type'];
        $error['code'] = (string) $xml->error['code'];
        if ($error['type'] <> '') {
            return $error;
        }
        return $resultsObj = new SimplyHiredJobAMaticApi_Results($xml, $this);
    }

    protected function _getRemoteIp() {
        if(!$this->client_ip && (php_sapi_name() == 'cli' || !isset($_SERVER['REMOTE_ADDR'])) ) {
            $host = gethostname();
            $this->client_ip = gethostbyname($host);
        } elseif(!$this->client_ip && isset($_SERVER['REMOTE_ADDR'])) {
            $this->client_ip = $_SERVER['REMOTE_ADDR'];
        }
        return $this->client_ip;
    }
    
    protected function _buildSearchPath(){
        $path = '/';
        foreach($this->search_settings_map as $search_property => $param){
            $val = (array_key_exists($search_property, $this->search_settings)) ? $this->search_settings[$search_property] : null;
            if(!is_null($val)){
                $path .= $param . '-' . urlencode($val) . '/';
            }
        }
        return $path;
    }

}
