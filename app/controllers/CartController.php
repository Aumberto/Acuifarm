<?php 

	Class CartController extends BaseController {

		public function postAddToCart()
		{
			/*
			$rules = array(
				'amount' => 'required|numeric',
				'book' => 'required|numeric|exists:books,id'
				);
			$validator = Validator::make(Input::all(), $rules);

			if ($validator->fails())
			{
				return Redirect::route('index')->with('error', 'El libro no se pudo añadir al carro!!');
			}
            */
			$member_id = Auth::user()->id;
			$pienso_id = Input::get('pienso');
			$amount = Input::get('amount');

			$pienso = Pienso::find($pienso_id );
			$total = $amount*1;
            
            /*
			$count = Cart::where('book_id', '=', $book_id)->where('member_id', '=', $member_id)->count();
			if ($count)
			{
				return Redirect::route('index')->with('error', 'El libro ya estaba añadido en el carro!!');
			}
            */
			Cart::create(
				array(
					'member_id' => $member_id,
					'pienso_id' => $pienso_id,
					'amount' => $amount,
					'total' => $total
					));

			return Redirect::to('pienso');
		}

		public function getIndex(){
			$member_id = Auth::user()->id;
			$cart_piensos=Cart::with('piensos')->where('member_id', '=', $member_id)->get();
			$cart_total=Cart::with('piensos')->where('member_id', '=', $member_id)->sum('total');

			if(!$cart_piensos)
			{
				return Redirect::route('index')->with('error', 'Su carro está vacío');
			}

			return View::make('cart')
			         ->with('cart_piensos', $cart_piensos)
			         ->with('cart_total', $cart_total);
		}

		public function getDelete($id)
		{
			$cart = Cart::find($id)->delete();

			return Redirect::route('cart');
		}

	}

 ?>