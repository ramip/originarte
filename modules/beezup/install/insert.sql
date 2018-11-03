
DELETE FROM `::DB_PREFIX::beezup_configuration`;

INSERT INTO `::DB_PREFIX::beezup_configuration`
	(`id_configuration`,`name`,`id_shop`, `id_shop_group`, `disable_disabled_product`,`disable_not_available`,`disable_oos_product`,`id_carrier`,`id_zone`,`image_type`,`id_default_lang`,`force_product_tax`,`set_attributes_as_product`)
VALUES
	(1,'default', NULL, NULL, 1,1,0,2, 1,'thickbox_default',1,0,0);

UPDATE `::DB_PREFIX::beezup_configuration` SET id_default_lang = (SELECT `value` FROM `::DB_PREFIX::configuration` WHERE name = 'PS_LANG_DEFAULT' LIMIT 1);

TRUNCATE TABLE `::DB_PREFIX::beezup_field`;

INSERT INTO `::DB_PREFIX::beezup_field`
	(`id_configuration`,`free_field`, `active`,`forced`,`editable`,`default`,`id_feature`,`id_attribute_group`,`values_list`,`balise`,`function`,`fields_group`)
VALUES
	-- References produits
	(1,0,1,1,0,'',0,0,'','reference','getReference','01.Référence produit'),
	(1,0,0,0,0,'',0,0,'','reference_produit','getProductReference','01.Référence produit'),
	(1,0,0,0,0,'',0,0,'','reference_fabriquant','getManufacturerReference','01.Référence produit'),
	(1,0,0,0,0,'',0,0,'','EAN','getEAN','01.Référence produit'),
	(1,0,0,0,0,'',0,0,'','UPC','getUpc','01.Référence produit'),
	-- Gestion déclinaisons
	(1,0,1,1,0,'',0,0,'','parent_or_child','getParentOrChild','01.Référence produit'),
	(1,0,1,1,0,'',0,0,'','parent_id','getParentId','01.Référence produit'),
	(1,0,0,0,0,'',0,0,'','parent_ref_produit','getParentProductReference','01.Référence produit'),
	-- Descriptif produit
	(1,0,1,1,0,'',0,0,'','marque','getManufacturer','02.Descriptif produit'),
	(1,0,1,1,0,'',0,0,'','nom','getName','02.Descriptif produit'),
	(1,0,1,1,0,'',0,0,'','resume','getShortDescription','02.Descriptif produit'),
	(1,0,1,1,0,'',0,0,'','description','getDescription','02.Descriptif produit'),
	(1,0,1,1,0,'',0,0,'','prix','getPriceTTC','02.Descriptif produit'),
	(1,0,0,0,0,'',0,0,'','ecotax','getEcoTax','02.Descriptif produit'),
	(1,0,0,0,0,'',0,0,'','etat','getCondition','02.Descriptif produit'),
	(1,0,0,0,1,'',0,0,'','garantie','getFeatureAttrGroup','02.Descriptif produit'),
	(1,0,0,0,0,'',0,0,'','poids','getWeight','02.Descriptif produit'),
	(1,0,0,0,1,'',0,0,'','type','getFeatureAttrGroup','02.Descriptif produit'),
	(1,0,1,1,0,'',0,0,'','url_produit','getURLProduct','02.Descriptif produit'),
	(1,0,1,1,0,'',0,0,'','url_image','getURLImage','02.Descriptif produit'),
	(1,0,0,0,0,'',0,0,'','attachements','getAttachements','02.Descriptif produit'),
	(1,0,0,0,0,'',0,0,'','tags','getTags','02.Descriptif produit'),
	(1,0,0,0,0,'',0,0,'','wholesale_price','getWholesalePrice','02.Descriptif produit'),
	(1,0,0,0,0,'',0,0,'','name_with_attributes','getNameAttributes','02.Descriptif produit'),
	(1,0,0,0,0,'',0,0,'','name_with_attributes_full','getGroupNameAttributes','02.Descriptif produit'),
	(1,0,0,0,0,'',0,0,'','all_features','getFeatures','02.Descriptif produit'),
	(1,0,0,0,0,'',0,0,'','all_features_oneblock','getFeaturesOneBlock','02.Descriptif produit'),
	(1,0,0,0,0,'',0,0,'','all_attributes','getAttributes','02.Descriptif produit'),
	(1,0,0,0,0,'',0,0,'','variation_theme','getVariationtheme','02.Descriptif produit'),

	-- Logistique
	(1,0,1,1,0,'',0,0,'','frais_de_port','getShipping','03.Logistique'),
	(1,0,1,0,0,'',0,0,'En stock|Hors stock','stock','getInStock','03.Logistique'),
	(1,0,1,0,0,'',0,0,'','quantite_stock','getStockQuantity','03.Logistique'),
	(1,0,1,1,1,'2-3',0,0,'','delais_livraison','getFeatureAttrGroup','03.Logistique'),
	(1,0,1,0,0,'',0,0,'','info_livraison','getDeliveryInfo','03.Logistique'),
	(1,0,0,0,0,'',0,0,'','largeur_colis','getProductWidth','03.Logistique'),
	(1,0,0,0,0,'',0,0,'','hauteur_colis','getProductHeight','03.Logistique'),
	(1,0,0,0,0,'',0,0,'','profondeur_colis','getProductDepth','03.Logistique'),
	(1,0,0,0,1,'0',0,0,'','nombre_colis','getFeatureAttrGroup','03.Logistique'),
	(1,0,0,0,0,'',0,0,'','message_en_stock','getProductAvailableNow','03.Logistique'),
	(1,0,0,0,0,'',0,0,'','message_hors_stock','getProductAvailableLater','03.Logistique'),
	(1,0,0,0,0,'',0,0,'','date_disponibilite','getProductAvailableDate','03.Logistique'),
	(1,0,0,0,0,'',0,0,'','additional_shipping_cost','getProductAdditionalShippingCost','03.Logistique'),




	-- Animation Commerciale
	(1,0,1,0,0,'',0,0,'','prix_barre','getPrixBarre','04.Animation commerciale'),
	(1,0,0,0,0,'',0,0,'','date_debut_promo','getPromoStart','04.Animation commerciale'),
	(1,0,0,0,0,'',0,0,'','date_fin_promo','getPromoEnd','04.Animation commerciale'),
	(1,0,0,0,0,'',0,0,'normal|special|nouveau|promo|solde','type_offre','getPromoType','04.Animation commerciale'),
	(1,0,0,0,1,'0',0,0,'','bundle','getBundle','04.Animation commerciale'),

	-- Categories
	(1,0,1,1,0,'0',0,0,'','categorie','getCategory','05.Catégories'),


	-- Vetements
	(1,0,0,0,1,'',0,0,'Homme|Femme|Fille|Garçon|Unisexe','vetements_sexe','getFeatureAttrGroup','06.Vêtements'),
	(1,0,0,0,1,'',0,0,'','vetements_couleur','getFeatureAttrGroup','06.Vêtements'),
	(1,0,0,0,1,'',0,0,'','vetements_taille','getFeatureAttrGroup','06.Vêtements'),
	(1,0,0,0,1,'',0,0,'','vetements_tranche_age','getFeatureAttrGroup','06.Vêtements'),


	-- Référencement
	(1,0,0,0,0,'',0,0,'','seo_title','getMetaTitle','07.Référencement'),
	(1,0,0,0,0,'',0,0,'','seo_metadescription','getMetaDescription','07.Référencement'),
	(1,0,0,0,0,'',0,0,'','seo_urlsimplifie','getMetaLinkRewrite','07.Référencement'),
	(1,0,0,0,0,'',0,0,'','seo_metamotscles','getMetaKeywords','07.Référencement');
