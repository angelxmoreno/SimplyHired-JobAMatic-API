<?php
class SimplyHiredJobAMaticApi_Results extends AbstractSimplyHiredJobAMaticApi {

    protected $request_url;
    protected $request_title;
    protected $request_time;
    protected $start_index;
    protected $num_results;
    protected $total_results;
    protected $total_viewable_results;
    protected $jobs_collection;

    function __construct(SimpleXMLElement $xml_response) {
        $this->request_url = (string) $xml_response->rq['url'];
        $this->request_title = (string) $xml_response->rq->t;
        $this->request_time = $this->parseDate($xml_response->rq->dt);
        $this->start_index = (int) $xml_response->rq->si;
        $this->num_results = (int) $xml_response->rq->rpd;
        $this->total_results = (int) $xml_response->rq->tr;
        $this->total_viewable_results = (int) $xml_response->rq->tv;
        $results_set = $xml_response->rs->r;
        //print_r($results_set);
        //die;
        $this->jobs_collection = new SimplyHiredJobAMaticApi_JobsCollection();
        foreach ($results_set as $job_xml) {
            $this->jobs_collection->add($job_xml);
        }
    }

}