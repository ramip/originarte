<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupOMOrderIdentifier
{

    # PROTECTED VARIABLES

    /**
     * For example "Amazon" or "CDiscount"
     *
     * @var string
     */
    protected $sMarketplaceTechnicalCode = '';

    /**
     * Account id, for example "1234"
     *
     * @var string Numeric string
     */
    protected $sAccountId = '';

    /**
     * Unique order id, for example "8D1DE1CE0BCC5AB98758345fbd14f95a6e17431458ecdd6"
     *
     * @var string 47 characters
     */
    protected $sBeezupOrderUUID = '';

    # STATIC METHODS

    /**
     * Creates identifier object from Beezup order
     *
     * @param BeezupOMOrderResult $oBeezupOrder
     *
     * @return BeezupOMOrderIdentifier New identifier
     */
    public static function fromBeezupOrder(BeezupOMOrderResult $oBeezupOrder)
    {
        $oResult = new self();

        return $oResult
            ->setMarketplaceTechnicalCode(
                $oBeezupOrder->getMarketPlaceTechnicalCode()
            )
            ->setAccountId($oBeezupOrder->getAccountId())
            ->setBeezupOrderUUID($oBeezupOrder->getBeezupOrderUUID());
        $oResult;
    } // fromBeezupOrder


    public static function fromLink($sLink)
    {
        $oResult = new self();
        $aData = explode('/', $sLink);
        if (count($aData) >= 6 && is_numeric($aData[4])) {
            return $oResult
                ->setMarketplaceTechnicalCode($aData[3])
                ->setAccountId($aData[4])
                ->setBeezupOrderUUID($aData[5]);
        } else {throw new Exception(
                sprintf(
                    'Unable to convert %s into order id',
                    $sLink
                )
            );
        }

        return $oResult;
    } // fromBeezupOrder

    public static function fromUrl($sUrl)
    {
        if (stristr($sUrl, 'go.beezup.com') !== false) {
            return self::fromGoUrl($sUrl);
        } else {
            if (stristr($sUrl, 'go2.beezup.com') !== false) {
                return self::fromGo2Url($sUrl);
            } else {
                if (stristr($sUrl, 'api.beezup.com') !== false) {
                    return self::fromApiUrl($sUrl);
                }
            }
        }
        throw new Exception(
            sprintf(
                'Unable to convert %s into order id',
                $sUrl
            )
        );
    }


    public static function fromGo2Url($sUrl)
    {
        $aData = array(
            'accountid'                => '',
            'beezuporderuuid'          => '',
            'marketplacetechnicalcode' => '',
        );
        if (!preg_match(
            "/^https?:\/\/go2.beezup.com\/index.html#!\/(app\/[\w\d-]+\/orders\/)?order\/[\w]+\/[\d]+\/[\d\w]+$/",
            $sUrl
        )
        ) {
            return $aData;
        }

        $regex
            = "/^https?:\/\/go2.beezup.com\/index.html#!\/(?:app\/[\w\d-]+\/orders\/)?order\/([\w]+)\/([\d]+)\/([\d\w]+)$/";
        preg_match($regex, $sUrl, $matches);
        $aData = array(
            'accountid'                => $matches[2],
            'beezuporderuuid'          => $matches[3],
            'marketplacetechnicalcode' => $matches[1],
        );

        return self::fromArray($aData);
    }

    public static function fromGoUrl($sUrl)
    {
        $aData = array(
            'accountid'                => '',
            'beezuporderuuid'          => '',
            'marketplacetechnicalcode' => '',
        );
        $sQuery = parse_url($sUrl, PHP_URL_QUERY);
        if ($sQuery) {
            parse_str($sQuery, $aQuery);
            if ($aQuery && is_array($aQuery)) {
                $aData['accountid'] = isset($aQuery['AccountId'])
                    ? $aQuery['AccountId'] : '';
                $aData['beezuporderuuid']
                    = isset($aQuery['BeezUPOrderUUId'])
                    ? $aQuery['BeezUPOrderUUId'] : '';
                $aData['marketplacetechnicalcode']
                    = isset($aQuery['MarketplaceTechnicalCode'])
                    ? $aQuery['MarketplaceTechnicalCode'] : '';
            }

            return self::fromArray($aData);
        }
        throw new Exception(
            sprintf(
                'Unable to convert %s into order id',
                $sUrl
            )
        );
    }

    public static function fromApiUrl($sUrl)
    {
        $aData = array(
            'accountid'                => '',
            'beezuporderuuid'          => '',
            'marketplacetechnicalcode' => '',
        );
        $sPath = parse_url($sUrl, PHP_URL_PATH);
        if ($sPath) {
            $aPath = explode('/', $sPath);
            if ($aPath && is_array($aPath)) {
                $aData['accountid'] = isset($aPath[5]) ? $aPath[5]
                    : '';
                $aData['beezuporderuuid'] = isset($aPath[6])
                    ? $aPath[6] : '';
                $aData['marketplacetechnicalcode'] = isset($aPath[4])
                    ? $aPath[4] : '';
            }

            return self::fromArray($aData);
        }
        throw new Exception(
            sprintf(
                'Unable to convert %s into order id',
                $sUrl
            )
        );
    }


    public static function fromBeezupOrderLink(BeezupOMLink $oLink)
    {
        return self::fromLink($oLink->getHref());
    } // fromBeezupOrder

    /**
     * Creates identifier object from array
     *
     * @param array $aData Should contain 3 keys: accountid, beezuporderuuid and marketplacetechnicalcode (key casing is insensitive)
     *
     * @throws InvalidArgumentException When array do not contain all required keys (values can be empty, though)
     * @return BeezupOMOrderIdentifier New identifier
     */
    public static function fromArray(array $aData = array())
    {
        $aData = array_change_key_case($aData, CASE_LOWER);
        if (!array_key_exists('accountid', $aData)
            || !array_key_exists('beezuporderuuid', $aData)
            || !array_key_exists('marketplacetechnicalcode', $aData)
        ) {
            $sErrorMessage
                = 'Array should have accountid, beezuporderuuid and marketplacetechnicalcode keys, '
                .(count($aData) ? implode(' ,', array_keys($aData)) : ' no key')
                .' given';
            throw new InvalidArgumentException($sErrorMessage);
        }
        $oResult = new BeezupOMOrderIdentifier();
        $oResult
            ->setMarketplaceTechnicalCode($aData['marketplacetechnicalcode'])
            ->setAccountId($aData['accountid'])
            ->setBeezupOrderUUID($aData['beezuporderuuid']);

        return $oResult;
    } // fromArray

    # MAGIC METHODS

    /**
     * Return order identifier as string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getMarketplaceTechnicalCode().'/'.$this->getAccountId()
            .'/'.$this->getBeezupOrderUUID();
    } // __toString

    # PUBLIC METHODS

    /**
     * Returns order identifier as array
     *
     * @return array of <string:key => string:value>
     */
    public function toArray()
    {
        return array(
            'MarketPlaceTechnicalCode' => $this->getMarketplaceTechnicalCode(),
            'AccountId'                => $this->getAccountId(),
            'BeezUPOrderUUID'          => $this->getBeezupOrderUUID(),
        );
    } // toArray

    # SETTERS & GETTERS

    /**
     * Sets marketplace technical code
     *
     * @param string $sMarketplaceTechnicalCode
     *
     * @return BeezupOMOrderIdentifier
     */
    public function setMarketplaceTechnicalCode($sMarketplaceTechnicalCode)
    {
        $this->sMarketplaceTechnicalCode = (string)$sMarketplaceTechnicalCode;

        return $this;
    } // setMarketplaceTechnicalCode

    /**
     * Gets marketplace technical code
     *
     * @return string marketplace technical code
     */
    public function getMarketplaceTechnicalCode()
    {
        return $this->sMarketplaceTechnicalCode;
    } // getMarketplaceTechnicalCode

    /**
     * Gets account id
     *
     * @param string $sAccountId
     *
     * @return BeezupOMOrderIdentifier Self
     */
    public function setAccountId($sAccountId)
    {
        $this->sAccountId = (string)$sAccountId;

        return $this;
    } // setAccountId

    /**
     * Sets account id
     *
     * @return string
     */
    public function getAccountId()
    {
        return $this->sAccountId;
    } // getAccountId

    /**
     * Sets unique order id
     *
     * @param string $sBeezupOrderUUID
     *
     * @return BeezupOMOrderIdentifier
     */
    public function setBeezupOrderUUID($sBeezupOrderUUID)
    {
        $this->sBeezupOrderUUID = (string)$sBeezupOrderUUID;

        return $this;
    } // setBeezupOrderUUID

    /**
     * Gets unique order id
     *
     * @return string
     */
    public function getBeezupOrderUUID()
    {
        return $this->sBeezupOrderUUID;
    } // getBeezupOrderUUID
}
