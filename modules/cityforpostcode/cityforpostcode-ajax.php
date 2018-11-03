<?php
/*
 * MODULO PARA REGISTRAR CIUDAD A PARTIR DEL CÓDIGO POSTAL EN LA
 * PAGINA DE REGISTRO DEL CLIENTE
 *
 * El módulo genera archivos sin extensión para cada pedido
 * estos archivos serán leidos por el ERP de la tienda
 *
 * @author Ramiro Perales <rperalesch@gmail.com>
 * @copyright (c)2013 - Ramiro Perales
 * @license GPLv3 - http://www.gnu.org/licenses/gpl-3.0.html
 * @version 1.1 - 08/09/2016

*/
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/cityforpostcode.php');

$cityForPostcode = new CityForPostcode();

(int) $id = $_POST['codpos'];
if($id > 0) {
   $city = $cityForPostcode->ajaxCall($id); //process($id);
   //echo $cityForPostcode->hookAjaxCall($id);
   $id_prov = substr($id, 0, 2); // obtener los 2 primeros dijitos de codigo postal
   $id_provincia = $cityForPostcode->getProvincia($id_prov);

   $list = array('city' => $city, 'id_provincia' => $id_provincia );
   $res = json_encode($list);
  }

echo $res;
