<?php

class AddressOption {
	protected $streetNumber = '', $route = '', $subpremise = '', $locality = '',
			$adminArea1 = '', $adminArea2 = '', $postCode = '', $postCodeExt ='',
			$country = '', $type = '', $boxLine ='';
	function __construct($xmlAddr, $str) {
		$this->type = $xmlAddr->type;
		if (count($this->type) == 0) $this->type = Array('street_address');
		if (preg_match('/^P\.?O\.?\s*BOX\s+([0-9-]+)/i', $str, $matches) === 1 &&
				count($matches) === 2 && is_numeric($matches[1]))
				$this->boxLine = 'PO Box ' . $matches[1];
		foreach($xmlAddr->address_component as $a) {
			if ('street_number' == $a->type) $this->streetNumber = $a->short_name;
			if ('route' == $a->type) $this->route = $a->short_name;
			if ('subpremise' == $a->type) $this->subpremise = $a->short_name;
			if ('locality' == $a->type[0]) $this->locality = $a->short_name;
			if ('administrative_area_level_1' == $a->type[0]) $this->adminArea1 = $a->short_name;
			if ('administrative_area_level_2' == $a->type[0]) $this->adminArea2 = $a->short_name;
			if ('postal_code' == $a->type) $this->postCode = $a->short_name;
			if ('postal_code_suffix' == $a->type) $this->postCodeExt = $a->short_name;
			if ('country' == $a->type[0]) $this->country = $a->short_name;
		}
	}
	public function optString() {
		if ($this->country != 'US') return '';
		else if ($this->_MatchType('street_address')) return $this->_StreetAddress();
		else if ($this->_MatchType('premise')) return $this->_StreetAddress();
		else if ($this->_MatchType('subpremise')) return $this->_StreetAddress();
		else if ($this->_MatchType('postal_code')) return $this->_PoBox();
		else if ($this->_MatchType('locality')) return $this->_PoBox();
		else return '';
	}
	private function _MatchType($str) {
		foreach ($this->type as $el)
			if ($el == $str) return true;
		return false;
	}
	private function _StreetAddress() {
		return '<option>' .
				$this->streetNumber . ' ' . $this->route . (strlen($this->subpremise) ? ' #' . $this->subpremise : '') . ', ' .
				$this->locality . ' ' . $this->adminArea1 . ' ' . $this->postCode . (strlen($this->postCodeExt) ? '-' . $this->postCodeExt : '') .
				'</option>';
	}
	private function _PoBox() {
		return '<option>' .
				$this->boxLine . ', ' .
				$this->locality . ' ' . $this->adminArea1 . ' ' . $this->postCode . (strlen($this->postCodeExt) ? '-' . $this->postCodeExt : '') .
				'</option>';
	}
}

class AddressBox {
	private $out = '';
	function __construct($addrStr) {
		$ch = curl_init('http://maps-api-ssl.google.com/maps/api/geocode/xml?address=' . $addrStr . '&sensor=false');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$xmlAddr = new SimpleXMLElement(curl_exec($ch));
		curl_close($ch);
		if ($xmlAddr->status != 'OK') die('No match found.  Please check the address and try again.');
		foreach ($xmlAddr->result as $res) {
			$opt = new AddressOption($res, urldecode($addrStr));
			$this->out .= $opt->optString() . PHP_EOL;
		}
	}
	public function optBox() {
		if (strlen($this->out) < 10) die('No match found.  Please check the address and try again.');
		echo '<select style="width: 100%;"onchange="update_address(this)">', PHP_EOL,
				'<option disabled selected>Select a validated address ...</option>',  PHP_EOL,
				$this->out, '</select>',  PHP_EOL, '<br/>',
				'If your address is not in this list, pleast double check your entry.  ',
				'If your address cannot be validated and you are sure it is correct, please <a href="?pg=contact&arg=address">contact us</a>', PHP_EOL;
	}
}


if (!isset($_SERVER["QUERY_STRING"])) die('Please enter an address to validate.');
$abox = new AddressBox($_SERVER["QUERY_STRING"]);
$abox->optBox();
?>
