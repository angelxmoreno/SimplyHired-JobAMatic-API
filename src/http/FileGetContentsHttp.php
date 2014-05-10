<?php
class FileGetContentsHttp implements InterfaceSimplyHiredJobAMaticApiHttp {
	public function get($url){
		$response =  file_get_contents($url);

		return $response;
	}
}