<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', array('as'=>'index', 'uses'=>'EstatusController@getIndex'));
Route::post('/user/login', array('uses'=>'UserController@postLogin'));
Route::get('/user/logout', array('uses'=>'UserController@getLogout'));


Route::get('/cart', array('before'=>'auth.basic','as'=>'cart','uses'=>'CartController@getIndex'));
Route::post('/cart/add', array('before'=>'auth.basic','uses'=>'CartController@postAddToCart'));
Route::get('/cart/delete/{id}', array('before'=>'auth.basic','as'=>'delete_book_from_cart','uses'=>'CartController@getDelete'));


// Rutas para el objeto proveedor
Route::get('/proveedor', array('uses'=>'ProveedorpiensoController@getIndex'));
Route::post('/proveedor/add', array('uses'=>'ProveedorpiensoController@addProveedor'));
Route::get('/proveedor/add', array('uses'=>'ProveedorpiensoController@newProveedor'));
Route::get('/proveedor/delete/{id}', array('uses'=>'ProveedorpiensoController@getDelete'));


// Rutas para el objeto pienso
Route::get('/pienso', array('uses'=>'PiensoController@getIndex'));
Route::get('/pienso/add', array('uses'=>'PiensoController@getNew'));
Route::post('/pienso/add', array('uses'=>'PiensoController@getAdd'));
Route::get('/pienso/delete/{id}', array('uses'=>'PiensoController@getDelete'));

// Rutas para el objeto Pedidos
Route::get('/pedido', array('uses' => 'PedidoController@getIndex'));
Route::get('/pedido/add', array('uses' =>'PedidoController@getAdd'));
Route::post('/pedido/add', array('uses' =>'PedidoController@getNew'));
Route::get('/pedido/ver/{id}', array('uses' =>'PedidoController@getVer'));
Route::get('/pedido/delete/{id}', array('uses' =>'PedidoController@getDelete'));
Route::get('/pedido/edit/{id}', array('uses' =>'PedidoController@getEdit'));
Route::post('/pedido/edit/{id}', array('uses' =>'PedidoController@getEdit'));
Route::get('/pedido/solicitudpago', array('uses' =>'PedidoController@Pedidos_a_pagar'));
Route::post('/pedido/solicitudpago', array('uses' =>'PedidoController@Pedidos_a_pagar'));
Route::get('/pedido/solicitudcarga', array('uses' =>'PedidoController@Pedidos_a_cargar'));
Route::post('/pedido/solicitudcarga', array('uses' =>'PedidoController@Pedidos_a_cargar'));
Route::get('/pedido/solicituddescarga', array('uses' =>'PedidoController@Pedidos_a_descargar'));
Route::post('/pedido/solicituddescarga', array('uses' =>'PedidoController@Pedidos_a_descargar'));

// Rutas para añadir detalles a los pedidos
Route::post('pedidodetalle/add', array('uses' => 'PedidodetalleController@getAdd'));
Route::get('pedidodetalle/delete/{id}', array('uses'=> 'PedidodetalleController@getDelete'));


// Rutas para el objeto consumos
Route::get('consumo', array('uses' => 'ConsumoController@getIndex'));
Route::get('consumo/semanal', array('uses' => 'ConsumoController@getWeek'));
Route::post('consumo/semanal', array('uses' => 'ConsumoController@getWeek'));
Route::get('consumo/semanal/{anyo}/{semana}', array('uses' => 'ConsumoController@getSemana'));
Route::post('consumo/add', array('uses'=>'ConsumoController@getAdd'));
Route::get('consumo/delete/semanal/{id}', array('uses' => 'ConsumoController@getWeekDelete'));
Route::get('consumo/edit/semanal/{id}', array('uses' => 'ConsumoController@getWeekEdit'));
Route::post('consumo/save/semanal', array('uses' => 'ConsumoController@getWeekSave'));
Route::get('consumo/proveedores', array('uses' => 'ConsumoController@getStockSemanal'));
Route::get('consumo/almacenes', array('uses' => 'ConsumoController@getStockSemanalAlmacenes'));
Route::post('consumo/almacenes', array('uses' => 'ConsumoController@getStockSemanalAlmacenes'));
Route::get('consumo/real', array('uses' => 'ConsumoController@ProcesarConsumosReales'));
// Rutas para el objeto semanas
Route::get('semana', array('uses' => 'SemanaController@getIndex'));
Route::get('semana/add', array('uses' => 'SemanaController@getAdd'));


//Peticiones Ajax
Route::post('ajax/jaulas', array('uses' => 'AjaxController@getJaulas'));
Route::post('ajax/pellets', array('uses' => 'AjaxController@getPellets'));
Route::post('ajax/alimentacion', array('uses' => 'AjaxController@getAlimentacion'));
Route::post('ajax/cantidadalimentacion', array('uses' => 'AjaxController@UpdateAlimentacion'));
Route::post('ajax/grafica/comparativaconsumo', array('uses' => 'AjaxController@GraficaConsumoRealModeloPropuesta'));
Route::post('ajax/grafica/status/stockfinal', array('uses' => 'AjaxController@GraficaStatusStockFinal'));
Route::post('ajax/grafica/status/contenedores', array('uses' => 'AjaxController@GraficaStatusContenedores'));
Route::post('ajax/grafica/consumo/semanal', array('uses' => 'AjaxController@GraficaConsumoSemanalGranjas'));
Route::post('ajax/consumos', array('uses' => 'AjaxController@MostrarConsumoJaula'));
Route::post('ajax/actualizarsimulacion', array('uses' => 'AjaxController@ActualizarSimulacion'));
Route::post('ajax/estrategia', array('uses' => 'AjaxController@NuevaEstrategia'));
Route::post('ajax/pedidos', array('uses' => 'AjaxController@PropuestaPedido'));
Route::post('ajax/ayunos', array('uses' => 'AjaxController@UpdateAyuno'));
Route::post('ajax/estadillos', array('uses' => 'AjaxController@CambioNumeroTomas'));
Route::post('ajax/estadillos_porcentaje', array('uses' => 'AjaxController@CambioPorcentajeTomas'));
Route::get('ajax/estadillos/excel/{fecha}/{granja}', array('uses' => 'AjaxController@GenerarExcel'));


// Rutas para el objeto Producción
//Route::get('/produccion/actualizarsimulacion', array('uses' => 'ProduccionController@getIndex'));

// Rutas para el objeto Alimentación
Route::get('/alimentacion', array('uses' => 'AlimentacionController@getIndex'));

// Rutas para las propuestas de alimentación
Route::get('propuesta', array('uses' => 'PropuestaAlimentacionController@getIndex'));
Route::get('propuesta/add', array('uses' => 'PropuestaAlimentacionController@getAdd'));
Route::post('propuesta/add', array('uses' => 'PropuestaAlimentacionController@getNew'));
Route::get('propuesta/ver/{id}', array('uses' => 'PropuestaAlimentacionController@getVer'));

// Rutas para los almacenes
Route::get('almacenes/stock', array('uses' => 'AlmacenController@stock'));
Route::post('almacenes/movimientos/nuevo', array('uses' => 'AlmacenController@InsertarMovimiento'));
Route::get('almacenes/movimientos/nuevo', array('uses' => 'AlmacenController@InsertarMovimiento'));
Route::get('almacenes/comprobacion', array('uses' => 'AlmacenController@AjusteAutomaticoAlmacenesPienso'));
Route::post('almacenes/comprobacion', array('uses' => 'AlmacenController@AjusteAutomaticoAlmacenesPienso'));
Route::get('almacenes/importarstock', array('uses' => 'AlmacenController@ImportarStock'));
Route::post('almacenes/importarstock', array('uses' => 'AlmacenController@ImportarStock'));

// Rutas para los traslados
Route::get('/traslado', array('uses' => 'TrasladoController@getIndex'));
Route::get('/traslado/add', array('uses' => 'TrasladoController@getAdd'));
Route::post('/traslado/add', array('uses' => 'TrasladoController@getNew'));
Route::get('/traslado/ver/{id}', array('uses' => 'TrasladoController@getVer'));
Route::get('/traslado/delete/{id}', array('uses' => 'TrasladoController@getDelete'));
Route::get('/traslado/entrada/{id}', array('uses' => 'TrasladoController@getEntrada'));

// Rutas para añadir detalles a los traslados
Route::post('trasladodetalle/add', array('uses' => 'TrasladodetalleController@getAdd'));
Route::get('trasladodetalle/delete/{id}', array('uses'=> 'TrasladodetalleController@getDelete'));

// Rutas para las siembras
Route::get('siembras', array('uses' => 'SiembraController@getIndex'));
Route::get('siembras/add', array('uses' => 'SiembraController@getAdd'));
Route::post('siembras/add', array('uses' => 'SiembraController@getNew'));
Route::get('siembras/editar/{id}', array('uses' => 'SiembraController@getEditar'));
Route::get('siembras/eliminar/{id}', array('uses' => 'SiembraController@getEliminar'));

// Rutas para los ayunos
Route::get('ayunos', array('uses' => 'AyunoController@getIndex'));
Route::post('ayunos', array('uses' => 'AyunoController@getIndex'));

// Rutas para los estadillos
Route::get('estadillos', array('uses' => 'EstadilloController@getIndex'));
Route::post('estadillos', array('uses' => 'EstadilloController@getIndex'));

// Prueba de fishtalk
Route::get("fishtalk", function(){
    $users = DB::connection('sqlsrv')->select("select * from consumo where date = '20150520'");
    var_dump($users);
});

// Rutas para importación de datos
Route::get('importacion/automatica', array('uses' => 'ProduccionController@ImportacionAutomatica'));
