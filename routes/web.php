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
		$data=make_feat($key);
		$features[$key] = $data;
	}
	return $features;
}

$router->get('/', function (\Illuminate\Http\Request $request) use ($router) {
	echo "Clogmia Json - Jbrowse";
});


$router->get('/features/{refseq}', function ($refseq, \Illuminate\Http\Request $request) use ($router) {

$symbol = "_";
	$seq = str_replace($symbol, "-5F", $refseq);

	$ch = curl_init('http://wikimachine/wiki/Special:Ask/format%3Djson/limit%3D500/link%3Dall/headers%3Dshow/searchlabel%3DJSON/class%3Dsortable-20wikitable-20smwtable/offset%3D/-5B-5BItem:%2B-5D-5D-20-5B-5BPlain-20ref-20id::'.$seq.'-5D-5D/-3FStart/-3FEnd/-3FType/-3FPlain-20id/-3FStrand/-3FId/mainlabel%3D/prettyprint%3Dtrue/unescape%3Dtrue');

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
	$arraySubfeature=array();
	$exonId=array();
	for($i=0;$i<count($results);$i++)
	{
		$date = array_values($results)[$i];
		$start = array_values($date)[0]["Start"][0];
		$end = array_values($date)[0]["End"][0];
		$strand = array_values($date)[0]["Strand"][0];
		$name = array_values($date)[0]["Plain id"][0];
		$type = array_values($date)[0]["Type"][0];
		$id = array_values($date)[0]["Id"][0];
		$vowels = "@Genome:clogmia6";
		$id = str_replace($vowels, "", $id);
		$id2 = str_replace($symbol, "-5F", $id);

		$chj = curl_init('http://wikimachine/wiki/Special:Ask/format%3Djson/limit%3D500/link%3Dall/headers%3Dshow/searchlabel%3DJSON/class%3Dsortable-20wikitable-20smwtable/offset%3D/-5B-5BItem:%2B-5D-5D-20-5B-5BPlain-20ref-20id::'.$id2.'-5D-5D/-3FStart/-3FEnd/-3FType/-3FPlain-20id/-3FStrand/mainlabel%3D/prettyprint%3Dtrue/unescape%3Dtrue');
		curl_setopt($chj, CURLOPT_RETURNTRANSFER, 1);
	    $contentj = curl_exec($chj);
	    curl_close($chj);
	    $jsonj = json_decode($contentj, true);
	   	$resultsj = $jsonj["results"];
		array_push($array3,$resultsj);

		$dat = array('end' => $end, 'name' => $id, 'start' => $start, 'strand' => $strand, /*'subfeatures' => array('NoInfo')*/ 'type' => $type, 'uniqueID' => $id);
		array_push($res, $dat); //Result1
		$vowels = "_";
		$idd = str_replace($vowels, " ", $id);
		array_push($arrayId, $idd);
	}

	for($j=0;$j<count($array3);$j++)
	{
		$date = array_values($array3)[$j]["Item:Annotation:".$arrayId[$j].".t1@Genome:clogmia6"]["printouts"];
		$endj = $date["End"][0];
		$startj = $date["Start"][0];
		$strandj = $date["Strand"][0];
		$idj = $date["Plain id"][0];
		$typex = $date['Type'][0];
		$vowels = "@Genome:clogmia6";
		$idj = str_replace($vowels, "", $idj);
		
		$vowels = "_";
		$idX = str_replace($vowels, "-5F", $idj);
		array_push($exonId, $idX);
		for($x=0;$x<count($exonId);$x++)
		{
			$chj = curl_init('http://wikimachine/wiki/Special:Ask/format%3Djson/limit%3D500/link%3Dall/headers%3Dshow/searchlabel%3DJSON/class%3Dsortable-20wikitable-20smwtable/offset%3D/-5B-5BItem:%2B-5D-5D-20-5B-5BPlain-20ref-20id::'.$exonId[$x].'-5D-5D/-3FStart/-3FEnd/-3FType/-3FPlain-20id/-3FStrand/-3FId/mainlabel%3D/prettyprint%3Dtrue/unescape%3Dtrue');
			curl_setopt($chj, CURLOPT_RETURNTRANSFER, 1);
		    $contentj = curl_exec($chj);
		    curl_close($chj);
		    $jsonj = json_decode($contentj, true);
		   	$resultsSubfeature = $jsonj["results"];
		   	$keySub=array_keys($resultsSubfeature);
		   	for($y=0;$y<count($keySub);$y++)
		   	{
		   		$dateSub = array_values($resultsSubfeature)[$y]["printouts"];
		   		$startExon = $dateSub["Start"][0];
		   		$endExon = $dateSub["End"][0];
				$strandExon = $dateSub["Strand"][0];
				$typeExon = $dateSub['Type'][0];
				$subfeatures2 = array(
	 			    'end' => $endExon,
	       			'start' => $startExon,
	       			'strand' =>$strandExon,
	      	  		'type' => $typeExon
				);
				array_push($arrayExon, $subfeatures2);
		   	}
		}
		$subfeatures = array(array(
		    'end' => $endj,
			'name' => $idj,
			'start' => $startj,
			'strand' =>$strandj,
			'subfeatures' => $arrayExon,
	  		'type' => $typex,
			'uniqueID' => $idj),
		);
		$data = $res[$j];
		$data['subfeatures']=$subfeatures;
		array_push($arrayPrueba, $data);
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

	$ch = curl_init('http://wikimachine/wiki/Special:Ask/format%3Djson/limit%3D500/link%3Dall/headers%3Dshow/searchlabel%3DJSON/class%3Dsortable-20wikitable-20smwtable/offset%3D/-5B-5BItem:%2B-5D-5D-20-5B-5BSize::%2B-5D-5D/-3FStart/-3FEnd/-3FSize/-3FId/mainlabel%3D/prettyprint%3Dtrue/unescape%3Dtrue');
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
		$start = array_values($date)[0]["Start"][0];
		$end = array_values($date)[0]["End"][0];
		$key = array_values($date)[0]["Id"][0];
		$vowels = "@Genome:clogmia6";
		$key = str_replace($vowels, "", $key);
		$data = array('length' => $size, 'name' => $key, 'start' => $start, 'end' => $end, 'seqChunkSize' => 20000);
		array_push($resp, $data);
	}
	return response()->json($resp);

});

$router->get('/names', function (\Illuminate\Http\Request $request) use ($router)
{

	$result = array();

	if ( $request->has('equals') ) {
		$refseq = $request->input('equals');
	}
	
	$find   = ':';
	$pos = strpos($refseq, $find);
	if ($pos === false) {
		$result=refseqNames($refseq);
	}
	else
	{
		$refseq = explode(":", $refseq);
		$refseq = $refseq[0];
		//$refseq=$refseq2[0];
		$result=refseqNames($refseq);
	}
	return response()->json( $result );
});

function refseqNames($refseq)
{

	if ( $refseq ) 
	{
		$symbol = "_";
		$seq = str_replace($symbol, "-20", $refseq);
		$symbol2 = "_";
		$seq2 = str_replace($symbol2, " ", $refseq);
		$find2   = '.g';
		$pos2 = strpos($refseq, $find2);
		if ($pos2 === false) 
		{
			$ch = curl_init('http://wikimachine/wiki/Special:Ask/format%3Djson/link%3Dall/headers%3Dshow/searchlabel%3DJSON/class%3Dsortable-20wikitable-20smwtable/offset%3D/limit%3D50/-5B-5B:Item:Annotation:'.$seq.'@Genome:clogmia6-5D-5D-20-5B-5BSize::%2B-5D-5D/-3FStart/-3FEnd/-3FRef-20id/mainlabel%3D/prettyprint%3Dtrue/unescape%3Dtrue');
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		    $content = curl_exec($ch);
		    curl_close($ch);
		    $json = json_decode($content, true);
		    if (count($json)>0) {

				$results = $json["results"]["Item:Annotation:".$seq2."@Genome:clogmia6"]["printouts"];
		    	$start = $results["Start"][0];
		    	$end = $results["End"][0];
		    	$ref = $results["Ref id"][0]["fulltext"];
		    	$location = array('ref' => $ref, 'start' => $start, 'end' => $end, 'tracks' => array("REST Test2 Track"), 'objectName' => $refseq);
		    }
		}
	    else
	    {
			$ch = curl_init('http://wikimachine/wiki/Special:Ask/format%3Djson/link%3Dall/headers%3Dshow/searchlabel%3DJSON/class%3Dsortable-20wikitable-20smwtable/offset%3D/limit%3D50/-5B-5B:Item:Annotation:'.$seq.'@Genome:clogmia6-5D-5D/-3FStart/-3FEnd/-3FRef-20id/mainlabel%3D/prettyprint%3Dtrue/unescape%3Dtrue');
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		    $content = curl_exec($ch);
		    curl_close($ch);
		    $json = json_decode($content, true);
			$results = $json["results"]["Item:Annotation:".$seq2."@Genome:clogmia6"]["printouts"];
	    	$start = $results["Start"][0];
	    	$end = $results["End"][0];
	    	$ref = $results["Ref id"][0]["fulltext"];
	    	$location = array('ref' => $ref, 'start' => $start, 'end' => $end, 'tracks' => array("REST Test2 Track"), 'objectName' => $refseq);
		}
		$data = array('name' => $refseq, 'location' => $location);
		$result=array($data);
    }
    return $result;
}
