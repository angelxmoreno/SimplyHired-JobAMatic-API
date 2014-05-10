<?php
//include needed files
require_once dirname(__FILE__) . '/src/abstract/InterfaceSimplyHiredJobAMaticApiHttp.php';
require_once dirname(__FILE__) . '/src/abstract/AbstractSimplyHiredJobAMaticApi.php';
require_once dirname(__FILE__) . '/src/SimplyHiredJobAMaticApi.php';
require_once dirname(__FILE__) . '/src/SimplyHiredJobAMaticApi/SimplyHiredJobAMaticApi_Results.php';
require_once dirname(__FILE__) . '/src/SimplyHiredJobAMaticApi/SimplyHiredJobAMaticApi_JobsCollection.php';
require_once dirname(__FILE__) . '/src/SimplyHiredJobAMaticApi/SimplyHiredJobAMaticApi_Job.php';

//include one of the Http Classes
require_once dirname(__FILE__) . '/src/http/FileGetContentsHttp.php';

//create the Http obj
$http = new FileGetContentsHttp();

$publisher_id = '28086';
$domain = 'cakephp-jobs.jobamatic.com';

$JobAMaticApi = new SimplyHiredJobAMaticApi($publisher_id, $domain, $http);
$jobResults = $JobAMaticApi
	->setQuery('ebay store')
	->setLocation('10001')
	->setMiles(1)
	->setSortByLastSeenDate('desc')
	->setWindowSize(100)
	->request();
var_dump($jobResults->toArray());