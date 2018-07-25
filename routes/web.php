<?php

use Illuminate\Http\Response;


function make_seq($length)
{
	$nucl = [ "A", "C", "T", "G" ];
	$data = "";

	for( $i=0; $i<$length; $i++ ) {

		$randInt = rand( 0, 3 );
		$data = $data.$nucl[$randInt];
	}

	return $data;
}

function namesGlobal()
{
	$ch = curl_init('http://clogmiawiki/wiki/Special:Ask/-5B-5BCategory:Contig-5D-5D/-3FId/-3FSize/mainlabel%3D/limit%3D100/prettyprint%3Dtrue/format%3Djson');
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $content = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($content, true);
	$results = $json["results"];
	$CHROM_NAMES = array();
	for($i=0;$i<count($results);$i++)
	{
		$date = array_values($results)[$i];
		$id = array_values($date)[0]["Id"][0];
		$vowels = "@Genome:clogmia6";
		$id = str_replace($vowels, "", $id);
		$data = array($id);
		array_push($CHROM_NAMES, $id);
	}
	return $CHROM_NAMES;
}

function seqsGlobal()
{
	$seqs = array();

	$CHROM_NAMES = namesGlobal();
	foreach ($CHROM_NAMES as $key) 
	{

		$seqs[$key]=make_seq(rand(1000, 3000));
	}
	return $seqs;
}

function featuresGlobal()
{
	$features = array();
	$CHROM_NAMES = namesGlobal();
	foreach ($CHROM_NAMES as $key) {
		//$data=make_feat($key);
		//$features[$key] = $data;
	}
	return $features;
}

$router->get('/s', function (\Illuminate\Http\Request $request) use ($router) {

});

$router->get('/features/{refseq}', function ($refseq, \Illuminate\Http\Request $request) use ($router) {

   $category = "gene";

	$ch = curl_init('http://clogmiawiki/wiki/Special:Ask/-5B-5BCategory:Gene-5D-5D-20-5B-5BLocation::'.$refseq.'-5D-5D/-3FStart/-3FEnd/-3FStrand/-3FName/-3FCategories/-3FId/mainlabel%3D/limit%3D100/prettyprint%3Dtrue/format%3Djson');

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $content = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($content, true);
	$results = $json["results"];
	$res = array();
	for($i=0;$i<count($results);$i++)
	{
		$date = array_values($results)[$i];
		$start = array_values($date)[0]["Start"][0];
		$end = array_values($date)[0]["End"][0];
		$strand = array_values($date)[0]["Strand"][0];
		$name = array_values($date)[0]["Name"][0];
		$id = array_values($date)[0]["Id"][0];
		$vowels = "@Genome:clogmia6";
		$id = str_replace($vowels, "", $id);
		$dat = array('end' => $end, 'name' => $id, 'start' => $start, 'strand' => $strand, 'type' => $category, 'uniqueID' => $id, 'subfeatures' => array( array (
		        'type' => 'mRNA',
		        'uniqueID' => $id.'.t2',
		        'start' => $start,
		        'end' => $end,
		        'strand' => $strand
		    )
		    )
		);

		array_push($res, $dat);
	}

	if ( $request->has('sequence') ) {

		$start = intval( $request->input('start') );
		$end = intval( $request->input('end') );
		$data = array('features' => array('seq' => '','start' => $start, 'end' => $end) );
	}
	else
	{
		$data = array('features' => $res);
	}
	return response()->json( $data );
	//var_dump($res);
});

$router->get('/stats/global', function (\Illuminate\Http\Request $request) use ($router) {

	$data = array('featureDensity' => 0.02);

	return response()->json( $data );
});

$router->get('/refSeqs.json', function (\Illuminate\Http\Request $request) use ($router) 
{
	$seqs = seqsGlobal();
	$resp = array();

	$ch = curl_init('http://clogmiawiki/wiki/Special:Ask/-5B-5BCategory:Contig-5D-5D/-3FId/-3FSize/mainlabel%3D/limit%3D100/prettyprint%3Dtrue/format%3Djson');
 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $content = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($content, true);
	$results = $json["results"];
	$CHROM_NAMES = array();
	for($i=0;$i<count($results);$i++)
	{
		$date = array_values($results)[$i];
		$size = array_values($date)[0]["Size"][0];
		$key = array_values($date)[0]["Id"][0];
		$vowels = "@Genome:clogmia6";
		$key = str_replace($vowels, "", $key);
		$data = array('length' => $size, 'name' => $key, 'start' => 0, 'end' => $size, 'seqChunkSize' => 20000);
		array_push($resp, $data);
	}
	return response()->json($resp);
});

$router->get('/names', function (\Illuminate\Http\Request $request) use ($router)
{
	$features = featuresGlobal();
	$CHROM_NAMES = namesGlobal();
	$resp = array();

	/*if ( $request->has('equals') ) {

		foreach ( $CHROM_NAMES as $key ) {

			foreach ( $features[$key] as $f ) {

				if( $f['name'] === $request->input('equals') ){

					$resp=array_push($resp,feat2searchLoc($key, $f));
				}
			}
		}
	}
	elseif ( $request->has('startswith') ) {

		foreach ( $CHROM_NAMES as $key ) {

			foreach ( $features[$key] as $f ) {

				if ($request->has('equals')){

					array_push($data,feat2searchLoc($key, $f));
				}
			}
		}
	}*/
	json_encode($resp);
	return response()->json( $resp );
});
