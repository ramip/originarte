<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupOMIdMappingNewbeezupid extends BeezupOMIdMappingField
{

    /**
     * (non-PHPdoc)
     * @see BeezupOMIdMapping::find()
     */
    public function find($sSelector)
    {
        list(
            $nIdProduct, $nIdProductAttribute
            )
            = $this->getNewIdProductAndAttribute($sSelector);

        return $this->existsProduct($nIdProduct, $nIdProductAttribute)
            ? array($nIdProduct, $nIdProductAttribute) : array();
    }

    public function findAll($sSelector)
    {
        $aReturn = array($this->getNewIdProductAndAttribute($sSelector));
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
        $aElement = stristr($sSelector, '_') ? explode('_', $sSelector, 2)
            : array($sSelector, 0);

        return array_map('intval', $aElement);
    }
}
