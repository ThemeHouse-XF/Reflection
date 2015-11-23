<?php

class ThemeHouse_PhpFile_Variable
{

	protected $_variableName;

	protected $_phpDoc = array();

	protected $_public = false;

	protected $_protected = false;

	protected $_static = false;

	protected $_value = array();

	public function __construct($variableName)
	{
		$this->_variableName = $variableName;
	}

	public function getVariableName()
	{
		return $this->_variableName;
	}

	public function getFullSignatureAsString()
	{
		$contents = '';
		if ($this->_public || substr($this->_variableName, 0, 1) != '_') {
			$contents .= "public ";
		} else
			if ($this->_protected || substr($this->_variableName, 0, 2) != '__') {
				$contents .= "protected ";
			} else {
				$contents .= "private ";
			}
		if ($this->_static) {
			$contents .= "static ";
		}
		$contents .= '$' . $this->_variableName;
		return $contents;
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