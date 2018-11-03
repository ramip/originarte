<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupOMIdMappingId extends BeezupOMIdMapping
{
    protected $nMultiplicator = null;

    /**
     * (non-PHPdoc)
     * @see BeezupOMIdMapping::find()
     */
    public function find($sSelector)
    {
        if (BeezupRegistry::get('BEEZUP_NEW_PRODUCT_ID_SYSTEM')) {
            list(
                $nIdProduct, $nIdProductAttribute
                )
                = $this->getNewIdProductAndAttribute($sSelector);
        } else {
            list(
                $nIdProduct, $nIdProductAttribute
                )
                = $this->getOldIdProductAndAttribute($sSelector);
        }

        return $this->existsProduct($nIdProduct, $nIdProductAttribute)
            ? array($nIdProduct, $nIdProductAttribute) : array();
    }

    public function findAll($sSelector)
    {
        if (BeezupRegistry::get('BEEZUP_NEW_PRODUCT_ID_SYSTEM')) {
            $aReturn = array($this->getNewIdProductAndAttribute($sSelector));
        } else {
            $aReturn = array($this->getOldIdProductAndAttribute($sSelector));
        }

        foreach (array_filter($aReturn) as $nIndex => $aValue) {
            if (!is_array($aValue) || count($aValue) != 2) {
                unset($aReturn[$nIndex]);
                continue;
            }
            list($nIdProduct, $nIdProductAttribute) = $aValue;
            if (!$this->existsProduct($nIdProduct, $nIdProductAttribute)) {
                unset($aReturn[$nIndex]);
            }
        }

        return array_values($aReturn);
    }


    protected function getNewIdProductAndAttribute($sSelector)
    {
        $nIdProduct = 0;
        $nIdProductAttribute = 0;
        $aElement = stristr($sSelector, '_') ? explode('_', $sSelector)
            : array($sSelector, 0);
        if (count($aElement) != 2) {
            return array(0, 0);
        }
        foreach ($aElement as $element) {
            if (filter_var($element, FILTER_VALIDATE_INT) === false) {
                return array(0, 0);
            }
        }

        return array_map('intval', $aElement);
    }

    protected function getOldIdProductAndAttribute($sSelector)
    {
        if (filter_var($sSelector, FILTER_VALIDATE_INT) === false) {
            return array(0, 0);
        }
        $nId = (int)$sSelector;
        $nMultiplicator = $this->getMultiplicator();
        $nIdProductAttribute = $nId % $nMultiplicator;
        $nIdProduct = ($nId - $nIdProductAttribute) / $nMultiplicator;

        return array($nIdProduct, $nIdProductAttribute);
    }

    protected function getMultiplicator()
    {
        if ($this->nMultiplicator === null) {
            $nMax = Db::getInstance()
                ->getValue(
                    'SELECT MAX(`id_product_attribute`) FROM `'
                    ._DB_PREFIX_.'product_attribute`'
                );
            $nMultiplicator = 1;
            while ($nMultiplicator < (int)$nMax) {
                $nMultiplicator *= 10;
            }
            $this->nMultiplicator = $nMultiplicator;
        }

        return $this->nMultiplicator;
    }
}
