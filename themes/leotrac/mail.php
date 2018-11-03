Mail.php
<?php
$nombre = $_POST['nombre'];
$email = $_POST['email'];
$producto = $_POST['producto'];
$cantidad = $_POST['cantidad'];
$color = $_POST['color'];
$otro-color = $_POST['otro-color'];
$formato = $_POST['formato'];
$formato-cerrado = $_POST['formato-cerrado'];
$papel = $_POST['papel'];
$papel-portada = $_POST['papel-portada'];
$acabado = $_POST['acabado'];
$mensaje = $_POST['mensaje'];
$formcontent="De: $name \n 
	Producto: $producto \n 
	Cantidad: $cantidad \n 
	Color: $color \n 
	Otro color: $otro-color \n 
	Formato abierto: $formato \n 
	Formato cerrado: $formato-cerrado \n
	Papel: $papel \n  
	Papel portada: $papel-portada \n 
	Acabado: $acabado \n 
	Mensaje: $mensaje";
$recipient = "info@originarte.com";
$subject = "Presupuesto personalizado";
$mailheader = "De: $email \r\n";
mail($recipient, $subject, $formcontent, $mailheader) or die("Error!");
echo "!Gracias!";
?>
