<?php
namespace app\models;
use Yii;

class ServiceKannel 
{
	private $xml;
	private $smscs;
	
	public function __construct()
	{
		$this->init();
	}

	public function init() {
		$url = Yii::$app->params['url_kannel'];
		
        $ch = curl_init();

        // define options
        $optArray = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true
        );

        // apply those options
        curl_setopt_array($ch, $optArray);

        // execute request and get response
        $response = curl_exec($ch);

		$this->xml=simplexml_load_string($response);

		$this->getStatusDevices();
	}

	public function getAllXml()
	{
		return $this->xml;	
	}

	public function reloadNewData() {
		$this->init();
	}

	public function checkIsOkToGhiveThemByRules($ordered, $array_keys) {
		foreach($ordered as $key => $each) {
			if(in_array($each['smsc_id'], $array_keys)) {
				return $each['smsc_id'];
			}
		}

		return false;
	}

	public function getVersion() {
		return $this->xml->version;
	}
	public function getStatus() {
		if(!empty($this->xml->status)) {
			return $this->xml->status;	
		}

		return false;
	}

	public function getStatusSms() {
		return $this->xml->sms;
	}

	public function getDlr() {
		return $this->xml->dlr;
	}
	/**
	 * 
	 * @param string $code - daca e nevoie se specifica un device anume 
	 * @return un array de device, daca este specificat si exista returneaza un array cu 
	 * informatii legat de device , in caz contrar daca nu exista returneaza false 
	 */
	public function getStatusDevices($code = NULL)
	{
		if(empty($this->xml)) {
			return false;
		}

		$smscObject=$this->xml->smscs;	

		
		$smscCount=$smscObject->count;
		
		$smscInnerObject = $smscObject->smsc;
		
		$smscInfo = array();
		$varadmin_id='admin-id';
		
		for($i=0; $i < $smscCount; $i++)
		{
			$device = (string)$smscInnerObject[$i]->id;
			$smscInfo[$device]['smsc_name']=(string) $smscInnerObject[$i]->name;
			$smscInfo[$device]['smsc_admin_id']=(string)$smscInnerObject[$i]->$varadmin_id;
			$smscInfo[$device]['smsc_id']=(string)$smscInnerObject[$i]->id;
			$smscInfo[$device]['smsc_status']=(string)$smscInnerObject[$i]->status;
			$smscInfo[$device]['smsc_failed']=(string)$smscInnerObject[$i]->failed;
			$smscInfo[$device]['smsc_queued']=(string)$smscInnerObject[$i]->queued;
			$smscInfo[$device]['sms']=(array)$smscInnerObject[$i]->sms;
			$smscInfo[$device]['dlr']=(array)$smscInnerObject[$i]->sms;
		}

		$this->smscs = $smscInfo;

		if(!  is_null($code))
		{
			return isset($smscInfo[$code]) ? $smscInfo[$code] : FALSE;
		} else {
			return $smscInfo;
		}
	}

	public function orderByLowQueue() {
		$ordered = array();
		
		// Obtain a list of columns
		foreach ($this->smscs as $key => $row) {
			$smsc_queued[$key]  = $row['sms']['sent']; // sau $row['smsc_queued']
		    $echipamente[$key] = $row['smsc_id'];
		}

		
		// Sort the data with volume descending, edition ascending
		// Add $data as the last parameter, to sort by the common key
		array_multisort($smsc_queued, SORT_ASC);

		foreach($smsc_queued as $key => $each) {
			$ordered[$key] = $this->smscs[$key];
		}

		return $ordered;
	}
	
	public function setNewStatus($smscs) {
		$this->smscs = $smscs;
	}

	public function getSmscs() {
		return $this->smscs;
	}

	public function getOrderNewStatus() {
		return $this->orderByLowQueue($this->smscs);
	}

	public function remove($key) {
		unset($this->smscs[$key]);
	}
	public function device($key) {
		if(isset($this->smscs[$key]))
			return $this->smscs[$key];

		return false;
	}

	public function checkIsOn($key) {
		$device = $this->device($key);
		$verify = explode(' ', $device['smsc_status']);

		return isset($verify[0]) && $verify[0] == 'online' ? true : false; 
		
	}

	public function increaseQueue($key, $increment = 1) {
		$this->smscs[$key]['smsc_queued'] += $increment;
	}

	
	public function getStatusService()
	{
		$status =  $this->xml->status;
		
		$status = explode(', ', $status);
		
		$info = array();
		$info['status_service'] = $status[0];
		$info['time_running_service'] = isset($status[1]) ? $status[1] : false;
		
		return $info;
	}

	/*
	* Ce echipamente au depasit pragul de limit_per_device, facem unset si returnam
	* si care sunt on sau disponibile
	*/
	public function isFree($limit_per_device) {
		if(empty($this->smscs)) return false;
		
		foreach($this->smscs as $key => $smsc) {
			if(! $this->checkIsOn($key) // false
				|| $smsc['smsc_queued'] >= $limit_per_device) {
				unset($this->smscs[$key]);
			}
		}
	}
	
	public function getPrimaryStatusService()
	{
		// kannel,sqlbox, bearebox
		$data = array(
			'sqlbox' => 0,
			'smsbox' => 0,
			'kannel' => 0
		);
		
		if (strpos(strtolower($this->xml->status), 'running') !== false)
		{
			$data['kannel'] = 1;
		}
		
		$boxes = $this->xml->boxes;
		if($boxes):
			foreach($boxes->box as $each)
			{
				if(empty($each->id) )
				{
					if (strpos(strtolower($each->status), 'on-line') !== false) 
					{
						$data['sqlbox'] = 1;
					}
					
				}
				
				
				if($each->id && ($each->id == Yii::app()->params['smsboxname']) )
				{
					if (strpos(strtolower($each->status), 'on-line') !== false)
					{
						$data['smsbox'] = 1;
					}
				}
			}
		endif;

		return $data;
	}
	
	public function getStatusServices()
	{
		$stats = self::getAllXml();
		
		if(! empty($stats) )
		{
			$stats = '<iframe src="'.Yii::app()->params['url_kannel_status'].'" width="100%" height="500px" scrolling="yes" style="overflow:scroll; margin-top:-4px; margin-left:-4px; border:none;"></iframe>';
		} else {
			$stats = '<div class="alert alert-danger"><p style="text-align: center"><i class="icon icon-warning-sign"></i> <strong>Serviciile sunt oprite!</strong></p></div>';
		}
		
		return $stats;
	}
}
