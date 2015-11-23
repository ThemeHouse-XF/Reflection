<?php

class ThemeHouse_PhpFile
{

	protected $_className;

	protected $_functions = array();

	protected $_phpDoc = array();

	protected $_implements;

	protected $_extends;

	protected $_constants = array();

	protected $_variables = array();

	protected $_addOnId;

	public function __construct($className = '')
	{
		if ($className) {
			$this->setClassName($className);
		}
	}

	public function setClassName($className)
	{
		$this->_className = $className;

		preg_match('/(XenForo|[a-zA-Z]*_[a-zA-Z]*)(?:_[a-zA-Z_]*)*/', $this->_className, $matches);

		if (isset($matches[1])) {
			$this->_addOnId = $matches[1];
		}
	}

	public function createFunction($functionName)
	{
		$function = new ThemeHouse_PhpFile_Function($functionName);
		$this->_functions[$functionName] = $function;
		return $function;
	}

	public function createConstant($constantName)
	{
		$constant = new ThemeHouse_PhpFile_Constant($constantName);
		$this->_constants[$constantName] = $constant;
		return $constant;
	}

	public function createVariable($variableName)
	{
		$variable = new ThemeHouse_PhpFile_Variable($variableName);
		$this->_variables[$variableName] = $variable;
		return $variable;
	}

	/**
	 *
	 * @return ThemeHouse_PhpFile_Function
	 */
	public function getFunction($functionName)
	{
		if (isset($this->_functions[$functionName])) {
			return $this->_functions[$functionName];
		}
		return $this->createFunction($functionName);
	}

	/**
	 *
	 * @return array
	 */
	public function getFunctions()
	{
		return $this->_functions;
	}

	/**
	 *
	 * @param string $functionName
	 * @param ThemeHouse_PhpFile_Function $function
	 */
	public function setFunction(ThemeHouse_PhpFile_Function $function)
	{
		$this->_functions[$function->getFunctionName()] = $function;
	}

	/**
	 *
	 * @return array
	 */
	public function getConstants()
	{
		return $this->_constants;
	}

	/**
	 *
	 * @return ThemeHouse_PhpFile_Constant
	 */
	public function getConstant($constantName)
	{
		if (isset($this->_constants[$constantName])) {
			return $this->_constants[$constantName];
		}
		return $this->createConstant($constantName);
	}

	/**
	 *
	 * @return array
	 */
	public function getVariables()
	{
		return $this->_variables;
	}

	/**
	 *
	 * @return ThemeHouse_PhpFile_PhpFileVariable
	 */
	public function getVariable($variableName)
	{
		if (isset($this->_variables[$variableName])) {
			return $this->_variables[$variableName];
		}
		return $this->createVariable($variableName);
	}

	public function getPhpDocAsString()
	{
		$contents = '';
		if (!empty($this->_phpDoc)) {
			$contents .= "\n/**";
			foreach ($this->_phpDoc as $phpDoc) {
				$contents .= rtrim("\n * $phpDoc");
			}
			$contents .= "\n */";
		}
		return $contents;
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

	public function setImplements($implements)
	{
		$this->_implements = $implements;
	}

	public function setExtends($extends)
	{
		$this->_extends = $extends;
	}

	protected static function _simpleArrayMerge($className, $functionName, $body)
	{
		$oldBody = call_user_func(array(
			$className,
			$functionName
		));
		$oldBody = $this->_simpleArrayQuote($oldBody);

		return array_merge($oldBody, $body);
	}

	protected function _simpleArrayQuote($array)
	{
		if (is_array($array)) {
			$newArray = array();
			foreach ($array as $key => $value) {
				$newArray[$this->_simpleArrayQuote($key)] = $this->_simpleArrayQuote($value);
			}
			return $newArray;
		} else {
			return '\'$body\'';
		}
	}

	/**
	 * Factory method to get the named PHP file.
	 * If the class does not exist or
	 * is not autoloadable, a blank php file will be returned.
	 *
	 * @param string Class name of new PHP file
	 * @param string Class to load from
	 *
	 * @return ThemeHouse_PhpFile
	 */
	public static function create($class = 'ThemeHouse_PhpFile', $className = '', $extraOptions = array())
	{
		$createClass = XenForo_Application::resolveDynamicClass($class, 'phpfile_th', 'ThemeHouse_PhpFile');
		if (!$createClass) {
			throw new XenForo_Exception("Invalid PHP file '$class' specified");
		}
		return new $createClass($className, $extraOptions);
	}

	public static function createFromReflectionClass(ThemeHouse_Reflection_Class $reflectionClass)
	{
		$phpFile = self::create('ThemeHouse_PhpFile', $reflectionClass->getName());

		foreach ($reflectionClass->getConstants() as $constantName => $constantValue) {
			$constant = $phpFile->getConstant($constantName);
			$constant->setValue($constantValue);
		}

		foreach ($reflectionClass->getDefaultProperties() as $propertyName => $propertyValue) {
			$variable = $phpFile->getVariable($propertyName);
			$variable->setValue($propertyValue);
		}

		foreach ($reflectionClass->getMethods(-1, 'ThemeHouse_Reflection_Method') as $method) {
			/* @var $method ThemeHouse_Reflection_Method */
			$function = $phpFile->getFunction($method->getName());
			$function->setBody($method->getBody());
			$function->setPhpDoc($method->getDocblock()
				->getContents());
			$function->setSignature(explode(',', $method->getSignature()));
			if ($method->isPublic()) {
				$function->setPublic(true);
			} elseif ($method->isProtected()) {
				$function->setProtected(true);
			}
		}

		return $phpFile;
	}

	public static function createFromContents($className, $contents = '')
	{
		$pattern = '#(<\?php\s*(?:/\*\*\s*(?:\*[^/]*)*\*/\s*)?[a-zA-Z_ ]*\s*{)(.*)}\s*#s';
		preg_match($pattern, $contents, $matches);
		if (empty($matches)) {
			return self::create('ThemeHouse_PhpFile', $className, $contents);
		}
		$phpFile = self::create('ThemeHouse_PhpFile', $className);
		$contents = $matches[1];
		$functions = $matches[2];
		unset($matches);
		$pattern = '#\s*(?:/\*\*\s*((?:\*.*\s*)*)\*/\s*)?(?:(public|protected|private) (static )?($([a-zA-Z_]*) = ([^;]);|function ([a-zA-Z_]*)\((.*)\)\s*{((?:.*\s)*)} /\* END (?:' .
			 $className . '::)?\g{-3} \*/)|const ([A-Z_]*) = (\'.*\'|0x[0-9ABCDEF]*);)\s*#U';
		preg_match_all($pattern, $functions, $matches);

		foreach ($matches[7] as $matchId => $functionName) {
			$isConstant = false;
			if ($matches[10][$matchId]) {
				$isConstant = true;
				$functionName = $matches[10][$matchId];
			}
			$isVariable = false;
			if ($matches[5][$matchId]) {
				$isVariable = true;
				$functionName = $matches[5][$matchId];
			}
			if ($matches[1][$matchId]) {
				$phpDocArray = explode("\n", trim($matches[1][$matchId]));
				foreach ($phpDocArray as &$phpDoc) {
					$phpDoc = substr(trim($phpDoc), 2);
				}
				unset($phpDoc);
				if ($isConstant) {
					$phpFile->getConstant($functionName)->setPhpDoc($phpDocArray);
				} else
					if ($isVariable) {
						$phpFile->getVariable($functionName)->setPhpDoc($phpDocArray);
					} else {
						$phpFile->getFunction($functionName)->setPhpDoc($phpDocArray);
					}
			}
			if ($isConstant) {
				$phpFile->getConstant($functionName)->setValue($matches[11][$matchId]);
				continue;
			}
			if ($isVariable) {
				$phpFile->getVariable($functionName)->setValue($matches[6][$matchId]);
				continue;
			}
			if ($matches[2][$matchId] == 'public') {
				$phpFile->getFunction($functionName)->setPublic();
			}
			if ($matches[2][$matchId] == 'protected') {
				$phpFile->getFunction($functionName)->setProtected();
			}
			if ($matches[3][$matchId]) {
				$phpFile->getFunction($functionName)->setStatic();
			}
			$signature = explode(', ', $matches[8][$matchId]);
			$phpFile->getFunction($functionName)->setSignature($signature);
			$body = preg_split('/(\r\n|\n|\r)/', $matches[9][$matchId]);
			foreach ($body as &$line) {
				for ($i = 2; $i > 0; $i--) {
					if (trim(substr($line, 0, $i * 4)) == '') {
						$line = substr($line, $i * 4);
						break;
					}
				}
			}
			$phpFile->getFunction($functionName)->setBody($body);
		}
		return $phpFile;
	}

	public function mergeFunctions(array $functions)
	{
		$count = 1000;
		$functionNames = array();
		foreach ($this->_functions as $functionName => $function) {
			$functionNames[$count] = $functionName;
			$count += 1000;
		}

		$count = 0;
		/* @var $function ThemeHouse_PhpFile_Function */
		foreach ($functions as $functionName => $function) {
			if (in_array($functionName, $functionNames)) {
				$count = array_search($functionName, $functionNames);
			} else {
				$count++;
				$functionNames[$count] = $functionName;
			}

			$body = $this->getFunction($functionName)->getBody();
			if ($this->getFunction($functionName)->hasMerge()) {
				if (!is_array($body[0])) {
					$body[0] = array(
						$body[0]
					);
				}
				$newKeys = array_keys($body[0]);
				$oldKeys = array();
				preg_match('#return array\((.*)\);#s', $function->getBodyAsString(), $newMatches);
				if (isset($newMatches[1])) {
					preg_match_all('#\s*(\'[A-Za-z_]*\') => array\(\s*(.*)\), /\* END \g{-2} \*/\s*#Us', $newMatches[1],
						$newerMatches);
					if (!empty($newerMatches[1])) {
						foreach ($newerMatches[1] as $key => $value) {
							$oldKeys[] = $value;
							$newKeys1 = (isset($body[0][$value]) ? array_keys($body[0][$value]) : array());
							$oldKeys1 = array();
							preg_match_all('#\s*(.*) => (.*), /\* END \g{-2} \*/\s*#s', $newerMatches[2][$key],
								$newestMatches);
							foreach ($newestMatches[1] as $newKey => $newestMatch) {
								$oldKeys1[] = $newestMatch;
								if (!isset($body[0][$value][$newestMatch])) {
									$body[0][$value][$newestMatch] = $newestMatches[2][$newKey];
								}
							}
							if (isset($body[0][$value])) {
								$body[0][$value] = $this->sort($body[0][$value], array_merge($oldKeys1, $newKeys1));
							}
						}
					} else {
						preg_match_all('#\s*(.*) => (.*), /\* END \g{-2} \*/\s*#s', $newMatches[1], $newestMatches);
						foreach ($newestMatches[1] as $newKey => $newestMatch) {
							$oldKeys[] = $newestMatch;
							if (!isset($body[0][$newestMatch])) {
								$body[0][$newestMatch] = $newestMatches[2][$newKey];
							}
						}
					}
				}
				$body[0] = $this->sort($body[0], array_merge($oldKeys, $newKeys));
				$this->getFunction($functionName)->setBody($body);
			} else {
				if (!$this->getFunction($functionName)->hasBody()) {
					$this->getFunction($functionName)->setBody($function->getBody());
				}
			}
			if (!$this->getFunction($functionName)->hasPhpDoc()) {
				$this->getFunction($functionName)->setPhpDoc($function->getPhpDoc());
			}
			if (!$this->getFunction($functionName)->hasSignature()) {
				$this->getFunction($functionName)->setSignature($function->getSignature());
			}
		}

		$this->_functions = $this->sort($this->_functions, $functionNames);
	}

	public function mergeConstants(array $constants)
	{
		$count = 1000;
		$constantNames = array();
		foreach ($this->_constants as $constantName => $constant) {
			$constantNames[$count] = $constantName;
			$count += 1000;
		}

		$count = 0;
		/* @var $constant ThemeHouse_PhpFile_Constant */
		foreach ($constants as $constantName => $constant) {
			if (in_array($constantName, $constantNames)) {
				$count = array_search($constantName, $constantNames);
			} else {
				$count++;
				$constantNames[$count] = $constantName;
			}

			if (!$this->getConstant($constantName)->hasValue()) {
				$this->getConstant($constantName)->setValue($constant->getValue());
			}
			if (!$this->getConstant($constantName)->hasPhpDoc()) {
				$this->getConstant($constantName)->setPhpDoc($constant->getPhpDoc());
			}
		}

		$this->_constants = $this->sort($this->_constants, $constantNames);
	}

	public function mergeVariables(array $variables)
	{
		$count = 1000;
		$variableNames = array();
		foreach ($this->_variables as $variableName => $variable) {
			$variableNames[$count] = $variableName;
			$count += 1000;
		}

		$count = 0;
		/* @var $variable ThemeHouse_PhpFile_Variable */
		foreach ($variables as $variableName => $variable) {
			if (in_array($variableName, $variableNames)) {
				$count = array_search($variableName, $variableNames);
			} else {
				$count++;
				$variableNames[$count] = $variableName;
			}

			if (!$this->getVariable($variableName)->hasValue()) {
				$this->getVariable($variableName)->setValue($variable->getValue());
			}
			if (!$this->getVariable($variableName)->hasPhpDoc()) {
				$this->getVariable($variableName)->setPhpDoc($variable->getPhpDoc());
			}
		}

		$this->_variables = $this->sort($this->_variables, $variableNames);
	}

	public function sort(array $old, $names)
	{
		$new = array();
		ksort($names);
		foreach ($names as $name) {
			if (isset($old[$name])) {
				$new[$name] = $old[$name];
			}
		}
		return $new;
	}

	public function getHeader()
	{
		$contents = "<?php" . "\n";

		if (isset($this->_phpDoc) && !empty($this->_phpDoc)) {
			$contents .= $this->getPhpDocAsString();
		}
		$contents .= "\n" . "class $this->_className";
		if (isset($this->_implements) && $this->_implements) {
			$contents .= " implements " . $this->_implements;
		} else
			if (isset($this->_extends) && $this->_extends) {
				$contents .= " extends " . $this->_extends;
			}
		$contents .= "\n{\n";

		return $contents;
	}

	public function setAddOnId($addOnId)
	{
		$this->_addOnId = $addOnId;
	}

	public function export($overwrite = false)
	{
		if (!$this->_addOnId) {
			throw new XenForo_Exception('Please specify an add-on id.');
		}

		$filename = str_replace('_', '/', $this->_className) . '.php';

		$existingContents = '';
		$fullFilename = XenForo_Autoloader::getInstance()->getRootDir() . '/' . $filename;

		if (file_exists($fullFilename)) {
			$existingContents = file_get_contents($fullFilename);

			$existingContentsArray = preg_split('/(\r\n|\n|\r)/', $existingContents);
			foreach ($existingContentsArray as $key => $value) {
				if (rtrim($value) != $value) {
					$existingContentsArray[$key] = rtrim($value);
				}
			}
			$existingContents = implode("\n", $existingContentsArray);
			$existingContents = str_replace("\t", '	', $existingContents);
			file_put_contents($fullFilename, $existingContents);

			$existingPhpFile = self::createFromContents($this->_className, $existingContents);
		} else {
			if (!file_exists(dirname($fullFilename))) {
				XenForo_Helper_File::createDirectory(dirname($fullFilename));
			}

			$existingPhpFile = self::createFromContents($this->_className);
		}

		$contents = $this->getHeader();

		$this->mergeConstants($existingPhpFile->getConstants());
		if (isset($this->_constants) && !empty($this->_constants)) {
			foreach ($this->_constants as $constantName => $constant) {
				$contents .= $constant->getPhpDocAsString();
				$contents .= "\n	";
				$contents .= 'const ' . $constant->getConstantName() . ' = ' . $constant->getValue() . ';' . "\n";
			}
		}

		$this->mergeVariables($existingPhpFile->getVariables());
		if (isset($this->_variables) && !empty($this->_variables)) {
			/* @var $variable ThemeHouse_PhpFile_Variable */
			foreach ($this->_variables as $variableName => $variable) {
				$contents .= $variable->getPhpDocAsString();
				$contents .= "\n	";
				$contents .= $variable->getFullSignatureAsString() . ' = ' . $variable->getValue() . ';' . "\n";
			}
		}

		$this->mergeFunctions($existingPhpFile->getFunctions());
		if (isset($this->_functions) && !empty($this->_functions)) {
			/* @var $function ThemeHouse_PhpFile_Function */
			foreach ($this->_functions as $functionName => $function) {
				$contents .= $function->getPhpDocAsString();

				$contents .= "\n	";
				$contents .= $function->getFullSignatureAsString();
				$contents .= "\n	{";
				$contents .= $function->getBodyAsString();
				$contents .= "\n	}\n";
			}
		}
		$contents .= "}";

		$export = XenForo_CodeEvent::fire('phpfile_export_th',
			array(
				&$filename,
				&$contents,
				$existingContents,
				$overwrite,
				$this->_addOnId
			));

		if ($export) {
			if (!file_exists(dirname(XenForo_AutoLoader::getInstance()->getRootDir() . '/' . $filename))) {
				mkdir(dirname(XenForo_AutoLoader::getInstance()->getRootDir() . '/' . $filename), 0, true);
			}
			file_put_contents(XenForo_AutoLoader::getInstance()->getRootDir() . '/' . $filename, $contents);
		}
	}
}

if (false === function_exists('lcfirst')) {

	/**
	 * Make a string's first character lowercase
	 *
	 * @param string $str
	 * @return string the resulting string.
	 */
	function lcfirst($str)
	{
		$str[0] = strtolower($str[0]);
		return (string) $str;
	}
}