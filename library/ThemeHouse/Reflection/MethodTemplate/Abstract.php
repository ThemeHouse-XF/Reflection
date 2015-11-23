<?php

abstract class ThemeHouse_Reflection_MethodTemplate_Abstract
{

    public static function getClass(XenForo_Controller $controller)
    {
        return $controller->getInput()->filterSingle('class', XenForo_Input::STRING);
    }

    public static function getName(XenForo_Controller $controller)
    {
        $class = self::getClass($controller);

        if ($class) {
            return substr(strrchr($class, '_'), 1);
        }

        return false;
    }

    public static function getPluralName(XenForo_Controller $controller)
    {
        $name = self::getName($controller);

        if ($name) {
            return $name . 's';
        }

        return false;
    }

    public static function createMethod(XenForo_Controller $controller, $method, $body = '', $signature = '')
    {
        $class = self::getClass($controller);

        $reflectionClass = new ThemeHouse_Reflection_Class($class);

        if ($reflectionClass->hasMethod($method)) {
            $reflectionMethod = $reflectionClass->getMethod($method);

            // TODO update signature
            // $reflectionMethod->setSignature($signature);
            $reflectionMethod->setMethodBody($body);
        } else {
            $reflectionClass->addMethod($method, $body, $signature);
        }

        return $method;
    }

    public static function checkConfiguration(XenForo_Controller $controller, $class, $prefix, $contentType,
        array &$configValues, array $configDefinitions)
    {
        if ($controller->getInput()->filterSingle('_xfConfirm', XenForo_Input::UINT)) {
            foreach ($configValues as $name => $value) {
                $newValue = $controller->getInput()->filterSingle($name, XenForo_Input::STRING);
                if ($newValue) {
                    $configValues[$name] = $newValue;
                } elseif (!$configValues[$name] && !empty($configDefinitions[$name]['required'])) {
                    throw $controller->responseException(
                        $controller->responseError(new XenForo_Phrase('please_complete_required_fields')));
                }
            }

            return true;
        }

        $methodTemplateId = $controller->getInput()->filterSingle('method_template_id', XenForo_Input::UINT);

        $viewParams = array(
            'configValues' => $configValues,
            'configDefinitions' => $configDefinitions,

            'methodTemplateId' => $methodTemplateId,

            'class' => $class,
            'prefix' => $prefix,
            'contentType' => $contentType
        );

        throw $controller->responseException(
            $controller->responseView('ThemeHouse_Reflection_ViewAdmin_MethodTemplate_Config',
                'th_method_template_config_reflection', $viewParams));
    }
}