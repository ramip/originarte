<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupOMValueExtractor
{

    /**
     * Extracts values from Prestashop objects
     *
     * @param string|array $mExtract Name of property or name of callback, or array with name of
     * @param source object $oObject
     *
     * @return mixed Extracted Value
     */
    public static function extract($mExtract, $oObject)
    {
        $mResult = null;
        if (is_object($oObject) && $oObject instanceof ObjectModel) {
            if (is_string($mExtract) && property_exists($oObject, $mExtract)) {
                $mResult = $oObject->{$mExtract};
            } else {
                if (is_string($mExtract) && method_exists($oObject, $mExtract)
                    && is_callable(array($oObject, $mExtract))
                ) {
                    $mResult = call_user_func(array($oObject, $mExtract));
                } else {
                    if (is_array($mExtract) && count($mExtract) > 0
                        && isset($mExtract['property'])
                        && property_exists($oObject, $mExtract['property'])
                    ) {
                        $mResult = $oObject->{$mExtract['property']};
                    } else {
                        if (is_array($mExtract) && count($mExtract) > 0
                            && isset($mExtract['method'])
                            && method_exists($oObject, $mExtract['method'])
                            && is_callable(array($oObject, $mExtract['method']))
                        ) {
                            $mResult = call_user_func_array(
                                array(
                                    $oObject,
                                    $mExtract,
                                ),
                                array_key_exists('args', $mExtract)
                                && is_array($mExtract['args'])
                                    ? $mExtract['args']
                                    : array()
                            );
                        } else {
                            if (is_array($mExtract) && count($mExtract) > 0) {
                                $mFirstExtract = array_shift($mExtract);
                                $mResult = self::extract(
                                    $mFirstExtract,
                                    $oObject
                                );
                                if (is_numeric($mResult)
                                    && Tools::substr($mFirstExtract, 0, 3)
                                    == 'id_'
                                    && class_exists(
                                        Tools::substr(
                                            $mFirstExtract,
                                            3
                                        )
                                    )
                                ) {
                                    $sClassName = Tools::substr(
                                        $mFirstExtract,
                                        3
                                    );
                                    $mResult = new $sClassName($mResult);
                                }
                                if (!empty($mExtract)) {
                                    $mResult = self::extract(
                                        $mExtract,
                                        $mResult
                                    );
                                }
                                $mExtract = $mFirstExtract;
                            }
                        }
                    }
                }
            }
        }
        // $mExtract: id_product, $mResult = 1 transforms into new Product(1)
        if (is_numeric($mResult) && is_string($mExtract)
            && Tools::substr($mExtract, 0, 3) == 'id_'
            && class_exists(Tools::substr($mExtract, 3))
        ) {
            $sClassName = Tools::substr($mExtract, 3);
            $mResult = new $sClassName($mResult);
        }

        return $mResult;
    }
}
