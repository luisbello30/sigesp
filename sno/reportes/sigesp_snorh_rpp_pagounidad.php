<?php
    session_start();   
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "opener.document.form1.submit();";		
		print "</script>";		
	}
	ini_set('memory_limit','256M');
	ini_set('max_execution_time','0');

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo,$as_desnom,$as_periodo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // T�tulo del reporte
		//	    		   as_desnom // descripci�n de la n�mina
		//	    		   as_periodo // per�odo actual de la n�mina
		//    Description: funci�n que guarda la seguridad de quien gener� el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 27/04/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		$ls_descripcion="Gener� el Reporte ".$as_titulo.". Para ".$as_desnom.". ".$as_periodo;
		$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_pagosunidadadmin.php",$ls_descripcion);
		return $lb_valido;
	}		
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_periodo,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // T�tulo del Reporte
		//	    		   as_desnom // Descripci�n de la n�mina
		//	    		   as_periodo // Descripci�n del per�odo
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime los encabezados por p�gina
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creaci�n: 26/05/2008
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(50,40,555,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,700,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,730,11,$as_titulo); // Agregar el t�tulo
		$li_tm=$io_pdf->getTextWidth(11,$as_periodo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,715,11,$as_periodo); // Agregar el t�tulo		
		$io_pdf->addText(500,750,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(506,743,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera_nomina($as_codnom, $as_desnom, &$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_nomban // Nombre del Banco
		//	    		   io_cabecera // Objeto cabecera
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: funci�n que imprime la cabecera por banco
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creaci�n: 26/05/2008 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $io_pdf->ezSetDy(-7);
		$la_dato_nomina[1]=array('codigo'=>"<b>".$as_codnom."</b>",'nombre'=>"<b>".$as_desnom."</b>");
		$la_columna=array('codigo'=>'','nombre'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 10,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>2, // Sombra entre l�neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('codigo'=>array('justification'=>'right','width'=>30), 
						 			   'nombre'=>array('justification'=>'left','width'=>520))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_dato_nomina,$la_columna,'',$la_config);
		
		 $la_dato_titulos[1]=array('unidad'=>'<b>UNIDAD</b>',
		                          'numero'=>'<b>CANTIDAD</b>',
								  'monto'=>'<b>MONTO</b>');
		$la_columna=array('unidad'=>'',
		                  'numero'=>'',
						  'monto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7.5, // Tama�o de Letras
						 'titleFontSize' => 8,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas						 
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('unidad'=>array('justification'=>'center','width'=>350),
						 			   'numero'=>array('justification'=>'center','width'=>60),
									   'monto'=>array('justification'=>'center','width'=>140))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_dato_titulos,$la_columna,'',$la_config);		
	}// uf_print_cabecera_nomina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($as_data,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci�n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci�n que imprime el detalle por banco
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creaci�n: 26/05/2008 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_columna=array('unidad'=>'',
		                  'numero'=>'',
						  'monto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7.5, // Tama�o de Letras
						 'titleFontSize' => 8,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas						 
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('unidad'=>array('justification'=>'left','width'=>350),
						 			   'numero'=>array('justification'=>'center','width'=>60),
									   'monto'=>array('justification'=>'right','width'=>140))); // Justificaci�n y ancho de la 
		$io_pdf->ezTable($as_data,$la_columna,'',$la_config);		
	}// end function uf_print_detalle
	///---------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_totales_nominas($total1,$total2,$io_pdf)
	{
		$la_data_total[1]=array('total'=>'<b>TOTAL POR NOMINA</b>',
								'total1'=>$total2,
								'total2'=>$total1);
		$la_columna=array('total'=>'',
						  'total1'=>'',
						  'total2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7.5, // Tama�o de Letras
						 'titleFontSize' => 8,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas						 
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('total'=>array('justification'=>'center','width'=>350),
									   'total1'=>array('justification'=>'center','width'=>60),
									   'total2'=>array('justification'=>'right','width'=>140))); // Justificaci�n y ancho de la 
		$io_pdf->ezTable($la_data_total,$la_columna,'',$la_config);		
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//----------------------------------------------------------------------------------------------------------------------------
	function uf_totales_unidad($as_data_unidad,$cantidad,$total,$io_pdf)
	{
		$io_pdf->ezSetDy(-30);
		$la_data_resumen[1]=array('resumen'=>'<b>RESUMEN POR UNIDAD</b>');
		$la_columna=array('resumen'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 10,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>2, // Sombra entre l�neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('resumen'=>array('justification'=>'center','width'=>550))); // Justificaci�n y ancho de la 
		$io_pdf->ezTable($la_data_resumen,$la_columna,'',$la_config);	
									   
		$la_data_total[1]=array('unidad'=>'<b>UNIDAD</b>',
								'total1'=>'<b>CANTIDAD</b>',
								'total2'=>'<b>MONTO</b>');
		$la_columna=array('unidad'=>'',
						  'total1'=>'',
						  'total2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7.5, // Tama�o de Letras
						 'titleFontSize' => 8,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas						 
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('unidad'=>array('justification'=>'center','width'=>350),
									   'total1'=>array('justification'=>'center','width'=>60),
									   'total2'=>array('justification'=>'center','width'=>140))); // Justificaci�n y ancho de la 
		$io_pdf->ezTable($la_data_total,$la_columna,'',$la_config);	
		
		$la_columna=array('unidad'=>'',
						  'total1'=>'',
						  'total2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7.5, // Tama�o de Letras
						 'titleFontSize' => 8,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas						 
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('unidad'=>array('justification'=>'left','width'=>350),
									   'total1'=>array('justification'=>'center','width'=>60),
									   'total2'=>array('justification'=>'right','width'=>140))); // Justificaci�n y ancho de la 
		$io_pdf->ezTable($as_data_unidad,$la_columna,'',$la_config);
		
		$la_data_totales[1]=array('unidad'=>'<b>TOTALES POR UNIDAD</b>',
								'total1'=>$cantidad,
								'total2'=>$total);
		$la_columna=array('unidad'=>'',
						  'total1'=>'',
						  'total2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7.5, // Tama�o de Letras
						 'titleFontSize' => 8,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas						 
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('unidad'=>array('justification'=>'center','width'=>350),
									   'total1'=>array('justification'=>'center','width'=>60),
									   'total2'=>array('justification'=>'right','width'=>140))); // Justificaci�n y ancho de la 
		$io_pdf->ezTable($la_data_totales,$la_columna,'',$la_config);					  
	}
	//----------------------------------------------------------------------------------------------------------------------------

     
	//-----------------------------------------------------  Instancia de las clases ------------------------------------------------
	require_once("../../shared/ezpdf/class.ezpdf.php");	
	$ls_bolivares="";
	require_once("sigesp_snorh_class_report.php");
	$io_report=new sigesp_snorh_class_report();					
    $ls_bolivares ="Bs.";
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	//----------------------------------------------------  Par�metros del encabezado  -----------------------------------------------
	$ls_titulo="<b>Resumen de Pagos por Unidad Administrativa</b>";
	//--------------------------------------------------  Par�metros para Filtar el Reporte  -----------------------------------------
	$ls_codnomdes=$io_fun_nomina->uf_obtenervalor_get("codnomdes","");
	$ls_codnomhas=$io_fun_nomina->uf_obtenervalor_get("codnomhas","");
	$ls_codperides=$io_fun_nomina->uf_obtenervalor_get("codperdes","");
	$ls_codperhas=$io_fun_nomina->uf_obtenervalor_get("codperhas","");
	$ls_unidaddes=$io_fun_nomina->uf_obtenervalor_get("codunides","");
	$ls_unidadhas=$io_fun_nomina->uf_obtenervalor_get("codunihas","");
	$ls_orden=$io_fun_nomina->uf_obtenervalor_get("orden","");
	$ld_aniodesde=substr($io_fun_nomina->uf_obtenervalor_get("fecdesper",""),6,4);
	$ld_aniohasta=substr($io_fun_nomina->uf_obtenervalor_get("fechasper",""),6,4);
	$ld_fecdesper=$io_fun_nomina->uf_obtenervalor_get("fecdesper","");
	$ld_fechasper=$io_fun_nomina->uf_obtenervalor_get("fechasper","");
	$ls_coddeddes=$io_fun_nomina->uf_obtenervalor_get("coddeddes","");
	$ls_coddedhas=$io_fun_nomina->uf_obtenervalor_get("coddedhas","");
	$ls_codtipperdes=$io_fun_nomina->uf_obtenervalor_get("codtipperdes","");
	$ls_codtipperhas=$io_fun_nomina->uf_obtenervalor_get("codtipperhas","");
	$ls_periodo= "Periodo Desde: ".$ld_fecdesper." - Per�odo Hasta: ".$ld_fechasper;
	
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo,'',$ls_periodo); // Seguridad de Reporte
	$lb_valido=true;
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_seleccionar_nominaunidad($ls_codnomdes,$ls_codnomhas,$ls_codperides,$ls_codperhas,$ls_orden,$ld_aniodesde,
														   $ld_aniohasta,$ls_coddeddes,$ls_coddedhas,$ls_codtipperdes,$ls_codtipperhas); 
	}
	if($lb_valido==false) // Existe alg�n error � no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		error_reporting(E_ALL);
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3.6,2.5,3,3); // Configuraci�n de los margenes en cent�metros
		uf_print_encabezado_pagina($ls_titulo,$ls_periodo,$io_pdf); // Imprimimos el encabezado de la p�gina
		$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el n�mero de p�gina	
		while ((!$io_report->rs_data->EOF)&&($lb_valido))
		{  
			$ls_codnom=$io_report->rs_data->fields["codnom"];
			$ls_desnom=$io_report->rs_data->fields["desnom"];		  
			uf_print_cabecera_nomina($ls_codnom, $ls_desnom, &$io_pdf);	
			$lb_valido=$io_report->uf_pagos_unidad($ls_codnom,$ls_codperides,$ls_codperhas,$ls_unidaddes,$ls_unidadhas,$ld_aniodesde,$ld_aniohasta,
												   $ls_coddeddes,$ls_coddedhas,$ls_codtipperdes,$ls_codtipperhas,$ls_orden);
			$li_j=0;
			$total_monto=0;
			$total_cantidad=0;
			while ((!$io_report->rs_detalle->EOF)&&($lb_valido))
			{  
				$ls_desuniadm=$io_report->rs_detalle->fields["desuniadm"];			  
				$ls_monto=$io_report->rs_detalle->fields["monnetres"];			  
				$total_monto=$total_monto+$ls_monto;
				$ls_uni1=$io_report->rs_detalle->fields["minorguniadm"];	
				$ls_uni2=$io_report->rs_detalle->fields["ofiuniadm"];	
				$ls_uni3=$io_report->rs_detalle->fields["uniuniadm"];	
				$ls_uni4=$io_report->rs_detalle->fields["depuniadm"];	
				$ls_uni5=$io_report->rs_detalle->fields["prouniadm"];	
				$ls_cantidad=$io_report->rs_detalle->fields["totalpersonal"];
				$total_cantidad=$total_cantidad+$ls_cantidad;		  
				$ls_data[$li_j]=array('unidad'=>$ls_desuniadm,'numero'=>$ls_cantidad,'monto'=>$io_fun_nomina->uf_formatonumerico($ls_monto));
				$li_j++;
				$io_report->rs_detalle->MoveNext();			      
			}
			if ($li_j>0)
			{
				uf_print_detalle($ls_data,&$io_pdf);
				uf_totales_nominas($io_fun_nomina->uf_formatonumerico($total_monto),$total_cantidad,$io_pdf);	
				unset($ls_data);
			}	
			$io_report->rs_data->MoveNext();	  
		}
		$lb_valido=$io_report->uf_pagos_unidad_totales($ls_codnomdes,$ls_codnomhas,$ls_codperides,$ls_codperhas,$ls_unidaddes,$ls_unidadhas,
													   $ld_aniodesde,$ld_aniohasta,$ls_coddeddes,$ls_coddedhas,$ls_codtipperdes,$ls_codtipperhas,
													   $ls_orden);						   
		$li_k=0;
		$ls_cant_t=0;
		$ls_tot_uni=0;
		while ((!$io_report->rs_detalle->EOF)&&($lb_valido))
		{
			$ls_uni=$io_report->rs_detalle->fields["desuniadm"];
			$ls_mon=$io_report->rs_detalle->fields["monnetres"];
			$ls_uni1=$io_report->rs_detalle->fields["minorguniadm"];	
			$ls_uni2=$io_report->rs_detalle->fields["ofiuniadm"];	
			$ls_uni3=$io_report->rs_detalle->fields["uniuniadm"];	
			$ls_uni4=$io_report->rs_detalle->fields["depuniadm"];	
			$ls_uni5=$io_report->rs_detalle->fields["prouniadm"];
			$ls_cant=$io_report->rs_detalle->fields["totalpersonal"];
			$ls_cant_t=$ls_cant_t+$ls_cant;
			$ls_tot_uni=$ls_tot_uni+$ls_mon;
			$ls_data_unidad[$li_k]=array('unidad'=>$ls_uni,'total2'=>$io_fun_nomina->uf_formatonumerico($ls_mon),'total1'=>$ls_cant);
			$li_k++;
			$io_report->rs_detalle->MoveNext();	  
		}
		if ($li_k>0)
		{								   
			uf_totales_unidad($ls_data_unidad,$ls_cant_t,$io_fun_nomina->uf_formatonumerico($ls_tot_uni),$io_pdf);	
		}
		if(($lb_valido)&&($li_j>0)&&($li_k>0)) // Si no ocurrio ning�n error
		{
			$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresi�n de los n�meros de p�gina
			$io_pdf->ezStream(); // Mostramos el reporte
		}
		else  // Si hubo alg�n error
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que reportar');"); 
			print(" close();");
			print("</script>");		
		}
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_nomina);
?> 