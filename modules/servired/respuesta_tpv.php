<?php
/*-----------------------------------------------------------------------------
Autor: Javier Barredo
Autor E-Mail: naveto@gmail.com
Fecha: Mayo 2011
Version : 0.7v4
Agradecimientos: Yago Ferrer por su mÃ³dulo de pago  que se utilizÃ³ como base de este mÃ³dulo.
Alberto FernÃ¡ndez por su ayuda con los testeos y las imÃ¡genes.
Version: 1.50 (solo probada en PS1.4)
AdaptaciÃ³n a PS 1.4: David Vidal (chienandalu@gmail.com)
HibridaciÃ³n del mÃ³dulo con versiones anteriores: Francisco J. Matas (fjmatad@hotmail.com)

Notas para la versiÃ³n de Servired 1.50 (28-5-2011)
--------------------------------

[-] AdaptaciÃ³n del mÃ³dulo a la versiÃ³n 1.4 de Prestashop:
  - El pago vÃ¡lido retorna a OrderConfirmation, de modo que sigue los cauces de los demÃ¡s mÃ³dulos de pago de Prestashop.
  - De este modo ahora el mÃ³dulo Google Analytics puede ofrecer estadÃ­sticas de estos pagos. Antes no se registraban dichas conversiones.
  - Adaptada plantilla pago-correcto.tpl
  - Corregido bug en plantilla pago-error.tpl
  - Corregido fallos en instalaciÃ³n y desinstalaciÃ³n en versiÃ³n 1.4
  - Corregido fallo de secure_key en PS 1.4
  - Corregida ruta de icono "personalizaciÃ³n"
  - pago_correcto.php deja de ser necesario
  - Algunas modificaciones de grÃ¡ficos
[*] HibridaciÃ³n del mÃ³dulo adaptado por David Vidal para aumentar la compatibilidad con las plataformas Sermepa.
  * Se redimensionan imagenes que quedaban cortadas en los resultados de la plataforma.
  * Se corrige error con pagos inferiores a 1 euros.
  * Se aÃ±ade selector para configurar el entorno.
  * Se aÃ±ade selector para configurar el tipo de firma.
  * Se aÃ±ade posibilidad para cobrar un recargo en tantos %.
  * Se aÃ±ade NotificaciÃ³n HTTP para entorno de pruebas.
  * Se aumenta el nÃºmero de versiÃ³n para no confundirlo con las anteriores, ya que existe una versiÃ³n 1.0 muy similar, pero con menos caracterÃ­sticas.

Released under the GNU General Public License
-----------------------------------------------------------------------------*/

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include(dirname(__FILE__).'/servired.php');
include(dirname(__FILE__).'/apiRedsys.php');


	// Se crea Objeto
	$miObj = new RedsysAPI;

if (!empty($_POST)){


	// Recoger datos de respuesta
	$total     = $_POST["Ds_Amount"];
	$pedido    = $_POST["Ds_Order"];
	$codigo    = $_POST["Ds_MerchantCode"];
	$moneda    = $_POST["Ds_Currency"];
	$respuesta = $_POST["Ds_Response"];
	$firma_remota = $_POST["Ds_Signature"];
	
	
	/** Extraer datos de la notificación **/
//	$total     = $miObj->getParameter('Ds_Amount');
//	$pedido    = $miObj->getParameter('Ds_Order');
//	$codigo    = $miObj->getParameter('Ds_MerchantCode');
//	$moneda    = $miObj->getParameter('Ds_Currency');
//	$respuesta = $miObj->getParameter('Ds_Response');
//	$id_trans = $miObj->getParameter('Ds_AuthorisationCode');

	// Creamos objeto
	$servired = new servired();
	//Verificamos opciones
	$error_pago = Configuration::get('SERVIRED_ERROR_PAGO');
	// ContraseÃ±a Secreta
	$clave = Configuration::get('SERVIRED_CLAVE');

	// CÃ¡lculo del SHA1
	$mensaje = $total . $pedido . $codigo . $moneda . $respuesta . $clave;
	$firma_local = strtoupper(sha1($mensaje));
	
	
	//SHA256
	
	
	$version = $_POST["Ds_SignatureVersion"];
	$datos = $_POST["Ds_MerchantParameters"];
    $signatureRecibida = $_POST["Ds_Signature"];
	
		
	//$kc ='Mk9m98IfEblmPfrpsawt7BmxObt98Jev';
	$kc = Configuration::get('SERVIRED_CLAVE');
	$firma = $miObj->createMerchantSignatureNotif($kc,$datos);	
	
	
	//decodificar los parametros del comercio 
	$decodec = $miObj->decodeMerchantParameters($datos);
		/** Extraer datos de la notificación **/
	$total     = $miObj->getParameter('Ds_Amount');
	$pedido    = $miObj->getParameter('Ds_Order');
	$codigo    = $miObj->getParameter('Ds_MerchantCode');
	$moneda    = $miObj->getParameter('Ds_Currency');
	$respuesta = $miObj->getParameter('Ds_Response');
	$id_trans = $miObj->getParameter('Ds_AuthorisationCode');

	
	$total  = number_format($total / 100,4,'.', '');
		$pedido = substr($pedido,0,8);
		$pedido = intval($pedido);
		$respuesta = intval($respuesta);
		$moneda_tienda = 1; // Euros
			
	

		
		

	

	if ($firma === $signatureRecibida){ 
		// Formatear variables
		// NINO - eliminar el punto de los miles para evitar error en pago
		// ORIGINAL - $total  = number_format($total / 100,4);
		
		
		
		

		
		
					    $file = fopen("log_respuesta.txt", "w");
						fwrite($file, date("d").'-'.date("m").'-'.date("y").'   '.date("G").':'.date("i").':'.date("s") . PHP_EOL);
						fwrite($file, "Clave: ".$clave . PHP_EOL);
						fwrite($file, "signatureRecibida: ".$signatureRecibida . PHP_EOL);
						fwrite($file, "firma: ".$firma . PHP_EOL);
						fwrite($file, "respuesta: ".$respuesta . PHP_EOL);
						fwrite($file, "pedido: ".$pedido . PHP_EOL);
//						fwrite($file, "_PS_OS_PAYMENT_: "._PS_OS_PAYMENT_ . PHP_EOL);
						fwrite($file, "total: ".$total . PHP_EOL);
//						fwrite($file, "servired_display_name: ".$servired->displayName . PHP_EOL);
//						fwrite($file, "cart_secure_key: ".$cart->secure_key . PHP_EOL);

//foreach($datos as $atributo=>$valor)
//	{
//		fwrite($file, "El " . $atributo . " vale " . $valor. PHP_EOL);
//
//	}


						fclose($file);	
		
		
		

		
		
		
		
		
		if ($respuesta < 101){
			

			
			// Compra vÃ¡lida
			$mailvars=array();
			$cart = new Cart($pedido);
			

			
			$servired->validateOrder($pedido, _PS_OS_PAYMENT_, $total, $servired->displayName, NULL, $mailvars, NULL, false, $cart->secure_key);
			
			

			
			
		}
		else {
			// Compra no vÃ¡lida
			if ($error_pago=="no"){
				//se anota el pedido como no pagado
				$servired->validateOrder($pedido, _PS_OS_ERROR_, 0, $servired->displayName, 'errores:'.$respuesta);
				}
			elseif ($error_pago=="si"){
				//Se permite al cliente intentar otra vez el pago
			}
		}
	}
}
?>
