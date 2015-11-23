<?php

class ThemeHouse_Reflection_ControllerAdmin_MethodTemplate extends XenForo_ControllerAdmin_Abstract
{

    protected function _preDispatch($action) { }

    /**
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionIndex()
    {
        $methodTemplateModel = $this->_getMethodTemplateModel();

        $viewParams = array(
            'methodTemplates' => $this->_getMethodTemplateModel()->getMethodTemplates()
        );

        return $this->responseView('ThemeHouse_Reflection_ViewAdmin_MethodTemplate_List',
            'th_method_template_list_reflection', $viewParams);
    }

    /**
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    protected function _getMethodTemplateAddEditResponse(array $methodTemplate)
    {
        $methodTemplateModel = $this->_getMethodTemplateModel();

        $contentTypes = $this->_getReflectionModel()->getReflectionHandlerContentTypeNames();

        $viewParams = array(
            'methodTemplate' => $methodTemplate,

            'contentTypes' => $contentTypes
        );

        return $this->responseView('ThemeHouse_Reflection_ViewAdmin_MethodTemplate_Edit',
            'th_method_template_edit_reflection', $viewParams);
    }

    /**
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionAdd()
    {
        $methodTemplate = array();

        return $this->_getMethodTemplateAddEditResponse($methodTemplate);
    }

    /**
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionEdit()
    {
        $methodTemplateId = $this->_input->filterSingle('method_template_id', XenForo_Input::UINT);
        $methodTemplate = $this->_getMethodTemplateOrError($methodTemplateId);

        return $this->_getMethodTemplateAddEditResponse($methodTemplate);
    }

    /**
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionSave()
    {
        $this->_assertPostOnly();

        $methodTemplateId = $this->_input->filterSingle('method_template_id', XenForo_Input::UINT);
        $dwData = $this->_input->filter(
            array(
                'title' => XenForo_Input::STRING,
                'callback_class' => XenForo_Input::STRING,
                'callback_method' => XenForo_Input::STRING,
                'content_type' => XenForo_Input::STRING
            ));

        $dw = XenForo_DataWriter::create('ThemeHouse_Reflection_DataWriter_MethodTemplate');
        if ($methodTemplateId) {
            $dw->setExistingData($methodTemplateId);
        }
        $dw->bulkSet($dwData);
        $dw->save();

        $methodTemplateId = $dw->get('method_template_id');

        return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
            XenForo_Link::buildAdminLink('method-templates') . $this->getLastHash($methodTemplateId));
    }

    /**
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionDelete()
    {
        if ($this->isConfirmedPost()) {
            return $this->_deleteData('ThemeHouse_Reflection_DataWriter_MethodTemplate', 'method_template_id',
                XenForo_Link::buildAdminLink('method-templates'));
        } else {
            $methodTemplateId = $this->_input->filterSingle('method_template_id', XenForo_Input::UINT);
            $methodTemplate = $this->_getMethodTemplateOrError($methodTemplateId);

            $viewParams = array(
                'methodTemplate' => $methodTemplate
            );

            return $this->responseView('ThemeHouse_Reflection_ViewAdmin_MethodTemplate_Delete',
                'th_method_template_delete_reflection', $viewParams);
        }
    }

    /**
     *
     * @return array
     */
    protected function _getMethodTemplateOrError($methodTemplateId)
    {
        return $this->getRecordOrError($methodTemplateId, $this->_getMethodTemplateModel(), 'getMethodTemplateById',
            'th_method_template_not_found_reflection');
    }

    /**
     *
     * @return ThemeHouse_Reflection_Model_MethodTemplate
     */
    protected function _getMethodTemplateModel()
    {
        return $this->getModelFromCache('ThemeHouse_Reflection_Model_MethodTemplate');
    }

    /**
     *
     * @return ThemeHouse_Reflection_Model_Reflection
     */
    protected function _getReflectionModel()
    {
        return $this->getModelFromCache('ThemeHouse_Reflection_Model_Reflection');
    }
}