<?php
class sigesp_include
{
	var $msg;
	function sigesp_include()
	{
                /*set_include_path('/home/lars/FirePHPCore/lib/'
                    . PATH_SEPARATOR
                    . get_include_path());
                require_once('FirePHPCore/fb.php');
                require_once("/home/lars/firephp/lib/FirePHP/Init.php");*/
		require_once("class_mensajes.php");
		require_once("class_sql.php");
		require_once("adodb/adodb.inc.php");
		$this->msg=new class_mensajes();	
	}

	function uf_conectar () 
	{
		$conec=&ADONewConnection($_SESSION["ls_gestor"]);
		//$conec->PConnect($_SESSION["ls_hostname"],$_SESSION["ls_login"],$_SESSION["ls_password"],$_SESSION["ls_database"]); 
		$servidor=$_SESSION["ls_hostname"].':'.$_SESSION["ls_port"];
		$conec->Connect($servidor,$_SESSION["ls_login"],$_SESSION["ls_password"],$_SESSION["ls_database"]); 
		
		//$conec->debug = true;
		$conec->SetFetchMode(ADODB_FETCH_ASSOC);
		if($conec===false)
		{
			$this->msg->message("No pudo conectar al servidor de base de datos, contacte al administrador del sistema");				
			exit();
		}
		return $conec;
	}
	
 	function uf_conectar_otra_bd ($as_hostname, $as_login, $as_password,$as_database,$as_gestor) 
	{ 
		$conec=&ADONewConnection($as_gestor);
			 
		$conec->Connect($as_hostname, $as_login, $as_password,$as_database); 
		
		$conec->SetFetchMode(ADODB_FETCH_ASSOC);
		if($conec===false)
		{
			$this->msg->message("No pudo conectar al servidor de base de datos, contacte al administrador del sistema");				
			exit();
		}
		return $conec;
	}
	
	function uf_obtener_parametros_conexion($as_path,$as_database,&$as_hostname,&$as_login,&$as_password,&$as_gestor)
	{
		require_once($as_path."sigesp_config.php");
		$as_hostname="";
		$as_login="";
		$as_password="";
		$as_gestor="";
		for($li_i=1;$li_i<=$i;$li_i++)
		{
			if($empresa["database"][$li_i]==$as_database)
			{
				$as_hostname=$empresa["hostname"][$li_i];
				$as_login=$empresa["login"][$li_i];
				$as_password=$empresa["password"][$li_i];
				$as_gestor=$empresa["gestor"][$li_i];
			}	
		}
	}
	
	function uf_conectar_odbc_db2()
	{
		require_once("sigesp_conexion_odbc_db2.php");
	    
		if((defined("DSN_DB2"))&&(defined("LOGIN_DB2"))&&(defined("PASSWORD_DB2"))&&(defined("DATABASE_DB2")))
		{
			
			if((trim(DSN_DB2) != "")&&(trim(LOGIN_DB2) != "")&&(trim(PASSWORD_DB2) != "")&&(trim(DATABASE_DB2) != ""))
			{
				$conec=&ADONewConnection('odbc_db2');
					 
				$conec->Connect(DSN_DB2,LOGIN_DB2,PASSWORD_DB2,DATABASE_DB2); 
				
				$conec->SetFetchMode(ADODB_FETCH_ASSOC);
				if($conec===false)
				{
					$this->msg->message("No pudo conectar al servidor de base de datos, contacte al administrador del sistema");				
					exit();
				}
				return $conec;
			}
			else
			{
			 $this->msg->message("Se deben completar todos los parametros de conexion, contacte a su administrador del sistema");				
			 exit();
			}
	    }
		else
		{
		 $this->msg->message("No se han definido los parametros de conexion, contacte a su administrador del sistema");				
		 exit();
		}

	}
}
?>
