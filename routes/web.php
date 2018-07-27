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
	$array3 = array();
	$arrayPrueba = array();
	$arrayId=array();
	$arrayIdx=array();
	$exonsName=array();
	$arrayExon=array();
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

		$chj = curl_init('http://clogmiawiki/w/api.php?action=smwparent&format=json&retrieve=tree&title=Gene:'.$id.'@Genome:clogmia6&type=Contig,Exon');
		curl_setopt($chj, CURLOPT_RETURNTRANSFER, 1);
	    $contentj = curl_exec($chj);
	    curl_close($chj);
	    $jsonj = json_decode($contentj, true);
	   	$resultsj = $jsonj["smwparent"];
		array_push($array3,$resultsj);

		$dat = array('end' => $end, 'name' => $id, 'start' => $start, 'strand' => $strand, 'type' => $category, 'uniqueID' => $id);
		array_push($res, $dat);

		$vowel = "_";
		$refseq = str_replace($vowel, " ", $refseq);
		$idx = str_replace($vowel, " ", $id);
		array_push($arrayId, $id);
		array_push($arrayIdx, $idx);
	}

	for($j=0;$j<count($array3);$j++)
	{
		$datx = array_values($array3)[$j]["content"]["Contig:".$refseq."@Genome:clogmia6"]["link"]["Ref_id"]['Gene:'.$arrayId[$j].'@Genome:clogmia6']['link']['Ref_id']['Transcript:'.$arrayIdx[$j].'.t1@Genome:clogmia6'];
		$startj = $datx['printouts']["Start"];
		$endj = $datx['printouts']["End"];
		$strandj = $datx['printouts']["Strand"];
		$idj = $datx['printouts']["Id"];
		$typex = $datx['type']["Categories"][0];
		$vowels = "@Genome:clogmia6";
		$idj = str_replace($vowels, "", $idj);
		$exons = $datx['link']['Ref_id'];
		array_push($exonsName, $exons);
		$exonn=$exonsName[$j];
		//var_dump($exonn);
		$exonId=array_keys($exonn);
		//print_r($exonId);
		for($x=0;$x<count($exonId);$x++)
		{
			$exon2=$exonn[$exonId[$x]];
			//print_r($exon2);
			$startExon = $exon2['printouts']["Start"];
			$endExon = $exon2['printouts']["End"];
			$strandExon = $exon2['printouts']["Strand"];
			$typeExon = $exon2['type']["Categories"][0];
			$subfeatures2 = array(
 			    'end' => $endExon,
       			'start' => $startExon,
       			'strand' =>$strandExon,
      	  		'type' => $typeExon
			);
			array_push($arrayExon, $subfeatures2);
		}
		$subfeatures = array(array(
 			    'end' => $endj,
       			'name' => $idj,
       			'start' => $startj,
       			'strand' =>$strandj,
       			'subfeatures' => $arrayExon,
      	  		'type' => 'mRNA',
        		'uniqueID' => $idj),
		);
		$data = $res[$j];
		$data['subfeatures']=$subfeatures;
		array_push($arrayPrueba, $data);
		$arrayExon=array();
	}

	if ( $request->has('sequence') ) {

		$start = intval( $request->input('start') );
		$end = intval( $request->input('end') );
		$data = array('features' => array('seq' => '','start' => $start, 'end' => $end) );
	}
	else
	{
		$data = array('features' => $arrayPrueba);
	}
	return response()->json( $data);

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


	json_encode($resp);
	return response()->json( $resp );
});
