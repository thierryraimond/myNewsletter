<?php
class Browser {
	
	private $browser = array(
		"IE" => array(
				"name" => "IE",
				"version" => 11.0
		),
		"Chrome" => array(
				"name" => "Chrome",
				"version" => 52.0
		),
		"Firefox" => array(
				"name" => "Firefox",
				"version" => 48.0
		),
		"Edge" => array(
				"name" => "Edge",
				"version" => 13.0
		),
		"Opera" => array(
				"name" => "Opera",
				"version" => 39.0
		)
	);
	// echo $browser["IE"]["name"] // 'IE'
	
	public function affiche() {	
		//$obj = json_decode($this->json);
		// var_dump(json_decode($this->json, true));
		//return $obj->{'name'};
		$res = "<table class=\"table table-bordered text-center\" ><tr>";
		foreach ($this->browser as $navigateur) {
			//$res .= $navigateur.": ";
// 			$res .= $navigateur['name']." => ".$navigateur['version']."<br/>";
// 			foreach ($navigateur as $key => $value) {
// 				//$res .= "\$navigateur[$key] => $value ; ";
// 				//$res .= "$value ";
// 			}

			$res .= "<th class=\"text-center\">
					<abbr title=\"".$navigateur['name']."\">
				<img src=\"images/".$navigateur['name']."_trans.png\" alt=\"Chrome\" width=\"25px\" height=\"25px\">		
					</abbr></th>";
		}
		$res .= "</tr><tr>";
		foreach ($this->browser as $navigateur) {
			$res .=	"<td><abbr title=\"Version ".$navigateur['version']."\">".$navigateur['version']."</abbr></td>";
		}
		$res .= "</tr></table>";
		return $res;
	}
	
	public function get_browser() {
		return $this->browser;
	}
	
}