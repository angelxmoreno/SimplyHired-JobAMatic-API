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
require_once dirname(__FILE__) . '/src/http/CurlHttp.php';

//create the Http obj
$http1 = new FileGetContentsHttp();

$publisher_id = '28086';
$domain = 'cakephp-jobs.jobamatic.com';

$JobAMaticApi1 = new SimplyHiredJobAMaticApi($publisher_id, $domain, $http1);
$jobResults1 = $JobAMaticApi1
	->setQuery('Ruby OR Rails')
	->setLocation('San Francisco')
	->setMiles(15)
	->setSortByLastSeenDate('desc')
	->setWindowSize(50)
	->setFragmentDescription(false)
	->request();
print_r($jobResults1->toArray());
/*********************************************************************************/
$http2 = new CurlHttp();

$JobAMaticApi2 = new SimplyHiredJobAMaticApi($publisher_id, $domain, $http1);
$jobResults2 = $JobAMaticApi2
	->setQuery('Wordpress')
	->setLocation('10001')
	->setLocationExact()
	->setSortByLastSeenDate('desc')
	->setWindowSize(100)
	->setFragmentDescription(true)
	->request();
print_r($jobResults2->toArray());