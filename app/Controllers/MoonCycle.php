<?php

namespace App\Controllers;
use App\Models\MoonCycleModel; 
class MoonCycle extends BaseController
{
 
	public function index(): string{
		// $data['title'] = ucfirst('Home'); 
		// return view('templates/header', $data)
			// . view('pages/home')
			// . view('templates/footer');
	}
	
	public function logcycles(){
		$moon_data = file_get_contents('https://aa.usno.navy.mil/api/moon/phases/date?date='. date('Y-m-d',strtotime('now')) .'&nump=48') ;
		$moon_data = json_decode($moon_data);
		if(!empty($moon_data)){
			if(isset($moon_data->phasedata)){
				$cycle = model(MoonCycleModel::Class);
				foreach($moon_data->phasedata as $data){
					if(!$cycle->exsits($data->year .'-'. $data->month .'-'. $data->day)){
					   $cycle->save([
							'cycle'  => $data->phase,
							'datetime' => $data->year .'-'. $data->month .'-'. $data->day .' '. $data->time,
						]);
					}
				}
			}
		}
	}
}
 
