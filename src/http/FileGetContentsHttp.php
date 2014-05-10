<?php
class FileGetContentsHttp implements InterfaceSimplyHiredJobAMaticApiHttp {
	public function get($url){
		return file_get_contents($url);
	}
}