/***********************************************************************************
* @Archivo javascript que incluye tanto los componentes como los eventos asociados 
* al proceso de asignar usuarios al tipo de personal. 
* @fecha de creaci�n: 24/10/2008
* @autor: Ing. Gusmary Balza
*************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/

var cambiar = false;
var panel      = '';
var pantalla   = 'usuariospersonal'; //par�metro para el catalogo
var ruta = '../../controlador/sss/sigesp_ctr_sss_usuariospermisos.php';
var RecordDefUsu = '';
var gridUsu   = '';
var dsusuario = '';
var arrEliminar = new Array();
var usuarioElim = '';
var toteliminar = 0;
var datosNuevo={'raiz':[{'codusu':'','nomusu':'','apeusu':''}]};

Ext.onReady
(
	function()
	{
	    Ext.QuickTips.init();
		Ext.form.Field.prototype.msgTarget = 'side';
		var agregar = new Ext.Action(
		{
			text: 'Agregar',
			handler: irAgregar,
			iconCls: 'bmenuagregar',
        	tooltip: 'Agregar un usuario al personal'
		});
		
		var quitar = new Ext.Action(
		{
			text: 'Quitar',
			handler: irQuitar,
			iconCls: 'bmenuquitar',
        	tooltip: 'Quitar usuario del personal'
		});
		
		Xpos = ((screen.width/2)-(550/2)); 
		Ypos = ((screen.height/2)-(500/2));
		//Panel con los componentes del formulario
		panel = new Ext.FormPanel({
        labelWidth: 75,
     	title: 'Proceso de Asignar Usuarios a Personal',
        bodyStyle:'padding:5px 5px 5px',
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',
	  	tbar: [],
        defaults: {width: 230},		   
		items:[{
			    xtype:'fieldset',
				title:'Datos del Tipo de Personal',
				id:'fsformpersonal',
    			autoHeight:true,
				autoWidth:true,
				cls :'fondo',	
			    items:[{			   
						xtype:'textfield',
						fieldLabel:'C�digo',
						name:'C�digo del personal',
						id:'txtcodigo',
						disabled: true,
						width:80
					},{
						xtype:'textfield',
						fieldLabel:'Denominaci�n',
						name:'Denominaci�n',
						id:'txtdenom',
						disabled: true,
						width:400
					}]
				},{
					xtype:'panel',
					width:500,
					title:'Usuarios para el Personal',
					tbar: [agregar,quitar],
					contentEl:'grid-usuariospersonal'
			}]
		});
	panel.render(document.body);
	
	//llamada a la funci�n
	obtenerGridUsuario();
});	//FIN

		
/***********************************************************************************
* @Funci�n para agregar un registro en la grid y llamar al cat�logo de usuarios.
* @parametros: 
* @retorno: 
* @fecha de creaci�n: 24/10/2008. 
* @autor: Ing. Gusmary Balza. 
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/	
	function irAgregar()
	{
		ParamGridTarget = gridUsu;
		var arreglotxt     = new Array('','');		
		var arreglovalores = new Array('codusu','cedusu','nomusu','apeusu','telusu','email','ultingusu','actusu','admusu','nota');
		ObjUsuario      = new catalogoUsuario();
		ObjUsuario.mostrarCatalogo('','',arreglotxt, arreglovalores);
	}			


/***********************************************************************************
* @Funci�n para crear la grid y pasarle los datos del usuario.
* @parametros: 
* @retorno: 
* @fecha de creaci�n: 24/10/2008. 
* @autor: Ing. Gusmary Balza. 
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/
	function obtenerGridUsuario()
	{	
		RecordDefUsu = Ext.data.Record.create
		([
			{name: 'codusu'}, 
			{name: 'nomusu'},
			{name: 'apeusu'}
		]);
		
		var DatosNuevo = {'raiz':[{'codusu':'','nomusu':'','apeusu':''}]};	
		dsusuariogrupo =  new Ext.data.Store({
		proxy: new Ext.data.MemoryProxy(DatosNuevo),
		reader: new Ext.data.JsonReader({
			root: 'raiz',               
			id: 'id'   
			},
				  RecordDefUsu
			),
			data: DatosNuevo
			});
		
		gridUsu = new Ext.grid.GridPanel({
				width:500,
				autoScroll:true,
				border:true,
				ds: dsusuariogrupo,
				cm: new Ext.grid.ColumnModel([
					{header: 'C�digo', width: 100, sortable: true,   dataIndex: 'codusu'},
					{header: 'Nombre', width: 200, sortable: true, dataIndex: 'nomusu'},
					{header: 'Apellido', width: 200, sortable: true, dataIndex: 'apeusu'}
				]),
				viewConfig: {
								forceFit:true
							},
				autoHeight:true,
				stripeRows: true
		});
		gridUsu.render('grid-usuariospersonal');
	}
	
		
/***********************************************************************************
* @Funci�n para confirmar eliminar un registro de la grid de usuarios.
* @parametros: 
* @retorno: 
* @fecha de creaci�n: 24/10/2008. 
* @autor: Ing. Gusmary Balza. 
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/
	function irQuitar()
	{
		var claveseleccionada = gridUsu.selModel.selections.keys;
		if(claveseleccionada.length > 0)
		{
			Ext.Msg.confirm('Alerta!','Realmente desea eliminar el registro?', borrarRegistro);
		} 
		else 
		{
			Ext.Msg.alert('Alerta!','Seleccione un registro para eliminar');
		}
	}
	
	
/***********************************************************************************
* @Funci�n para eliminar un registro de la grid de usuarios.
* @parametros: 
* @retorno: 
* @fecha de creaci�n: 15/08/2008. 
* @autor: Ing. Gusmary Balza. 
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/	
	function borrarRegistro(btn) 
	{
		if (btn=='yes') 
		{
			var filaseleccionada = gridUsu.getSelectionModel().getSelected();
			if (filaseleccionada)
			{
				usuarioElim    = gridUsu.getSelectionModel().getSelected().get('codusu');
				arrEliminar[toteliminar] = usuarioElim;
				toteliminar++;
				gridUsu.store.remove(filaseleccionada);
				Ext.Msg.alert('Exito','Registro eliminado');				
			}
		} 
	}
	
	
/***********************************************************************************
* @Limpiar campos del formulario
* @parametros: 
* @retorno: 
* @fecha de creaci�n: 24/10/2008. 
* @autor: Ing. Gusmary Balza. 
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/		
	function limpiarCampos() 
	{		 
		panel.getComponent('fsformpersonal').getComponent('txtcodigo').setValue('');
		panel.getComponent('fsformpersonal').getComponent('txtdenom').setValue('');
	}


/***********************************************************************************
* @Funci�n que limpia los campos y asigna un nuevo c�digo
* @parametros: 
* @retorno: 
* @fecha de creaci�n: 24/10/2008. 
* @autor: Ing. Gusmary Balza. 
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/
	function irCancelar()
	{
		limpiarCampos();
		gridUsu.store.removeAll();
		gridUsu.store.loadData(datosNuevo);
		gridUsu.store.commitChanges();
		arrEliminar = new Array();
		toteliminar = 0;
		cambiar = false;
	}


/***********************************************************************************
* @Funci�n que guarda o actualiza los datos del proceso de asignaci�n.
* @parametros: 
* @retorno: 
* @fecha de creaci�n: 24/10/2008. 
* @autor: Ing. Gusmary Balza. 
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/
	function irGuardar()
	{
		valido=true;
	/*	if((!tbnuevo)&&(!cambiar))
		{
			valido=false;
			Ext.MessageBox.alert('Error','No tiene permiso para Incluir.');
		}*/
		if((!tbactualizar)&&(cambiar))
		{
			valido=false;
			Ext.MessageBox.alert('Error','No tiene permiso para Modificar.');
		}
		
		if (Ext.getCmp('txtcodigo').getValue()=='')
		{
			Ext.Msg.alert('Mensaje','Debe seleccionar un Tipo de Personal');
		}
		else
		{			 	
			obtenerMensaje('procesar','','Guardando Datos');
			
			var cadenaJson = "{'oper': 'actualizar','codsis':'SNO','seleccionado':'personal','sistema': sistema,'vista': vista,'codtippersss': '"+Ext.getCmp('txtcodigo').getValue()+"','dentippersss': '"+Ext.getCmp('txtdenom').getValue()+"'";				
			arrAdmin = gridUsu.store.getModifiedRecords();
			cadenaJson=cadenaJson+ ",datosAdmin:[";
			total = arrAdmin.length;
			if (total>0)
			{	
				for (i=0; i < total; i++)
				{
					if (i==0)
					{
						cadenaJson = cadenaJson +"{'codusu':'"+ arrAdmin[i].get('codusu')+ "'}";
					}
					else
					{
						cadenaJson = cadenaJson +",{'codusu':'"+ arrAdmin[i].get('codusu')+ "'}";
					}
				}			
			}
			cadenaJson = cadenaJson + ']';
			cadenaJson=cadenaJson+ ',datosEliminar:[';
			total = arrEliminar.length;
			if (total>0)
			{
				for (i=0; i < total; i++)
				{
					if (i==0)
					{
						cadenaJson = cadenaJson +"{'codusu':'"+ arrEliminar[i]+ "'}";
					}
					else
					{
						cadenaJson = cadenaJson +",{'codusu':'"+ arrEliminar[i]+ "'}";
					}
				}			
			}
			cadenaJson = cadenaJson + ']}';
			objdata= eval('(' + cadenaJson + ')');	
			objdata=Ext.util.JSON.encode(objdata);
			parametros = 'objdata='+objdata; 
			Ext.Ajax.request({
			url : ruta,
			params : parametros,
			method: 'POST',
			success: function (resultado, request)
			{ 
				datos = resultado.responseText;
				Ext.Msg.hide();
				var datajson = eval('(' + datos + ')');
				if (datajson.raiz.valido==true)
				{	
					Ext.MessageBox.alert('Mensaje', datajson.raiz.mensaje);
					irCancelar();  
				}
				else
				{
					Ext.MessageBox.alert('Error', datajson.raiz.mensaje);
				}
			},
			failure: function (result,request) 
			{ 
				Ext.Msg.hide();
				Ext.MessageBox.alert('Error', 'Error al procesar la Informaci�n'); 
			}					
			});
		}
	}


/***********************************************************************************
*  @Funci�n que llama al catalogo para mostrar los datos del tipo de personal.
* @parametros: 
* @retorno: 
* @fecha de creaci�n: 24/10/2008. 
* @autor: Ing. Gusmary Balza. 
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/
	function irBuscar()
	{
		var arreglotxt     = new Array('txtcodigo','txtdenom');		
		var arreglovalores = new Array('codtippersss','dentippersss');			
		objCatPersonal = new catalogoPersonal();
		objCatPersonal.mostrarCatalogoPersonal(arreglotxt, arreglovalores);
		//cambiar = true;
	}
	
	
/***********************************************************************************
* @Funci�n que elimina un usuario de un tipo de personal seleccionado.
* @parametros: 
* @retorno: 
* @fecha de creaci�n: 24/10/2008. 
* @autor: Ing. Gusmary Balza. 
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/
	function irEliminar()
	{
		var Result;		
		Ext.MessageBox.confirm('Confirmar', '�Desea eliminar todos los Usuarios?', Result);
				
		function Result(btn)
		{
			if(btn=='yes')
			{ 
				if (validarObjetos('txtcodigo','60','novacio')=='0')
				{					
					Ext.MessageBox.alert('Mensaje','No existen datos para eliminar');
				}
				else
				{
					obtenerMensaje('procesar','','Eliminando Datos');
					var objdata ={
						'oper': 'eliminar', 
						'codsis':'SNO',
						'seleccionado': 'personal',
						'sistema': sistema,
						'vista': vista,
						'codtippersss': Ext.getCmp('txtcodigo').getValue()						
					};	
				
					objdata=Ext.util.JSON.encode(objdata);
					parametros = 'objdata='+objdata;
					alert(parametros);
					Ext.Ajax.request({
					url : ruta,
					params : parametros,
					method: 'POST',
					success: function ( resultado, request )
					{ 
						datos = resultado.responseText;
						Ext.Msg.hide();
						var datajson = eval('(' + datos + ')');
						if (datajson.raiz.valido==true)
						{
							Ext.MessageBox.alert('Mensaje',datajson.raiz.mensaje);
							irCancelar();	  
						}
						else
						{
							Ext.MessageBox.alert('Error', datajson.raiz.mensaje);
						}
					},
					failure: function ( result, request)
					{ 
						Ext.Msg.hide();
						Ext.MessageBox.alert('Error', 'Error al procesar la informaci�n'); 
					} 
					});
				}
			}
		};		
	}	
	
