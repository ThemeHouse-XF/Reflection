<?php

class ThemeHouse_Reflection_Model_MethodTemplate extends XenForo_Model
{

    public function getMethodTemplateById($methodTemplateId)
    {
        return $this->_getDb()->fetchRow(
            '
                SELECT *
                FROM xf_method_template
                WHERE method_template_id = ?
            ', $methodTemplateId);
    }

    public function getMethodTemplates(array $conditions = array(), array $fetchOptions = array())
    {
        $limitOptions = $this->prepareLimitFetchOptions($fetchOptions);

        $whereConditions = $this->prepareMethodTemplateConditions($conditions, $fetchOptions);

        return $this->fetchAllKeyed(
            $this->limitQueryResults(
                '
                    SELECT *
                    FROM xf_method_template AS method_template
                    WHERE ' . $whereConditions . '
                ', $limitOptions['limit'], $limitOptions['offset']),
            'method_template_id');
    }

    public function prepareMethodTemplateConditions(array $conditions, array &$fetchOptions)
    {
        $sqlConditions = array();

        $db = $this->_getDb();

        if (!empty($conditions['content_type'])) {
            $sqlConditions[] = "method_template.content_type = " . $db->quote($conditions['content_type']);
        }

        return $this->getConditionsForClause($sqlConditions);
    }

    public function prepareMethodTemplate()
    {
        return array();
    }
}