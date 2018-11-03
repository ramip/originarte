<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupOMIdMappingIdp extends BeezupOMIdMappingField
{
    protected $sFieldName = 'id_product';

    public function findAll($sSelector)
    {
        if (filter_var($sSelector, FILTER_VALIDATE_INT) === false) {
            return array();
        }

        return parent::findAll($sSelector);
    }

    protected function findAllProductAndAttributeIds($sQuery)
    {
        return array();
    }

    protected function getProductAttributeQuery($sSelector)
    {
        return '';
    }
}
