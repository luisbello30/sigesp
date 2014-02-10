<?php
class sigesp_sno_class_report_historico_contablesbsf
{
	//-----------------------------------------------------------------------------------------------------------------------------------
	function sigesp_sno_class_report_historico_contablesbsf()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_sno_class_report_historico_contablesbsf
		//		   Access: public 
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 22/05/2006 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../../shared/class_folder/sigesp_include.php");
		$io_include=new sigesp_include();
		$this->io_conexion=$io_include->uf_conectar();
		require_once("../../shared/class_folder/class_sql.php");
		$this->io_sql=new class_sql($this->io_conexion);	
		$this->DS=new class_datastore();
		$this->DS_detalle=new class_datastore();
		$this->DS_detalle_2=new class_datastore();
		require_once("../../shared/class_folder/class_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
		require_once("../../shared/class_folder/class_funciones.php");
		$this->io_funciones=new class_funciones();		
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
        $this->ls_codnom=$_SESSION["la_nomina"]["codnom"];
        $this->ls_peractnom=$_SESSION["la_nomina"]["peractnom"];
		$this->ls_anocurnom=$_SESSION["la_nomina"]["anocurnom"];
		$this->li_rac=$_SESSION["la_nomina"]["racnom"];
	}// end function sigesp_sno_class_report_historico_contablesbsf
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_config($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_config
		//		   Access: public
		//	    Arguments: as_sistema  // Sistema al que pertenece la variable
		//				   as_seccion  // Secci�n a la que pertenece la variable
		//				   as_variable  // Variable nombre de la variable a buscar
		//				   as_valor  // valor por defecto que debe tener la variable
		//				   as_tipo  // tipo de la variable
		//	      Returns: $ls_resultado variable buscado
		//	  Description: Funci�n que obtiene una variable de la tabla config
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 01/01/2006 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_valor="";
		$ls_sql="SELECT value ".
				"  FROM sigesp_config ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codsis='".$as_sistema."' ".
				"   AND seccion='".$as_seccion."' ".
				"   AND entry='".$as_variable."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_select_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			$li_i=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_valor=$row["value"];
				$li_i=$li_i+1;
			}
			if($li_i==0)
			{
				$lb_valido=$this->uf_insert_config($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo);
				if ($lb_valido)
				{
					$ls_valor=$this->uf_select_config($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo);
				}
			}
			$this->io_sql->free_result($rs_data);		
		}
		return rtrim($ls_valor);
	}// end function uf_select_config
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_config($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_config
		//		   Access: public
		//	    Arguments: as_sistema  // Sistema al que pertenece la variable
		//				   as_seccion  // Secci�n a la que pertenece la variable
		//				   as_variable  // Variable a buscar
		//				   as_valor  // valor por defecto que debe tener la variable
		//				   as_tipo  // tipo de la variable
		//	      Returns: $lb_valido True si se ejecuto el insert � False si hubo error en el insert
		//	  Description: Funci�n que inserta la variable de configuraci�n
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 01/01/2006 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql->begin_transaction();		
		$ls_sql="DELETE ".
				"  FROM sigesp_config ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codsis='".$as_sistema."' ".
				"   AND seccion='".$as_seccion."' ".
				"   AND entry='".$as_variable."' ";		
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_insert_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$this->io_sql->rollback();
		}
		else
		{
			switch ($as_tipo)
			{
				case "C"://Caracter
					$valor = $as_valor;
					break;

				case "D"://Double
					$as_valor=str_replace(".","",$as_valor);
					$as_valor=str_replace(",",".",$as_valor);
					$valor = $as_valor;
					break;

				case "B"://Boolean
					$valor = $as_valor;
					break;

				case "I"://Integer
					$valor = intval($as_valor);
					break;
			}
			$ls_sql="INSERT INTO sigesp_config(codemp, codsis, seccion, entry, value, type)VALUES ".
					"('".$this->ls_codemp."','".$as_sistema."','".$as_seccion."','".$as_variable."','".$valor."','".$as_tipo."')";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Report M�TODO->uf_insert_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$this->io_sql->rollback();
			}
			else
			{
				$this->io_sql->commit();
			}
		}
		return $lb_valido;
	}// end function uf_insert_config	
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableaportes_presupuesto()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableaportes_presupuesto
		//         Access: public (desde la clase sigesp_sno_r_contableaportes)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de las cuentas presupuestarias que afectan los conceptos de tipo P2
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 11/05/2006 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que se integran directamente con presupuesto
		$ls_sql="SELECT sno_hconcepto.codpro as programatica,  sno_hconcepto.estcla, spg_cuentas.spg_cuenta AS cueprepatcon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsalaux) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.intprocon = '1'".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon ".
				"   AND substr(sno_hconcepto.codpro,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hconcepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hconcepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hconcepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hconcepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hconcepto.codpro, spg_cuentas.spg_cuenta ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que no se integran directamente con presupuesto
		// entonces las buscamos seg�n la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codprouniadm as programatica, sno_hunidadadmin.estcla, spg_cuentas.spg_cuenta AS cueprepatcon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsalaux) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon ".
				"   AND substr(sno_hunidadadmin.codprouniadm,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hunidadadmin.codprouniadm, spg_cuentas.spg_cuenta ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_contableaportes_presupuesto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_contableaportes_presupuesto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableaportes_contable()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableaportes_contable
		//         Access: public (desde la clase sigesp_sno_r_contableaportes)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de las cuentas contables que afectan los conceptos de tipo P2
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 11/05/2006 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);	
		$li_parametros=trim($this->uf_select_config("SNO","CONFIG","CONTA GLOBAL","0","I"));
		switch($li_parametros)
		{
			case 0: // La contabilizaci�n es global
				$ls_modoaporte=$this->uf_select_config("SNO","NOMINA","CONTABILIZACION APORTES","OCP","C");
				$li_genrecapo=str_pad($this->uf_select_config("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO APORTE","0","I"),1,"0");
				break;
			
			case 1: // La contabilizaci�n es por n�mina
				$ls_modoaporte=trim($_SESSION["la_nomina"]["conaponom"]);
				$li_genrecapo=str_pad(trim($_SESSION["la_nomina"]["recdocapo"]),1,"0");
				break;
		}
		$ls_group=" GROUP BY spg_cuentas.sc_cuenta ";
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos que se 
		// integran directamente con presupuesto estas van por el debe de contabilidad
		$ls_sql="SELECT spg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta, 'D' as operacion, sum(sno_hsalida.valsalaux) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.intprocon = '1'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon ".
				"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
				"   AND substr(sno_hconcepto.codpro,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hconcepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hconcepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hconcepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hconcepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				$ls_group;
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos que NO se 
		// integran directamente con presupuesto entonces las buscamos seg�n la estructura de la unidad administrativa a 
		// la que pertenece el personal, estas van por el debe de contabilidad
		$ls_sql=$ls_sql." UNION ".
				"SELECT spg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta, 'D' as operacion, sum(sno_hsalida.valsalaux) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon ".
				"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
				"   AND substr(sno_hunidadadmin.codprouniadm,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				$ls_group;
		if(($ls_modoaporte=="OC")&&($li_genrecapo=="1"))
		{
			// Buscamos todas aquellas cuentas contables de los conceptos, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta, 'H' as operacion, sum(sno_hsalida.valsalaux) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas, rpc_proveedor ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_hsalida.valsal <> 0 ".
					"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
					"   AND scg_cuentas.status = 'C' ".
					"   AND sno_hconcepto.codprov <> '----------' ".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					"	AND sno_hconcepto.codemp = rpc_proveedor.codemp ".
					"	AND sno_hconcepto.codprov = rpc_proveedor.cod_pro ".
					"   AND scg_cuentas.codemp = rpc_proveedor.codemp ".
					"   AND scg_cuentas.sc_cuenta = rpc_proveedor.sc_cuenta ".
					" GROUP BY scg_cuentas.sc_cuenta ";
			// Buscamos todas aquellas cuentas contables de los conceptos, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta, 'H' as operacion, sum(sno_hsalida.valsalaux) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas, rpc_beneficiario ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_hsalida.valsal <> 0 ".
					"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
					"   AND scg_cuentas.status = 'C' ".
					"   AND sno_hconcepto.cedben <> '----------' ".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					"	AND sno_hconcepto.codemp = rpc_beneficiario.codemp ".
					"	AND sno_hconcepto.cedben = rpc_beneficiario.ced_bene ".
					"   AND scg_cuentas.codemp = rpc_beneficiario.codemp ".
					"   AND scg_cuentas.sc_cuenta = rpc_beneficiario.sc_cuenta ".
					" GROUP BY scg_cuentas.sc_cuenta ";
		}
		else
		{
			// Buscamos todas aquellas cuentas contables de los conceptos, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta, 'H' as operacion, sum(sno_hsalida.valsalaux) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND scg_cuentas.status = 'C'".
					"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
					"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND scg_cuentas.codemp = sno_hconcepto.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_hconcepto.cueconpatcon ".
					" GROUP BY scg_cuentas.sc_cuenta ".
					" ORDER BY operacion, cuenta ";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_contableaportes_contable ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);
				$this->DS_detalle->group_by(array('0'=>'cuenta','1'=>'operacion'),array('0'=>'total'),'total');
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_contableaportes_contable
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableconceptos_presupuesto()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableconceptos_presupuesto
		//         Access: public (desde la clase sigesp_sno_r_contableconceptos)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de las cuentas presupuestarias que afectan los conceptos de tipo A, D, P1
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 22/05/2006 								Fecha �ltima Modificaci�n : 
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql="SELECT sno_hconcepto.codpro as programatica, sno_hconcepto.estcla, sno_hconcepto.cueprecon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsalaux) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '1'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND substr(sno_hconcepto.codpro,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hconcepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hconcepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hconcepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hconcepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hconcepto.codpro, sno_hconcepto.cueprecon ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que no se integran directamente con presupuesto
		// entonces las buscamos seg�n la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codprouniadm as programatica, sno_hunidadadmin.estcla,  sno_hconcepto.cueprecon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsalaux) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND substr(sno_hunidadadmin.codprouniadm,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hunidadadmin.codprouniadm, sno_hconcepto.cueprecon ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hconcepto.codpro as programatica, sno_hconcepto.estcla, sno_hconcepto.cueprecon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsalaux) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hconcepto.sigcon = 'E'".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '1'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND substr(sno_hconcepto.codpro,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hconcepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hconcepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hconcepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hconcepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hconcepto.codpro, sno_hconcepto.cueprecon ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que no se integran directamente con presupuesto
		// entonces las buscamos seg�n la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codprouniadm as programatica,  sno_hunidadadmin.estcla, sno_hconcepto.cueprecon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsalaux) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hconcepto.sigcon = 'E'".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND substr(sno_hunidadadmin.codprouniadm,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hunidadadmin.codprouniadm, sno_hconcepto.cueprecon ".
				" ORDER BY programatica, cueprecon";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_contableconceptos_presupuesto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_contableconceptos_presupuesto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableconceptos_contable()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_contableconceptos_contable 
		//	    Arguments: 
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Funci�n que se encarga de procesar la data para la contabilizaci�n de los conceptos
		//     Creado por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 08/11/2006
		// SE MODIFICARON LAS TABLAS TH POR H
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_group="  GROUP BY scg_cuentas.sc_cuenta ";
		$li_parametros=$this->uf_select_config("SNO","CONFIG","CONTA GLOBAL","0","I");
		switch($li_parametros)
		{
			case 0: // La contabilizaci�n es global
				$ls_cuentapasivo=trim($this->uf_select_config("SNO","CONFIG","CTA.CONTA","-------------------------","C"));
				$ls_modo=trim($this->uf_select_config("SNO","NOMINA","CONTABILIZACION","OCP","C"));
				break;
				
			case 1: // La contabilizaci�n es por n�mina
				$ls_cuentapasivo=trim($_SESSION["la_nomina"]["cueconnom"]);
				$ls_modo=trim($_SESSION["la_nomina"]["consulnom"]);
				break;
		}
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D, que se 
		// integran directamente con presupuesto, estas van por el debe de contabilidad
		$ls_sql="SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion, CAST('D' AS char(1)) as operacion, sum(sno_hsalida.valsalaux) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.intprocon = '1' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
				"   AND substr(sno_hconcepto.codpro,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hconcepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hconcepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hconcepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hconcepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				$ls_group;
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D que NO se 
		// integran directamente con presupuesto entonces las buscamos seg�n la estructura de la unidad administrativa a 
		// la que pertenece el personal, estas van por el debe de contabilidad
		$ls_sql=$ls_sql." UNION ".
				"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion, CAST('D' AS char(1)) as operacion, sum(sno_hsalida.valsalaux) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
				"   AND substr(sno_hunidadadmin.codprouniadm,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				$ls_group;
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D, que se 
		// integran directamente con presupuesto, estas van por el debe de contabilidad
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D, que se 
		// integran directamente con presupuesto, estas van por el debe de contabilidad
		$ls_sql=$ls_sql." UNION ".
				"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion, CAST('H' AS char(1)) as operacion, sum(sno_hsalida.valsalaux) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.sigcon = 'E' ".
				"   AND sno_hconcepto.intprocon = '1' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
				"   AND substr(sno_hconcepto.codpro,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hconcepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hconcepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hconcepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hconcepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				$ls_group;
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D que NO se 
		// integran directamente con presupuesto entonces las buscamos seg�n la estructura de la unidad administrativa a 
		// la que pertenece el personal, estas van por el debe de contabilidad
		$ls_sql=$ls_sql." UNION ".
				"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion, CAST('H' AS char(1)) as operacion, sum(sno_hsalida.valsalaux) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.sigcon = 'E' ".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
				"   AND substr(sno_hunidadadmin.codprouniadm,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				$ls_group;
		$ls_sql=$ls_sql." UNION ".
				"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion, CAST('D' AS char(1)) as operacion, sum(sno_hsalida.valsalaux) as total ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND sno_hconcepto.sigcon = 'B' ".
				"   AND scg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND scg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND scg_cuentas.sc_cuenta = sno_hconcepto.cueconcon ".
				$ls_group;
		$ls_sql=$ls_sql." UNION ".
				"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion, CAST('H' AS char(1)) as operacion, sum(sno_hsalida.valsalaux) as total ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3' )".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND scg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND scg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND scg_cuentas.sc_cuenta = sno_hconcepto.cueconcon ".
				$ls_group;
		if($ls_modo=="OC") // Si el modo de contabilizar la n�mina es Compromete y Causa tomamos la cuenta pasivo de la n�mina.
		{
			// Buscamos todas aquellas cuentas contables de los conceptos A y D, estas van por el haber de contabilidad
			switch($_SESSION["ls_gestor"])
			{
				case "MYSQL":
					$ls_cadena="CONVERT('".$ls_cuentapasivo."' USING utf8) as cuenta";
					break;
				case "POSTGRE":
					$ls_cadena="CAST('".$ls_cuentapasivo."' AS char(25)) as cuenta";
					break;					
			}
			$ls_sql=$ls_sql." UNION ".
					"SELECT ".$ls_cadena.", MAX(scg_cuentas.denominacion) AS denominacion,  CAST('H' AS char(1)) as operacion, -sum(sno_hsalida.valsalaux) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_banco, scg_cuentas ".
					" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
					"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
					"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3' )".
					"   AND sno_hsalida.valsal <> 0 ".
					"   AND (sno_hpersonalnomina.pagbanper = 1  OR sno_hpersonalnomina.pagtaqper = 1) ".
					"   AND sno_hpersonalnomina.pagefeper = 0 ".
					"   AND scg_cuentas.status = 'C'".
					"   AND scg_cuentas.sc_cuenta = '".$ls_cuentapasivo."' ".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hsalida.codemp = sno_banco.codemp ".
					"   AND sno_hsalida.codnom = sno_banco.codnom ".
					"   AND sno_hsalida.codperi = sno_banco.codperi ".
					"   AND sno_hpersonalnomina.codemp = sno_banco.codemp ".
					"   AND sno_hpersonalnomina.codban = sno_banco.codban ".
					"   AND scg_cuentas.codemp = sno_banco.codemp ".
					" GROUP BY scg_cuentas.sc_cuenta ";
			$ls_sql=$ls_sql." UNION ".
					"SELECT ".$ls_cadena.", MAX(scg_cuentas.denominacion) AS denominacion,  CAST('H' AS char(1)) as operacion, -sum(sno_hsalida.valsalaux) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, scg_cuentas ".
					" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
					"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
					"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3')".
					"   AND sno_hsalida.valsal <> 0".
					"   AND sno_hpersonalnomina.pagbanper = 0 ".
					"   AND sno_hpersonalnomina.pagtaqper = 0 ".
					"   AND sno_hpersonalnomina.pagefeper = 1 ".
					"   AND scg_cuentas.sc_cuenta = '".$ls_cuentapasivo."' ".
					"   AND scg_cuentas.status = 'C'".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND scg_cuentas.codemp = sno_hpersonalnomina.codemp ".
					" GROUP BY scg_cuentas.sc_cuenta ";
		}
		else
		{
			// Buscamos todas aquellas cuentas contables de los conceptos A y D, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion,  CAST('H' AS char(1)) as operacion, -sum(sno_hsalida.valsalaux) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_banco, scg_cuentas ".
					" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
					"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
					"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3')".
					"   AND sno_hsalida.valsal <> 0".
					"   AND (sno_hpersonalnomina.pagbanper = 1 OR sno_hpersonalnomina.pagtaqper = 1) ".
					"   AND sno_hpersonalnomina.pagefeper = 0 ".
					"   AND scg_cuentas.status = 'C'".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hsalida.codemp = sno_banco.codemp ".
					"   AND sno_hsalida.codnom = sno_banco.codnom ".
					"   AND sno_hsalida.codperi = sno_banco.codperi ".
					"   AND sno_hpersonalnomina.codemp = sno_banco.codemp ".
					"   AND sno_hpersonalnomina.codban = sno_banco.codban ".
					"   AND scg_cuentas.codemp = sno_banco.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_banco.codcuecon ".
					" GROUP BY scg_cuentas.sc_cuenta ";
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion, CAST('H' AS char(1)) as operacion, -sum(sno_hsalida.valsalaux) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, scg_cuentas ".
					" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
					"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
					"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3')".
					"   AND sno_hsalida.valsal <> 0".
					"   AND sno_hpersonalnomina.pagbanper = 0 ".
					"   AND sno_hpersonalnomina.pagtaqper = 0 ".
					"   AND sno_hpersonalnomina.pagefeper = 1 ".
					"   AND scg_cuentas.status = 'C'".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND scg_cuentas.codemp = sno_hpersonalnomina.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_hpersonalnomina.cueaboper ".
					" GROUP BY scg_cuentas.sc_cuenta ";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_contableconceptos_contable ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);
				$this->DS_detalle->group_by(array('0'=>'cuenta','1'=>'operacion'),array('0'=>'total'),'total');
			}
			$this->io_sql->free_result($rs_data);
		}		
		return  $lb_valido;    
	}// end function uf_contableconceptos_contable
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableconceptos_presupuesto_enmohca()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableconceptos_presupuesto
		//         Access: public (desde la clase sigesp_sno_r_contableconceptos)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de las cuentas presupuestarias que afectan los conceptos de tipo A, D, P1
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 22/05/2006 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql="SELECT sno_hconcepto.codpro as programatica, sno_hconcepto.cueprecon, spg_cuentas.denominacion, sum(sno_hsalida.valsalaux) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '1'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND substr(sno_hconcepto.codpro,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hconcepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hconcepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hconcepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hconcepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hconcepto.codpro, sno_hconcepto.cueprecon, spg_cuentas.denominacion ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que no se integran directamente con presupuesto
		// entonces las buscamos seg�n la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codprouniadm as programatica, sno_hconcepto.cueprecon, spg_cuentas.denominacion, sum(sno_hsalida.valsalaux) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND substr(sno_hunidadadmin.codprouniadm,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hunidadadmin.codprouniadm, sno_hconcepto.cueprecon, spg_cuentas.denominacion ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hconcepto.codpro as programatica, sno_hconcepto.cueprecon, spg_cuentas.denominacion, sum(sno_hsalida.valsalaux) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hconcepto.sigcon = 'E'".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '1'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND substr(sno_hconcepto.codpro,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hconcepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hconcepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hconcepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hconcepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hconcepto.codpro, sno_hconcepto.cueprecon, spg_cuentas.denominacion ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que no se integran directamente con presupuesto
		// entonces las buscamos seg�n la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codprouniadm as programatica, sno_hconcepto.cueprecon, spg_cuentas.denominacion, sum(sno_hsalida.valsalaux) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hconcepto.sigcon = 'E'".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND substr(sno_hunidadadmin.codprouniadm,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hunidadadmin.codprouniadm, sno_hconcepto.cueprecon, spg_cuentas.denominacion ".
				" ORDER BY programatica, cueprecon";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_contableconceptos_presupuesto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
				$this->DS->group_by(array('0'=>'programatica','1'=>'cueprecon'),array('0'=>'total'),'total');
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_contableconceptos_presupuesto_enmohca
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableaportes_presupuesto_proyecto()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableaportes_presupuesto_proyecto
		//         Access: public (desde la clase sigesp_sno_r_contableaportes)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de las cuentas presupuestarias que afectan los conceptos de tipo P2
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 19/07/2007 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que se integran directamente con presupuesto
		$ls_sql="SELECT sno_hconcepto.codpro as programatica,  sno_hconcepto.estcla, spg_cuentas.spg_cuenta AS cueprepatcon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsalaux) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.intprocon = '1'".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon ".
				"   AND substr(sno_hconcepto.codpro,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hconcepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hconcepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hconcepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hconcepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hconcepto.codpro, spg_cuentas.spg_cuenta ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que no se integran directamente con presupuesto
		// entonces las buscamos seg�n la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codprouniadm as programatica,  sno_hunidadadmin.estcla, spg_cuentas.spg_cuenta AS cueprepatcon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsalaux) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon ".
				"   AND substr(sno_hunidadadmin.codprouniadm,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hunidadadmin.codprouniadm, spg_cuentas.spg_cuenta ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_contableaportes_presupuesto_proyecto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		if($lb_valido)
		{
			$lb_valido=$this->uf_contableaportes_presupuesto_proyecto_dt();
			$this->DS->group_by(array('0'=>'programatica','1'=>'cueprepatcon'),array('0'=>'total'),'total');		
			$this->DS->sortData('programatica');
		}
		return $lb_valido;
	}// end function uf_contableaportes_presupuesto_proyecto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableaportes_presupuesto_proyecto_dt()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableaportes_presupuesto_proyecto_dt
		//         Access: public (desde la clase sigesp_sno_r_contableaportes)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de las cuentas presupuestarias que afectan los conceptos de tipo P2
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 19/07/2007 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQL":
				$ls_cadena=" ROUND((SUM(sno_hsalida.valsalaux)*sno_hproyectopersonal.pordiames),2) ";
				break;
			case "POSTGRE":
				$ls_cadena=" ROUND(CAST((sum(sno_hsalida.valsalaux)*sno_hproyectopersonal.pordiames) AS NUMERIC),2) ";
				break;					
		}
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que se integran directamente con presupuesto
		$ls_sql="SELECT MAX(sno_hproyecto.estproproy) AS estproproy,sno_hproyecto.estcla,  MAX(spg_cuentas.spg_cuenta) AS spg_cuenta, ".
				"		sum(sno_hsalida.valsalaux) AS total, sno_hconcepto.codprov, ".$ls_cadena." AS montoparcial, ".
				"		sno_hconcepto.cedben, sno_hconcepto.codconc, sno_hproyecto.codproy, sno_hproyectopersonal.codper, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion ".
				"  FROM sno_hproyectopersonal, sno_hproyecto, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.conprocon = '1' ".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hproyectopersonal.codemp = sno_hsalida.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hsalida.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hsalida.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hsalida.codperi ".
				"   AND sno_hproyectopersonal.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hproyectopersonal.codemp = sno_hproyecto.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hproyecto.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hproyecto.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hproyecto.codperi ".
				"   AND sno_hproyectopersonal.codproy = sno_hproyecto.codproy ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon ".
				"   AND substr(sno_hproyecto.estproproy,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hproyecto.estproproy,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hproyecto.estproproy,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hproyecto.estproproy,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hproyecto.estproproy,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hproyecto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hproyectopersonal.codper, sno_hproyecto.codproy, sno_hconcepto.codconc ".
				" ORDER BY sno_hproyectopersonal.codper, sno_hproyecto.codproy, sno_hconcepto.codconc ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_contableaportes_presupuesto_proyecto_dt ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$ls_codant="";
			$li_acumulado=0;
			$li_totalant=0;
			$ls_programaticaant="";
			$ls_estclaproyant="";
			$ls_cuentaant="";
			$ls_denominacionant="";
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codper=$row["codper"];
				$li_montoparcial=$row["montoparcial"];
				$li_total=$row["total"];
				$ls_estproproy=$row["estproproy"];
				$ls_estclaproy=$row["estcla"];
				$ls_spgcuenta=$row["spg_cuenta"];
				$ls_denominacion=$row["denominacion"];
				if($ls_codper!=$ls_codant)
				{
					if($li_acumulado!=0)
					{
						if(round($li_acumulado,2)!=round($li_totalant,2))
						{
							$li_montoparcial=round(($li_totalant-$li_acumulado),2);
							$this->DS->insertRow("programatica",$ls_programaticaant);
							$this->DS->insertRow("estcla",$ls_estclaproyant);
							$this->DS->insertRow("cueprepatcon",$ls_cuentaant);
							$this->DS->insertRow("total",$li_montoparcial);
							$this->DS->insertRow("denominacion",$ls_denominacionant);
						}
						$li_acumulado=0;
					}
					$li_acumulado=$li_acumulado+$row["montoparcial"];
					$li_montoparcial=round($row["montoparcial"],2);
					$ls_programaticaant=$ls_estproproy;
					$ls_estclaproyant=$ls_estclaproy;
					$ls_cuentaant=$ls_spgcuenta;
					$ls_codant=$ls_codper;
					$ls_denominacionant=$ls_denominacion;
				}
				else
				{
					$li_acumulado=$li_acumulado+$li_montoparcial;
					$ls_programaticaant=$ls_estproproy;
					$ls_estclaproyant=$ls_estclaproy;
					$ls_cuentaant=$ls_spgcuenta;
					$li_totalant=$li_total;
					$ls_denominacionant=$ls_denominacion;
				}
				$this->DS->insertRow("programatica",$ls_estproproy);
				$this->DS->insertRow("estcla",$ls_estclaproy);
				$this->DS->insertRow("cueprepatcon",$ls_spgcuenta);
				$this->DS->insertRow("total",$li_montoparcial);
				$this->DS->insertRow("denominacion",$ls_denominacion);
			}
			if($li_acumulado!=$li_totalant)
			{
				$li_montoparcial=round(($li_totalant-$li_acumulado),2);
				$this->DS->insertRow("programatica",$ls_programaticaant);
				$this->DS->insertRow("estcla",$ls_estclaproyant);
				$this->DS->insertRow("cueprepatcon",$ls_cuentaant);
				$this->DS->insertRow("total",$li_montoparcial);
				$this->DS->insertRow("denominacion",$ls_denominacionant);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_contableaportes_presupuesto_proyecto_dt
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableaportes_contable_proyecto()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableaportes_contable_proyecto
		//         Access: public (desde la clase sigesp_sno_r_contableaportes)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de las cuentas contables que afectan los conceptos de tipo P2
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 19/07/2007 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);	
		$li_parametros=trim($this->uf_select_config("SNO","CONFIG","CONTA GLOBAL","0","I"));
		switch($li_parametros)
		{
			case 0: // La contabilizaci�n es global
				$ls_modoaporte=$this->uf_select_config("SNO","NOMINA","CONTABILIZACION APORTES","OCP","C");
				$li_genrecapo=str_pad($this->uf_select_config("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO APORTE","0","I"),1,"0");
				break;
			
			case 1: // La contabilizaci�n es por n�mina
				$ls_modoaporte=trim($_SESSION["la_nomina"]["conaponom"]);
				$li_genrecapo=str_pad(trim($_SESSION["la_nomina"]["recdocapo"]),1,"0");
				break;
		}
		$ls_group=" GROUP BY spg_cuentas.sc_cuenta ";
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos que se 
		// integran directamente con presupuesto estas van por el debe de contabilidad
		$ls_sql="SELECT spg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta, 'D' as operacion, sum(sno_hsalida.valsalaux) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.intprocon = '1' ".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon ".
				"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
				"   AND substr(sno_hconcepto.codpro,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hconcepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hconcepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hconcepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hconcepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				$ls_group;
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos que NO se 
		// integran directamente con presupuesto entonces las buscamos seg�n la estructura de la unidad administrativa a 
		// la que pertenece el personal, estas van por el debe de contabilidad
		$ls_sql=$ls_sql." UNION ".
				"SELECT spg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta, 'D' as operacion, sum(sno_hsalida.valsalaux) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon ".
				"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
				"   AND substr(sno_hunidadadmin.codprouniadm,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				$ls_group;
		if(($ls_modoaporte=="OC")&&($li_genrecapo=="1"))
		{
			// Buscamos todas aquellas cuentas contables de los conceptos, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta, 'H' as operacion, sum(sno_hsalida.valsalaux) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas, rpc_proveedor ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_hsalida.valsal <> 0 ".
					"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
					"   AND scg_cuentas.status = 'C' ".
					"   AND sno_hconcepto.codprov <> '----------' ".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					"	AND sno_hconcepto.codemp = rpc_proveedor.codemp ".
					"	AND sno_hconcepto.codprov = rpc_proveedor.cod_pro ".
					"   AND scg_cuentas.codemp = rpc_proveedor.codemp ".
					"   AND scg_cuentas.sc_cuenta = rpc_proveedor.sc_cuenta ".
					" GROUP BY scg_cuentas.sc_cuenta ";
			// Buscamos todas aquellas cuentas contables de los conceptos, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta, 'H' as operacion, sum(sno_hsalida.valsalaux) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas, rpc_beneficiario ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_hsalida.valsal <> 0 ".
					"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
					"   AND scg_cuentas.status = 'C' ".
					"   AND sno_hconcepto.cedben <> '----------' ".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					"	AND sno_hconcepto.codemp = rpc_beneficiario.codemp ".
					"	AND sno_hconcepto.cedben = rpc_beneficiario.ced_bene ".
					"   AND scg_cuentas.codemp = rpc_beneficiario.codemp ".
					"   AND scg_cuentas.sc_cuenta = rpc_beneficiario.sc_cuenta ".
					" GROUP BY scg_cuentas.sc_cuenta ";
		}
		else
		{
			// Buscamos todas aquellas cuentas contables de los conceptos, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta, 'H' as operacion, sum(sno_hsalida.valsalaux) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND scg_cuentas.status = 'C'".
					"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
					"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND scg_cuentas.codemp = sno_hconcepto.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_hconcepto.cueconpatcon ".
					" GROUP BY scg_cuentas.sc_cuenta ".
					" ORDER BY operacion, cuenta ";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_contableaportes_contable_proyecto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		if($lb_valido)
		{
			$lb_valido=$this->uf_contableaportes_contable_proyecto_dt();
			$this->DS_detalle->group_by(array('0'=>'cuenta','1'=>'operacion'),array('0'=>'total'),'total');		
			$this->DS_detalle->sortData('operacion');
		}
		return $lb_valido;
	}// end function uf_contableaportes_contable_proyecto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableaportes_contable_proyecto_dt()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableaportes_contable_proyecto_dt
		//         Access: public (desde la clase sigesp_sno_r_contableaportes)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de las cuentas contables que afectan los conceptos de tipo P2
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 19/07/2007 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);	
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQL":
				$ls_cadena=" ROUND((SUM(abs(sno_hsalida.valsalaux))*sno_hproyectopersonal.pordiames),2) ";
				break;
			case "POSTGRE":
				$ls_cadena=" ROUND(CAST((sum(abs(sno_hsalida.valsalaux))*sno_hproyectopersonal.pordiames) AS NUMERIC),2) ";
				break;					
		}
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos que se 
		// integran directamente con presupuesto estas van por el debe de contabilidad
		$ls_sql="SELECT scg_cuentas.sc_cuenta, CAST('D' AS char(1)) as operacion, sum(abs(sno_hsalida.valsalaux)) as total, ".
				"		".$ls_cadena." AS montoparcial, sno_hconcepto.codprov, sno_hconcepto.cedben, sno_hconcepto.codconc, ".
				"		sno_hproyectopersonal.codper, sno_hproyecto.codproy, MAX(scg_cuentas.denominacion) as denoconta ".
				"  FROM sno_hproyectopersonal, sno_hproyecto, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.conprocon = '1' ".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_hproyectopersonal.codemp = sno_hsalida.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hsalida.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hsalida.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hsalida.codperi ".
				"   AND sno_hproyectopersonal.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hproyectopersonal.codemp = sno_hproyecto.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hproyecto.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hproyecto.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hproyecto.codperi ".
				"   AND sno_hproyectopersonal.codproy = sno_hproyecto.codproy ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon ".
				"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
				"   AND substr(sno_hproyecto.estproproy,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hproyecto.estproproy,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hproyecto.estproproy,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hproyecto.estproproy,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hproyecto.estproproy,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hproyecto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hproyectopersonal.codper, sno_hproyecto.codproy, sno_hconcepto.codconc ".
				" ORDER BY sno_hproyectopersonal.codper, sno_hproyecto.codproy, sno_hconcepto.codconc ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_contableaportes_contable_proyecto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$ls_codant="";
			$li_acumulado=0;
			$li_totalant=0;
			$ls_cuentaant="";
			$ls_operacionant="";
			$ls_denominacionant="";
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codper=$row["codper"];
				$li_montoparcial=$row["montoparcial"];
				$li_total=$row["total"];
				$ls_cuenta=$row["sc_cuenta"];
				$ls_operacion=$row["operacion"];
				$ls_denominacion=$row["denoconta"];
				if($ls_codper!=$ls_codant)
				{
					if($li_acumulado!=0)
					{
						if(round($li_acumulado,2)!=round($li_totalant,2))
						{
							$li_montoparcial=round(($li_totalant-$li_acumulado),2);
							$this->DS_detalle->insertRow("operacion",$ls_operacionant);
							$this->DS_detalle->insertRow("cuenta",$ls_cuentaant);
							$this->DS_detalle->insertRow("total",$li_montoparcial);
							$this->DS_detalle->insertRow("denoconta",$ls_denominacionant);
						}
						$li_acumulado=0;
					}
					$li_acumulado=$li_acumulado+$row["montoparcial"];
					$li_montoparcial=round($row["montoparcial"],2);
					$ls_operacionant=$ls_operacion;
					$ls_cuentaant=$ls_cuenta;
					$ls_codant=$ls_codper;
					$ls_denominacionant=$ls_denominacion;
				}
				else
				{
					$li_acumulado=$li_acumulado+$li_montoparcial;
					$ls_operacionant=$ls_operacion;
					$ls_cuentaant=$ls_cuenta;
					$li_totalant=$li_total;
					$ls_denominacionant=$ls_denominacion;
				}
				$this->DS_detalle->insertRow("operacion",$ls_operacion);
				$this->DS_detalle->insertRow("cuenta",$ls_cuenta);
				$this->DS_detalle->insertRow("total",$li_montoparcial);
				$this->DS_detalle->insertRow("denoconta",$ls_denominacion);
			}
			if($li_acumulado!=$li_totalant)
			{
				$li_montoparcial=round(($li_totalant-$li_acumulado),2);
				$this->DS_detalle->insertRow("operacion",$ls_operacionant);
				$this->DS_detalle->insertRow("cuenta",$ls_cuentaant);
				$this->DS_detalle->insertRow("total",$li_montoparcial);
				$this->DS_detalle->insertRow("denoconta",$ls_denominacionant);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_contableaportes_contable_proyecto_dt
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableconceptos_presupuesto_proyecto()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableconceptos_presupuesto_proyecto
		//         Access: public (desde la clase sigesp_sno_r_contableconceptos)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de las cuentas presupuestarias que afectan los conceptos de tipo A, D, P1
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 19/07/2007 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql="SELECT sno_hconcepto.codpro as programatica, sno_hconcepto.estcla, sno_hconcepto.cueprecon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsalaux) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '1' ".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND substr(sno_hconcepto.codpro,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hconcepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hconcepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hconcepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hconcepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hconcepto.codpro, sno_hconcepto.cueprecon ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que no se integran directamente con presupuesto
		// entonces las buscamos seg�n la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codprouniadm as programatica, sno_hunidadadmin.estcla,  sno_hconcepto.cueprecon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsalaux) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND substr(sno_hunidadadmin.codprouniadm,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hunidadadmin.codprouniadm, sno_hconcepto.cueprecon ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hconcepto.codpro as programatica, sno_hconcepto.estcla, sno_hconcepto.cueprecon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsalaux) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hconcepto.sigcon = 'E'".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '1'".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND substr(sno_hconcepto.codpro,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hconcepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hconcepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hconcepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hconcepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hconcepto.codpro, sno_hconcepto.cueprecon ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que no se integran directamente con presupuesto
		// entonces las buscamos seg�n la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codprouniadm as programatica, sno_hunidadadmin.estcla, sno_hconcepto.cueprecon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsalaux) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hconcepto.sigcon = 'E'".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND substr(sno_hunidadadmin.codprouniadm,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hunidadadmin.codprouniadm, sno_hconcepto.cueprecon ".
				" ORDER BY programatica, cueprecon";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_contableconceptos_presupuesto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		if($lb_valido)
		{
			$lb_valido=$this->uf_contableconceptos_presupuesto_proyecto_dt();
			$this->DS->group_by(array('0'=>'programatica','1'=>'cueprecon'),array('0'=>'total'),'total');		
			$this->DS->sortData('programatica');
		}
		return $lb_valido;
	}// end function uf_contableconceptos_presupuesto_proyecto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableconceptos_presupuesto_proyecto_dt()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableconceptos_presupuesto_proyecto_dt
		//         Access: public (desde la clase sigesp_sno_r_contableconceptos)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de las cuentas presupuestarias que afectan los conceptos de tipo A, D, P1
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 19/07/2007 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQL":
				$ls_cadena=" ROUND((SUM(sno_hsalida.valsalaux)*sno_hproyectopersonal.pordiames),2) ";
				break;
			case "POSTGRE":
				$ls_cadena=" ROUND(CAST((sum(sno_hsalida.valsalaux)*sno_hproyectopersonal.pordiames) AS NUMERIC),2) ";
				break;					
		}
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql="SELECT sno_hproyectopersonal.codper, sno_hproyectopersonal.codproy, sno_hproyecto.estproproy, sno_hproyecto.estcla, spg_cuentas.spg_cuenta,".
				"		".$ls_cadena." as montoparcial, sum(sno_hsalida.valsalaux) AS total, MAX(spg_cuentas.denominacion) AS denominacion ".
				"  FROM sno_hproyectopersonal, sno_hproyecto, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.conprocon = '1' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hproyectopersonal.codemp = sno_hsalida.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hsalida.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hsalida.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hsalida.codperi ".
				"   AND sno_hproyectopersonal.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hproyectopersonal.codemp = sno_hproyecto.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hproyecto.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hproyecto.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hproyecto.codperi ".
				"   AND sno_hproyectopersonal.codproy = sno_hproyecto.codproy ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND substr(sno_hproyecto.estproproy,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hproyecto.estproproy,56,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hproyecto.estproproy,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hproyecto.estproproy,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hproyecto.estproproy,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hproyecto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hproyectopersonal.codper, sno_hproyectopersonal.codproy ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql=$ls_sql." UNION ".
		$ls_sql="SELECT sno_hproyectopersonal.codper, sno_hproyectopersonal.codproy, sno_hproyecto.estproproy, sno_hproyecto.estcla, spg_cuentas.spg_cuenta,".
				"		".$ls_cadena." as montoparcial, sum(sno_hsalida.valsalaux) AS total, MAX(spg_cuentas.denominacion) AS denominacion ".
				"  FROM sno_hproyectopersonal, sno_hproyecto, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hconcepto.sigcon = 'E'".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.conprocon = '1' ".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_hproyectopersonal.codemp = sno_hsalida.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hsalida.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hsalida.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hsalida.codperi ".
				"   AND sno_hproyectopersonal.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hproyectopersonal.codemp = sno_hproyecto.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hproyecto.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hproyecto.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hproyecto.codperi ".
				"   AND sno_hproyectopersonal.codproy = sno_hproyecto.codproy ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND substr(sno_hproyecto.estproproy,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hproyecto.estproproy,56,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hproyecto.estproproy,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hproyecto.estproproy,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hproyecto.estproproy,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hproyecto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hproyectopersonal.codper, sno_hproyectopersonal.codproy ".
				" ORDER BY codper, codproy ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_contableconceptos_presupuesto_proyecto_dt ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$ls_codant="";
			$li_acumulado=0;
			$li_totalant=0;
			$ls_programaticaant="";
			$ls_estclaproyant="";
			$ls_cuentaant="";
			$ls_denominacionant="";
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codper=$row["codper"];
				$li_montoparcial=round($row["montoparcial"],2);
				$li_total=round($row["total"],2);
				$ls_estproproy=$row["estproproy"];
				$ls_estclaproy=$row["estcla"];
				$ls_spgcuenta=$row["spg_cuenta"];
				$ls_denominacion=$row["denominacion"];
				if($ls_codper!=$ls_codant)
				{
					if($li_acumulado!=0)
					{
						if(round($li_acumulado,2)!=round($li_totalant,2))
						{
							$li_montoparcial=round(($li_totalant-$li_acumulado),2);
							$this->DS->insertRow("programatica",$ls_programaticaant);
							$this->DS->insertRow("estcla",$ls_estclaproyant);
							$this->DS->insertRow("cueprecon",$ls_cuentaant);
							$this->DS->insertRow("total",$li_montoparcial);
							$this->DS->insertRow("denominacion",$ls_denominacionant);
						}
						$li_acumulado=0;
					}
					$li_acumulado=$li_acumulado+$row["montoparcial"];
					$li_montoparcial=round($row["montoparcial"],2);
					$ls_programaticaant=$ls_estproproy;
					$ls_estclaproyant=$ls_estclaproy;
					$ls_cuentaant=$ls_spgcuenta;
					$ls_codant=$ls_codper;
					$ls_denominacionant=$ls_denominacion;
				}
				else
				{
					$li_acumulado=$li_acumulado+$li_montoparcial;
					$ls_programaticaant=$ls_estproproy;
					$ls_estclaproyant=$ls_estclaproy;
					$ls_cuentaant=$ls_spgcuenta;
					$li_totalant=$li_total;
					$ls_denominacionant=$ls_denominacion;
				}
				$this->DS->insertRow("programatica",$ls_estproproy);
				$this->DS->insertRow("estcla",$ls_estclaproy);
				$this->DS->insertRow("cueprecon",$ls_spgcuenta);
				$this->DS->insertRow("total",$li_montoparcial);
				$this->DS->insertRow("denominacion",$ls_denominacion);
			}
			if($li_acumulado!=$li_totalant)
			{
				$li_montoparcial=round(($li_totalant-$li_acumulado),2);
				$this->DS->insertRow("programatica",$ls_programaticaant);
				$this->DS->insertRow("estcla",$ls_estclaproyant);
				$this->DS->insertRow("cueprecon",$ls_cuentaant);
				$this->DS->insertRow("total",$li_montoparcial);
				$this->DS->insertRow("denominacion",$ls_denominacionant);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_contableconceptos_presupuesto_proyecto_dt
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableconceptos_contable_proyecto()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_contableconceptos_contable_proyecto 
		//	    Arguments: 
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Funci�n que se encarga de procesar la data para la contabilizaci�n de los conceptos
		//     Creado por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 19/07/2007
		// SE MODIFICARON LAS TABLAS TH POR H
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_group="  GROUP BY scg_cuentas.sc_cuenta ";
		$li_parametros=$this->uf_select_config("SNO","CONFIG","CONTA GLOBAL","0","I");
		switch($li_parametros)
		{
			case 0: // La contabilizaci�n es global
				$ls_cuentapasivo=trim($this->uf_select_config("SNO","CONFIG","CTA.CONTA","-------------------------","C"));
				$ls_modo=trim($this->uf_select_config("SNO","NOMINA","CONTABILIZACION","OCP","C"));
				break;
				
			case 1: // La contabilizaci�n es por n�mina
				$ls_cuentapasivo=trim($_SESSION["la_nomina"]["cueconnom"]);
				$ls_modo=trim($_SESSION["la_nomina"]["consulnom"]);
				break;
		}
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D, que se 
		// integran directamente con presupuesto, estas van por el debe de contabilidad
		$ls_sql="SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion, CAST('D' AS char(1)) as operacion, sum(sno_hsalida.valsalaux) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.intprocon = '1' ".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
				"   AND substr(sno_hconcepto.codpro,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hconcepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hconcepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hconcepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hconcepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				$ls_group;
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D que NO se 
		// integran directamente con presupuesto entonces las buscamos seg�n la estructura de la unidad administrativa a 
		// la que pertenece el personal, estas van por el debe de contabilidad
		$ls_sql=$ls_sql." UNION ".
				"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion, CAST('D' AS char(1)) as operacion, sum(sno_hsalida.valsalaux) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
				"   AND substr(sno_hunidadadmin.codprouniadm,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				$ls_group;
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D, que se 
		// integran directamente con presupuesto, estas van por el debe de contabilidad
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D, que se 
		// integran directamente con presupuesto, estas van por el debe de contabilidad
		$ls_sql=$ls_sql." UNION ".
				"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion, CAST('H' AS char(1)) as operacion, sum(sno_hsalida.valsalaux) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.sigcon = 'E' ".
				"   AND sno_hconcepto.intprocon = '1' ".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
				"   AND substr(sno_hconcepto.codpro,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hconcepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hconcepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hconcepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hconcepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				$ls_group;
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D que NO se 
		// integran directamente con presupuesto entonces las buscamos seg�n la estructura de la unidad administrativa a 
		// la que pertenece el personal, estas van por el debe de contabilidad
		$ls_sql=$ls_sql." UNION ".
				"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion, CAST('H' AS char(1)) as operacion, sum(sno_hsalida.valsalaux) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.sigcon = 'E' ".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
				"   AND substr(sno_hunidadadmin.codprouniadm,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				$ls_group;
		$ls_sql=$ls_sql." UNION ".
				"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion, CAST('D' AS char(1)) as operacion, sum(sno_hsalida.valsalaux) as total ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.sigcon = 'B' ".
				"   AND scg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND scg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND scg_cuentas.sc_cuenta = sno_hconcepto.cueconcon ".
				$ls_group;
		$ls_sql=$ls_sql." UNION ".
				"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion, CAST('H' AS char(1)) as operacion, sum(sno_hsalida.valsalaux) as total ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3' )".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND scg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND scg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND scg_cuentas.sc_cuenta = sno_hconcepto.cueconcon ".
				$ls_group;
		if($ls_modo=="OC") // Si el modo de contabilizar la n�mina es Compromete y Causa tomamos la cuenta pasivo de la n�mina.
		{
			// Buscamos todas aquellas cuentas contables de los conceptos A y D, estas van por el haber de contabilidad
			switch($_SESSION["ls_gestor"])
			{
				case "MYSQL":
					$ls_cadena="CONVERT('".$ls_cuentapasivo."' USING utf8) as cuenta";
					break;
				case "POSTGRE":
					$ls_cadena="CAST('".$ls_cuentapasivo."' AS char(25)) as cuenta";
					break;					
			}
			$ls_sql=$ls_sql." UNION ".
					"SELECT ".$ls_cadena.", MAX(scg_cuentas.denominacion) AS denominacion,  CAST('H' AS char(1)) as operacion, -sum(sno_hsalida.valsalaux) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_banco, scg_cuentas ".
					" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
					"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
					"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3' )".
					"   AND sno_hsalida.valsal <> 0 ".
					"   AND (sno_hpersonalnomina.pagbanper = 1 OR sno_hpersonalnomina.pagtaqper = 1) ".
					"   AND sno_hpersonalnomina.pagefeper = 0 ".
					"   AND scg_cuentas.status = 'C'".
					"   AND scg_cuentas.sc_cuenta = '".$ls_cuentapasivo."' ".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hsalida.codemp = sno_banco.codemp ".
					"   AND sno_hsalida.codnom = sno_banco.codnom ".
					"   AND sno_hsalida.codperi = sno_banco.codperi ".
					"   AND sno_hpersonalnomina.codemp = sno_banco.codemp ".
					"   AND sno_hpersonalnomina.codban = sno_banco.codban ".
					"   AND scg_cuentas.codemp = sno_banco.codemp ".
					" GROUP BY scg_cuentas.sc_cuenta ";
			$ls_sql=$ls_sql." UNION ".
					"SELECT ".$ls_cadena.", MAX(scg_cuentas.denominacion) AS denominacion,  CAST('H' AS char(1)) as operacion, -sum(sno_hsalida.valsalaux) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, scg_cuentas ".
					" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
					"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
					"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3')".
					"   AND sno_hsalida.valsal <> 0".
					"   AND sno_hpersonalnomina.pagbanper = 0 ".
					"   AND sno_hpersonalnomina.pagtaqper = 0 ".
					"   AND sno_hpersonalnomina.pagefeper = 1 ".
					"   AND scg_cuentas.sc_cuenta = '".$ls_cuentapasivo."' ".
					"   AND scg_cuentas.status = 'C'".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND scg_cuentas.codemp = sno_hpersonalnomina.codemp ".
					" GROUP BY scg_cuentas.sc_cuenta ";
		}
		else
		{
			// Buscamos todas aquellas cuentas contables de los conceptos A y D, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion,  CAST('H' AS char(1)) as operacion, -sum(sno_hsalida.valsalaux) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_banco, scg_cuentas ".
					" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
					"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
					"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3')".
					"   AND sno_hsalida.valsal <> 0".
					"   AND (sno_hpersonalnomina.pagbanper = 1 OR sno_hpersonalnomina.pagtaqper = 1) ".
					"   AND sno_hpersonalnomina.pagefeper = 0 ".
					"   AND scg_cuentas.status = 'C'".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hsalida.codemp = sno_banco.codemp ".
					"   AND sno_hsalida.codnom = sno_banco.codnom ".
					"   AND sno_hsalida.codperi = sno_banco.codperi ".
					"   AND sno_hpersonalnomina.codemp = sno_banco.codemp ".
					"   AND sno_hpersonalnomina.codban = sno_banco.codban ".
					"   AND scg_cuentas.codemp = sno_banco.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_banco.codcuecon ".
					" GROUP BY scg_cuentas.sc_cuenta ";
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion, CAST('H' AS char(1)) as operacion, -sum(sno_hsalida.valsalaux) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, scg_cuentas ".
					" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
					"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
					"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3')".
					"   AND sno_hsalida.valsal <> 0".
					"   AND sno_hpersonalnomina.pagbanper = 0 ".
					"   AND sno_hpersonalnomina.pagtaqper = 0 ".
					"   AND sno_hpersonalnomina.pagefeper = 1 ".
					"   AND scg_cuentas.status = 'C'".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND scg_cuentas.codemp = sno_hpersonalnomina.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_hpersonalnomina.cueaboper ".
					" GROUP BY scg_cuentas.sc_cuenta ";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_contableconceptos_contable_proyecto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		if($lb_valido)
		{
			$lb_valido=$this->uf_contableconceptos_contable_proyecto_dt();
			$this->DS_detalle->group_by(array('0'=>'cuenta','1'=>'operacion'),array('0'=>'total'),'total');		
			$this->DS_detalle->sortData('operacion');
		}
		return $lb_valido;    
	}// end function uf_contableconceptos_contable_proyecto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableconceptos_contable_proyecto_dt()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_contableconceptos_contable_proyecto_dt 
		//	    Arguments: 
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Funci�n que se encarga de procesar la data para la contabilizaci�n de los conceptos
		//     Creado por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 19/07/2007
		// SE MODIFICARON LAS TABLAS TH POR H
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQL":
				$ls_cadena=" ROUND((SUM(sno_hsalida.valsalaux)*sno_hproyectopersonal.pordiames),2) ";
				break;
			case "POSTGRE":
				$ls_cadena=" ROUND(CAST((sum(sno_hsalida.valsalaux)*sno_hproyectopersonal.pordiames) AS NUMERIC),2) ";
				break;					
		}
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D, que se 
		// integran directamente con presupuesto, estas van por el debe de contabilidad
		$ls_sql="SELECT scg_cuentas.sc_cuenta as cuenta, CAST('D' AS char(1)) as operacion, sum(sno_hsalida.valsalaux) as total, ".
				"		".$ls_cadena." as montoparcial, sno_hproyectopersonal.codper, sno_hproyectopersonal.codproy, ".
				"		MAX(scg_cuentas.denominacion) AS denominacion ".
				"  FROM sno_hproyectopersonal, sno_hproyecto, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.conprocon = '1' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hproyectopersonal.codemp = sno_hsalida.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hsalida.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hsalida.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hsalida.codperi ".
				"   AND sno_hproyectopersonal.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hproyectopersonal.codemp = sno_hproyecto.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hproyecto.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hproyecto.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hproyecto.codperi ".
				"   AND sno_hproyectopersonal.codproy = sno_hproyecto.codproy ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
				"   AND substr(sno_hproyecto.estproproy,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hproyecto.estproproy,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hproyecto.estproproy,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hproyecto.estproproy,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hproyecto.estproproy,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hproyecto.estcla = spg_cuentas.estcla".
				" GROUP BY sno_hproyectopersonal.codper, sno_hproyectopersonal.codproy ";
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D, que se 
		// integran directamente con presupuesto, estas van por el debe de contabilidad
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D, que se 
		// integran directamente con presupuesto, estas van por el debe de contabilidad
		$ls_sql=$ls_sql." UNION ".
		$ls_sql="SELECT scg_cuentas.sc_cuenta as cuenta, CAST('D' AS char(1)) as operacion, sum(sno_hsalida.valsalaux) as total, ".
				"		".$ls_cadena." as montoparcial, sno_hproyectopersonal.codper, sno_hproyectopersonal.codproy, ".
				"		MAX(scg_cuentas.denominacion) AS denominacion ".
				"  FROM sno_hproyectopersonal, sno_hproyecto, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.sigcon = 'E' ".
				"   AND sno_hconcepto.conprocon = '1' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hproyectopersonal.codemp = sno_hsalida.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hsalida.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hsalida.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hsalida.codperi ".
				"   AND sno_hproyectopersonal.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hproyectopersonal.codemp = sno_hproyecto.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hproyecto.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hproyecto.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hproyecto.codperi ".
				"   AND sno_hproyectopersonal.codproy = sno_hproyecto.codproy ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
				"   AND substr(sno_hproyecto.estproproy,0,26) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hproyecto.estproproy,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hproyecto.estproproy,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hproyecto.estproproy,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hproyecto.estproproy,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hproyecto.estcla = spg_cuentas.estcla".
				" GROUP BY sno_hproyectopersonal.codper, sno_hproyectopersonal.codproy ".
				" ORDER BY codper, codproy ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_contableconceptos_contable_proyecto_dt ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$ls_codant="";
			$li_acumulado=0;
			$li_totalant=0;
			$ls_cuentaant="";
			$ls_operacionant="";
			$ls_denominacionant="";
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codper=$row["codper"];
				$li_montoparcial=$row["montoparcial"];
				$li_total=$row["total"];
				$ls_cuenta=$row["cuenta"];
				$ls_operacion=$row["operacion"];
				$ls_denominacion=$row["denominacion"];
				if($ls_codper!=$ls_codant)
				{
					if($li_acumulado!=0)
					{
						if(round($li_acumulado,2)!=round($li_totalant,2))
						{
							$li_montoparcial=round(($li_totalant-$li_acumulado),2);
							$this->DS_detalle->insertRow("operacion",$ls_operacionant);
							$this->DS_detalle->insertRow("cuenta",$ls_cuentaant);
							$this->DS_detalle->insertRow("total",$li_montoparcial);
							$this->DS_detalle->insertRow("denominacion",$ls_denominacionant);
						}
						$li_acumulado=0;
					}
					$li_acumulado=$li_acumulado+$row["montoparcial"];
					$li_montoparcial=round($row["montoparcial"],2);
					$ls_operacionant=$ls_operacion;
					$ls_cuentaant=$ls_cuenta;
					$ls_codant=$ls_codper;
					$ls_denominacionant=$ls_denominacion;
				}
				else
				{
					$li_acumulado=$li_acumulado+$li_montoparcial;
					$ls_operacionant=$ls_operacion;
					$ls_cuentaant=$ls_cuenta;
					$li_totalant=$li_total;
					$ls_denominacionant=$ls_denominacion;
				}
				$this->DS_detalle->insertRow("operacion",$ls_operacion);
				$this->DS_detalle->insertRow("cuenta",$ls_cuenta);
				$this->DS_detalle->insertRow("total",$li_montoparcial);
				$this->DS_detalle->insertRow("denominacion",$ls_denominacion);
			}
			if($li_acumulado!=$li_totalant)
			{
				$li_montoparcial=round(($li_totalant-$li_acumulado),2);
				$this->DS_detalle->insertRow("operacion",$ls_operacionant);
				$this->DS_detalle->insertRow("cuenta",$ls_cuentaant);
				$this->DS_detalle->insertRow("total",$li_montoparcial);
				$this->DS_detalle->insertRow("denominacion",$ls_denominacionant);
			}
			$this->io_sql->free_result($rs_data);
			$this->io_sql->free_result($rs_data);
		}		
		return  $lb_valido;    
	}// end function uf_contableconceptos_contable_proyecto_dt
	//-----------------------------------------------------------------------------------------------------------------------------------
}
?>