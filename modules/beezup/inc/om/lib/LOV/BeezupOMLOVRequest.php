<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupOMLOVRequest extends BeezupOMRequest
{
    protected $sCultureName = 'en';
    protected $sListName = null;

    /**
     * @return the $sCultureName
     */
    public function getCultureName()
    {
        return $this->sCultureName;
    }

    /**
     * @param string $sCultureName
     */
    public function setCultureName($sCultureName)
    {
        $this->sCultureName = $sCultureName;

        return $this;
    }

    /**
     * @return the $sListName
     */
    public function getListName()
    {
        return $this->sListName;
    }

    /**
     * @param NULL $sListName
     */
    public function setListName($sListName)
    {
        $this->sListName = $sListName;

        return $this;
    }
}
