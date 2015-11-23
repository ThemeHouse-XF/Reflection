<?php

abstract class ThemeHouse_Reflection_ControllerAdmin_Abstract extends XenForo_ControllerAdmin_Abstract
{

    abstract public function actionAddMethod();

    protected function _getMethodAddResponse($class, $prefix, $contentType)
    {
        $reflectionClass = new ThemeHouse_Reflection_Class(get_class($class));

        /* @var $methodTemplateModel ThemeHouse_Reflection_Model_MethodTemplate */
        $methodTemplateModel = $this->getModelFromCache('ThemeHouse_Reflection_Model_MethodTemplate');

        $class = array(
            'class' => get_class($class)
        );

        if ($this->_request->isPost()) {
            $method = $this->_input->filterSingle('method', XenForo_Input::STRING);

            $methodTemplateId = $this->_input->filterSingle('method_template_id', XenForo_Input::UINT);

            if ($methodTemplateId) {
                $methodTemplate = $methodTemplateModel->getMethodTemplateById($methodTemplateId);

                $method = call_user_func(
                    array(
                        $methodTemplate['callback_class'],
                        $methodTemplate['callback_method']
                    ), $this, $class, $prefix, $contentType);
            } elseif ($method) {
                if (!$reflectionClass->hasMethod($method)) {
                    $reflectionClass->addMethod($method);
                }
            }

            $addOnId = $this->_getAddOnIdFromClassName($class['class']);

            XenForo_Helper_Cookie::setCookie('edit_addon_id', $addOnId);

            if ($method) {
                return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL,
                    XenForo_Link::buildAdminLink($prefix . '/edit-method', $class,
                        array(
                            'method' => $method
                        )));
            } else {
                return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL,
                    XenForo_Link::buildAdminLink($prefix, $class));
            }
        }

        $methodTemplates = $methodTemplateModel->getMethodTemplates(
            array(
                'content_type' => $contentType
            ));

        $viewParams = array(
            'methodTemplates' => $methodTemplates,
            'redirect' => $this->getDynamicRedirect(),
            'prefix' => $prefix,
            'class' => $class
        );

        return $this->responseView('ThemeHouse_Reflection_ViewAdmin_Model_Method_Add', 'th_method_add_reflection',
            $viewParams);
    }

    abstract public function actionEditMethod();

    protected function _getMethodEditResponse($class, $prefix)
    {
        $reflectionClass = new ThemeHouse_Reflection_Class(get_class($class));

        $method = $this->_input->filterSingle('method', XenForo_Input::STRING);

        if (!$reflectionClass->hasMethod($method)) {
            return $this->responseNoPermission();
        }

        /* @var $reflectionMethod ThemeHouse_Reflection_Method */
        $reflectionMethod = $reflectionClass->getMethod($method, 'ThemeHouse_Reflection_Method');

        $signature = $reflectionMethod->getSignature();
        $fileName = $reflectionMethod->getFileName();
        $startLine = $reflectionMethod->getStartLine();
        $endLine = $reflectionMethod->getEndLine();

        $body = $reflectionMethod->getBody();

        $declaringClass = $reflectionMethod->getDeclaringClass();
        $declaringClassName = $declaringClass->getName();

        if (is_subclass_of($declaringClassName, get_class($class))) {
            $selectedAddOnId = $this->_getAddOnIdFromClassName($declaringClassName);
        } else {
            $selectedAddOnId = $this->_getAddOnIdFromClassName(get_class($class));
        }

        $addOnModel = $this->_getAddOnModel();

        $class = array(
            'class' => get_class($class)
        );

        $viewParams = array(
            'class' => $class,
            'redirect' => $this->getDynamicRedirect(),

            'prefix' => $prefix,

            'method' => $method,

            'body' => $body,
            'signature' => $signature,

            'addOnOptions' => $addOnModel->getAddOnOptionsListIfAvailable(),
            'addOnSelected' => $selectedAddOnId
        );

        return $this->responseView('ThemeHouse_Reflection_ViewAdmin_Reflection_Method_Edit',
            'th_method_edit_reflection', $viewParams);
    }

    abstract protected function _getAddOnIdFromClassName($className);

    public function actionSaveMethod()
    {
        $input = $this->_input->filter(
            array(
                'class' => XenForo_Input::STRING,
                'method' => XenForo_Input::STRING,
                'addon_id' => XenForo_Input::STRING,
                'signature' => XenForo_Input::STRING,
                'body' => XenForo_Input::STRING
            ));

        $reflectionClass = new ThemeHouse_Reflection_Class($input['class']);

        XenForo_Helper_Cookie::setCookie('edit_addon_id', $input['addon_id']);

        if ($reflectionClass->hasMethod($input['method'])) {
            /* @var $reflectionMethod ThemeHouse_Reflection_Method */
            $reflectionMethod = $reflectionClass->getMethod($input['method'], 'ThemeHouse_Reflection_Method');

            $declaringClass = $reflectionMethod->getDeclaringClass();
            $declaringClassName = $declaringClass->getName();

            if ($declaringClassName == $input['class']) {
                $reflectionMethod->setMethodBody($input['body']);

                return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_UPDATED,
                    $this->getDynamicRedirect());
            }
        }

        $reflectionClass->addMethod($input['method'], $input['body'], $input['signature']);

        return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_UPDATED,
            $this->getDynamicRedirect());
    }

    protected function _getMethodDeleteResponse($class, $prefix)
    {
        $reflectionClass = new ThemeHouse_Reflection_Class(get_class($class));

        $method = $this->_input->filterSingle('method', XenForo_Input::STRING);

        if (!$reflectionClass->hasMethod($method)) {
            return $this->responseNoPermission();
        }

        if ($this->isConfirmedPost()) {
            $reflectionClass->deleteMethod($method);

            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
                XenForo_Link::buildAdminLink($prefix,
                    array(
                        'class' => get_class($class)
                    )));
        } else {
            $viewParams = array(
                'class' => array(
                    'class' => get_class($class)
                ),
                'method' => $method,
                'prefix' => $prefix
            );

            return $this->responseView('ThemeHouse_Reflection_ViewAdmin_Method_Delete', 'th_method_delete_reflection', $viewParams);
        }
    }

    /**
     * Get the add-on model.
     *
     * @return XenForo_Model_AddOn
     */
    protected function _getAddOnModel()
    {
        return $this->getModelFromCache('XenForo_Model_AddOn');
    }
}