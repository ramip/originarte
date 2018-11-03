<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
<html> 
<head> 
   	<title>Mándanos tus comentarios</title> 
</head> 

<body bgcolor="#cccc66" text="#003300" link="#006060" vlink="#006060"> 
<h2>Formulario de correo.</h2>
<?php
if (!isset($_POST['email'])) {
?>
  <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
    <label>
      Nombre:
      <input name="nombre" type="text" />
    </label>
<br>
    <label>
      Teléfono:
      <input name="telefono" type="text" />
    </label>
<br>
    <label>
      Email:
      <input name="email" type="text" />
    </label>
<br>
    <label>
      Mensaje:
      <textarea name="mensaje" rows="6" cols="50"></textarea>
    </label>
<br>
    <input type="reset" value="Borrar" />
    <input type="submit" value="Enviar" />
  </form>
<?php
}else{
  $mensaje="Mensaje del formulario de contacto de nnatali.com";
  $mensaje.= "\nNombre: ". $_POST['nombre'];
  $mensaje.= "\nEmail: ".$_POST['email'];
  $mensaje.= "\nTelefono: ". $_POST['telefono'];
  $mensaje.= "\nMensaje: \n".$_POST['mensaje'];
  $destino= "ramiroperales@hotmail.com";
  $remitente = $_POST['email'];
  $asunto = "Mensaje enviado por: ".$_POST['nombre'];
  mail($destino,$asunto,$mensaje,"FROM: $remitente");
?>
  <p><strong>Mensaje enviado.</strong></p>
<?php
}
?>

</body> 
</html>
