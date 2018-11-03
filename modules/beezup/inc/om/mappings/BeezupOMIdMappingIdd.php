<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupOMIdMappingIdd extends BeezupOMIdMappingField
{
    protected $sFieldName = 'id_product_attribute';

    protected function findAllProductIds($sQuery)
    {
        return array();
    }

    public function findAll($sSelector)
    {
        if (filter_var($sSelector, FILTER_VALIDATE_INT) === false) {
            return array();
        }

        return parent::findAll($sSelector);
    }


    protected function getProductQuery($sSelector)
    {
        return '';
    }
}
