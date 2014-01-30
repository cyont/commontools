<?php

class EDS{
	private $eds_user = 'sunion-mealplans';
	private $eds_pass = 'JLnzW9RyWJmWexnpuTwHaZ2BU04gZcs9';
	private $cred;
   	private $opts;
    private $ctx;
	private $url;
	public $vals;
	
	function __construct($id=NULL){
		$this->cred = sprintf('Authorization: Basic %s', base64_encode($this->eds_user.':'.$this->eds_pass));
        $this->opts = array( 'http' => array ('method'=>'GET', 'header'=>$this->cred));
        $this->ctx = stream_context_create($this->opts);
		if(!empty($id))
			return $this->getRecordFromId($id);
	}
	/**
	 * @return boolean
	 */
	function getRecordFromId($id){
		$this->url = 'https://eds.arizona.edu/people/'.$id;

        // send our request and retrieve the DSML response
        $dsml = file_get_contents($this->url,false,$this->ctx);
		
		$xmlread = new XMLReader();
		$xmlread->xml($dsml);
		$xmlread->read();
		$xmlread->read();
		$xmlread->read();
		if($xmlread->isEmptyElement)
			return false;
		$xmlread->read();
		$xmlread->read();
		$xmlread->read();
		$xmlread->read();
		while(1){
			if($xmlread->localName == 'attr'){
				$name = $xmlread->getAttribute('name');
				$xmlread->read();
				
			}
			elseif($xmlread->localName == 'value'){
				$this->vals[$name][] = $xmlread->readInnerXML();
				$xmlread->read();
				$xmlread->read();
				$xmlread->read();
			}
			
			
			if(!$xmlread->read())
				break;
		}
		foreach($this->vals as $key => $values){
			if(count($values) == 1)
				$this->vals[$key] = $values[0];
		}
		return true;
	}
	function getValueforKey($key){
		return $this->vals[$key];
	}
	function hasKey($key){
		return array_key_exists($key, $this->vals);
	}
}
