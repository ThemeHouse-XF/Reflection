<?php

class ThemeHouse_Reflection_Class extends Zend_Reflection_Class
{

    public function addMethod($methodName, $body = '', $signature = '')
    {
        $lines = preg_split("/\\r\\n|\\r|\\n/", $body);

        foreach ($lines as &$line) {
            $line = "\t\t" . $line;
        }

        $publicProtected = substr($methodName, 0, 1) == '_' ? 'protected' : 'public';

        array_unshift($lines, "\t" . '{');
        array_unshift($lines, "\t" . $publicProtected . ' function ' . $methodName . '(' . $signature . ')');
        array_unshift($lines, '');
        array_push($lines, "\t" . '}');

        $fileName = $this->getFileName();

        $firstLines = array_slice(file($fileName, FILE_IGNORE_NEW_LINES), 0, $this->getEndLine() - 1, true);

        $lastLines = array_slice(file($fileName, FILE_IGNORE_NEW_LINES), $this->getEndLine() - 1, null, true);

        $lines = array_merge($firstLines, $lines, $lastLines);

        $file = implode(PHP_EOL, $lines);
        $file = str_replace("\t", '    ', trim($file));

        file_put_contents($fileName, $file);
    }

    /**
     *
     * @return ThemeHouse_Reflection_Method
     */
    public function getMethod($name, $reflectionClass = 'ThemeHouse_Reflection_Method')
    {
        return parent::getMethod($name, $reflectionClass);
    }

    public function hasOveridden($methodName)
    {
        $declaringClass = $this->getMethod('_getTables')->getDeclaringClass();
        if ($declaringClass->getName() == $this->getName()) {
            return true;
        }

        return false;
    }

    public function deleteMethod($methodName)
    {
        $method = $this->getMethod($methodName);

        $startLine = $method->getStartLine(true);
        $endLine = $method->getEndLine();

        $fileName = $this->getFileName();

        $firstLines = array_slice(file($fileName, FILE_IGNORE_NEW_LINES), 0, $startLine - 1, true);

        $lastLines = array_slice(file($fileName, FILE_IGNORE_NEW_LINES), $endLine, null, true);

        $lines = array_merge($firstLines, $lastLines);

        $file = implode(PHP_EOL, $lines);
        $file = str_replace("\t", '    ', trim($file));

        file_put_contents($fileName, $file);
    }
}