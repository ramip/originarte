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

if ( !defined( '_PS_VERSION_' ) )
  exit;

class CityForPostcode extends Module
{
	private $_postErrors = array();
	function __construct () {
	 	$this->name = 'cityforpostcode';
	 	$this->tab = 'front_office_features';
	 	$this->version = '1.0';
		$this->displayName = 'Insertar ciudad a partir del código postal';
		$this->author = 'Ramiro Perales';
		$this->description = $this->l('Permite llenar de forma automática el campo ciudad de la página de registro a partir de registrar el código postal.');
		parent::__construct();
		$this->confirmUninstall = $this->l('Estas seguro que desea desinstalar el módulo?');
	}

	function install() {
		if(!parent::install()
			OR !$this->registerHook('header')
			OR !$this->registerHook('createAccount')
			OR !$this->registerHook('createAccountForm')
			OR !$this->registerHook('createAccountTop')
			OR !$this->registerHook('backOfficeHeader')
      OR !$this->registerHook('footer')
			)
			return false;
		$this->createInsertBDTable('dump.sql');
		return true;
	}

	function uninstall()	{
		if( !parent::uninstall() )
			return false;
		$this->dropTable($this->name);
		return true;
	}

	public function getContent() {
            $html = '
                <div style="float:left">
                    <h2>'.$this->l('Ciudad a partir del Código Postal').'</h2>
                </div>
                ';
            return $html.$this->displayForm();
	}

	public function displayForm() {

		$output = '
		<fieldset class="space" style="clear:both">
			<legend><img src="../img/admin/unknown.gif" alt="" class="middle" />'.$this->l('Ayuda').'</legend>
			<div style="float:left; width:40%">
				<p>'.$this->l('El módulo esta programado en base a la plantilla por defecto de prestashop, si tienes personalizada la plantilla, y estas han cambiado de forma significativamente la esctructura, nombres e ID de los estilos ó capas de los archivos tpl que contiene los formularios de registro es posible que el módulo no funcione correctamente.').'</p>
			</div>
			<div style="float:right; width:40%;"><a href="http://ramip.net/" target="_blank">'.$this->name.'</a></div>
		</fieldset>';
		return $output;
	}

	public function hookcreateAccountForm($params) {
	    //TODO: En local hosta cambiar la ruta
//		$ruta = _PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name.'/'.$this->name.'-ajax.php'; // local
        $ruta = $this->getProtocolDomain().'/modules/'.$this->name.'/'.$this->name.'-ajax.php'; // server
		$html = '
		<script>
			$(document).ready(function(){
				$("#postcode").blur(function(evento) {
				    console.log("click en blur");
					var cad = $("#postcode").val();
					var num = $("#postcode").val().length;
					var cp = cad;
					evento.preventDefault();
                      $.ajax({
                        type: "POST",
                        url : "'.$ruta.'",
                        data: "codpos="+cp,
                        dataType: "json",
                        success: function(responseTxt,string, jqXHR) {
                            console.log("id_provincia = "+responseTxt.id_provincia);
                            $("#city").val(responseTxt.city);             
                            $("#id_country").val("6").trigger("change");
            //              $("#id_country option[value=6]").attr("selected", "selected");
                            $("#id_state option[value="+responseTxt.id_provincia+"]").attr("selected", "selected");
            //              alert(responseTxt.city);
                        }
                      });
				});
			});
		</script>
		<div id="destino" style="display:none"></div>
		';
		return $html;
	}
	public function hookcreateAccountTop($params) {
		//return 'hookcreateAccountTop<hr>';
	}

  public function hookHeader($params) {
    // ocultaremos los forms country=pais y id_state=provincia
    // que en address.tpl fueron añadidos la clase hide_rpc
    $script = '
		<script>
			$(document).ready(function(){
        $(".hide_rpc").css("display","none");
      });
    </script>
    ';
    return $script;
  }
  //function siteURL() {
  public function getProtocolDomain()
  {
    $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ||
      $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
      $domainName = $_SERVER['HTTP_HOST'];
      return $protocol.$domainName;
  }
  public function hookFooter($params) {
    return $this->hookcreateAccountForm($params);
  }

	public function createInsertBDTable ($filename) {
		$path = _PS_ROOT_DIR_.'/modules/'.$this->name.'/'.$filename;

		$sql_contents = file_get_contents($path);
		$sql_contents = explode(";", $sql_contents);
		foreach($sql_contents as $query){
			$result = Db::getInstance()->Execute($query);
			if (!$result)
				 return  "Error on import of ".$query;
		}
	}

	public function dropTable($table) {
		$sql = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.$table.'`;';
		return Db::getInstance()->Execute($sql);
	}

	public function hookBackOfficeHeader($params) {
		//$this->createInsertBDTable('dump.sql');
		//$this->dropTable($this->name);
	}

	public function ajaxCall($id) {
    $id = trim($id);
	$query = "SELECT * FROM `"._DB_PREFIX_.$this->name."` WHERE `CodPostal` LIKE '".$id."%'";
	$row = Db::getInstance()->getRow($query);
//   echo json_encode($jsondata);
   //echo $jsondata;
	return $row['Municipio'];
 }

 public function getProvincia($id)
 {
   $query = "SELECT * FROM `"._DB_PREFIX_.$this->name."` WHERE `CodProv` = ".$id;
   $row = Db::getInstance()->getRow($query);
   $prov = $row['Municipio'];

   $query = "SELECT * FROM `"._DB_PREFIX_."state` WHERE `name` LIKE '".$prov."'";
   $row = Db::getInstance()->getRow($query);

   if(!isset($row['id_state']) || $row['id_state']=='')
    return '322'; // si no se encuentra ninguna provincia devolvemos codigo de barcelona
 	 return $row['id_state'];
 }

}
