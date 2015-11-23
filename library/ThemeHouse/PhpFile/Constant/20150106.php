<?php

class ThemeHouse_PhpFile_Constant
{

	protected $_constantName;

	protected $_phpDoc = array();

	protected $_value = array();

	public function __construct($constantName)
	{
		$this->_constantName = $constantName;
	}

	public function getConstantName()
	{
		return $this->_constantName;
	}

	public function getValue()
	{
		return $this->_value;
	}

	public function hasValue()
	{
		return !empty($this->_value);
	}

	public function setValue($value)
	{
		$this->_value = $value;
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

	public function hasPhpDoc()
	{
		return !empty($this->_phpDoc);
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
}