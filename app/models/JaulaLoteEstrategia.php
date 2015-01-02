<?php 
	
	class JaulaLoteEstrategia extends Eloquent{

		protected $table = 'jaula_lote_estrategia';
		protected $fillable = array('jaula_id', 'lote_id', 'porcentaje', 'fecha_inicio');

		public function jaula()
		{
    	  return $this->belongsTo('Jaula');
        }

    	public function lote()
    	{
    	 return $this->belongsTo('Lote');
        }

	}

 ?>