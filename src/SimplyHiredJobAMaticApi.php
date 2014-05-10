<?php

/**
 * SimplyHiredJobAMaticApi class
 * 
 * A PHP wrapper for SimplyHired's Job-a-matic API
 */
class SimplyHiredJobAMaticApi {

    /**
     * The Publisher ID
     * 
     * @var string 
     */
    protected $publisher_id;

    /**
     * The Domain of the Job-a-matic API
     * 
     * @var string
     */
    protected $domain;

    /**
     *  The Object in charge of fetching the XML response from the API endpoint
     * 
     * @var InterfaceSimplyHiredJobAMaticApiHttp 
     */
    protected $http;

    /**
     * Search Style
     * A required search configuration value
     * 
     * @var string 
     */
    protected $search_style = '2';

    /**
     * Configuration Flag
     * A required search configuration value
     * 
     * @var string 
     */
    protected $configuration_flag = 'r';

    /**
     * Client IP
     * A required value, which contains the IP address of the visitor
     * 
     * @var string
     */
    protected $client_ip;

    /**
     * Description Fragment
     * An optional value, which is used to indicate if you choose to show
     * clipped or full job description. By default, you will get clipped job
     * description, unless the value is set to false or 0.
     * 
     * @var string 
     */
    protected $description_fragment = '0';

    /**
     * A lookup array for mapping search variables to their corresponding
     * variable names according to the API
     * 
     * @var array 
     */
    protected $search_settings_map = array(
        'query' => 'q',
        'location' => 'l',
        'miles' => 'mi',
        'sort' => 'sb',
        'window_size' => 'ws',
        'page_number' => 'pn',
        'job_source_type' => 'fsr',
    );

    /**
     * The array that holds the search parameters and options set
     * 
     * @var string
     */
    public $search_settings = array();

    /**
     * An array holding the available search options
     * 
     * @var array 
     */
    protected $sort_options = array(
        'rd', // relevance descending (default)
        'ra', // relevance ascending
        'dd', // last seen date descending
        'da', // last seen date ascending
        'td', // title descending
        'ta', // title ascending
        'cd', // company descending
        'ca', // company ascending
        'ld', // location descending
        'la', // location ascending
    );

    /**
     * An array holding the available job source types
     * @var array
     */
    protected $job_source_type_options = array(
        'primary', // exclude job boards
        'job_board', // job boards only
    );

    /**
     * The endpoint for the API calls
     * @var string
     */
    protected $end_point = 'http://api.simplyhired.com/a/jobs-api/xml-v2';

    /**
     * Constructor
     * 
     * @param string $publisher_id
     * @param string $domain
     * @param InterfaceSimplyHiredJobAMaticApiHttp $http
     * @throws Exception
     */
    public function __construct($publisher_id, $domain, InterfaceSimplyHiredJobAMaticApiHttp $http) {
        if (trim($publisher_id) == '' || is_null($publisher_id)) {
            throw new Exception('The Publisher ID is required');
        } else {
            $this->publisher_id = $publisher_id;
        }

        if (trim($domain) == '' || is_null($domain)) {
            throw new Exception('The Job-a-matic Domain is required');
        } else {
            $this->domain = $domain;
        }
        $this->http = $http;
    }

    /**
     * Sets the search query
     * 
     * Examples search criterias:
     *      AND - Match all of the terms connected by AND. The default connector for search terms (case sensitive)
     *      OR - Match at least one of the terms connected by OR (case sensitive)
     *      NOT - Exclude matches on these terms (case sensitive)
     *      ( ) - Group order of operations
     * 
     * The XML API supports basic Boolean searches as follows. The XML API also
     * supports these job-related search requests:
     *      All the words: Engineering AND Manager
     *      Exact phrase: "Engineering Manager"
     *      At least one of the words: Engineering OR Manager
     *      Without words: Engineering NOT Manager
     *      Job title: title:(Engineering Manager)
     *      Company name: company:(Apple)
     *      Occupation onet: onet:(13-205*) - Based on O*net, the U.S. Government's Occupation Classification System
     * 
     * @param string $query
     * @return \SimplyHiredJobAMaticApi
     */
    public function setQuery($query) {
        $this->search_settings['query'] = $query;
        return $this;
    }

    /**
     * Sets the location
     * Location can be a zipcode, state, or city-state combination. Currently,
     * there is no support for multiple location search.
     * 
     * @param string $location
     * @return \SimplyHiredJobAMaticApi
     */
    public function setLocation($location) {
        $this->search_settings['location'] = $location;
        return $this;
    }

    /**
     * Sets miles to "mi-exact" for jobs only within a specific city
     * 
     * @return \SimplyHiredJobAMaticApi
     */
    public function setLocationExact() {
        $this->search_settings['miles'] = 'mi-exact';
        return $this;
    }

    /**
     * Sets the miles parameter indicating the number of miles from the
     * location.
     * Miles value should be a number from "1" to "100". Miles represents the
     * radius from the zip code, if specified in Location, or an approximate
     * geographical "city center" if only city and state are present. If Miles
     * is not specified, search will default to a radius of 25 miles.
     * 
     * @param integer $miles
     * @return \SimplyHiredJobAMaticApi
     */
    public function setMiles($miles) {
        $miles = ((int) $miles <= 100 && (int) $miles >= 1) ? (int) $miles : 25;
        $this->search_settings['miles'] = $miles;
        return $this;
    }

    /**
     * Sets the sort the sort order of organic jobs (sponsored jobs have a
     * fixed sort order)
     * Valid values include:
     *      rd = relevance descending (default)
     *      ra = relevance ascending
     *      dd = last seen date descending
     *      da = last seen date ascending
     *      td = title descending
     *      ta = title ascending
     *      cd = company descending
     *      ca = company ascending
     *      ld = location descending
     *      la = location ascending
     *
     * 
     * @param string $sort
     * @return \SimplyHiredJobAMaticApi
     */
    public function setSort($sort) {
        $sort = in_array($sort, $this->sort_options) ? $sort : 'rd';
        $this->search_settings['sort'] = $sort;
        return $this;
    }

    /**
     * Helper method for setting the sort parameter by revalance
     * 
     * @see setSort()
     * @param string $direction
     * @return \SimplyHiredJobAMaticApi
     */
    public function setSortByRelevance($direction = 'desc') {
        $sort = ($direction == 'asc') ? 'ra' : 'rd';
        $this->setSort($sort);
        return $this;
    }

    /**
     * Helper method for setting the sort parameter by last seen date
     * 
     * @see setSort()
     * @param string $direction
     * @return \SimplyHiredJobAMaticApi
     */
    public function setSortByLastSeenDate($direction = 'desc') {
        $sort = ($direction == 'asc') ? 'da' : 'dd';
        $this->setSort($sort);
        return $this;
    }

    /**
     * Helper method for setting the sort parameter by title
     * 
     * @see setSort()
     * @param string $direction
     * @return \SimplyHiredJobAMaticApi
     */
    public function setSortByTitle($direction = 'desc') {
        $sort = ($direction == 'asc') ? 'ta' : 'td';
        $this->setSort($sort);
        return $this;
    }

    /**
     * Helper method for setting the sort parameter by company
     * 
     * @see setSort()
     * @param string $direction
     * @return \SimplyHiredJobAMaticApi
     */
    public function setSortByCompany($direction = 'desc') {
        $sort = ($direction == 'asc') ? 'ca' : 'cd';
        $this->setSort($sort);
        return $this;
    }

    /**
     * Helper method for setting the sort parameter by location
     * 
     * @see setSort()
     * @param string $direction
     * @return \SimplyHiredJobAMaticApi
     */
    public function setSortByLocation($direction = 'desc') {
        $sort = ($direction == 'asc') ? 'la' : 'ld';
        $this->setSort($sort);
        return $this;
    }

    /**
     * Sets the Window Size
     * An integer representing the number of results returned. When available,
     * the API will return 10 jobs by default. The API is limited to a maximum
     * of 100 results per request.
     * 
     * @param integer $window_size
     * @return \SimplyHiredJobAMaticApi
     */
    public function setWindowSize($window_size) {
        $window_size = ((int) $window_size <= 100 && (int) $window_size >= 1) ? (int) $window_size : 10;
        $this->search_settings['window_size'] = $window_size;
        return $this;
    }

    /**
     * Sets the Page Number
     * An integer representing the page number of the results returned.
     * 
     * @param integer $page_number
     * @return \SimplyHiredJobAMaticApi
     */
    public function setPageNumber($page_number) {
        $this->search_settings['page_number'] = (int) $page_number;
        return $this;
    }

    /**
     * Sets the Job Source
     * A parameter indicating the job source type.
     * Valid values include:
     *      primary = exclude job boards
     *      job_board = job boards only
     * 
     * @param string $job_source_type
     * @return \SimplyHiredJobAMaticApi
     */
    public function setJobSourceType($job_source_type) {
        $job_source_type = in_array($job_source_type, $this->job_source_type_options) ? $job_source_type : null;
        $this->search_settings['job_source_type'] = $job_source_type;
        return $this;
    }

    /**
     * Sets the Description Fragment
     * An optional value, which is used to indicate if you choose to show
     * clipped or full job description. By default, you will get clipped job
     * description.
     * 
     * @param boolean $clip_description
     * @return \SimplyHiredJobAMaticApi
     */
    public function setFragmentDescription($clip_description = 0) {
        $this->description_fragment = (int) $clip_description;
        return $this;
    }

    /**
     * Performs the request
     * builds the endpoint for the given class search settings and uses the
     * http object to perform a GET on the url
     * 
     * @return \SimplyHiredJobAMaticApi_Results
     * @throws Exception
     */
    public function request() {
        $url = $this->_buildUrl();
        $raw_response = $this->http->get($url);
        $response = $this->_parseXmlResults($raw_response);

        if ($response instanceof SimplyHiredJobAMaticApi_Results) {
            return $response;
        } elseif (is_array($response)) {
            throw new Exception('API Error code: "' . $response['code'] . '" with message:"' . $response['type'] . '"');
        } else {
            throw new Exception('XML Error');
        }
    }

    /**
     * Helper method for converting this object to an array
     * 
     * @return array
     */
    public function toArray() {
        $properties = array(
            'publisher_id' => $this->publisher_id,
            'domain' => $this->domain,
            'search_style' => $this->search_style,
            'configuration_flag' => $this->configuration_flag,
            'client_ip' => $this->client_ip,
        );
        return array_merge($this->search_settings, $properties);
    }

    /**
     * Builds an endpoint using the given search settings
     * 
     * @param array $params
     * @return string
     */
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

    /**
     * Builds the SimplyHiredJobAMaticApi_Results object using the XML response
     * 
     * @param string $xml_string
     * @return \SimplyHiredJobAMaticApi_Results
     */
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

    /**
     * Helper method for getting the remote ip and allowing the class to work
     * via the commandline
     * 
     * @return string
     */
    protected function _getRemoteIp() {
        if (!$this->client_ip && (php_sapi_name() == 'cli' || !isset($_SERVER['REMOTE_ADDR']))) {
            $host = gethostname();
            $this->client_ip = gethostbyname($host);
        } elseif (!$this->client_ip && isset($_SERVER['REMOTE_ADDR'])) {
            $this->client_ip = $_SERVER['REMOTE_ADDR'];
        }
        return $this->client_ip;
    }

    /**
     * Converts the search settings into a path as per the API's documentation
     * 
     * @return string
     */
    protected function _buildSearchPath() {
        $path = '/';
        foreach ($this->search_settings_map as $search_property => $param) {
            $val = (array_key_exists($search_property, $this->search_settings)) ? $this->search_settings[$search_property] : null;
            if (!is_null($val)) {
                $path .= $param . '-' . urlencode($val) . '/';
            }
        }
        return $path;
    }

}
