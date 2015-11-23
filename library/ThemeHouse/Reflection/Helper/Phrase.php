<?php

class ThemeHouse_Reflection_Helper_Phrase
{

    public static function getPhraseName($string, $addOnId)
    {
        list($start, $end) = explode('_', $addOnId, 2);
    
        $phraseName = strtolower($start) . '_' . $string . '_' . strtolower($end);
    
        $phraseNameLength = strlen($phraseName);
    
        if ($phraseNameLength <= 100) {
            return $phraseName;
        }
    
        $splitString = explode('_', $string);
    
        $splitString = array_reverse($splitString);
    
        $remainingCharactersToRemove = $phraseNameLength - 100;
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
    
        $phraseName = strtolower($start) . '_' . $string . '_' . strtolower($end);
    
        return $phraseName;
    }
}