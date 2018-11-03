<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupOMProductIdentityMapper
{
    protected $aFieldMappings = array();
    protected $aIdFieldsMappingsCache = array();
    protected $bDebugMode = false;

    protected $aPsIdentityFields
        = array(
            array('value' => 'id', 'name' => 'Reference (reference)'),
            array(
                'value' => 'reference',
                'name'  => 'Product reference (reference_produit)',
            ),
            array('value' => 'ean', 'name' => 'Code EAN 13 (EAN)'),
            array(
                'value' => 'referenceSupplier',
                'name'  => 'Supplier reference (reference_fabriquant)',
            ),
            array('value' => 'name', 'name' => 'Product name (name)'),
            array('value' => 'idp', 'name' => 'Product id (idp)'),
            array('value' => 'idd', 'name' => 'Declination id (idd)'),
            array(
                'value' => 'idpidd',
                'name'  => 'Product id + Declination id (idpidd)',
            ),
            array(
                'value' => 'newbeezupid',
                'name'  => 'New reference (idp_idd)',
            ),
            array(
                'value' => 'fakereference',
                'name'  => 'CREATE PRODUCT matching reference_produit',
            ),
        );

    # PRODUCT IDENTITY MAPPING

    /**
     *
     * @param array $aFieldMappings
     */
    public function __construct(array $aFieldMappings = array())
    {
        $this->aFieldMappings = $aFieldMappings;
    }

    public function setDebugMode($bDebugMode)
    {
        $this->bDebugMode = (bool)$bDebugMode;
    }

    public function isDebugModeActivated()
    {
        return $this->bDebugMode;
    }

    /**
     * Returns possible identity fields
     *
     * @return array of array <value=>string: name=>string: display name>
     */
    public function getPsIdentityFields()
    {
        if ($this->isDebugModeActivated()) {
            return $this->aPsIdentityFields;
        } else {
            $aResult = array();
            foreach ($this->aPsIdentityFields as $aField) {
                if ($aField['value'] === 'fakereference') {
                    continue;
                }
                $aResult[] = $aField;
            }
            return $aResult;
        }
    }

    public function getMappingCallbacks($sStore)
    {
        $aResult = array();
        $aMappings = array_key_exists($sStore, $this->aFieldMappings)
            ? $this->aFieldMappings[$sStore] : array();
        foreach ($aMappings as $sName) {
            if (!$this->isDebugModeActivated() && $sName === 'fakereference') {
                continue;
            }
            $aResult[] = $this->getMappingCallback($sName);
        }

        return $aResult;
    }

    /**
     * Returns instance of mapping class
     * Instance once created is cached, so effectively it works as singleton
     *
     * @param string $sStore Store name
     *
     * @throws RuntimeException If unable to find / load corresponding class
     * @return BeezupOMIdMapping Mapping class
     * @todo think about possible configuration of mapping class
     */
    public function getMappingCallback($sName)
    {
        if (!isset($this->aIdFieldsMappingsCache[$sName])) {
            if (!$sName) {
                throw new RuntimeException(
                    sprintf(
                        'Unable to find identity field mapping %s',
                        $sName
                    )
                );
            } // if
            $sMappingClassPath = dirname(__FILE__).DIRECTORY_SEPARATOR
                .'mappings'.DIRECTORY_SEPARATOR;
            $sClassName = 'BeezupOMIdMapping'
                .Tools::ucfirst(Tools::strtolower($sName));
            $sFileName = $sMappingClassPath.$sClassName.'.php';
            if (!file_exists($sFileName)) {
                throw new RuntimeException(
                    sprintf(
                        'Unable to load mapping file %s for mapping %s',
                        $sFileName,
                        $sName
                    )
                );
            }
            require_once $sMappingClassPath.'BeezupOMIdMapping.php';
            require_once $sMappingClassPath.'BeezupOMIdMappingComposed.php';
            require_once $sMappingClassPath.'BeezupOMIdMappingField.php';
            require_once $sFileName;
            if (!class_exists($sClassName, false)) {
                throw new RuntimeException(
                    sprintf(
                        'Unable to find class %s for mapping %s',
                        $sClassName,
                        $sName
                    )
                );
            }
            $this->aIdFieldsMappingsCache[$sName] = new $sClassName();
            $this->aIdFieldsMappingsCache[$sName]->setMappingIdentifier($sName);
        }

        return $this->aIdFieldsMappingsCache[$sName];
    }
}
