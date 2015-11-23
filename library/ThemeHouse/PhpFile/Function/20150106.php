<?php

class ThemeHouse_PhpFile_Function
{

	protected $_functionName;

	protected $_phpDoc = array();

	protected $_public = false;

	protected $_protected = false;

	protected $_static = false;

	protected $_signature = array();

	protected $_body = array();

	protected $_merge = false;

	public function __construct($functionName)
	{
		$this->_functionName = $functionName;
	}

	public function getFunctionName()
	{
		return $this->_functionName;
	}

	public function getPhpDoc()
	{
		return $this->_phpDoc;
	}

	public function getPhpDocAsString()
	{
		$contents = '';
		if (!empty($this->_phpDoc)) {
			$contents .= "\n	/**";
			foreach ($this->_phpDoc as $phpDoc) {
				$contents .= rtrim("\n	 * $phpDoc");
			}
			$contents .= "\n	 */";
		}
		return $contents;
	}

	public function getSignature()
	{
		return $this->_signature;
	}

	public function getFullSignatureAsString()
	{
		$contents = '';
		if ($this->_public || substr($this->_functionName, 0, 1) != '_') {
			$contents .= "public ";
		} else
			if ($this->_protected || substr($this->_functionName, 0, 2) != '__') {
				$contents .= "protected ";
			} else {
				$contents .= "private ";
			}
		if ($this->_static) {
			$contents .= "static ";
		}
		$contents .= "function $this->_functionName(";
		if (!empty($this->_signature)) {
			$contents .= array_shift($this->_signature);
			foreach ($this->_signature as $signature) {
				$contents .= ", $signature";
			}
		}
		$contents .= ")";
		return $contents;
	}

	public function getBody()
	{
		return $this->_body;
	}

	public function getBodyAsString()
	{
		$contents = '';
		$this->_body = array_values($this->_body);
		foreach ($this->_body as $lineNo => $line) {
			if (is_array($line)) {
				$contents .= "\n		" . 'return ' . $this->_bodyArrayToString($line) . ';';
			} elseif ((!$line && $lineNo == count($this->_body) - 1) || (!$contents && !$line)) {
				continue;
			} elseif (trim($line) == '') {
				$contents .= "\n";
			} else {
				$contents .= "\n		" . rtrim($line);
			}
		}
		return $contents;
	}

	protected function _bodyArrayToString(array $array, $tabs = "		")
	{
		$string = "array(";

		$i = 0;
		foreach ($array as $key => $value) {
			$i++;
			if (is_array($value)) {
				$value = $this->_bodyArrayToString($value, "$tabs	");
			}
			if (is_int($key)) {
				if ($i == count($array)) {
					$string .= "\n$tabs	$value";
				} else {
					$string .= "\n$tabs	$value,";
				}
			} else {
				$string .= "\n$tabs	$key => $value,";
			}
		}
		$string .= "\n$tabs)";

		return $string;
	}

	public function hasMerge()
	{
		return $this->_merge;
	}

	public function hasBody()
	{
		$body = array();
		foreach ($this->_body as $lineNo => $line) {
			// remove any blank first and last lines
			if ((!$line && $lineNo == count($this->_body) - 1) || (empty($body) && !$line))
				continue;

			$body[$lineNo] = $line;
		}
		return !empty($body);
	}

	public function hasPhpDoc()
	{
		return !empty($this->_phpDoc);
	}

	public function hasSignature()
	{
		return !empty($this->_signature);
	}

	public function isPublic()
	{
		return $this->_public;
	}

	public function isProtected()
	{
		return $this->_protected;
	}

	public function isStatic()
	{
		return $this->_static;
	}

	public function setPhpDoc($phpDoc)
	{
		if (is_array($phpDoc)) {
			$this->_phpDoc = $phpDoc;
		} else {
			$this->_phpDoc = array(
				$phpDoc
			);
		}
	}

	public function setBody(array $body)
	{
		$this->_body = $body;
	}

	public function addToBody($body)
	{
		$this->_body[] = $body;
	}

	public function setSignature(array $signature)
	{
		$this->_signature = $signature;
	}

	public function setMerge($merge = true)
	{
		$this->_merge = $merge;
	}

	public function setPublic($public = true)
	{
		$this->_public = $public;
	}

	public function setProtected($protected = true)
	{
		$this->_protected = $protected;
	}

	public function setStatic($static = true)
	{
		$this->_static = $static;
	}
}