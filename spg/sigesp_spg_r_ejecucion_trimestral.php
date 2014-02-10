<?php
session_start();
if (!array_key_exists("la_logusr",$_SESSION))
   {
	 print "<script language=JavaScript>";
	 print "location.href='../sigesp_inicio_sesion.php'";
	 print "</script>";		
   }
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	require_once("../shared/class_folder/sigesp_c_seguridad.php");
	$io_seguridad= new sigesp_c_seguridad();
    
	$dat=$_SESSION["la_empresa"];
	$ls_empresa=$dat["codemp"];
	$ls_logusr=$_SESSION["la_logusr"];
	$ls_sistema="SPG";
	$ls_ventanas="sigesp_spg_r_ejecucion_trimestral.php";

	$la_seguridad["empresa"]=$ls_empresa;
	$la_seguridad["logusr"]=$ls_logusr;
	$la_seguridad["sistema"]=$ls_sistema;
	$la_seguridad["ventanas"]=$ls_ventanas;
	
	if (array_key_exists("permisos",$_POST)||($ls_logusr=="PSEGIS"))
	{	
		if($ls_logusr=="PSEGIS")
		{
			$ls_permisos="";
			$la_accesos=$io_seguridad->uf_sss_load_permisossigesp();
		}
		else
		{
			$ls_permisos           = $_POST["permisos"];
			$la_accesos["leer"]    = $_POST["leer"];
			$la_accesos["incluir"] = $_POST["incluir"];
			$la_accesos["cambiar"] = $_POST["cambiar"];
			$la_accesos["eliminar"]= $_POST["eliminar"];
			$la_accesos["imprimir"]= $_POST["imprimir"];
			$la_accesos["anular"]  = $_POST["anular"];
			$la_accesos["ejecutar"]= $_POST["ejecutar"];
		}
	}
	else
	{
		$la_accesos["leer"]="";
		$la_accesos["incluir"]="";
		$la_accesos["cambiar"]="";
		$la_accesos["eliminar"]="";
		$la_accesos["imprimir"]="";
		$la_accesos["anular"]="";
		$la_accesos["ejecutar"]="";
		$ls_permisos=$io_seguridad->uf_sss_load_permisos($ls_empresa,$ls_logusr,$ls_sistema,$ls_ventanas,$la_accesos);
	}
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<script type="text/javascript" language="JavaScript1.2" src="../shared/js/disabled_keys.js"></script>
<script language="javascript">
	if(document.all)
	{ //ie 
		document.onkeydown = function(){ 
		if(window.event && (window.event.keyCode == 122 || window.event.keyCode == 116 || window.event.ctrlKey)){
		window.event.keyCode = 505; 
		}
		if(window.event.keyCode == 505){ 
		return false; 
		} 
		} 
	}
</script>
<title>EJECUCION TRIMESTRAL DE GASTOS </title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<script type="text/javascript" language="JavaScript1.2" src="js/stm31.js"></script>
<meta http-equiv="" content="text/html; charset=iso-8859-1"><meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Type" content="text/html; charset=">
<style type="text/css">
<!--
a:link {
	color: #006699;
}
a:visited {
	color: #006699;
}
a:hover {
	color: #006699;
}
a:active {
	color: #006699;
}
-->
</style>
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.Estilo1 {font-weight: bold}
.Estilo2 {font-size: 14px}
.Estilo3 {font-size: 9px}
-->
</style></head>
<body>
<table width="780" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr> 
    <td height="30" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
          <?php
	   if(array_key_exists("confinstr",$_SESSION["la_empresa"]))
	  {
      if($_SESSION["la_empresa"]["confinstr"]=='A')
	  {
   ?>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" language="JavaScript1.2" src="js/menu.js"></script></td>
  <?php
      }
      elseif($_SESSION["la_empresa"]["confinstr"]=='V')
	  {
   ?>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" language="JavaScript1.2" src="js/menu_2007.js"></script></td>
  <?php
      }
      elseif($_SESSION["la_empresa"]["confinstr"]=='N')
	  {
   ?>
       <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" language="JavaScript1.2" src="js/menu_2008.js"></script></td>
  <?php
      }
	  	 }
	  else
	  {
   ?>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" language="JavaScript1.2" src="js/menu_2008.js"></script></td>
	<?php 
	}
	?>
  </tr>
  <tr>
    <td height="20" bgcolor="#FFFFFF" class="toolbar">&nbsp;</td>
  </tr>
  <tr> 
    <td height="20" bgcolor="#FFFFFF" class="toolbar"><a href="javascript: ue_showouput();"><img src="../shared/imagebank/tools20/imprimir.gif" width="20" height="20" border="0" title="Imprimir"></a>
		<a href="javascript: ue_showouput_excel();"><img src="../shared/imagebank/tools20/excel.jpg" width="20" height="20" border="0" title="Imprimir"></a>
	<a href="sigespwindow_blank.php"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" width="20" height="20" border="0" title="Salir"></a><a href="javascript:ue_openexcel();"></a><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" width="20" height="20" title="Ayuda"></td>
  </tr>
</table>
  <?php
require_once("../shared/class_folder/sigesp_include.php");
$io_in=new sigesp_include();
$con=$io_in->uf_conectar();

require_once("../shared/class_folder/class_datastore.php");
$io_ds=new class_datastore();

require_once("../shared/class_folder/class_sql.php");
$io_sql=new class_sql($con);

require_once("../shared/class_folder/class_mensajes.php");
$io_msg=new class_mensajes();

require_once("../shared/class_folder/class_funciones.php");
$io_funcion=new class_funciones(); 


$la_emp=$_SESSION["la_empresa"];
if(array_key_exists("operacion",$_POST))
{
	$ls_operacion=$_POST["operacion"];
}
else
{
	$ls_operacion="";	
}
if (array_key_exists("codestpro1",$_POST))
   {
     $ls_codestpro1=$_POST["codestpro1"];	   
   }
else
   {
     $ls_codestpro1="";
   }
if (array_key_exists("codestpro2",$_POST))
   {
    $ls_codestpro2=$_POST["codestpro2"];	   
   }
else
   {
     $ls_codestpro2="";
   }
if (array_key_exists("codestpro3",$_POST))
   {
     $ls_codestpro3=$_POST["codestpro3"];	   
   }
else
   {
     $ls_codestpro3="";
   }
if (array_key_exists("codestpro4",$_POST))
   {
     $ls_codestpro4=$_POST["codestpro4"];	   
   }
else
   {
     $ls_codestpro4="";
   }
if (array_key_exists("codestpro5",$_POST))
   {
     $ls_codestpro5=$_POST["codestpro5"];	   
   }
else
   {
     $ls_codestpro5="";
   }
if (array_key_exists("codestpro1h",$_POST))
   {
      $ls_codestpro1h=$_POST["codestpro1h"];	   
   }
else
   {
      $ls_codestpro1h="";
   }
if (array_key_exists("codestpro2h",$_POST))
   {
     $ls_codestpro2h=$_POST["codestpro2h"];	   
   }
else
   {
     $ls_codestpro2h="";
   }
if (array_key_exists("codestpro3h",$_POST))
   {
     $ls_codestpro3h=$_POST["codestpro3h"];	   
   }
else
   {
     $ls_codestpro3h="";
   }
if (array_key_exists("codestpro4h",$_POST))
   {
     $ls_codestpro4h=$_POST["codestpro4h"];	   
   }
else
   {
     $ls_codestpro4h="";
   }
if (array_key_exists("codestpro5h",$_POST))
   {
     $ls_codestpro5h=$_POST["codestpro5h"];	   
   }
else
   {
     $ls_codestpro5h="";
   }
if (array_key_exists("cmbmes",$_POST)) 
   {
     $ls_cmbmes=$_POST["cmbmes"];
   }
else
   {
     $ls_cmbmes="s1";
   }
if	(array_key_exists("cmbnivel",$_POST))
	{
	  $ls_cmbnivel=$_POST["cmbnivel"];
    }
else
	{
	  $ls_cmbnivel="s1";
	} 
if	(array_key_exists("txtcodfuefindes",$_POST))
	{
	  $ls_codfuefindes=$_POST["txtcodfuefindes"];
    }
else
	{
	  $ls_codfuefindes="";
	} 
	
if	(array_key_exists("txtcodfuefinhas",$_POST))
	{
	  $ls_codfuefinhas=$_POST["txtcodfuefinhas"];
    }
else
	{
	  $ls_codfuefinhas="";
	} 
	
if  (array_key_exists("estclades",$_POST))
	{
	  $ls_estclades=$_POST["estclades"];
    }
else
	{
	  $ls_estclades="";
	}	
if  (array_key_exists("estclahas",$_POST))
	{
	  $ls_estclahas=$_POST["estclahas"];
    }
else
	{
	  $ls_estclahas="";	
	}  	
	
?>
</div> 
<p>&nbsp;</p>
<form name="form1" method="post" action="">
<?php 
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	if (($ls_permisos)||($ls_logusr=="PSEGIS"))
	{
		print("<input type=hidden name=permisos id=permisos value='$ls_permisos'>");
		print("<input type=hidden name=leer     id=leer     value='$la_accesos[leer]'>");
		print("<input type=hidden name=incluir  id=incluir  value='$la_accesos[incluir]'>");
		print("<input type=hidden name=cambiar  id=cambiar  value='$la_accesos[cambiar]'>");
		print("<input type=hidden name=eliminar id=eliminar value='$la_accesos[eliminar]'>");
		print("<input type=hidden name=imprimir id=imprimir value='$la_accesos[imprimir]'>");
		print("<input type=hidden name=anular   id=anular   value='$la_accesos[anular]'>");
		print("<input type=hidden name=ejecutar id=ejecutar value='$la_accesos[ejecutar]'>");
		
	}
	else
	{
		
		print("<script language=JavaScript>");
		print(" location.href='sigespwindow_blank.php'");
		print("</script>");
	}
	//////////////////////////////////////////////         SEGURIDAD               //////////////////////////////////////////////
?>
  <table width="523" height="18" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="505" colspan="2" class="titulo-ventana"><p>EJECUCION  TRIMESTRAL DE GASTOS Y <br>
        APLICACIONES FINANCIERAS (RESUMEN INSTITUCIONAL)</p>
      </td>
    </tr>
  </table>
  <table width="400" border="0" align="center" cellpadding="0" cellspacing="1" class="formato-blanco">
    <tr>
      <td width="109"></td>
    </tr>
    <tr style="display:compact">
      <td height="17" colspan="3" align="center"><div align="left"><span class="Estilo2"></span></div></td>
    </tr>
   
    <tr style="display:compact">
      <td height="40" colspan="3" align="center">      <div align="left">
        <p>
          <?php 
		$li_estmodest=$_SESSION["la_empresa"]["estmodest"];
		if($li_estmodest==1)
		{
	   ?>
        </p>
        <table width="400" height="65" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco"  va>
          <!--DWLayoutTable-->
          <tr class="titulo-celda">
            <td height="13" colspan="9" valign="top" class="titulo-celdanew"><strong class="titulo-celdanew">Rango Codigo Programatico </strong></td>
          </tr>
          <tr class="formato-blanco">
            <td width="38" height="18"><div align="right">Desde</div></td>
            <td width="136" height="18"><input name="codestpro1" type="text" id="codestpro1" value="<?php print $ls_codestpro1 ?>" size="22" readonly></td>
            <td width="20"><a href="javascript:catalogo_estpro1();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Estructura Programatica 1"></a></td>
            <td width="20"></td>
            <td width="135"><input name="codestpro2" type="text" id="codestpro2" value="<?php print $ls_codestpro2 ?>" size="22" readonly></td>
            <td width="20"><a href="javascript:catalogo_estpro2();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Estructura Programatica 2"></a></td>
            <td width="20"></td>
            <td width="136"><input name="codestpro3" type="text" id="codestpro3" value="<?php print $ls_codestpro3 ?>" size="22" readonly></td>
            <td width="24"><a href="javascript:catalogo_estpro3();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Estructura Programatica 3"></a></td>
          </tr>
          <tr class="formato-blanco">
            <td height="29"><div align="right">Hasta</div></td>
            <td height="29"><input name="codestpro1h" type="text" id="codestpro1h" value="<?php print $ls_codestpro1h ?>" size="22" readonly></td>
            <td><a href="javascript:catalogo_estprohas1();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Estructura Programatica 1"></a></td>
            <td></td>
            <td><input name="codestpro2h" type="text" id="codestpro2h" value="<?php print $ls_codestpro2h  ?>" size="22" readonly></td>
            <td><a href="javascript:catalogo_estprohas2();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Estructura Programatica 2"></a></td>
            <td></td>
            <td><input name="codestpro3h" type="text" id="codestpro3h" value="<?php print $ls_codestpro3h ?>" size="22" readonly></td>
            <td><a href="javascript:catalogo_estprohas3();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Estructura Programatica 3"></a></td>
          </tr>
        </table>
        <p>
          <?php 
		  }
		  else
		  {
		?></p>
        <table width="400" height="65" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco"  va>
          <!--DWLayoutTable-->
          <tr class="titulo-celda">
            <td height="13" colspan="15" valign="top" class="titulo-celdanew"><strong class="titulo-celdanew">Rango Codigo Programatico </strong></td>
          </tr>
          <tr class="formato-blanco">
            <td width="41" height="18"><div align="right">Desde</div></td>
            <td width="50" height="18"><input name="codestpro1" type="text" id="codestpro1" value="<?php print $ls_codestpro1 ?>" size="5" maxlength="2" style="text-align:center"></td>
            <td width="20"><a href="javascript:catalogo_estpro1();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Estructura Programatica 1"></a></td>
            <td width="40"></td>
            <td width="50"><input name="codestpro2" type="text" id="codestpro2" value="<?php print $ls_codestpro2 ?>" size="5" maxlength="2" style="text-align:center"></td>
            <td width="27"><a href="javascript:catalogo_estpro2();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Estructura Programatica 2"></a></td>
            <td width="51"></td>
            <td width="50"><input name="codestpro3" type="text" id="codestpro3" value="<?php print $ls_codestpro3 ?>" size="5" maxlength="2" style="text-align:center"></td>
            <td width="20"><a href="javascript:catalogo_estpro3();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Estructura Programatica 3"></a></td>
            <td width="40"><label></label></td>
            <td width="50"><input name="codestpro4" type="text" id="codestpro4" value="<?php print $ls_codestpro4 ?>" size="5" maxlength="2" style="text-align:center"></td>
            <td width="29"><a href="javascript:catalogo_estpro4();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Estructura Programatica 3"></a></td>
            <td width="40"><!--DWLayoutEmptyCell-->&nbsp;</td>
            <td width="50"><label>
              <input name="codestpro5" type="text" id="codestpro5" value="<?php print  $ls_codestpro5 ?>" size="5" maxlength="2" style="text-align:center">
            <a href="javascript:catalogo_estpro5();"></a></label></td>
            <td width="35"><a href="javascript:catalogo_estpro5();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Estructura Programatica 3"></a></td>
          </tr>
          <tr class="formato-blanco">
            <td height="29"><div align="right">Hasta</div></td>
            <td height="29"><input name="codestpro1h" type="text" id="codestpro1h" value="<?php print $ls_codestpro1h ?>" size="5" maxlength="2" style="text-align:center"></td>
            <td><a href="javascript:catalogo_estprohas1();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Estructura Programatica 1"></a></td>
            <td></td>
            <td><input name="codestpro2h" type="text" id="codestpro2h" value="<?php print $ls_codestpro2h  ?>" size="5" maxlength="2" style="text-align:center"></td>
            <td><a href="javascript:catalogo_estprohas2();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Estructura Programatica 2"></a></td>
            <td></td>
            <td><input name="codestpro3h" type="text" id="codestpro3h" value="<?php print $ls_codestpro3h ?>" size="5" maxlength="2" style="text-align:center"></td>
            <td><a href="javascript:catalogo_estprohas3();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Estructura Programatica 3"></a></td>
            <td><!--DWLayoutEmptyCell-->&nbsp;</td>
            <td><label>
              <input name="codestpro4h" type="text" id="codestpro4h" value="<?php print  $ls_codestpro4h ?>" size="5" maxlength="2" style="text-align:center">
            </label></td>
            <td><a href="javascript:catalogo_estprohas4();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Estructura Programatica 3"></a></td>
            <td><!--DWLayoutEmptyCell-->&nbsp;</td>
            <td><input name="codestpro5h" type="text" id="codestpro5h" value="<?php print  $ls_codestpro5h ?>" size="5" maxlength="2" style="text-align:center"></td>
            <td><a href="javascript:catalogo_estprohas5();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Estructura Programatica 3"></a></td>
          </tr>
        </table>
        <?php
		  }
		 ?>
      </div></td>
    </tr>
    <tr>
      <td height="13" colspan="3" align="left">&nbsp;</td>
    </tr>
    <tr style="display:none">
      <td height="13" colspan="3" align="left"><table width="400" height="39" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
        <tr class="titulo-celdanew">
          <td height="13" colspan="5"><strong>Intervalo de Fuente de Financiamiento </strong></td>
        </tr>
        <tr>
          <td width="96" height="22"><div align="right"><span class="style1 style14">Desde</span></div></td>
          <td width="167"><div align="left">
              <input name="txtcodfuefindes" type="text" id="txtcodfuefindes"  style="text-align:center" value="<?php print $ls_codfuefindes; ?>" size="10" readonly>
          <a href="javascript:catalogo_fuefindes();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Estructura Programatica 1"></a></div></td>
          <td width="94"><div align="right">Hasta</div></td>
          <td width="120"><input name="txtcodfuefinhas" type="text" id="txtcodfuefinhas" style="text-align:center" value="<?php print $ls_codfuefinhas; ?>" size="10" readonly>
            <a href="javascript:catalogo_fuefinhas();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Estructura Programatica 1"></a></td>
          <td width="80"><a href="javascript:catalogo_fuefinhas();"></a></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td height="13" colspan="3" align="left">&nbsp;</td>
    </tr>
    
    <tr>
      <td height="22" colspan="3" align="left"><table width="400" height="40" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
        <tr class="titulo-celdanew">
          <td height="13" colspan="2"><strong class="titulo-celdanew">Seleccione Trimestre</strong></td>
        </tr>
        <tr>
          <td width="244" height="21"><div align="right">Trimestre</div></td>
          <td width="304" height="21"><select name="cmbmes" id="cmbmes">
            <option value="0103">Enero - Marzo</option>
            <option value="0406">Abril - Junio</option>
            <option value="0709">Julio - Septiembre</option>
            <option value="1012">Octubre - Diciembre</option>
          </select></td>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td height="13" colspan="3" align="left"><strong><span class="style14">        
      </span></strong></div></td>
    </tr>
    <tr>
      <td height="19" colspan="3" align="center"><div align="right"><span class="Estilo1">
	  <input name="estclades" type="hidden" id="estclades" value="<?php print $ls_estclades;?>">
      <input name="estclahas" type="hidden" id="estclahas" value="<?php print $ls_estclahas;?>">
      <input name="estmodest" type="hidden" id="estmodest" value="<?php print  $li_estmodest; ?>">
      <input name="operacion"   type="hidden"   id="operacion"   value="<?php print $ls_operacion;?>">
</span></a></div></td>
    </tr>
  </table>
  <p align="center">
<input name="total" type="hidden" id="total" value="<?php print $totrow;?>">
</p>
</form>      
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script language="JavaScript">

function ue_showouput()
{
	f=document.form1;
	ls_estmodest=<? print $li_estmodest;?>;
	if (ls_estmodest==1)
	{
		codestpro1  = f.codestpro1.value;
		codestpro2  = f.codestpro2.value;
		codestpro3  = f.codestpro3.value;
		codestpro4  = '0000000000000000000000000';
		codestpro5  = '0000000000000000000000000';
		codestpro1h = f.codestpro1h.value;
		codestpro2h = f.codestpro2h.value;
		codestpro3h = f.codestpro3h.value;
		codestpro4h  = '0000000000000000000000000';
		codestpro5h  = '0000000000000000000000000';
	}
	else
	{
		codestpro1  = f.codestpro1.value;
		codestpro2  = f.codestpro2.value;
		codestpro3  = f.codestpro3.value;
		codestpro4  = f.codestpro4.value;
		codestpro5  = f.codestpro5.value;
		codestpro1h = f.codestpro1h.value;
		codestpro2h = f.codestpro2h.value;
		codestpro3h = f.codestpro3h.value;
		codestpro4h  = f.codestpro4h.value;
		codestpro5h  = f.codestpro5h.value;
	}
	cmbmes = f.cmbmes.value;
	estmodest   = f.estmodest.value;
	txtcodfuefindes = f.txtcodfuefindes.value;
	txtcodfuefinhas = f.txtcodfuefinhas.value;
	estclades=f.estclades.value;
	estclahas=f.estclahas.value;
    if(estmodest==1)
	{
		if((cmbmes=="s1"))
		{
		  alert("Por Favor Seleccionar todos los parametros de busqueda");
		}
		else
		{
		   pagina="reportes/sigesp_spg_rpp_ejecucion_trimestral_inst_07.php?cmbmes="+cmbmes+"&codestpro1="+codestpro1
				  +"&codestpro2="+codestpro2+"&codestpro3="+codestpro3+"&codestpro4="+codestpro4+"&codestpro5="+codestpro5+"&codestpro1h="+codestpro1h+"&codestpro2h="+codestpro2h+"&codestpro3h="+codestpro3h+"&codestpro4h="+codestpro4h+"&codestpro5h="+codestpro5h+"&txtcodfuefindes="+txtcodfuefindes+"&txtcodfuefinhas="+txtcodfuefinhas+"&estclades="+estclades+"&estclahas="+estclahas;
		   window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
		}
	}
	else
	{
		codestpro4  = f.codestpro4.value;
		codestpro5  = f.codestpro5.value;
		codestpro4h = f.codestpro4h.value;
		codestpro5h = f.codestpro5h.value;
		if((cmbmes=="s1"))
		{
		  alert("Por Favor Seleccionar todos los parametros de busqueda");
		}
		else
		{
		   pagina="reportes/sigesp_spg_rpp_ejecucion_trimestral_inst_07.php?cmbmes="+cmbmes+"&codestpro1="+codestpro1
		   +"&codestpro2="+codestpro2+"&codestpro3="+codestpro3+"&codestpro4="+codestpro4+"&codestpro5="+codestpro5
		   +"&codestpro1h="+codestpro1h+"&codestpro2h="+codestpro2h+"&codestpro3h="+codestpro3h+"&codestpro4h="+codestpro4h
		   +"&codestpro5h="+codestpro5h+"&txtcodfuefindes="+txtcodfuefindes
		   +"&txtcodfuefinhas="+txtcodfuefinhas+"&estclades="+estclades+"&estclahas="+estclahas;
		   window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
		}
	}	
}





function ue_showouput_excel()
{
	f=document.form1;
	ls_estmodest=<? print $li_estmodest;?>;
	if (ls_estmodest==1)
	{
		codestpro1  = f.codestpro1.value;
		codestpro2  = f.codestpro2.value;
		codestpro3  = f.codestpro3.value;
		codestpro4  = '0000000000000000000000000';
		codestpro5  = '0000000000000000000000000';
		codestpro1h = f.codestpro1h.value;
		codestpro2h = f.codestpro2h.value;
		codestpro3h = f.codestpro3h.value;
		codestpro4h  = '0000000000000000000000000';
		codestpro5h  = '0000000000000000000000000';
	}
	else
	{
		codestpro1  = f.codestpro1.value;
		codestpro2  = f.codestpro2.value;
		codestpro3  = f.codestpro3.value;
		codestpro4  = f.codestpro4.value;
		codestpro5  = f.codestpro5.value;
		codestpro1h = f.codestpro1h.value;
		codestpro2h = f.codestpro2h.value;
		codestpro3h = f.codestpro3h.value;
		codestpro4h  = f.codestpro4h.value;
		codestpro5h  = f.codestpro5h.value;
	}
	cmbmes = f.cmbmes.value;
	estmodest   = f.estmodest.value;
	txtcodfuefindes = f.txtcodfuefindes.value;
	txtcodfuefinhas = f.txtcodfuefinhas.value;
	estclades=f.estclades.value;
	estclahas=f.estclahas.value;
    if(estmodest==1)
	{
		if((cmbmes=="s1"))
		{
		  alert("Por Favor Seleccionar todos los parametros de busqueda");
		}
		else
		{
		   pagina="reportes/sigesp_spg_rpp_ejecucion_trimestral_inst_07_excel.php?cmbmes="+cmbmes+"&codestpro1="+codestpro1
				  +"&codestpro2="+codestpro2+"&codestpro3="+codestpro3+"&codestpro4="+codestpro4+"&codestpro5="+codestpro5+"&codestpro1h="+codestpro1h+"&codestpro2h="+codestpro2h+"&codestpro3h="+codestpro3h+"&codestpro4h="+codestpro4h+"&codestpro5h="+codestpro5h+"&txtcodfuefindes="+txtcodfuefindes+"&txtcodfuefinhas="+txtcodfuefinhas+"&estclades="+estclades+"&estclahas="+estclahas;
		   window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
		}
	}
	else
	{
		codestpro4  = f.codestpro4.value;
		codestpro5  = f.codestpro5.value;
		codestpro4h = f.codestpro4h.value;
		codestpro5h = f.codestpro5h.value;
		if((cmbmes=="s1"))
		{
		  alert("Por Favor Seleccionar todos los parametros de busqueda");
		}
		else
		{
		   pagina="reportes/sigesp_spg_rpp_ejecucion_trimestral_inst_07_excel.php?cmbmes="+cmbmes+"&codestpro1="+codestpro1
		   +"&codestpro2="+codestpro2+"&codestpro3="+codestpro3+"&codestpro4="+codestpro4+"&codestpro5="+codestpro5
		   +"&codestpro1h="+codestpro1h+"&codestpro2h="+codestpro2h+"&codestpro3h="+codestpro3h+"&codestpro4h="+codestpro4h
		   +"&codestpro5h="+codestpro5h+"&txtcodfuefindes="+txtcodfuefindes
		   +"&txtcodfuefinhas="+txtcodfuefinhas+"&estclades="+estclades+"&estclahas="+estclahas;
		   window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
		}
	}	
}








function catalogo_estpro1()
{
	   pagina="sigesp_cat_public_estpro1.php?tipo=reporte";
	   window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
}
function catalogo_estpro2()
{
	f=document.form1;
	codestpro1=f.codestpro1.value;
	estclades =f.estclades.value;
	
	if(codestpro1!="")
	{
		pagina="sigesp_cat_public_estpro2.php?codestpro1="+codestpro1+"&estcla="+estclades+"&tipo=reporte";
		window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
	}
	else
	{
		alert("Seleccione la Estructura nivel 1");
	}
}
function catalogo_estpro3()
{
	f=document.form1;
	codestpro1=f.codestpro1.value;
	codestpro2=f.codestpro2.value;
	codestpro3=f.codestpro3.value;
	estmodest=f.estmodest.value;
	estclades =f.estclades.value;
	if(estmodest==1)
	{
		if((codestpro1!="")&&(codestpro2!="")&&(codestpro3==""))
		{
			pagina="sigesp_cat_public_estpro3.php?codestpro1="+codestpro1+"&codestpro2="+codestpro2+"&estcla="+estclades+"&tipo=reporte";
			window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
		}
		else
		{
			pagina="sigesp_cat_public_estpro.php?tipo=reporte";
			window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
		}
	}
	else
	{
		if((codestpro1!="")&&(codestpro2!=""))
		{
			pagina="sigesp_cat_public_estpro3.php?codestpro1="+codestpro1+"&codestpro2="+codestpro2+"&estcla="+estclades+"&tipo=reporte";
			window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
		}
		else
		{
              alert("Seleccione la Estructura nivel 2");
		}
	}	
}
function catalogo_estpro4()
{
	f=document.form1;
	codestpro1=f.codestpro1.value;
	codestpro2=f.codestpro2.value;
	codestpro3=f.codestpro3.value;	
	estclades =f.estclades.value;
	if((codestpro1!="")&&(codestpro2!="")&&(codestpro3!=""))
	{
		pagina="sigesp_cat_public_estpro4.php?codestpro1="+codestpro1+"&codestpro2="+codestpro2+"&codestpro3="+codestpro3+"&estcla="+estclades+"&tipo=reporte";
		window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
	}
	else
	{
		alert("Seleccione la Estructura nivel 3 ");
	}
}
function catalogo_estpro5()
{
	f=document.form1;
	codestpro1=f.codestpro1.value;
	codestpro2=f.codestpro2.value;
	codestpro3=f.codestpro3.value;
	codestpro4=f.codestpro4.value;
	codestpro5=f.codestpro5.value;
	estclades =f.estclades.value;
	if((codestpro1!="")&&(codestpro2!="")&&(codestpro3!="")&&(codestpro4!="")&&(codestpro5==""))
	{
		pagina="sigesp_cat_public_estpro5.php?codestpro1="+codestpro1+"&codestpro2="+codestpro2
		                                     +"&codestpro3="+codestpro3+"&codestpro4="+codestpro4+"&estcla="+estclades+"&tipo=reporte";
		window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
	}
	else
	{
			pagina="sigesp_cat_public_estprograma.php?tipo=reporte";
		    window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
	}
}

function catalogo_estprohas1()
{
	   pagina="sigesp_cat_public_estpro1.php?tipo=rephas";
	   window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
}
function catalogo_estprohas2()
{
	f=document.form1;
	codestpro1h=f.codestpro1h.value;
	estclahas =f.estclahas.value;
	if((codestpro1h!=""))
	{
		pagina="sigesp_cat_public_estpro2.php?codestpro1="+codestpro1h+"&estcla="+estclahas+"&tipo=rephas";
		window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
	}
	else
	{
		alert("Seleccione la Estructura nivel 1");
	}
}
function catalogo_estprohas3()
{
	f=document.form1;
	codestpro1h=f.codestpro1h.value;
	codestpro2h=f.codestpro2h.value;
	codestpro3h=f.codestpro3h.value;
	estmodest=f.estmodest.value;
	estclahas =f.estclahas.value;
	if(estmodest==1)
	{
		if((codestpro1h!="")&&(codestpro2h!="")&&(codestpro3h==""))
		{
			pagina="sigesp_cat_public_estpro3.php?codestpro1="+codestpro1h+"&codestpro2="+codestpro2h+"&estcla="+estclahas+"&tipo=rephas";
			window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
		}
		else
		{
			pagina="sigesp_cat_public_estpro.php?tipo=rephas";
			window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
		}
	}
	else
	{
		if((codestpro1h!="")&&(codestpro2h!=""))
		{
			pagina="sigesp_cat_public_estpro3.php?codestpro1="+codestpro1h+"&codestpro2="+codestpro2h+"&estcla="+estclahas+"&tipo=rephas";
			window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
		}
		else
		{
            alert("Seleccione la Estructura nivel 2");
		}
	}	
}
function catalogo_estprohas4()
{
	f=document.form1;
	codestpro1h=f.codestpro1h.value;
	codestpro2h=f.codestpro2h.value;
	codestpro3h=f.codestpro3h.value;
	estclahas =f.estclahas.value;
	if((codestpro1h!="")&&(codestpro2h!="")&&(codestpro3h!=""))
	{
		pagina="sigesp_cat_public_estpro4.php?codestpro1="+codestpro1h+"&codestpro2="+codestpro2h
		                                     +"&codestpro3="+codestpro3h+"&estcla="+estclahas+"&tipo=rephas";
		window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
	}
	else
	{
		alert("Seleccione la Estructura nivel 3 ");
	}
}
function catalogo_estprohas5()
{
	f=document.form1;
	codestpro1h=f.codestpro1h.value;
	codestpro2h=f.codestpro2h.value;
	codestpro3h=f.codestpro3h.value;
	codestpro4h=f.codestpro4h.value;
	codestpro5h=f.codestpro5h.value;
	estclahas =f.estclahas.value;
	if((codestpro1h!="")&&(codestpro2h!="")&&(codestpro3h!="")&&(codestpro4h!="")&&(codestpro5h==""))
	{
		pagina="sigesp_cat_public_estpro5.php?codestpro1="+codestpro1h+"&codestpro2="+codestpro2h
		                                     +"&codestpro3="+codestpro3h+"&codestpro4="+codestpro4h+"&estcla="+estclahas
											 +"&tipo=rephas";
		window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
	}
	else
	{
		pagina="sigesp_cat_public_estprograma.php?tipo=rephas";
		window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
	}
}
function catalogo_fuefindes()
{
    f=document.form1;
    pagina="sigesp_spg_cat_fuente.php?tipo=REPORTE_DESDE";
    window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=520,height=400,resizable=yes,location=no");
}

function catalogo_fuefinhas()
{
    f=document.form1;
    pagina="sigesp_spg_cat_fuente.php?tipo=REPORTE_HASTA";
    window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=520,height=400,resizable=yes,location=no");
}
function ue_openexcel()
{
	f=document.form1;
	li_imprimir=f.imprimir.value;
	if(li_imprimir==1)
	{
		ls_estmodest=<? print $li_estmodest;?>;
	    if (ls_estmodest==1)
	    {
			codestpro1  = f.codestpro1.value;
			codestpro2  = f.codestpro2.value;
			codestpro3  = f.codestpro3.value;
			codestpro4  = '0000000000000000000000000';
			codestpro5  = '0000000000000000000000000';
			codestpro1h = f.codestpro1h.value;
			codestpro2h = f.codestpro2h.value;
			codestpro3h = f.codestpro3h.value;
			codestpro4h  = '0000000000000000000000000';
			codestpro5h  = '0000000000000000000000000';
	    }
	    else
	    {
			codestpro1  = f.codestpro1.value;
			codestpro2  = f.codestpro2.value;
			codestpro3  = f.codestpro3.value;
			codestpro4  = f.codestpro4.value;
			codestpro5  = f.codestpro5.value;
			codestpro1h = f.codestpro1h.value;
			codestpro2h = f.codestpro2h.value;
			codestpro3h = f.codestpro3h.value;
			codestpro4h  = f.codestpro4h.value;
			codestpro5h  = f.codestpro5h.value;
		}
		cmbmes = f.cmbmes.value;
		estmodest   = f.estmodest.value;
		txtcodfuefindes = f.txtcodfuefindes.value;
		txtcodfuefinhas = f.txtcodfuefinhas.value;
		estclades=f.estclades.value;
		estclahas=f.estclahas.value;
		if(estmodest==1)
		{
			if((cmbmes=="s1"))
			{
			  alert("Por Favor Seleccionar todos los parametros de busqueda");
			}
			else
			{
			   pagina="reportes/sigesp_spg_rpp_ejecucion_trimestral_inst_07_excel.php?cmbmes="+cmbmes+"&codestpro1="+codestpro1
					  +"&codestpro2="+codestpro2+"&codestpro3="+codestpro3+"&codestpro4="+codestpro4+"&codestpro5="+codestpro5+"&codestpro1h="+codestpro1h+"&codestpro2h="+codestpro2h+"&codestpro3h="+codestpro3h+"&codestpro4h="+codestpro4h+"&codestpro5h="+codestpro5h+"&txtcodfuefindes="+txtcodfuefindes+"&txtcodfuefinhas="+txtcodfuefinhas+"&estclades="+estclades+"&estclahas="+estclahas;
			   window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
			}
		}
		else
		{
			codestpro4  = f.codestpro4.value;
			codestpro5  = f.codestpro5.value;
			codestpro4h = f.codestpro4h.value;
			codestpro5h = f.codestpro5h.value;
			if((cmbmes=="s1"))
			{
			  alert("Por Favor Seleccionar todos los parametros de busqueda");
			}
			else
			{
			   pagina="reportes/sigesp_spg_rpp_ejecucion_trimestral_inst_07_excel.php?cmbmes="+cmbmes+"&codestpro1="+codestpro1
			   +"&codestpro2="+codestpro2+"&codestpro3="+codestpro3+"&codestpro4="+codestpro4+"&codestpro5="+codestpro5
			   +"&codestpro1h="+codestpro1h+"&codestpro2h="+codestpro2h+"&codestpro3h="+codestpro3h+"&codestpro4h="+codestpro4h
			   +"&codestpro5h="+codestpro5h+"&txtcodfuefindes="+txtcodfuefindes
			   +"&txtcodfuefinhas="+txtcodfuefinhas+"&estclades="+estclades+"&estclahas="+estclahas;
			   window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
			}
		}
	}
	else
	{
       alert("No tiene permiso para realizar esta operacion");	
	}	
}
</script>
</html>