<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class GetCategoryProcessor extends BeezupProcessorAbstract
{
    /**
     * Extract and insert category information from product into xml
     *
     * @param Product             $product
     * @param BeezupField         $field
     * @param DOMElement          $xml
     * @param BeezupConfiguration $config
     * @param integer             $idDeclension
     * @param string              $productType
     */
    public function process(
        Product $product,
        BeezupField $field,
        DOMElement $xml,
        BeezupConfiguration $config,
        $idDeclension = null,
        $productType = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        if (BeezupRegistry::get('BEEZUP_CATEGORY_DEEPEST')) {
            $category_id = $this->getDeepestCategoryId($product);
        } else {
            $category_id = $product->id_category_default;
        } // if

        $list = $this->recursiveGetParent(
            new Category(
                $category_id,
                $config->id_default_lang
            ),
            $config->id_default_lang
        );

        $tmpField = clone($field);
        $length = count($list);
        for ($i = $length; $i > 0; $i--) {
            $category = $list[$i - 1];
            $tmpField->balise = $field->balise.'_'.($length - $i + 1);
            $this->addDOMElement($tmpField, $category->name, $xml);
        }
        unset($tmpField);
    }

    /**
     * Reccursively get array of category three path
     *
     * @param Category $category
     * @param integer  $idLang
     *
     * @return array
     */
    protected function recursiveGetParent(Category $category, $idLang)
    {
        if (!$category || !$category->id) {
            return array();
        } // if

        $result = array($category);
        if ($category->is_root_category
            || $category->id_parent == $category->id
        ) {
            return $result;
        } else {
            return array_merge(
                $result,
                $this->recursiveGetParent(
                    new Category(
                        $category->id_parent,
                        $idLang
                    ),
                    $idLang
                )
            );
        }
    }

    /**
     * Choosing deepest category in which product is placed; If there are more than one such category, category by default is favorised
     *
     * @param Product $product
     *
     * @return number Id of category
     */
    protected function getDeepestCategoryId(Product $product)
    {
        $query = 'SELECT cp.`id_category`, IF(cp.`id_category` = '
            .(int)$product->id_category_default.',1,0) AS is_default FROM `'
            ._DB_PREFIX_.'category_product` cp 
					LEFT JOIN `'._DB_PREFIX_.'category` c ON (c.id_category = cp.id_category) 
						'.Shop::addSqlAssociation('category', 'c').' 
					WHERE cp.`id_product` = '.(int)$product->id
            .' ORDER BY c.level_depth DESC, is_default DESC';

        $query_result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);

        return (int)($query_result ? $query_result['id_category']
            : $product->id_category_default);
    }
}
