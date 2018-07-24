<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
   
   $scaffold = "scaffold_113";
   $category = "Gene";
	//$json = file_get_contents('http://clogmiawiki/wiki/Special:Ask/-5B-5BCategory:'.$category.'-5D-5D-20-5B-5BLocation::'.$scaffold.'-5D-5D/-3FStart/-3FEnd/mainlabel%3D/limit%3D500/prettyprint%3Dtrue/format%3Djson');
		//print_r(json_decode($json));
	//echo count($json);
	//return response()->json($obj);
	$ch = curl_init('http://clogmiawiki/wiki/Special:Ask/-5B-5BCategory:'.$category.'-5D-5D-20-5B-5BLocation::'.$scaffold.'-5D-5D/-3FStart/-3FEnd/-3FStrand/-3FName/mainlabel%3D/limit%3D500/prettyprint%3Dtrue/format%3Djson'); // add your url which contains json file
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $content = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($content, true);
    //print_R($json);
    //var_dump($json["results"]);
	$results = $json["results"];
	$data = array("results" => $results);
	//$data = array_push($data, $results);
	//var_dump($results["Gene:scaffold 113.g"]);
	$date = array_values($results)[2];
	//var_dump($results);

	$result = array();
	echo count($results);
	for($i=0;$i<count($results);$i++)
	{
		$date = array_values($results)[$i];
		//echo "12";	
		echo "</br></br>";
		var_dump($date);

	}
	//var_dump($result);



});
