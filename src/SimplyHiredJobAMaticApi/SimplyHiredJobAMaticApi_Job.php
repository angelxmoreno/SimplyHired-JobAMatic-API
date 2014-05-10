<?php
class SimplyHiredJobAMaticApi_Job extends AbstractSimplyHiredJobAMaticApi {
    protected $job_key;
    protected $title;
    protected $company_name;
    protected $company_url;
    protected $source;
    protected $url;
    protected $type;
    protected $is_paid = false;
    protected $is_sponsored = false;
    protected $is_organic = false;
    protected $location;
    protected $city;
    protected $state;
    protected $postcode;
    protected $county;
    protected $region;
    protected $country;
    protected $date_last_seen;
    protected $date_that_posted;
    protected $description;

    function __construct(SimpleXMLElement $xml_response) {
        $this->title = (string) $xml_response->jt;
        $this->company_name = (string) $xml_response->cn;
        $this->company_url = (string) $xml_response->cn['url'];
        $this->source = (string) $xml_response->src;
        $this->url = (string) $xml_response->src['url'];
        $this->job_key = $this->_getJobKeyFromUrl();
        $this->type = (string) $xml_response->ty;
        switch ($this->type) {
            case 'sponsored':
                $this->is_sponsored = true;
                break;
            case 'organic':
                $this->is_organic = true;
                break;    
            default:
                die("\n unknown type: '{$this->type}' for '{$this->title}'");
        }

        $this->location = (string) $xml_response->loc;
        $this->city = (string) $xml_response->loc['cty'];
        $this->state = (string) $xml_response->loc['st'];
        $this->postcode = (string) $xml_response->loc['postal'];
        $this->county = (string) $xml_response->loc['county'];
        $this->region = (string) $xml_response->loc['region'];
        $this->country = (string) $xml_response->loc['country'];
        $this->date_last_seen = $this->parseDate($xml_response->ls);
        $this->date_that_posted = $this->parseDate($xml_response->dp);
        $this->description = (string) $xml_response->e;
    }
    
    protected function _getJobKeyFromUrl(){
        $pattern = '#/jobkey-([^/]+)/#i';
        if(preg_match($pattern, $this->url, $matches)){
            return $matches[1];
        }
        return null;
    }

}