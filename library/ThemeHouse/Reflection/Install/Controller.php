<?php

class ThemeHouse_Reflection_Install_Controller extends ThemeHouse_Install
{

    protected $_resourceManagerUrl = 'https://xenforo.com/community/resources/reflection.4055/';

    protected function _getTables()
    {
        return array(
            'xf_method_template' => array(
                'method_template_id' => 'int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
                'title' => 'varchar(255) NOT NULL',
                'callback_class' => 'varchar(75) NOT NULL',
                'callback_method' => 'varchar(50) NOT NULL',
                'content_type' => 'varchar(255) NOT NULL'
            )
        );
    }

    protected function _getUniqueKeys()
    {
        return array(
            'xf_method_template' => array(
                'callback_class_callback_method_content_type' => array(
                    'callback_class',
                    'callback_method',
                    'content_type'
                )
            )
        );
    }
}