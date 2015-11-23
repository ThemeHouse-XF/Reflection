<?php

class ThemeHouse_Reflection_Model_Reflection extends XenForo_Model
{

    /**
     * Gets an array of phrases identifying each reflection handler content type
     *
     * @return array [$contentType => XenForo_Phrase $phrase]
     */
    public function getReflectionHandlerContentTypeNames()
    {
        $phrases = array();

        foreach ($this->getReflectionHandlers() as $contentType => $handler) {
            $phrases[$contentType] = new XenForo_Phrase($handler->getContentTypePhraseKey());
        }

        return $phrases;
    }

    /**
     * Gets all reflection handler classes.
     *
     * @return array [content type] =>
     * ThemeHouse_Reflection_ReflectionHandler_Abstract
     */
    public function getReflectionHandlers()
    {
        $classes = $this->getContentTypesWithField('reflection_handler_class');
        $handlers = array();
        foreach ($classes as $contentType => $class) {
            if (!class_exists($class)) {
                continue;
            }

            $class = XenForo_Application::resolveDynamicClass($class);
            $handlers[$contentType] = new $class();
        }

        return $handlers;
    }
}