<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupOMDataHandler
{

    /**
     * Words which don't
     *
     * @var unknown_type
     */
    protected $aGettersNameReplacement
        = array(
            'src' => array(
                'BeezUP',
                'UUID',
                'ECommerce',
                'LOV',
                'CSharp',
                'ETag',
                'MarketPlace',
            ),
            'tgt' => array(
                'Beezup',
                'Uuid',
                'Ecommerce',
                'Lov',
                'Csharp',
                'Etag',
                'Marketplace',
            ),
        );

    protected $sUtcDateCastFormat = 'Y-m-d\TH:i:s\Z';//DateTime::ISO8601;

    /**
     * Creates new object from array
     *
     * @require PHP 5.3.0
     *
     * @param array $aData
     *
     * @return static Self
     */
    public static function fromArray(array $aData = array())
    {
        $oResult = new static;
        foreach ($aData as $sKey => $mValue) {
            $sCamelCaseKey = preg_replace_callback(
                '#_(\S)#',
                function ($aMatches) {
                    return Tools::strtoupper($aMatches[1]);
                },
                Tools::strtolower($sKey)
            );
            $sSetterMethod = 'set'.Tools::ucfirst($sCamelCaseKey);
            if (method_exists($oResult, $sSetterMethod) && is_scalar($mValue)) {
                call_user_func(
                    array($oResult, $sSetterMethod),
                    stristr(Tools::strtolower($sKey), 'utcdate')
                        ? new DateTime($mValue, new DateTimeZone('UTC'))
                        : $mValue
                );
            } // if
        } // foreach

        return $oResult;
    }

    public function toArray()
    {
        $aResult = array();
        $oReflection = new ReflectionClass($this);
        foreach ($oReflection->getMethods() as $oMethod) {
            $sName = $oMethod->getName();
            if (Tools::substr($sName, 0, 3) === 'get'
                && $sName !== 'getTransitionLinkByRel'
                && $sName !== 'getLinkByRel'
            ) {
                $sName = str_replace(
                    $this->aGettersNameReplacement['src'],
                    $this->aGettersNameReplacement['tgt'],
                    $sName
                );
                $sExportName = trim(
                    Tools::strtolower(
                        preg_replace(
                            '/([A-Z])/',
                            '_$1',
                            Tools::substr($sName, 3)
                        )
                    ),
                    '_'
                );
                $aResult[$sExportName]
                    = $this->convert(
                    call_user_func(
                        array(
                            $this,
                            $oMethod->getName(),
                        )
                    )
                );
            }
        }

        return $aResult;
    }

    protected function convert($mValue)
    {
        if (is_object($mValue)) {
            if (method_exists($mValue, 'toArray')) {
                $mValue = call_user_func(array($mValue, 'toArray'));
            } else {
                if ($mValue instanceof DateTime) {
                    $mValue = $mValue->format($this->sUtcDateCastFormat);
                } else {
                    if (method_exists($mValue, '__toString')) {
                        $mValue = (string)$mValue;
                    }
                }
            }
        } else {
            if (is_array($mValue)) {
                foreach ($mValue as $mKey => $mElement) {
                    $mValue[$mKey] = $this->convert($mElement);
                }
            }
        }

        return $mValue;
    }
}
