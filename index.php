<?php	
	//procesar
	header("Access-Control-Allow-Origin: *");
	header("Content-Type:application/json");
	require 'file.php';
	include("functions.php");
	if(!empty($_GET['name'])){
		
		$name=$_GET['name'];
		if(true){
		
		//exec("python descarga.py ".$name);  
		
		extraer($name);
		
		exec("R -f clustering.r  ");
		exec("R -q --no-save < transpuesta.r > ");
		//echo "<br> <br>";
		//echo  "<a href="."http://172.17.9.195/vigtech/1/rest/clustering.pdf".">Dendograma Documentos Palabras claves</a>" ;	
		//echo "<br> <br>";
		//echo  "<a href="."http://172.17.9.195/vigtech/1/rest/transpuesta.pdf".">Transpuesta palabras claves documentos</a>" ;
		respuesta();
		}else{


		$price=get_price($name);

		if(empty ($price))
			deliver_response (200,"book not found", NULL);
		else
			deliver_response (200,"book found",$price);
	}}
	else
	{
		deliver_response (400,"invalid request", NULL);
	}
function deliver_response ($status,$status_message,$data)
{
	header ("HTTP/1.1 $status $status_message");
	$response['status']=$status;
	$response['status_message']=$status_message;
	$response['data']=$data;
	
	$json_response=json_encode($response);
	echo $json_response;
}
function respuesta(){
	
	$myfile = fopen("grupos.txt", "r") or die("Unable to open file!");
	$cadena= fread($myfile,filesize("grupos.txt"));
	fclose($myfile);	
	formato($cadena);
	
		
	
}
function formato($data){
	//echo "<br>";	
	$datos=array();
	$token = strtok($data, "\n");
	$token = strtok("\n");	
	while ($token !== false){
						
						array_push($datos, $token);
						
						$token = strtok("\n");	
				}
	//print_r($datos);
	
	$nodes=array();
	foreach($datos as $elemento){
	$prueba=array();	
	$token = strtok($elemento, "	");
	
					
	$prueba['name']= $campo=str_replace('_', ' ',preg_replace("/[^A-Za-z0-9\-\/.-_]/", "", $token));
						
	$token = strtok("	");
	$prueba['group']= $token;
	//echo json_encode($prueba, JSON_UNESCAPED_SLASHES);
	//echo "<br>";
	array_push($nodes, json_encode($prueba, JSON_UNESCAPED_SLASHES));
	
				
}
	//echo "<br><br>";
	//echo "<br><br>";
	$json=array();
	$links=array();
	$nodos=preg_replace('/\\\\\"/',"\"", json_encode($nodes, JSON_UNESCAPED_SLASHES));
	$nodos=preg_replace("/}\",\"{/", "},{", $nodos);

	$json['nodes']=$nodos;
	$json['links']=json_encode($links, JSON_UNESCAPED_SLASHES);
	$response= preg_replace('/\\\\\"/',"\"",json_encode($json, JSON_UNESCAPED_SLASHES));
	$response=preg_replace("/\"\[\"/", "[", $response);
	$response=preg_replace("/\"\]\"/", "]", $response);
	$response=preg_replace("/\"\[\]\"/", "[]", $response);
	$response='{"network":'.$response;
	
	$indicadores = fopen("datos.json", "r") or die("Unable to open file!");
	$indicador= fread($indicadores,filesize("datos.json"));
	fclose($indicadores);
	$response=$response.', "data":'.$indicador.'}';
	echo $response;	
}
?>
