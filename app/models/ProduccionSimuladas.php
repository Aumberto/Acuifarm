<?php 

 class ProduccionSimuladas extends Eloquent
 {
 	protected $table = 'produccion_simulado';
    protected $fillable = array('date', 'site', 'unitname', 'groupid', 'stock_count_ini', 'stock_avg_ini', 'stock_bio_ini', 'input_count', 'input_avg', 'input_bio', 
    	                        'mortality_count', 'mortality_avg', 'mortality_bio', 'harvested_count', 'harvested_avg', 'harvested_bio', 
    	                        'culling_count', 'culling_avg', 'culling_bio', 'deviation_count', 'deviation_avg', 'deviation_bio', 
    	                        'stock_count_fin', 'stock_avg_fin', 'stock_bio_fin', 'FCR', 'SGR', 'SFR', 'porcentaje_toma', 'cantidad_toma_modelo', 'cantidad_toma', 'ayuno');
 }

 ?>