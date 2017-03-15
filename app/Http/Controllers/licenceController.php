<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class licenceController extends Controller
{



    private function decode($coddedSTR){
		
		$orderArray = array();
        $KeyAndPcArray = array();
        $ret = array();
		$ret["key"] = "";
		$ret["pc"] = "";
		$ret["order"] = array();
        $str3Array = str_split($coddedSTR, 3);

		$orginazedArray = array();
		for ($i = 0; $i < 64; $i++)
		{
			array_push($orginazedArray, "");
		}

        foreach ($str3Array as $str3) {
            $str = str_split($str3, 2);
            array_push($orderArray,$str[0]);
            array_push($KeyAndPcArray,$str[1]);
        }
		$ret["order"] = $orderArray;
		for ($i = 0; $i < 64; $i++)
		{
			$orginazedArray[intval($orderArray[$i])] = $KeyAndPcArray[$i];

		}
		
		for ($i = 0; $i < 32; $i++)
		{
			$ret["key"] = $ret["key"] . $orginazedArray[$i];
		}

		for ($i = 32; $i < 64; $i++)
		{
			$ret["pc"] = $ret["pc"] . $orginazedArray[$i];
		}
        return $ret;

    }

    public function licenceStatus($coddedSTR) {

		$decoded = $this->decode($coddedSTR);
		$key = $decoded["key"];
		$pc =  $decoded["pc"];


		$order = $decoded["order"];

		$keys = DB::table('keys')->where('key', $key)->first();
		if ($keys === null) {return $this->CodeThis("H", $order);}

		$pcChanges = intval($keys->pcChanges) + 1;
		$is_active = $keys->is_active;

		if ($keys->pc <> $pc){
			DB::table('keys')
				->where('key', $key)
				->update(['pcChanges' => $pcChanges]);
			DB::table('keys')
				->where('key', $key)
				->update(['pc' => $pc]);
		}


		if ($pcChanges > 40) {return $this->CodeThis("B", $order);}
		if ($is_active <> true) {return $this->CodeThis("D", $order);}

		
		return $this->CodeThis("A", $order);

    }

	private function CodeThis ($string, $order) {
		$pool = array_merge(range(0,9),range('A', 'H'));
		$ReturnString = "";
		$Read = rand(0, 9);

		foreach ($order as $int) {
			if ($int == 20){
				$ReturnString .= $Read;
			}
			else if ($int == intval($Read)){
				$ReturnString .= $string;
			}else{
				$ReturnString .= $pool[mt_rand(0, count($pool) - 1)];
			}
		}
		return $ReturnString;

	}




}
