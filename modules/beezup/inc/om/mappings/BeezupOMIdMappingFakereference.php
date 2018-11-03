<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupOMIdMappingFakeReference extends BeezupOMIdMappingField
{
    protected static $aCache = array();

    protected $sFieldName = 'reference';

    public function findAll($sSelector)
    {
        $aResult = array();
        if (isset(self::$aCache[$sSelector])) {
            return $aResult;
        }

        $sQueryAttrs = $this->getProductAttributeQuery($sSelector);
        $sQueryProducts = $this->getProductQuery($sSelector);

        $aResult
            = array_merge(
                $this->findAllProductAndAttributeIds($sQueryAttrs),
                $this->findAllProductIds($sQueryProducts)
            );

        $aResult = array_unique($aResult, SORT_REGULAR);

        if (empty($aResult)) {
            $oProduct = new Product();

            $oProduct->name
                = array(
                (int)Configuration::get('PS_LANG_DEFAULT') => "fake product "
                    .$sSelector,
            );
            $oProduct->reference = $sSelector;
            $oProduct->price = rand(1, 1000) + (rand(0, 100) / 100);
            $oProduct->link_rewrite
                = array(
                (int)Configuration::get(
                    'PS_LANG_DEFAULT'
                ) => 'auto-rewrite-failed-'
                    .$sSelector,
            );
            $oProduct->quantity = rand(10, 1000);
            $oProduct->id_category_default
                = (int)Configuration::get('PS_HOME_CATEGORY');
            $oProduct->description_short
                = array(
                (int)Configuration::get(
                    'PS_LANG_DEFAULT'
                ) => "fake product description short "
                    .$sSelector,
            );
            $oProduct->description
                = array(
                (int)Configuration::get(
                    'PS_LANG_DEFAULT'
                ) => "fake product description  "
                    .$sSelector,
            );
            $oProduct->active = 1;
            if ($oProduct->add() && $oProduct->id
                && Validate::isLoadedObject($oProduct)
            ) {
                self::$aCache[$sSelector] = $oProduct->id;

                return array(array($oProduct->id, 0));
            }
        }

        return array();
    }
}
