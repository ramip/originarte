<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupFieldProcessor implements BeezupProcessorInterface
{
    /** @var BeezupFieldProcessor class instance for Singleton pattern */
    protected static $instance;

    /** @var array specific subprocessors list */
    protected $processors = array();

    /**
     * Initalise processor by loading all subprocessors and context
     *
     * @return void
     */
    protected function __construct()
    {
        $this->loadProcessors()
            ->loadContext();
    }

    /**
     * Disabled clone for Singleton pattern
     *
     * @return void
     */
    protected function __clone()
    {
    }

    /**
     * Get processor instance
     *
     * @return BeezupFieldProcessor
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Load all subprocessors
     *
     * @return BeezupFieldProcessor
     */
    protected function loadProcessors()
    {
        $files = glob(dirname(__FILE__).'/processors/*Processor.php');

        foreach ($files as $file) {
            $className = pathinfo($file, PATHINFO_FILENAME);
            require_once($file);

            if (!class_exists($className, false)) {
                continue;
            }

            $processor = new $className();

            $methodName = Tools::strtolower(Tools::substr($className, 0, 1))
                .(Tools::substr($className, 1, -9));

            if (!$processor instanceof BeezupProcessorInterface) {
                continue;
            }

            $this->processors[$methodName] = $processor;
        }

        return $this;
    }

    /**
     * Inialize Prestashop context
     *
     * @return BeezupFieldProcessor
     */
    protected function loadContext()
    {
        $ctx = Context::getContext();
        $ctx->country = new Country((int)Configuration::get('BEEZUP_COUNTRY'));

        return $this;
    }

    /**
     * Process product by finding related subprocessor from BeezupField
     * or insert error attribute in xml if no subprocessor found
     *
     * @param Product             $product
     * @param BeezupField         $field
     * @param DOMElement          $xml
     * @param BeezupConfiguration $config
     * @param integer             $idDeclension
     * @param string              $productType
     *
     * @return mixed
     */
    public function process(
        Product $product,
        BeezupField $field,
        DOMElement $xml,
        BeezupConfiguration $config,
        $idDeclension = null,
        $productType = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        if (array_key_exists($field->function, $this->processors)) {
            return $this->processors[$field->function]->process(
                $product,
                $field,
                $xml,
                $config,
                $idDeclension,
                $productType
            );
        } else {
            $DomElement = new DOMElement(
                $field->balise,
                'Unknown processor '.Tools::ucfirst($field->function)
                .'Processor'
            );
            $xml->appendChild($DomElement);
            $DomElement->setAttribute('error', true);
        }
    }
}
