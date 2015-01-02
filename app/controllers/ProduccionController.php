<?php 

class ProduccionController extends BaseController
{

	public function getIndex(){
		//Obtenemos los valores de la semana actual, mezclando valores reales y simulados
		$this->actualizarSimulacion();
        return View::make('produccion.index');
	}

	public function actualizarSimulacion()
    {
		//Buscamos el último valor real introducido
		$fecha = DB::table('produccion_real')->max('date');
		//var_dump($fecha);
		// Recuperamos todos los registros de produccion real que coinciden con dicha fecha
		//Actualizamos los datos iniciales de los datos reales
       
       //Actualizamos primero los valores iniciales de los datos de producción real
        
        $actualizacion_datos_iniciales = DB::update('Update produccion_real pr1, produccion_real pr2 
                                                     set pr1.stock_count_ini = pr2.stock_count_fin, 
                                                         pr1.stock_avg_ini = pr2.stock_avg_fin, 
                                                         pr1.stock_bio_ini = pr2.stock_bio_fin 
                                                   where pr1.stock_count_ini = 0 
                                                     and pr2.unitname = pr1.unitname 
                                                     and pr2.groupid  =  pr1.groupid 
                                                     and pr1.date = DATE_ADD(pr2.date, INTERVAL 1 DAY)'); 

        $producciones_reales = ProduccionReales::where('date', '=', $fecha)
		                                     ->orderBy('site')
		                                     ->orderBy('unitname')
		                                     ->orderBy('groupid')
		                                     ->get();
		//echo $produccion_real;
		// Para cada uno de los registros, actualizamos a partir de hoy 60 días
		//$fecha_ini = new DateTime($fecha);
        //var_dump($fecha_ini);
        //$fecha_ini->modify('+1 day');
        //var_dump($fecha_ini);
        //$fecha_temp = new DateTime($fecha);
        //$fecha_temp->modify('+1 day');
        //$dias_actualizar = $this->DiasActualiza($fecha_ini);

        //$fecha_fin = $fecha_temp->modify('+' . $dias_actualizar . ' day');
        //var_dump($fecha_ini);
        //var_dump($fecha_fin);
        foreach ($producciones_reales as $produccion_real)
		{
             
             $fecha_ini = new DateTime($fecha);
             //var_dump($fecha_ini);
             $fecha_ini->modify('+1 day');
             //var_dump($fecha_ini);
             $fecha_temp = new DateTime($fecha);
             $fecha_temp->modify('+1 day');
             $dias_actualizar = $this->DiasActualiza($fecha_ini);

             $fecha_fin = $fecha_temp->modify('+' . $dias_actualizar . ' day');
            
            if (($produccion_real->stock_count_fin) > 0)
             {
               $this->actualizarSimulacionIntervalo($produccion_real->site, 
                                                    $produccion_real->unitname, 
                                                    $produccion_real->groupid, 
                                                    $produccion_real->stock_count_fin, 
                                                    $produccion_real->stock_avg_fin, 
                                                    $fecha_ini, 
                                                    $fecha_fin);
            } 
            else
            {
                // Debemos eliminar los datos simulados que hay de la jaula y lote a partir de hoy
            }
            
            // Actualizamos los datos de consumos
            $consumo = new ConsumoController;
            $fecha_ini = new DateTime($fecha);
             //var_dump($fecha_ini);
             $fecha_ini->modify('+1 day');
             //var_dump($fecha_ini);
             $fecha_temp = new DateTime($fecha);
             $fecha_temp->modify('+1 day');
             $dias_actualizar = $this->DiasActualiza($fecha_ini);

             $fecha_fin = $fecha_temp->modify('+' . $dias_actualizar . ' day');
            //echo 'Fecha Ini: ';
            //var_dump($fecha_ini);
            //echo 'Fecha Fin: ';
            //var_dump($fecha_fin);
            if (($produccion_real->stock_count_fin) > 0)

            {
                $consumo->ActualizarConsumoSimuladoIII($produccion_real->unitname, 
                                                    $fecha_ini, 
                                                    $fecha_fin,0);
            }
            else
            {
                // Debemos eliminar los datos simulados que hay de la jaula y lote a partir de hoy
            }
        }

        /*
			$dia_ini = new DateTime($produccion_real->date);
			
			// Recuperamos los datos de producción reales
			// Stock inicial
			$stock_count_ini = $produccion_real->stock_count_fin;
			$stock_avg_ini   = $produccion_real->stock_avg_fin;
			$stock_bio_ini   = $produccion_real->stock_bio_fin;

			// Mortalidad
			$mortality_count = $produccion_real->mortality_count;
			$mortality_avg   = $produccion_real->mortality_avg;
			$mortality_bio   = $produccion_real->mortality_bio;
            $dias_actualizar = $this->DiasActualiza($dia_ini); 
            //echo  ' Dias a actualizar: ' . $dias_actualizar;
			for ($i=0;$i < $dias_actualizar ; $i++)
			 {
                // Recuperamos el valor de la tabla de produccion simulada que coincida
                // con la granja, jaula, lote y fecha que vamos a actualizar
                $dia_ini->modify('+1 day');


                // Obtenemos la temperatura que le corresponde al día
                $temperatura = $this->temperatura_dia(1, $dia_ini);
                
                // Calculamos el SGR teórico en base a la temperatura y al peso medio inicial.
                $SGR = $this->CalcularSGR(1,$stock_avg_ini,$temperatura );
                
                // Calculamos el FCR teórico en base a la temperatura y al peso medio inicial.
                $FCR = $this->CalcularFCR(1, $stock_avg_ini, $temperatura, $SGR);

                // Calculamos el consumo de pienso recomendado
                $SFR = $SGR * $FCR;
                $pienso_recomendado = ($SFR * (($stock_count_ini * $stock_avg_ini)/1000))/100 ;
                $cantidad_toma_modelo = $pienso_recomendado;
                $sacos = $pienso_recomendado/25;
                $pienso_recomendado= number_format(ceil($sacos)*25, 0);

                // Inicializamos la variable de % de la estrategia de alimentación.
                $PorcentajeEstrategia = 1;    
                //echo 'Comprobar el stock_avg_ini: ' .  $stock_avg_ini . ' y la segunda parte de la fórmula vale: ' . pow(($SGR/100+1), 1) . ' El SGR vale: '. $SGR;         
                $stock_avg_fin = $stock_avg_ini * pow(($SGR/100+1), 1); 
                $stock_count_fin = ($stock_count_ini) - 12;
                $stock_bio_fin = ($stock_avg_fin * $stock_count_fin) /1000;

                $produccion_simula = ProduccionSimuladas::where('date', '=', $dia_ini)
                                                        ->where('site', '=', $produccion_real->site)
                                                        ->where('unitname', '=', $produccion_real->unitname)
                                                        ->where('groupid', '=', $produccion_real->groupid)
                                                        ->first();

                $consumo= Consumo::where('fecha', '=', $dia_ini)
                                 ->where('granja', '=', $produccion_real->site)
                                 ->where('jaula', '=', $produccion_real->unitname)
                                 ->where('lote', '=', $produccion_real->groupid)
                                 ->first();



                if ($produccion_simula)
                {
                	//echo 'Hay que actualizar';

                    // Primero actualizamos los datos de producción
                	$registro_actualizar = ProduccionSimuladas::find($produccion_simula->id);
                     
                    $stock_avg_fin = $stock_avg_ini * pow((((($registro_actualizar->porcentaje_toma/100) * $SFR)/$FCR)/100+1), 1); 
                    $stock_count_fin = ($stock_count_ini) - 12;
                    $stock_bio_fin = ($stock_avg_fin * $stock_count_fin) /1000;

                    $cantidad_toma_modelo = ($SFR * (($stock_count_ini * $stock_avg_ini)/1000))/100 ;
                    $pienso_recomendado = ((($registro_actualizar->porcentaje_toma/100) * $SFR) * (($stock_count_ini * $stock_avg_ini)/1000))/100 ;
                    $sacos = $pienso_recomendado/25;
                    $pienso_recomendado= number_format(ceil($sacos)*25, 0);

                	$registro_actualizar->stock_count_ini = $stock_count_ini;
                	$registro_actualizar->stock_bio_ini = $stock_bio_ini;
                	$registro_actualizar->stock_avg_ini = $stock_avg_ini;
                	$registro_actualizar->stock_count_fin = $stock_count_fin;
                	$registro_actualizar->stock_avg_fin = $stock_avg_fin;
                	$registro_actualizar->stock_bio_fin = $stock_bio_ini;
                    $registro_actualizar->FCR = $FCR;
                    $registro_actualizar->SFR = $SFR;
                    $registro_actualizar->SGR = $SGR;
                    $registro_actualizar->cantidad_toma_modelo = $cantidad_toma_modelo;
                    $registro_actualizar->cantidad_toma = $pienso_recomendado;
                	$registro_actualizar->save();

                    
                }
                else
                {
                	//echo 'Hay que insertar un valor nuevo';

                    // Primero insertamos los datos de producción
                	$registro_nuevo = new ProduccionSimuladas;
                	$registro_nuevo->date = $dia_ini;
                	$registro_nuevo->site = $produccion_real->site;
                	$registro_nuevo->unitname = $produccion_real->unitname;
                	$registro_nuevo->groupid = $produccion_real->groupid;
                	$registro_nuevo->stock_count_ini = $stock_count_ini;
                	$registro_nuevo->stock_bio_ini = $stock_bio_ini;
                	$registro_nuevo->stock_avg_ini = $stock_avg_ini;
                	$registro_nuevo->stock_count_fin = $stock_count_fin;
                	$registro_nuevo->stock_avg_fin = $stock_avg_fin;
                	$registro_nuevo->stock_bio_fin = $stock_bio_fin;
                    $registro_actualizar->FCR = $FCR;
                    $registro_actualizar->SFR = $SFR;
                    $registro_actualizar->SGR = $SGR;
                    $registro_actualizar->cantidad_toma_modelo = $cantidad_toma_modelo;
                    $registro_actualizar->cantidad_toma = $pienso_recomendado;
                	$registro_nuevo->save();
                    
                    

                }   
                /*
                if ($consumo)
                {
                    // Ahora actualizamos los datos de consumo.
                    $registro_consumo_actualizar = Consumo::find($consumo->id);
                    $registro_consumo_actualizar->cantidad_recomendada = $pienso_recomendado;
                    $registro_consumo_actualizar->cantidad = ($consumo->porcentaje_estrategia/100) * $pienso_recomendado;
                    $registro_consumo_actualizar->save();
                }
                else
                {
                    // Ahora insertamos los datos de consumo
                       // 1. Buscamos el proveedor más usado en la granja.
                    $proveedor_pienso_id = 1;

                    // Creamos un objeto Proveedorpienso
                    $proveedor_pienso = Proveedorpienso::find($proveedor_pienso_id);

                    // Averiguamos el tamaño de pellet de ese proveedor para el peso medio del pez
                    $diametro_pellet = DB::table('tamanio_pellets')->where('proveedor_pienso_id', '=', $proveedor_pienso_id)
                                                                   ->where('pm_min', '<=', $stock_avg_ini)
                                                                   ->where('pm_max', '>=', $stock_avg_ini)->orderBy('updated_at', 'desc')->first();
                    //Buscamos un pienso que sea del proveedor y del pellet señalado
                    $pienso = DB::table('piensos')->where('proveedor_id', '=', $proveedor_pienso_id)->where('diametro_pellet_id', '=', $diametro_pellet->id)->orderBy('updated_at', 'desc')->first();
                    //var_dump($pienso);
                    //var_dump($diametro_pellet);
                       // 2. Buscamos el diámetro de pellets que nos ofrece el proveedor de pienso
                       //    para el peso medio de pez.
                    $registro_nuevo_consumo = new Consumo;
                    $registro_nuevo_consumo->granja = $produccion_real->site;
                    $registro_nuevo_consumo->fecha = $dia_ini;
                    $registro_nuevo_consumo->jaula = $produccion_real->unitname;
                    $registro_nuevo_consumo->lote = $produccion_real->groupid;
                    $registro_nuevo_consumo->proveedor = $proveedor_pienso->nombre;
                    $registro_nuevo_consumo->proveedor_id = $proveedor_pienso_id;
                    $registro_nuevo_consumo->pienso = $pienso->nombre;
                    $registro_nuevo_consumo->pienso_id = $pienso->id;
                    $registro_nuevo_consumo->codigo_pienso = $pienso->codigo;
                    $registro_nuevo_consumo->diametro_pienso = $diametro_pellet->diametro; 
                    $registro_nuevo_consumo->cantidad_recomendada = $pienso_recomendado;
                    $registro_nuevo_consumo->cantidad = $pienso_recomendado;
                    $registro_nuevo_consumo->porcentaje_estrategia = 100;
                    $registro_nuevo_consumo->save();
                }
                */
                //echo 'Stock Ini: ' .  $stock_avg_ini . ' Stock Fin: ' .  $stock_avg_fin;                                  
                /*
			 	$stock_count_ini = $stock_count_fin;
			    $stock_avg_ini   = $stock_avg_fin;
			    $stock_bio_ini   = $stock_bio_fin;
			 }
			//echo $produccion_real->unitname; */
		
                           
	} // end function

    // Función privada que nos devuelve la temperatura para un día concreto según un modelo de 
    // temperatura indicado
	private function temperatura_dia($id_m_temperatura, $fecha)
    {

      // Obtenemos el mes de la fecha introducida.
         $mes = $fecha->format('n');
         //$campo_mes;
         //print_r("fecha" . $fecha);
      // Dependiendo del mes, seleccionamos el campo correspondiente de la tabla m_temperatura

        $ModeloTemperatura = MTemperatura::find($id_m_temperatura);
        $temperatura = 0;
         switch ($mes) {
           case '01':
             $campo_mes = 'enero';
             $temperatura = $ModeloTemperatura->enero;
             break;

            case '02':
             $campo_mes = 'febrero';
             $temperatura = $ModeloTemperatura->febrero;
             break;

            case '03':
             $campo_mes = 'marzo';
             $temperatura = $ModeloTemperatura->marzo;
             break;

            case '04':
             $campo_mes = 'abril';
             $temperatura = $ModeloTemperatura->abril;
             break;

            case '05':
             $campo_mes = 'mayo';
             $temperatura = $ModeloTemperatura->mayo;
             break;

            case '06':
             $campo_mes = 'junio';
             $temperatura = $ModeloTemperatura->junio;
             break;

            case '07':
             $campo_mes = 'julio';
             $temperatura = $ModeloTemperatura->julio;
             break;

            case '08':
             $campo_mes = 'agosto';
             $temperatura = $ModeloTemperatura->agosto;
             break;

            case '09':
             $campo_mes = 'septiembre';
             $temperatura = $ModeloTemperatura->septiembre;
             break;

            case '10':
             $campo_mes = 'octubre';
             $temperatura = $ModeloTemperatura->octubre;
             break;

            case '11':
             $campo_mes = 'noviembre';
             $temperatura = $ModeloTemperatura->noviembre;
             break;

            case '12':
             $campo_mes = 'diciembre';
             $temperatura = $ModeloTemperatura->diciembre;
             break;

           default:
             $campo_mes = 'diciembre';
             $temperatura = $ModeloTemperatura->diciembre;
             break;
         }
     

         return $temperatura;
    }

    private function CalcularSGR($id_m_crecimiento, $peso, $temperatura)
    {
      // Obtenemos los valores de la fórmula de SGR correspondientes al modelo de crecimiento.
      // Fórmula SGR = a_lub [Ln(P/Pmax)]^2 * Seno [seno(T-Tcero)/(Tmax-Tcero)]
      
      $modelo_crecimiento = MCrecimiento::find($id_m_crecimiento);

      
      // Damos valores a las variables de la fórmula.
      
      $_a_lub = $modelo_crecimiento->SGR_a_lub;
      $_peso_max = $modelo_crecimiento->SGR_Peso_max;
      $_t_cero = $modelo_crecimiento->SGR_T_cero;
      $_t_max = $modelo_crecimiento->SGR_T_max;
      $_seno = $modelo_crecimiento->SGR_Seno;

      // Calculamos la fórmula.
      $SGR = $_a_lub * pow((log($peso/$_peso_max)), 2) * sin($_seno * (($temperatura -$_t_cero)/($_t_max - $_t_cero)));

      // Devolvemos el resultado
      
      return $SGR;
    }

    private function CalcularFCR($id_m_crecimiento, $peso, $temperatura, $SGR)
    {
      // Obtenemos los valores de la fórmula de FCR correspondientes al modelo de crecimiento.
      // Fórmula FCR = cte + ((peso ^-a)/SGR)

      $modelo_crecimiento = MCrecimiento::find($id_m_crecimiento);

      // Damos valores a las variables de la fórmula.
      $_cte = $modelo_crecimiento->FCR_cte;
      $_a = $modelo_crecimiento->FCR_a;

      //Calculamos finalmente el valor de FCR
      $FCR = $_cte + (pow($peso, ((-1) * $_a))/$SGR);

      return $FCR;
    }

    private function DiasActualiza($fecha)
    {
        //var_dump($fecha);
        //echo ' algo: ' . $fecha->format('N');
        $num_dia = (7 - $fecha->format('N')) + 70 ;
        //echo 'Num dias:' . $num_dia;
        return $num_dia;
    }

   // Función pública que actualiza los datos de producción simulada en un intervalo de tiempo.
    // 
    public function actualizarSimulacionIntervalo($granja, $jaula, $lote, $stock_count_ini, $stock_avg_ini, $fecha_ini, $fecha_fin)
    {
       //Inicializamos las variables locales
        $fecha_inicial = $fecha_ini;
        $fecha_final   = $fecha_fin;
       //echo ' Granja: ' . $granja;
       //echo ' Jaula: ' . $jaula;
       //echo ' lote: ' . $lote;
       //echo ' Stock Count ini: ' . $stock_count_ini;
       //echo ' Stock Avg ini: ' . $stock_avg_ini;
       //echo ' Propuesta: ' . $propuesta_alimentacion;
       //echo ' fecha inicial: ' . $fecha_ini;
       //echo ' fecha final: ' . $fecha_fin;
       //var_dump($fecha_inicial);
       //var_dump($fecha_fin);
       $salir = FALSE;
       //Tenemos que recorrer todos los días desde la fecha ini hasta la fecha fin
       
        while (($fecha_inicial <= $fecha_final) and !$salir) 
        {
            // Si el $stock_count_ini significa que el lote se ha cerrado el día anterior, ya que la simulación
            // empieza a partir de datos reales
            if ($stock_count_ini <= 0)
            {
               // Debemos eliminar los datos de la tabla simulación a partir de este momento para dicho lote en esta jaula
                $datos_produccion_simulado = ProduccionSimuladas::where('unitname', '=', $jaula)
                                                                ->where('groupid', '=', $lote)
                                                                ->where('date', '>=', $fecha_inicial)
                                                                ->get();
                foreach ($datos_produccion_simulado as $dato_produccion_simulado)
                {
                    $dato_produccion_simulado->delete();
                }
               // Debemos eliminar los datos de la tabla de consumos a partir de este momento para dicho lote en esta jaula

                $datos_consumo_simulado  = Consumo::where('jaula', '=', $jaula)
                                                  ->where('lote', '=', $lote)
                                                  ->where('fecha', '>=', $fecha_inicial)
                                                  ->get();
                foreach ($datos_consumo_simulado as $dato_consumo_simulado)
                {
                    $dato_consumo_simulado->delete();
                }                                  
               // Salimos del bucle
               $salir = TRUE;
            }
            else
            {
               //echo ' entramos en el bucle: ' . $i;
            
               // Obtenemos la temperatura que le corresponde al día
               $temperatura = $this->temperatura_dia(1, $fecha_inicial);
               //echo ' Temperatura: ' .$temperatura;
               // Calculamos el SGR teórico en base a la temperatura y al peso medio inicial.
               $SGR = $this->CalcularSGR(1,$stock_avg_ini,$temperatura );
               //echo ' SGR: ' .$SGR;    
               // Calculamos el FCR teórico en base a la temperatura y al peso medio inicial.
               $FCR = $this->CalcularFCR(1, $stock_avg_ini, $temperatura, $SGR);
               //echo ' FCR: ' .$FCR;
               // Calculamos el consumo de pienso recomendado
               $SFR = $SGR * $FCR;
               //echo ' SFR: ' .$SFR;
               //Calculamos la cantidad de pienso que recomienda el modelo
               $pienso_recomendado_modelo = ($SFR * (($stock_count_ini * $stock_avg_ini)/1000))/100 ;
               //echo ' pienso_recomendado_modelo: ' .$pienso_recomendado_modelo;
            
               //Calculamos la cantidad de pienso en base al % de la propuesta que nos introducen en el sistema.
                 // Bucamos que % le corresponde al lote en dicha jaula en esta fecha
               $propuesta_alimentacion = $this->jaula_lote_estrategia($jaula, $lote, $fecha_inicial);
               //echo $propuesta_alimentacion;
               //$propuesta_alimentacion = 2;
               $pienso_propuesta = round((($pienso_recomendado_modelo * $propuesta_alimentacion) / 100), 0, PHP_ROUND_HALF_DOWN);
               $pienso_propuesta = number_format(ceil(($pienso_propuesta/25))*25, 0, '.', '');
               //echo ' pienso_propuesta: ' .$pienso_propuesta;
            
            
               //Calculamos el SGR real según los datos de la toma
               //var_dump($fecha_inicial);
               //echo ' Stock ini count: ' .  $stock_count_ini . ' stock avg ini ' . $stock_avg_ini;
               $SGR_propuesta = (($pienso_propuesta * 100) / (($stock_count_ini * $stock_avg_ini)/1000)) / $FCR ;
               //echo ' SGR_propuesta: ' .$SGR_propuesta;
               //Calculamos el nuevo peso medio basándonos en el SGR obtenido anteriormente
               $stock_avg_fin = $stock_avg_ini * pow(($SGR_propuesta/100+1), 1); 
               //echo ' stock_avg_fin: ' .$stock_avg_fin; 
            
            
            
               //Calculamos la mortalidad que le corresponde al lote. De momento sólo restamos 12 unidades cada día
               $mortality_count = 0;
               $mortality_avg =  $stock_avg_ini;
               $mortality_bio = ($mortality_count * $mortality_avg) / 1000;

               //echo 'Jaula: ' . $jaula . ' Lote: ' . $lote;
               // Buscamos si existe registro de esa jaula y lote en la fecha que estamos

               $datos_produccion_simulado = ProduccionSimuladas::where('unitname', '=', $jaula)
                                                            ->where('date', '=', $fecha_inicial)
                                                            ->get();
            
               // Si existen datos, actualizamos

               if (count($datos_produccion_simulado)>0)
               {
                 foreach ($datos_produccion_simulado as $dato_produccion_simulado)
                 {
                    if ($dato_produccion_simulado->groupid <> $lote)
                  {
                     // Debemos eliminar los datos de esa jaula y lote desde hoy en adelante.
                    $datos_a_eliminar = DB::select('Delete from produccion_simulado 
                                                          where date >=? 
                                                            and unitname = ? 
                                                            and groupid = ?', array($fecha_inicial, $dato_produccion_simulado->unitname, $dato_produccion_simulado->groupid));
                    // Debemos eliminar los datos de consumo de esa jaula y lote desde hoy en adelante.

                    $datos_a_eliminar = DB::select('Delete from consumos
                                                          where fecha >=?
                                                          and jaula = ?
                                                          and lote = ?', array($fecha_inicial, $dato_produccion_simulado->unitname, $dato_produccion_simulado->groupid));
                    $salir = TRUE;
                  }
                  else
                  {
                     //echo ' Actualizamos datos';
                    //echo $datos_produccion_simulado;
                    // Recuperamos si existen datos de pescas
                    $harvested_count = $dato_produccion_simulado->harvested_count;
                    $harvested_avg   = $stock_avg_ini;
                    $harvested_bio   = ($harvested_count * $harvested_avg) / 1000;

                    //Calculamos la cantidad de pienso en base al % de la propuesta que nos introducen en el sistema.
                    // Comprobamos si la jaula está en ayuno
                    if ($dato_produccion_simulado->ayuno == 1)  //Está en ayuno
                    {
                       $propuesta_alimentacion = 0;
                       // Debemos eliminar el registro de consumo, si lo hubiera.
                       $datos_consumo_simulado_a_eliminar  = Consumo::where('jaula', '=', $jaula)
                                                                    ->where('lote', '=', $lote)
                                                                    ->where('fecha', '=', $fecha_inicial)
                                                                    ->delete();

                    }
                    $pienso_propuesta = round((($pienso_recomendado_modelo * $propuesta_alimentacion) / 100), 0, PHP_ROUND_HALF_DOWN);
                    $pienso_propuesta = number_format(ceil(($pienso_propuesta/25))*25, 0, '.', '');
                    //echo ' pienso_propuesta: ' .$pienso_propuesta;
            
            
                    //Calculamos el SGR real según los datos de la toma
                    $SGR_propuesta = (($pienso_propuesta * 100) / ((($stock_count_ini-$harvested_count) * $stock_avg_ini)/1000)) / $FCR ;
                    //echo ' SGR_propuesta: ' .$SGR_propuesta;
                    //Calculamos el nuevo peso medio basándonos en el SGR obtenido anteriormente
                    $stock_avg_fin = $stock_avg_ini * pow(($SGR_propuesta/100+1), 1); 


                    //echo $datos_produccion_simulado->harvested_count;

                    //Actualizamos los datos
                    $dato_produccion_simulado->stock_count_ini      = $stock_count_ini;
                    $dato_produccion_simulado->stock_avg_ini        = $stock_avg_ini;
                    $dato_produccion_simulado->stock_bio_ini        = ($stock_count_ini * $stock_avg_ini) /1000;

                    $dato_produccion_simulado->mortality_count      = $mortality_count;
                    $dato_produccion_simulado->mortality_avg        = $mortality_avg;
                    $dato_produccion_simulado->mortality_bio        = $mortality_bio;
              
                    $dato_produccion_simulado->harvested_avg        = $harvested_avg;
                    $dato_produccion_simulado->harvested_bio        = $harvested_bio; 
                    $stock_count_fin                                 = $stock_count_ini + $dato_produccion_simulado->input_count - $mortality_count - $harvested_count;
                    if ($stock_count_fin <= 0)
                    {
                      $stock_count_fin = 0;
                      
                    }
                    $dato_produccion_simulado->stock_count_fin      = $stock_count_fin;
                    $dato_produccion_simulado->stock_avg_fin        = $stock_avg_fin;
                    $dato_produccion_simulado->stock_bio_fin        = (($stock_count_ini + $dato_produccion_simulado->input_count - $mortality_count - $harvested_count ) * $stock_avg_fin) / 1000;

                    $dato_produccion_simulado->FCR                  = $FCR;
                    $dato_produccion_simulado->SGR                  = $SGR;
                    $dato_produccion_simulado->SFR                  = $SFR;

                    $dato_produccion_simulado->porcentaje_toma     = $propuesta_alimentacion;
                    $dato_produccion_simulado->cantidad_toma_modelo = $pienso_recomendado_modelo;
                    $dato_produccion_simulado->cantidad_toma        = $pienso_propuesta;

                    // Guardamos
                    $dato_produccion_simulado->save();
                    if ($stock_count_fin <=0)
                    {
                        $salir = TRUE;
                    }
                  }
                 }

                 // Comprobamos que se trata del mismo lote
                 

                
               }
               else
               {  
                   //echo 'Creamos nuevos datos';

            
                   //No existen datos, por lo que debemos insertar un nuevo registro.
                 $harvested_count = 0;
                 $harvested_avg   = $stock_avg_ini;
                 $harvested_bio   = ($harvested_count * $harvested_avg) / 1000;

                 //Calculamos la cantidad de pienso en base al % de la propuesta que nos introducen en el sistema.
                 $pienso_propuesta = round((($pienso_recomendado_modelo * 100) / 100), 0, PHP_ROUND_HALF_DOWN);
                 $pienso_propuesta = number_format(ceil(($pienso_propuesta/25))*25, 0, '.', '');
                 //echo ' pienso_propuesta: ' .$pienso_propuesta;
            
            
                 //Calculamos el SGR real según los datos de la toma
                 $SGR_propuesta = (($pienso_propuesta * 100) / (($stock_count_ini * $stock_avg_ini)/1000)) / $FCR ;
                 //echo ' SGR_propuesta: ' .$SGR_propuesta;
                 //Calculamos el nuevo peso medio basándonos en el SGR obtenido anteriormente
                 $stock_avg_fin = $stock_avg_ini * pow(($SGR_propuesta/100+1), 1); 

                 $registro_nuevo = new ProduccionSimuladas;
                 $registro_nuevo->date = $fecha_inicial;
                 $registro_nuevo->site = $granja;
                 $registro_nuevo->unitname = $jaula;
                 $registro_nuevo->groupid = $lote;

                 $registro_nuevo->stock_count_ini      = $stock_count_ini;
                 $registro_nuevo->stock_avg_ini        = $stock_avg_ini;
                 $registro_nuevo->stock_bio_ini        = ($stock_count_ini * $stock_avg_ini)/1000;

                 $registro_nuevo->mortality_count      = $mortality_count;
                 $registro_nuevo->mortality_avg        = $mortality_avg;
                 $registro_nuevo->mortality_bio        = $mortality_bio;

                 $registro_nuevo->harvested_count      = $harvested_count;
                 $registro_nuevo->harvested_avg        = $harvested_avg;
                 $registro_nuevo->harvested_bio        = $harvested_bio;

                 $stock_count_fin  = $stock_count_ini - $mortality_count - $harvested_count;
                 if ($stock_count_fin <= 0)
                  {
                   $stock_count_fin = 0;
                   $salir = TRUE;
                  }

                 $registro_nuevo->stock_count_fin      = $stock_count_fin;
                 $registro_nuevo->stock_avg_fin        = $stock_avg_fin;
                 $registro_nuevo->stock_bio_fin        = (($stock_count_ini  - $mortality_count - $harvested_count) * $stock_avg_fin) / 1000;

                 $registro_nuevo->FCR                  = $FCR;
                 $registro_nuevo->SGR                  = $SGR;
                 $registro_nuevo->SFR                  = $SFR;

                 $registro_nuevo->porcentaje_toma      = $propuesta_alimentacion;
                 $registro_nuevo->cantidad_toma_modelo = $pienso_recomendado_modelo;
                 $registro_nuevo->cantidad_toma        = $pienso_propuesta;

                 // Guardamos
                 $registro_nuevo->save();
               }
            
               $stock_count_ini = $stock_count_fin;
               $stock_avg_ini   = $stock_avg_fin; 
               $fecha_inicial->modify('+1 day');
            }



            

        } // while    */
         return true;
    }

    private function jaula_lote_estrategia($jaula, $lote, $fecha)
    {
        // Averiguamos los id de la jaula y del lote
        $objeto_jaula = Jaula::where('nombre', '=', $jaula)->first();
        $objeto_lote  = Lote::where('nombre', '=', $lote)->first();

        // Obtenemos el porcentaje que le corresponde
        $jaula_lote_estrategia_objeto = JaulaLoteEstrategia::where('jaula_id', '=', $objeto_jaula->id)
                                                      ->where('lote_id', '=', $objeto_lote->id)
                                                      ->where('fecha_inicio', '<=', $fecha)
                                                      ->orderby('created_at', 'desc')
                                                      ->first();

        if(count($jaula_lote_estrategia_objeto) > 0)
        {
            $estrategia = $jaula_lote_estrategia_objeto->porcentaje;
        }
        else
        {
            $estrategia = 100;
        }
        
        return $estrategia;
    }

    
}

 ?>