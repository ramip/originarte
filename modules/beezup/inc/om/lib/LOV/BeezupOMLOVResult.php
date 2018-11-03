<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupOMLOVResult extends BeezupOMResult
{
    protected $aValues = array();


    /**
     * @return the $aValues
     */
    public function getValues()
    {
        return $this->aValues;
    }

    /**
     * @param multitype: $aValues
     */
    public function setValues($aValues)
    {
        $this->aValues = $aValues;

        return $this;
    }

    public function addValue(BeezupOMLOVValue $oValue)
    {
        $this->aValues[] = $oValue;

        return $this;
    }

    public static function fromArray(array $aData = array())
    {
        $oResult = new BeezupOMLOVResult();
        if (array_key_exists('values', $aData) && is_array($aData['values'])) {
            foreach ($aData['values'] as $aValue) {
                $oResult->addValue(BeezupOMLOVValue::fromArray($aValue));
            }
        }

        return $oResult;
    }

    public function toArray()
    {
        $aResult = array(
            'values' => array(),
        );

        foreach ($this->getValues() as $oValue) {
            $aResult['values'][] = $oValue->toArray();
        }

        return $aResult;
    }

    public function getCodeIdentifiers()
    {
        $aResult = array();
        foreach ($this->getValues() as $oValue) {
            $aResult[] = $oValue->getCodeIdentifier();
        }

        return $aResult;
    }
}
