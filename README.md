SimplyHired-JobAMatic-API
=========================

PHP wrapper for Simplyhired's Job-a-matic API

To Dos
==
1. Finish adding Doc blocka to all classes and methods
1. Create Tests
1. Create composer file

Examples
==
First included the need files

```php
require_once dirname(__FILE__) . '/src/abstract/InterfaceSimplyHiredJobAMaticApiHttp.php';
require_once dirname(__FILE__) . '/src/abstract/AbstractSimplyHiredJobAMaticApi.php';
require_once dirname(__FILE__) . '/src/SimplyHiredJobAMaticApi.php';
require_once dirname(__FILE__) . '/src/SimplyHiredJobAMaticApi/SimplyHiredJobAMaticApi_Results.php';
require_once dirname(__FILE__) . '/src/SimplyHiredJobAMaticApi/SimplyHiredJobAMaticApi_JobsCollection.php';
require_once dirname(__FILE__) . '/src/SimplyHiredJobAMaticApi/SimplyHiredJobAMaticApi_Job.php';

//include one of the Http Classes
require_once dirname(__FILE__) . '/src/http/FileGetContentsHttp.php';

//OR

require_once dirname(__FILE__) . '/src/http/CurlHttp.php';
```

Example 1:

```php
//create the Http obj
$http1 = new FileGetContentsHttp();

$publisher_id = '28086';
$domain = 'cakephp-jobs.jobamatic.com';

$JobAMaticApi1 = new SimplyHiredJobAMaticApi($publisher_id, $domain, $http1);
/*
 * Search for jobs that mention "Ruby" or "Rails" within a 15 mile radius of
 * the city of San Francisco. Return the first 50 jobs and sort by the date the
 * job was last updated in descending order. Show the full job description for
 * each job.
 */
$jobResults1 = $JobAMaticApi1
	->setQuery('Ruby OR Rails')
	->setLocation('San Francisco')
	->setMiles(15)
	->setSortByLastSeenDate('desc')
	->setWindowSize(50)
	->setFragmentDescription(false)
	->request();
print_r($jobResults1->toArray());
```

Example 2:

```php
//create the Http obj
$http2 = new CurlHttp();

$JobAMaticApi2 = new SimplyHiredJobAMaticApi($publisher_id, $domain, $http1);
/*
 * Search for jobs that mention "Wordpress" only within the zip code 10001.
 * Return the first 100 jobs and sort by revalance in descending order. Show the
 * full description of each job.
 */
$jobResults2 = $JobAMaticApi2
	->setQuery('Wordpress')
	->setLocation('10001')
	->setLocationExact()
	->setSortByRelevance()
	->setWindowSize(100)
	->setFragmentDescription(true)
	->request();
print_r($jobResults2->toArray());
```

