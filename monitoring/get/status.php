<?php
include("scripts/StatsException.php");
include("scripts/Stats.php");
include("scripts/Server.php");
include("../config.php");


$output = array();
$now = "";
$max = "";

foreach ($confmon as $config){
	try
	{
		$MOTD = $config['name'];
		$out = (array)\Minecraft\Stats::retrieve(new \Minecraft\Server($config[ip]));
		array_push($output, array("motd" => $MOTD, "offline" => 0, "cur_p" => $out['online_players'], "max_p" => $out['max_players']));

		$now = $now + $out['online_players'];
		$max = $max + $out['max_players'];

		if(!file_exists('cache')){
		file_put_contents("cache", json_encode(array('absolute' => 0, 'day' => array('time' => 0, 'slots' => 0))));
		}

		$abs = json_decode(file_get_contents("cache"), true)['absolute'];
		if($now > $abs){
			
			file_put_contents("cache", json_encode(array('absolute' => $now)));
		}
		$day = json_decode(file_get_contents("cache"), true)['day']['slots'];
		if($now > $day){
			$new = json_decode(file_get_contents("cache"), true);
			if($new['day']['time'] == 0){
				$new['day']['time'] = time();
			}
			$new['day']['slots'] = $now;
			file_put_contents('cache', json_encode($new));
		}
		
		$t = json_decode(file_get_contents("cache"), true);
			$t = time()-$t['day']['time'];
			  if($t>=86400) {
			  $new = json_decode(file_get_contents("cache"), true);
			   $new['day']['time'] = time();
			   $new['day']['slots'] = 0;
			   file_put_contents('cache', json_encode($new));
			  }

	}catch(Minecraft\StatsException $e){
		array_push($output, array("motd" => $MOTD, "offline" => 1));
	}
}
//print_r($out);
if(isset($_GET['all']) && $_GET['all']==1){
$abs = json_decode(file_get_contents("cache"), true)['absolute'];
$day = json_decode(file_get_contents("cache"), true)['day']['slots'];
print_r(json_encode(array('cur' => $now, 'max' => $max, 'absolute' => $abs, 'day' => $day)));
}else{
print_r(json_encode($output));
}
?>