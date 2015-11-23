<?php

class ThemeHouse_Reflection_Listener_ContainerAdminParams extends ThemeHouse_Listener_ContainerParams
{

    protected function _run() { }

    public static function containerAdminParams(array &$params, XenForo_Dependencies_Abstract $dependencies)
    {
        $params = self::createAndRun('ThemeHouse_Reflection_Listener_ContainerAdminParams', $params, $dependencies);
    }
}