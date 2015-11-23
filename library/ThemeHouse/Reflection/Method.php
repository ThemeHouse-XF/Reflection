<?php

class ThemeHouse_Reflection_Method extends Zend_Reflection_Method
{

    public function getSignature($includeMethodName = false)
    {
        $parameters = $this->getParameters();
        
        /*
         * $signature = array();
         * foreach ($parameters as $parameter) {
         * /* @var $parameter ReflectionParameter
         */
        /*
         * $paramString = (string) $parameter;
         * preg_match('/^Parameter #[0-9]* \[ <(?:required|optional)> (.*)
         * \]$/', $paramString, $matches);
         * $signature[] = $matches[1];
         * }
         */
        
        $lines = array_slice(file($this->getDeclaringClass()->getFileName(), FILE_IGNORE_NEW_LINES), 
            $this->getStartLine() - 1, ($this->getEndLine() - $this->getStartLine() + 1), true);
        
        foreach ($lines as &$line) {
            $line = trim($line);
        }
        
        $body = implode(' ', $lines);
        
        preg_match('#^(.*?\))\s*{.*}#', $body, $matches);
        
        if ($includeMethodName) {
            return $matches[1];
        }
        
        preg_match('#\((.*)\)$#', $matches[1], $matches);
        
        if ($matches) {
            $signature = trim($matches[1]);
        } else {
            $signature = '';
        }
        
        /*
         * if ($includeMethodName) {
         * $signature = 'function ' . $this->getName() . '(' . $signature . ')';
         * if ($this->isStatic()) {
         * $signature = 'static ' . $signature;
         * }
         * if ($this->isPublic()) {
         * $signature = 'public ' . $signature;
         * } elseif ($this->isPrivate()) {
         * $signature = 'private ' . $signature;
         * } elseif ($this->isPrivate()) {
         * $signature = 'protected ' . $signature;
         * }
         * if ($this->isAbstract()) {
         * $signature = 'abstract ' . $signature;
         * } elseif ($this->isFinal()) {
         * $signature = 'final ' . $signature;
         * }
         * }
         */
        
        return $signature;
    }

    public function getBody()
    {
        $lines = array_slice(file($this->getDeclaringClass()->getFileName(), FILE_IGNORE_NEW_LINES), 
            $this->getStartLine() - 1, ($this->getEndLine() - $this->getStartLine() + 1), true);
        
        $body = implode("\n", $lines);
        
        preg_match('#\)\s*{(.*)}#s', $body, $matches);
        
        $lines = preg_split("/\\n/", $matches[1]);
        
        $contents = '';
        $trim = -1;
        $trimValue = '';
        foreach ($lines as $line) {
            if ($trim == -1) {
                $contents = ltrim($line);
                if (trim($contents)) {
                    $trim = strlen($line) - strlen($contents);
                    $trimValue = substr($line, 0, $trim);
                    $contents .= "\n";
                }
            } else {
                if (substr($line, 0, $trim) == $trimValue) {
                    $contents .= substr($line, $trim) . "\n";
                } else {
                    $contents .= $line . "\n";
                }
            }
        }
        
        $body = str_replace('    ', "\t", trim($contents));
        
        return $body;
    }

    public function setMethodBody($body)
    {
        $lines = preg_split("/\\r\\n|\\r|\\n/", $body);
        
        foreach ($lines as &$line) {
            $line = "\t\t" . $line;
        }
        
        array_unshift($lines, "\t" . '{');
        array_unshift($lines, "\t" . $this->getSignature(true));
        array_push($lines, "\t" . '}');
        
        $fileName = $this->getDeclaringClass()->getFileName();
        
        $firstLines = array_slice(file($fileName, FILE_IGNORE_NEW_LINES), 0, $this->getStartLine() - 1, true);
        
        $lastLines = array_slice(file($fileName, FILE_IGNORE_NEW_LINES), $this->getEndLine(), null, true);
        
        $lines = array_merge($firstLines, $lines, $lastLines);
        
        $file = implode(PHP_EOL, $lines);
        $file = str_replace("\t", '    ', trim($file));
        
        file_put_contents($fileName, $file);
    }

    public function getReturnTag()
    {
        $docBlock = $this->getDocblock();
        $tag = $docBlock->getTag('return');
        return $tag->getType();
    }

    public function addRowToArrayKeyInMethod($key, $newRow)
    {
        $body = $this->getBody();
        
        $bodyArray = explode("\n", $body);
        
        $arrayStart = null;
        $tableStart = null;
        $newBodyArray = array();
        foreach ($bodyArray as $line) {
            if ($arrayStart === null) {
                if (preg_match('#^(\s*)return array\(\s*$#', $line, $matches)) {
                    $arrayStart = $matches[1];
                }
                $newBodyArray[] = $line;
                continue;
            }
            if ($tableStart === null) {
                if (preg_match('#^' . $arrayStart . '\);\s*$#', $line)) {
                    $arrayStart = null;
                    // TODO add new table
                } elseif (preg_match('#^(\s*)\'' . $key . '\' => array\(\s*$#', $line, $matches)) {
                    $tableStart = $matches[1];
                }
                $newBodyArray[] = $line;
                continue;
            }
            if (preg_match('#^' . $tableStart . '\)\s*$#', $line)) {
                $lastRow = array_pop($newBodyArray);
                if (!preg_match('#,\s*$#', $lastRow)) {
                    $lastRow = rtrim($lastRow) . ',';
                }
                $newBodyArray[] = $lastRow;
                $newBodyArray[] = $newRow;
                $arrayStart = null;
                $tableStart = null;
            }
            $newBodyArray[] = $line;
        }
        
        $newBody = implode("\n", $newBodyArray);
        
        $this->setMethodBody($newBody);
    }
}