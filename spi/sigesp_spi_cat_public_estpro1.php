<?php
session_start();
if (!array_key_exists("la_logusr",$_SESSION))
   {
	 print "<script language=JavaScript>";
	 print "close();";
	 print "opener.document.form1.submit();";
	 print "</script>";		
   }
$la_empresa		  	= $_SESSION["la_empresa"];
$li_estmodest     	= $la_empresa["estmodest"];
$li_loncodestpro1 	= $la_empresa["loncodestpro1"];
$li_estpreing 		= $la_empresa["estpreing"]; 
$li_size1         	= $li_loncodestpro1+10;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat�logo de <?php print $la_empresa["nomestpro1"] ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
a:link {
	color: #006699;
}
a:visited {
	color: #006699;
}
a:active {
	color: #006699;
}
-->
</style>
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
</head>

<body>
<form name="form1" method="post" action="">
  <p align="center">&nbsp;</p>
  	 <table width="550" border="0" cellpadding="0" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td height="21" colspan="2" class="titulo-celda"><input name="operacion" type="hidden" id="operacion">
        Cat&aacute;logo de <?php print $la_empresa["nomestpro1"] ?> </td>
       </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td width="84" height="21" style="text-align:right">C&oacute;digo</td>
        <td width="464" height="21" style="text-align:left"><input name="codigo" type="text" id="codigo" size="<?php print $li_size1 ?>" maxlength="<?php print $li_loncodestpro1 ?>" style="text-align:center">        
        </div></td>
      </tr>
      <tr>
        <td height="21" style="text-align:right">Denominaci&oacute;n</td>
        <td height="21"><div align="left">
          <input name="denominacion" type="text" id="denominacion" size="80">
        </div></td>
      </tr>
      <tr>
        <td height="21">&nbsp;</td>
        <td height="21"><div align="right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0"> Buscar</a></div></td>
      </tr>
    </table>
	 <div align="center"><br>
         <?php
require_once("../shared/class_folder/sigesp_include.php");
require_once("../shared/class_folder/class_sql.php");
$io_include = new sigesp_include();
$ls_conect  = $io_include->uf_conectar();
$io_sql     = new class_sql($ls_conect);
$io_msg     = new class_mensajes();

if (array_key_exists("operacion",$_POST))
   {
	 $ls_operacion  = $_POST["operacion"];
	 $ls_codestpro1 = $_POST["codigo"];
	 $ls_denestpro1 = $_POST["denominacion"];
   }
else
   {
	 $ls_operacion  = "BUSCAR";
	 $ls_codestpro1 = "";
	 $ls_denestpro1 = "";
   }
if(array_key_exists("tipo",$_GET))
{
	$ls_tipo=$_GET["tipo"];
}
else
{
	$ls_tipo="";
}
print "<table width=550 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
print "<tr class=titulo-celda>";
print "<td width=160 style=text-align:center>C�digo</td>";
print "<td width=350 style=text-align:center>Denominaci�n</td>";
print "<td width=40 style=text-align:center>Tipo</td>";
print "</tr>";
if ($ls_operacion=="BUSCAR")
{
	if (!empty($ls_codestpro1))
	{
		$ls_codestpro1 = str_pad($ls_codestpro1,25,0,0);
	}
	
	if ($li_estpreing==1)
	{
		$ls_sql=" SELECT DISTINCT(spg_ep1.codestpro1), spg_ep1.denestpro1, spg_ep1.estcla  ".
				"	 FROM spg_ep1 LEFT OUTER JOIN spi_cuentas_estructuras ".
				"	   ON spi_cuentas_estructuras.codemp=spg_ep1.codemp ".
				"	  AND spi_cuentas_estructuras.codestpro1 =spg_ep1.codestpro1  ".
				"	  AND spi_cuentas_estructuras.estcla  =spg_ep1.estcla ".
				"	WHERE spg_ep1.codemp='".$la_empresa["codemp"]."' ".
				"	  AND spg_ep1.codestpro1 like '%".$ls_codestpro1."%' ".
				" 	  AND spg_ep1.denestpro1 like '%".$ls_denestpro1."%' ".
				"	  AND spg_ep1.codestpro1 <> '-------------------------'  ".
				"    ORDER BY spg_ep1.codestpro1";
	}
	else
	{
		$ls_sql=" SELECT DISTINCT(spg_ep1.codestpro1), spg_ep1.denestpro1, spg_ep1.estcla  ".
				"	 FROM spg_ep1, spi_cuentas_estructuras ".
				"	WHERE spg_ep1.codemp='".$la_empresa["codemp"]."' ".
				"	  AND spg_ep1.codestpro1 like '%".$ls_codestpro1."%' ".
				" 	  AND spg_ep1.denestpro1 like '%".$ls_denestpro1."%' ".
				"	  AND spg_ep1.codestpro1 <> '-------------------------' ".
				"	  AND spi_cuentas_estructuras.codemp=spg_ep1.codemp ".
				"	  AND spi_cuentas_estructuras.codestpro1 =spg_ep1.codestpro1  ".
				"	  AND spi_cuentas_estructuras.estcla  =spg_ep1.estcla ".
				"    ORDER BY spg_ep1.codestpro1";
	}
	

	$rs_data = $io_sql->select($ls_sql);
	if ($rs_data===false)
	{
		$io_msg->message("Error en Consulta, Contacte al Administrador del Sistema !!!");
	}
	else
	{
	  	$li_numrows = $io_sql->num_rows($rs_data);
	  	if ($li_numrows>0)
		{
		   	while ($row=$io_sql->fetch_row($rs_data))
			{
				print "<tr class=celdas-blancas>";
				$ls_codestpro1 = trim(substr($row["codestpro1"],-$li_loncodestpro1));
				$ls_denestpro1 = $row["denestpro1"]; 
				$ls_estcla     = $row["estcla"];
				if ($ls_estcla=='P')
				{
					$ls_denestcla='Proyecto';
				}
				elseif($ls_estcla=='A')
				{
					$ls_denestcla='Acci�n';
				}
				if($ls_tipo=="")
				{
					print "<td width=160 style=text-align:center><a href=\"javascript: aceptar('$ls_codestpro1','$ls_denestpro1','$ls_estcla');\">".$ls_codestpro1."</a></td>";
					print "<td width=350 style=text-align:left>".$ls_denestpro1."</td>";
					print "<td width=40 style=text-align:center>".$ls_denestcla."</td>";
					print "</tr>";
				}
				if($ls_tipo=="reporteacumdes")
				{
					print "<td width=160 style=text-align:center><a href=\"javascript: aceptar_reporteacumdes('$ls_codestpro1','$ls_estcla');\">".$ls_codestpro1."</a></td>";
					print "<td width=350 style=text-align:left>".$ls_denestpro1."</td>";
					print "<td width=40 style=text-align:center>".$ls_denestcla."</td>";
					print "</tr>";
				}
				if($ls_tipo=="reporteacumhas")
				{
					print "<td width=160 style=text-align:center><a href=\"javascript: aceptar_reporteacumhas('$ls_codestpro1','$ls_estcla');\">".$ls_codestpro1."</a></td>";
					print "<td width=350 style=text-align:left>".$ls_denestpro1."</td>";
					print "<td width=40 style=text-align:center>".$ls_denestcla."</td>";
					print "</tr>";
				}
			}
		} 
	  	else
		{
			$io_msg->message("No posee relaci�n con cuentas de Ingreso !!!");
		}
	}
}
print "</table>";
?>
 </div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script language="JavaScript">
fop = opener.document.form1;

function aceptar(ls_codestpro1,ls_denestpro1,ls_estcla)
{
    fop.codestpro1.value   = ls_codestpro1;
    fop.denestpro1.value   = ls_denestpro1;
	//fop.hidtipestpro.value = ls_estcla;
	fop.estcla.value = ls_estcla; 
	fop.codestpro2.value   = "";
    fop.denestpro2.value   = "";
	fop.codestpro3.value   = "";
    fop.denestpro3.value   = "";
	if ("<?php print $li_estmodest;?>"==2)
	   {
		 fop.codestpro4.value="";
		 fop.denestpro4.value="";
		 fop.codestpro5.value="";
		 fop.denestpro5.value="";
	   }
	close();
}

function aceptar_reporteacumdes(ls_codestpro1,ls_estcla)
{	
	if ("<?php print $li_estmodest;?>"==2)
	{
		if  (opener.document.getElementById('codestpro12') ) 
		{      
			fop.codestpro12.value   = ls_codestpro1;
		}
		if  (opener.document.getElementById('codestpro1'))  
		{      
			fop.codestpro1.value   = ls_codestpro1;
		}
		//fop.codestpro12.value   = ls_codestpro1;
		fop.estclades.value 	= ls_estcla;
	}
	else
	{
		fop.codestpro1.value   	= ls_codestpro1;
		fop.estclades.value 	= ls_estcla; 
	}
	
	
	close();
}

function aceptar_reporteacumhas(ls_codestpro1,ls_estcla)
{
	if ("<?php print $li_estmodest;?>"==2)
	{
		if  (opener.document.getElementById('codestpro1h2') ) 
		{      
			fop.codestpro1h2.value   = ls_codestpro1;
		}
		if  (opener.document.getElementById('codestpro1h') ) 
		{      
			fop.codestpro1h.value   = ls_codestpro1;
		}		
		//fop.codestpro1h2.value   = ls_codestpro1;
		fop.estclahas.value 	= ls_estcla;
	}
	else
	{
		fop.codestpro1h.value   = ls_codestpro1;
		fop.estclahas.value 	= ls_estcla; 
	}
    
	close();
}

function ue_search()
{
  f=document.form1;
  f.operacion.value="BUSCAR";
  f.action="sigesp_spi_cat_public_estpro1.php?tipo=<?php print $ls_tipo; ?>";
  f.submit();
}
</script>
</html>
