INSERT INTO `::DB_PREFIX::beezup_field`
	(`id_configuration`,`active`,`forced`,`editable`,`default`,`id_feature`,`id_attribute_group`,`values_list`,`balise`,`function`,`fields_group`)
VALUES
	(1,0,0,0,'','','','','attachements','getAttachements','02.Descriptif produit'),
	(1,0,0,0,'','','','','tags','getTags','02.Descriptif produit'),
	(1,0,0,0,'','','','','largeur_colis','getProductWidth','03.Logistique'),
	(1,0,0,0,'','','','','hauteur_colis','getProductHeight','03.Logistique'),
	(1,0,0,0,'','','','','profondeur_colis','getProductDepth','03.Logistique'),
	(1,0,0,1,'0','','','','nombre_colis','getFeatureAttrGroup','03.Logistique'),
	(1,0,0,0,'','','','','message_en_stock','getProductAvailableNow','03.Logistique'),
	(1,0,0,0,'','','','','message_hors_stock','getProductAvailableLater','03.Logistique'),
	(1,0,0,0,'','','','','date_disponibilite','getProductAvailableDate','03.Logistique'),
	(1,0,0,0,'','','','','additional_shipping_cost','getProductAdditionalShippingCost','03.Logistique'),
	(1,0,0,0,'','','','','seo_title','getMetaTitle','07.Référencement'),
	(1,0,0,0,'','','','','seo_metadescription','getMetaDescription','07.Référencement'),
	(1,0,0,0,'','','','','seo_urlsimplifie','getMetaLinkRewrite','07.Référencement'),
	(1,0,0,0,'','','','','seo_metamotscles','getMetaKeywords','07.Référencement');
