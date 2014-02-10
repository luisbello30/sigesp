<?php
    session_start();   
	ini_set('memory_limit','1024M');
	ini_set('max_execution_time ','0');
	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
		require_once ("../../shared/writeexcel/class.writeexcel_workbookbig.inc.php");
		require_once ("../../shared/writeexcel/class.writeexcel_worksheet.inc.php");
		$lo_archivo = tempnam("/tmp", "listado_cheques_y_carta_orden.xls");
		$lo_libro = &new writeexcel_workbookbig($lo_archivo);
		$lo_hoja = &$lo_libro->addworksheet();
	//---------------------------------------------------------------------------------------------------------------------------
	// para crear la data necesaria del reporte
		require_once("sigesp_scb_class_report.php");
		require_once("../../shared/class_folder/class_fecha.php");
		require_once("../../shared/class_folder/class_funciones.php");
		require_once("../../shared/class_folder/sigesp_include.php");
        require_once("../../shared/class_folder/class_sql.php");
		require_once("../../shared/class_folder/class_datastore.php");    
		
		$io_conect  = new sigesp_include();
		$con        = $io_conect->uf_conectar();
		$io_report  = new sigesp_scb_class_report($con);
		$io_funcion = new class_funciones();			
		$io_fecha   = new class_fecha();
	    $io_sql     = new class_sql($con);
	//---------------------------------------------------------------------------------------------------------------------------
	//Par�metros para Filtar el Reporte
	$ls_codemp    = $_SESSION["la_empresa"]["codemp"];
	$ld_fecdesde  = $_GET["fecdes"];
	$ld_fechasta  = $_GET["fechas"];
	$ls_codope    = $_GET["codope"];
	$ls_codban    = $_GET["codban"];
	$ls_nomban    = $_GET["nomban"];
	$ls_ctaban    = $_GET["ctaban"];
	$ls_codconcep = $_GET["codconcep"];
	$ls_orden     = $_GET["orden"];
	$ls_estatus   = $_GET["hidestmov"];
	$ls_tipbol      = 'Bs.';
	$ls_tiporeporte = 0;
	$ls_tiporeporte = $_GET["tiporeporte"];
	$ls_documento = $_GET["documento"];
	$ls_conchk 		= $_GET["chked"];
	$ls_numdocchk   = $_GET["chkados"];
	
	//Opci�n para los selectivos
	$lr_numdocchk= split('>>',$ls_numdocchk);
    $lr_datos= array_unique($lr_numdocchk);
    $li_total= count($lr_datos);
	sort($lr_datos,SORT_STRING);
	//Opci�n para los selectivos

	
	global $ls_tiporeporte;
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_scb_class_reportbsf.php");
		$io_report = new sigesp_scb_class_reportbsf($con);
		$ls_tipbol = 'Bs.F.';
	}
	
	//---------------------------------------------------------------------------------------------------------------------------
	//Par�metros del encabezado
		$ldt_fecha="Desde  ".$ld_fecdesde."  al ".$ld_fechasta."";
		$ls_titulo="LISTADO DE CHEQUES / CARTA ORDEN";   
		
		
		    
	//---------------------------------------------------------------------------------------------------------------------------
	//Busqueda de la data 
	$lb_valido = true;
	if ($ls_conchk==1)
	{
		
		
				$lo_encabezado= &$lo_libro->addformat();
				$lo_encabezado->set_bold();
				$lo_encabezado->set_font("Verdana");
				$lo_encabezado->set_align('center');
				$lo_encabezado->set_size('11');
				$lo_titulo= &$lo_libro->addformat();
				$lo_titulo->set_bold();
				$lo_titulo->set_font("Verdana");
				$lo_titulo->set_align('center');
				$lo_titulo->set_size('9');		
				$lo_datacenter= &$lo_libro->addformat();
				$lo_datacenter->set_font("Verdana");
				$lo_datacenter->set_align('center');
				$lo_datacenter->set_size('9');
				$lo_dataleft= &$lo_libro->addformat();
				$lo_dataleft->set_text_wrap();
				$lo_dataleft->set_font("Verdana");
				$lo_dataleft->set_align('left');
				$lo_dataleft->set_size('9');
				$lo_dataright= &$lo_libro->addformat(array(num_format => '#,##0.00'));
				$lo_dataright->set_font("Verdana");
				$lo_dataright->set_align('right');
				$lo_dataright->set_size('9');
				$lo_hoja->set_column(0,0,30);
				$lo_hoja->set_column(1,1,45);
				$lo_hoja->set_column(2,5,30);
				
				$ls_nomtipcta = $io_report->uf_select_data($io_sql,"SELECT nomtipcta FROM scb_tipocuenta t, scb_ctabanco c WHERE c.codemp='".$ls_codemp."' AND c.codtipcta=t.codtipcta AND c.ctaban='".$ls_ctaban."'","nomtipcta");
				
				$lo_hoja->write(0, 2, $ls_titulo,$lo_encabezado);
				$lo_hoja->write(1, 2, $ldt_fecha,$lo_encabezado);
				$lo_hoja->write(3, 0, "Banco  :",$lo_titulo);
				$lo_hoja->write(3, 1, $ls_nomban, $lo_datacenter);
				
				$lo_hoja->write(4, 0, "Tipo de Cuenta  :",$lo_titulo);
				$lo_hoja->write(4, 1, $ls_nomtipcta, $lo_datacenter);
				
				$lo_hoja->write(5, 0, "Cuenta :",$lo_titulo);
				$lo_hoja->write(5, 1, $ls_ctaban, $lo_datacenter);
				
				//$li_row++;
				$lo_hoja->write(6, 0, "Nro",$lo_titulo);
				$lo_hoja->write(6, 1, "Documento",$lo_titulo);
				$lo_hoja->write(6, 2, "Fecha",$lo_titulo);
				$lo_hoja->write(6, 3, "Proveedor/Beneficiario",$lo_titulo);
				$lo_hoja->write(6, 4, "Concepto",$lo_titulo);
				$lo_hoja->write(6, 5, "Monto",$lo_titulo);
				$lo_hoja->write(4, 6, "Estatus",$lo_titulo);
				
				
		$li_cont = 0;
		$li_row = 8;
		for($li_p=0;$li_p<$li_total;$li_p++)
		{
			$ls_documento=$lr_datos[$li_p]; //print "$ls_documento <br>";
			$rs_data   = $io_report->uf_cargar_documentos_ch_co($ls_codope,$ld_fecdesde,$ld_fechasta,$ls_codban,$ls_ctaban,$ls_codconcep,$ls_estmov,$ls_orden,$ls_documento,$ls_conchk,&$lb_valido);
			//---------------------------------------------------------------------------------------------------------------------------
			// Impresi�n de la informaci�n encontrada en caso de que exista
			if($lb_valido==false) // Existe alg�n error � no hay registros
			{
				//print("<script language=JavaScript>");
				//print(" alert('No hay nada que Reportar !!!');"); 
				//print(" close();");
				//print("</script>");
			}
			else // Imprimimos el reporte
			{
				//aqui iba
				//
				$ldec_totaldebitos  = 0;
				$ldec_totalcreditos = 0;
				$ldec_saldo         = 0;
				$ld_totanu          = 0;//print "$rs_data <br><br>";
				//print "antes  ";
				while (!$rs_data->EOF) 				//($row=$io_sql->fetch_row($rs_data))
				{
					
					   $li_cont++;//print "ingres�  $li_cont <br><br><br>";
					   $ls_numdoc    = " ".$rs_data->fields["numdoc"];
					   $ldec_monto   = $rs_data->fields["monto"]; 
					   $ld_fecmov    = $rs_data->fields["fecmov"];
					   $ld_fecmov    = $io_funcion->uf_convertirfecmostrar($ld_fecmov);
					   $ls_nombre    = $rs_data->fields["nomproben"];
					   $ls_codope    = $rs_data->fields["codope"]; 
					   $ls_conmov    = $rs_data->fields["conmov"];
					   $ls_estbpd    = $rs_data->fields["estbpd"];
					   $ls_estmov    = $rs_data->fields["estmov"];
					   $ls_numcarord = $rs_data->fields["numcarord"];
					   if ($ls_estbpd=='T')
						  {
							$ls_numdoc = $ls_numcarord;
						  } 
					   if ($ls_estatus=='A')
						  {
							$ld_totanu = ($ld_totanu+$ldec_monto);
						  }
						switch($ls_codope)
						{
							case "ND":
							  if ($ls_estmov=='A')
								 {
								   $ld_totdeb = ($ld_totdeb+$ldec_monto);
								 }
							  else
								 {
								   $ld_totcre = ($ld_totcre+$ldec_monto);						   
								 }
							break;
							case "NC":
							  if ($ls_estmov=='A')
								 {
								   $ld_totcre = ($ld_totcre+$ldec_monto);						   
								 }
							  else
								 {
								   $ld_totdeb = ($ld_totdeb+$ldec_monto);
								 }
							break;
							case "DP":
							  if ($ls_estmov=='A')
								 {
								   $ld_totcre = ($ld_totcre+$ldec_monto);						   
								 }
							  else
								 {
								   $ld_totdeb = ($ld_totdeb+$ldec_monto);
								 }
							break;
							case "CH":
							  if ($ls_estmov=='A')
								 {
								   $ld_totdeb = ($ld_totdeb+$ldec_monto);
								 }
							  else
								 {
								   $ld_totcre = ($ld_totcre+$ldec_monto);						   
								 }
							break;
							case "RE":
							  if ($ls_estmov=='A')
								 {
								   $ld_totdeb = ($ld_totdeb+$ldec_monto);
								 }
							  else
								 {
								   $ld_totcre = ($ld_totcre+$ldec_monto);						   
								 }
							break;
						}
						//print "$li_row <br>";
					   $li_row++;
					   $lo_hoja->write($li_row, 0, $li_cont, $lo_datacenter);
					   $lo_hoja->write($li_row, 1, $ls_numdoc, $lo_datacenter);
					   $lo_hoja->write($li_row, 2, $ld_fecmov, $lo_datacenter);
					   $lo_hoja->write($li_row, 3, $ls_nombre, $lo_dataleft);
					   $lo_hoja->write($li_row, 4, $ls_conmov, $lo_dataleft);
					   $lo_hoja->write($li_row, 5, $ldec_monto, $lo_dataright);
					   $lo_hoja->write($li_row, 6, $ls_estmov, $lo_datacenter);
					   $rs_data->MoveNext();
				} // while
				unset($rs_data);
			}//else
		}
					//$li_row++;
//					$lo_hoja->write($li_row, 0, "C: DOCUMENTOS CONTABILIZADOS",$lo_titulo);
//					$lo_hoja->write($li_row, 1, "N: DOCUMENTOS POR CONTABILIZAR",$lo_titulo);
//					$lo_hoja->write($li_row, 2, "A: DOCUMENTOS ANULADOS",$lo_titulo);
//					
//					$li_row++;
//					$lo_hoja->write($li_row, 0, "O: DOCUMENTOS ORIGINALES",$lo_titulo);
//					$lo_hoja->write($li_row, 1, "L: DOCUMENTOS SIN AFECTACI�N CONTABLE",$lo_titulo);
//					if ($ls_estatus=='A')
//					 {
//					   $li_row=$li_row+1;
//					   $lo_hoja->write($li_row, 2, "Total Anulados",$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'10')));
//					   $lo_hoja->write($li_row, 3, $ld_totanu, $lo_dataright);
//					 }
//				  else
//					 {
//					   $ld_saldo = $ld_totcre-$ld_totdeb;//Calculo del saldo total para todas las cuentas
//					   $li_row=$li_row+1;
//					   $lo_hoja->write($li_row, 2, "Total Cr�ditos",$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'10')));
//					   $lo_hoja->write($li_row, 3, $ld_totcre, $lo_dataright);
//			
//					   $li_row=$li_row+1;
//					   $lo_hoja->write($li_row, 2, "Total D�bitos",$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'10')));
//					   $lo_hoja->write($li_row, 3, $ldec_totdeb, $lo_dataright);
//			
//					   $li_row=$li_row+1;			
//					   $lo_hoja->write($li_row, 2, "Total Saldo",$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'10')));
//					   $lo_hoja->write($li_row, 3, $ld_saldo, $lo_dataright);
//					 }

					$lo_libro->close();
					header("Content-Type: application/x-msexcel; name=\"listado_cheques_y_carta_orden.xls\"");
					header("Content-Disposition: inline; filename=\"listado_cheques_y_carta_orden.xls\"");
					$fh=fopen($lo_archivo, "rb");
					fpassthru($fh);
					unlink($lo_archivo);
					print("<script language=JavaScript>");
					//print(" close();");
					print("</script>");
	
	}
 else
   {
 			$rs_data   = $io_report->uf_cargar_documentos_ch_co($ls_codope,$ld_fecdesde,$ld_fechasta,$ls_codban,$ls_ctaban,$ls_codconcep,$ls_estmov,$ls_orden,$ls_documento,$ls_conchk,&$lb_valido);
			//---------------------------------------------------------------------------------------------------------------------------
			// Impresi�n de la informaci�n encontrada en caso de que exista
			if($lb_valido==false) // Existe alg�n error � no hay registros
			{
				print("<script language=JavaScript>");
				print(" alert('No hay nada que Reportar !!!');"); 
				//print(" close();");
				print("</script>");
			}
			else // Imprimimos el reporte
			{
				$lo_encabezado= &$lo_libro->addformat();
				$lo_encabezado->set_bold();
				$lo_encabezado->set_font("Verdana");
				$lo_encabezado->set_align('center');
				$lo_encabezado->set_size('11');
				$lo_titulo= &$lo_libro->addformat();
				$lo_titulo->set_bold();
				$lo_titulo->set_font("Verdana");
				$lo_titulo->set_align('center');
				$lo_titulo->set_size('9');		
				$lo_datacenter= &$lo_libro->addformat();
				$lo_datacenter->set_font("Verdana");
				$lo_datacenter->set_align('center');
				$lo_datacenter->set_size('9');
				$lo_dataleft= &$lo_libro->addformat();
				$lo_dataleft->set_text_wrap();
				$lo_dataleft->set_font("Verdana");
				$lo_dataleft->set_align('left');
				$lo_dataleft->set_size('9');
				$lo_dataright= &$lo_libro->addformat(array(num_format => '#,##0.00'));
				$lo_dataright->set_font("Verdana");
				$lo_dataright->set_align('right');
				$lo_dataright->set_size('9');
				$lo_hoja->set_column(0,0,30);
				$lo_hoja->set_column(1,1,45);
				$lo_hoja->set_column(2,5,30);
		
				$lo_hoja->write(0, 2, $ls_titulo,$lo_encabezado);
				$lo_hoja->write(1, 2, $ldt_fecha,$lo_encabezado);
				$lo_hoja->write(3, 0, "Banco  :",$lo_titulo);
				$lo_hoja->write(3, 1, $ls_nomban, $lo_datacenter);
				$lo_hoja->write(4, 0, "Tipo de Cuenta  :",$lo_titulo);
				$lo_hoja->write(4, 1, $ls_tipcta, $lo_datacenter);
				$lo_hoja->write(5, 2, "Cuenta :",$lo_titulo);
				$lo_hoja->write(5, 3, $ls_ctaban, $lo_datacenter);
				
				$li_row = 6;
				$lo_hoja->write(6, 0, "Nro",$lo_titulo);
				$lo_hoja->write(6, 1, "Documento",$lo_titulo);
				$lo_hoja->write(6, 2, "Fecha",$lo_titulo);
				$lo_hoja->write(6, 3, "Proveedor/Beneficiario",$lo_titulo);
				$lo_hoja->write(6, 4, "Concepto",$lo_titulo);
				$lo_hoja->write(6, 5, "Monto",$lo_titulo);
				//$lo_hoja->write(4, 6, "Estatus",$lo_titulo);
				
				$ldec_totaldebitos  = 0;
				$ldec_totalcreditos = 0;
				$ldec_saldo         = 0;
				$ld_totanu          = 0;
				
				$li_cont = 0;
				while($row=$io_sql->fetch_row($rs_data))
					 {
					   $li_cont++;
					   $ls_numdoc    = " ".$row["numdoc"];
					   $ldec_monto   = $row["monto"]; 
					   $ld_fecmov    = $row["fecmov"];
					   $ld_fecmov    = $io_funcion->uf_convertirfecmostrar($ld_fecmov);
					   $ls_nombre    = $row["nomproben"];
					   $ls_codope    = $row["codope"]; 
					   $ls_conmov    = $row["conmov"];
					   $ls_estbpd    = $row["estbpd"];
					   $ls_estmov    = $row["estmov"];
					   $ls_numcarord = $row["numcarord"];
					   if ($ls_estbpd=='T')
						  {
							$ls_numdoc = $ls_numcarord;
						  } 
					   if ($ls_estatus=='A')
						  {
							$ld_totanu = ($ld_totanu+$ldec_monto);
						  }
						switch($ls_codope)
						{
							case "ND":
							  if ($ls_estmov=='A')
								 {
								   $ld_totdeb = ($ld_totdeb+$ldec_monto);
								 }
							  else
								 {
								   $ld_totcre = ($ld_totcre+$ldec_monto);						   
								 }
							break;
							case "NC":
							  if ($ls_estmov=='A')
								 {
								   $ld_totcre = ($ld_totcre+$ldec_monto);						   
								 }
							  else
								 {
								   $ld_totdeb = ($ld_totdeb+$ldec_monto);
								 }
							break;
							case "DP":
							  if ($ls_estmov=='A')
								 {
								   $ld_totcre = ($ld_totcre+$ldec_monto);						   
								 }
							  else
								 {
								   $ld_totdeb = ($ld_totdeb+$ldec_monto);
								 }
							break;
							case "CH":
							  if ($ls_estmov=='A')
								 {
								   $ld_totdeb = ($ld_totdeb+$ldec_monto);
								 }
							  else
								 {
								   $ld_totcre = ($ld_totcre+$ldec_monto);						   
								 }
							break;
							case "RE":
							  if ($ls_estmov=='A')
								 {
								   $ld_totdeb = ($ld_totdeb+$ldec_monto);
								 }
							  else
								 {
								   $ld_totcre = ($ld_totcre+$ldec_monto);						   
								 }
							break;
						}
					   $li_row=$li_row+1;
					   $lo_hoja->write($li_row, 0, $li_cont, $lo_datacenter);
					   $lo_hoja->write($li_row, 1, $ls_numdoc, $lo_datacenter);
					   $lo_hoja->write($li_row, 2, $ld_fecmov, $lo_datacenter);
					   $lo_hoja->write($li_row, 3, $ls_nombre, $lo_dataleft);
					   $lo_hoja->write($li_row, 4, $ls_conmov, $lo_dataleft);
					   $lo_hoja->write($li_row, 5, $ldec_monto, $lo_dataright);
					   //$lo_hoja->write($li_row, 6, $ls_estmov, $lo_datacenter);
					 }
		
				//	$li_row++;
				//	$lo_hoja->write($li_row, 0, "C: DOCUMENTOS CONTABILIZADOS",$lo_titulo);
//					$lo_hoja->write($li_row, 1, "N: DOCUMENTOS POR CONTABILIZAR",$lo_titulo);
//					$lo_hoja->write($li_row, 2, "A: DOCUMENTOS ANULADOS",$lo_titulo);
//					
//					$li_row++;
//					$lo_hoja->write($li_row, 0, "O: DOCUMENTOS ORIGINALES",$lo_titulo);
//					$lo_hoja->write($li_row, 1, "L: DOCUMENTOS SIN AFECTACI�N CONTABLE",$lo_titulo);
					
//				  if ($ls_estatus=='A')
//					 {
//					   $li_row=$li_row+1;
//					   $lo_hoja->write($li_row, 2, "Total Anulados",$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'10')));
//					   $lo_hoja->write($li_row, 3, $ld_totanu, $lo_dataright);
//					 }
//				  else
//					 {
//					   $ld_saldo = $ld_totcre-$ld_totdeb;//Calculo del saldo total para todas las cuentas
//					   $li_row=$li_row+1;
//					   $lo_hoja->write($li_row, 2, "Total Cr�ditos",$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'10')));
//					   $lo_hoja->write($li_row, 3, $ld_totcre, $lo_dataright);
//			
//					   $li_row=$li_row+1;
//					   $lo_hoja->write($li_row, 2, "Total D�bitos",$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'10')));
//					   $lo_hoja->write($li_row, 3, $ldec_totdeb, $lo_dataright);
//			
//					   $li_row=$li_row+1;			
//					   $lo_hoja->write($li_row, 2, "Total Saldo",$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'10')));
//					   $lo_hoja->write($li_row, 3, $ld_saldo, $lo_dataright);
//					 }
		
					$lo_libro->close();
					header("Content-Type: application/x-msexcel; name=\"listado_cheques_y_carta_orden.xls\"");
					header("Content-Disposition: inline; filename=\"listado_cheques_y_carta_orden.xls\"");
					$fh=fopen($lo_archivo, "rb");
					fpassthru($fh);
					unlink($lo_archivo);
					print("<script language=JavaScript>");
					//print(" close();");
					print("</script>");
			}
	
   }	
?> 