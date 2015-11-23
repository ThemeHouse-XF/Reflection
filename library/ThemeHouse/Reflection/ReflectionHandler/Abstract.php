<?php

abstract class ThemeHouse_Reflection_ReflectionHandler_Abstract
{

    /**
     * The phrase key that names the content type for this reflection handler.
     * Must be overriden by children.
     *
     * @var string
     */
    protected $_contentTypePhraseKey = '';

    /**
     * Returns the phrase key of a phrase that names the content type managed by
     * this handler.
     *
     * @return string
     */
    public function getContentTypePhraseKey()
    {
        if ($this->_contentTypePhraseKey) {
            return $this->_contentTypePhraseKey;
        }

        return 'unknown';
    }
}