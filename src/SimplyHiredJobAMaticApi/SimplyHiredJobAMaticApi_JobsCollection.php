<?php
class SimplyHiredJobAMaticApi_JobsCollection extends AbstractSimplyHiredJobAMaticApi {
    protected $jobs = array();
    public function add($job_xml){
        $this->jobs[] = new SimplyHiredJobAMaticApi_Job($job_xml);
    }
    public function toArray() {
        $jobs = array();
        foreach($this->jobs as $jobObj){
            $jobs[] = $jobObj->toArray();
        }
        return $jobs;
    }
}