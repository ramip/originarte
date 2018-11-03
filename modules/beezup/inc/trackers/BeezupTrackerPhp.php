<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupTrackerPhp extends BeezupTrackerAbstract
{
    public function trackPaymentTop()
    {
        $cart = Context::getContext()->cart;
        $smarty = Context::getContext()->smarty;
        $products = $cart->getProducts();
        $productId = array();
        $productPrice = array();
        $productQuantity = array();
        $productMargin = array();

        $orderTotal = Tools::ps_round(
            $cart->getOrderTotal(
                false,
                Cart::BOTH_WITHOUT_SHIPPING
            ),
            2
        );

        foreach ($products as $product) {
            $idProduct = (int)$product['id_product'];
            $idProductAttribute = (int)$product['id_product_attribute'];

            if (Configuration::get('BEEZUP_TRACKER_USE_PS_ID')) {
                $productId[] = $idProduct;
            } else {
                $productId[] = BeezupProduct::getIdProductAndAttribute(
                    $idProduct,
                    $idProductAttribute
                );
            }
            $fProductPrice = 0;
            $productPrice[]
                = $fProductPrice = Product::getPriceStatic(
                    $idProduct,
                    false,
                    $idProductAttribute,
                    2,
                    null,
                    false,
                    true,
                    1,
                    false,
                    null,
                    (int)$cart->id
                );

            $productQuantity[] = (int)$product['cart_quantity'];


            if ($this->getUseMargins()) {
                $productMargin[] = $fProductPrice - Tools::ps_round(
                    $this->_getWholesalePrice(
                        $idProduct,
                        $idProductAttribute
                    ),
                    2
                );
            }
        }

        $imgSrc = $this->getBaseUrl()
            .'/SO?StoreId='.$this->getStoreId()
            .'&OrderMerchantId='.(int)$cart->id
            .'&TotalCost='.$orderTotal
            .'&ValidPayement=FALSE'
            .'&ListProductId='.implode('|', $productId)
            .'&ListProductQuantity='.implode('|', $productQuantity)
            .'&ListProductUnitPrice='.implode('|', $productPrice);

        if ($this->getUseMargins()) {
            $imgSrc .= '&ListProductMargin='.implode('|', $productMargin);
        }

        return '<img src="'.$imgSrc.'" alt="" />';
    }


    public function trackOrderConfirmation(Order $order)
    {
        $this->_validatePaymentTracker($order);
    }

    public function trackNewOrder(Order $order)
    {
        if (0 == $this->getValidationMethod()) {
            $this->_validatePaymentTracker($order);
        }
    }

    public function trackOrderUpdate(Order $order, OrderState $orderState)
    {
        if (1 == $this->getValidationMethod()
            && $orderState->id == _PS_OS_DELIVERED_
        ) {
            $this->_validatePaymentTracker($order);
        }
    }

    /**
     * Generic validation method
     *
     * @access protected
     *
     * @param Order $order
     * @@return boolean
     */
    protected function _validatePaymentTracker(Order $order)
    {
        if (!Validate::isLoadedObject($order)) {
            return false;
        }

        if ($order->id_cart) {
            $url = $this->getBaseUrl()
                .'/Tracker/UpdateValidPayement'
                .'?storeId='.$this->getStoreId()
                .'&orderMerchantId='.$order->id_cart
                .'&validPayement=true'
                .'&jsiId=JSOrderUpdate'
                .'&VisitorId='.(int)$order->id_customer;

            if (extension_loaded('curl')) {
                $ch = curl_init();
                curl_setopt_array(
                    $ch,
                    array(
                        CURLOPT_URL            => $url,
                        CURLOPT_CONNECTTIMEOUT => 10,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_FAILONERROR    => true,
                        CURLOPT_SSL_VERIFYPEER => false,
                    )
                );
                $ret = curl_exec($ch);

                if (false === $ret) {
                    $this->getModule()->addLog(
                        'Tracker error : '
                        .curl_error($ch)
                        .' : '.$url
                    );
                }
                curl_close($ch);
            } else {
                $ret = Tools::file_get_contents($url);
            }
        }

        return true;
    }

    /**
     * Get product wholesaleprice (with or without attribute)
     *
     * @param integer $idProduct
     * @param integer $idProductAttribute
     *
     * @return float
     */
    protected function _getWholesalePrice($idProduct, $idProductAttribute = 0)
    {
        $sql = 'SELECT '
            .'p.`wholesale_price` AS product_price, '
            .'pa.`wholesale_price` AS product_attribute_price '
            .'FROM `'._DB_PREFIX_.'product` p '
            .'LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa '
            .'ON p.`id_product` = pa.`id_product` '
            .'AND pa.`id_product_attribute` = '.(int)$idProductAttribute.' '
            .'WHERE p.`id_product` = '.(int)$idProduct;

        $line = Db::getInstance()->getRow($sql);

        if ($line['product_attribute_price'] > 0) {
            return (float)$line['product_attribute_price'];
        } else {
            return (float)$line['product_price'];
        }
    }
}
