<?php

class ThemeHouse_Reflection_DataWriter_MethodTemplate extends XenForo_DataWriter
{

    /**
     * Gets the fields that are defined for the table.
     * See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            'xf_method_template' => array(
                'method_template_id' => array(
                    'type' => self::TYPE_UINT,
                    'autoIncrement' => true
                ),
                'title' => array(
                    'type' => self::TYPE_STRING,
                    'required' => true
                ),
                'callback_class' => array(
                    'type' => self::TYPE_STRING,
                    'required' => true
                ),
                'callback_method' => array(
                    'type' => self::TYPE_STRING,
                    'autoIncrement' => true
                ),
                'content_type' => array(
                    'type' => self::TYPE_STRING,
                    'required' => true
                )
            )
        );
    }

    /**
     * Gets the actual existing data out of data that was passed in.
     * See parent for explanation.
     *
     * @param mixed
     *
     * @return array|false
     */
    protected function _getExistingData($data)
    {
        if (!$methodTemplateId = $this->_getExistingPrimaryKey($data, 'method_template_id')) {
            return false;
        }

        $methodTemplate = $this->_getMethodTemplateModel()->getMethodTemplateById($methodTemplateId);
        if (!$methodTemplate) {
            return false;
        }

        return $this->getTablesDataFromArray($methodTemplate);
    }

    /**
     * Gets SQL condition to update the existing record.
     *
     * @return string
     */
    protected function _getUpdateCondition($tableName)
    {
        return 'method_template_id = ' . $this->_db->quote($this->getExisting('method_template_id'));
    }

    /**
     *
     * @return ThemeHouse_Reflection_Model_MethodTemplate
     */
    protected function _getMethodTemplateModel()
    {
        return $this->getModelFromCache('ThemeHouse_Reflection_Model_MethodTemplate');
    }
}