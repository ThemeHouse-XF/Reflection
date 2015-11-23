<?php

class ThemeHouse_Reflection_Helper_Template
{

    public static function getTemplateName($string, $addOnId)
    {
        list($start, $end) = explode('_', $addOnId, 2);
        
        $templateName = strtolower($start) . '_' . $string . '_' . strtolower($end);
        
        $templateNameLength = strlen($templateName);
        
        if ($templateNameLength <= 50) {
            return $templateName;
        }
        
        $splitString = explode('_', $string);
        
        $splitString = array_reverse($splitString);
        
        $remainingCharactersToRemove = $templateNameLength - 50;
        $stringCount = count($splitString);
        
        while ($remainingCharactersToRemove > 0) {
            foreach ($splitString as &$string) {
                if (!$remainingCharactersToRemove) {
                    break;
                }
                $charactersToRemove = ceil($remainingCharactersToRemove/$stringCount);
                if (strlen($string) == $charactersToRemove) {
                    $string = '';
                    $remainingCharactersToRemove -= $charactersToRemove + 1;
                }
                $string = substr($string, 0, -$charactersToRemove);
                $remainingCharactersToRemove -= $charactersToRemove;
                $stringCount--;
            }
        }
        
        $splitString = array_filter(array_reverse($splitString));
        
        $string = implode('_', $splitString);
        
        $templateName = strtolower($start) . '_' . $string . '_' . strtolower($end);
        
        return $templateName;
    }

    public static function camelCaseToSnakeCase($camelCase)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $camelCase, $matches);
        $snakeCase = $matches[0];
        foreach ($snakeCase as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $snakeCase);
    }

    public static function snakeCaseToCamelCase($snakeCase, $lcFirst = false)
    {
        $snakeCase = str_replace(' ', '', ucwords(str_replace('_', ' ', $snakeCase)));
        
        if ($lcFirst) {
            if (PHP_VERSION_ID < 50300) {
                $snakeCase = strtolower(substr($snakeCase, 0, 1)) . substr($snakeCase, 1);
            } else {
                $snakeCase = lcfirst($snakeCase);
            }
        }
        
        return $snakeCase;
    }

    public static function createAdminTemplate($title, $template, $addOnId)
    {
        /* @var $adminTemplateModel XenForo_Model_AdminTemplate */
        $adminTemplateModel = XenForo_Model::create('XenForo_Model_AdminTemplate');
        
        $adminTemplate = $adminTemplateModel->getAdminTemplateByTitle($title);
        
        /* @var $adminTemplateDw XenForo_DataWriter_AdminTemplate */
        $adminTemplateDw = XenForo_DataWriter::create('XenForo_DataWriter_AdminTemplate');
        if ($adminTemplate) {
            $adminTemplateDw->setExistingData($adminTemplate);
        }
        $adminTemplateDw->bulkSet(
            array(
                'title' => $title,
                'template' => $template,
                'addon_id' => $addOnId
            ));
        $adminTemplateDw->save();
    }
}