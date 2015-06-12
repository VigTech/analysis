<?php
function substri_count($haystack, $needle)
{
    return substr_count(strtoupper($haystack), strtoupper($needle));
}
function in_array_r($needle, $haystack) {
    foreach ($haystack as $item) {
		if(in_array($needle, $haystack)){
			return true;
	}
    }
	return false;
}

function mdArrayMap($func, $arr){
  $ret = array();
  foreach ($arr as $key => $val) {
      $ret[$key] = (is_array($val) ? mdArrayMap($func, $val) : $func($val));
  }
  return $ret;
}

function hIndex($citas){
	$resultado=1;	
	rsort($citas, SORT_NUMERIC);
	foreach($citas as $dato){
		if($dato <= $resultado){
			
			return $resultado-1;
		}

		$resultado=$resultado+1;
	}

return $resultado-1;
}



function extraer($ruta){
	
	$citasaux=array();	

	$autorkey=0;
	$autoraux=array();
	$autorlabels=array();
	$autorvalores=array();
	$autorgraficolabels="";
	$autorgraficovalores="";

	$doclabels=array();
	$docvalores=array();
	$docresponselabels="";
	$docresponsevalores="";

	$revistakey=0;
	$revistaaux=array();
	$revistalabels=array();
	$revistavalores=array();
	$revistagraficolabels="";
	$revistagraficovalores="";

	$afiliakey=0;
	$afiliaaux=array();
	$afilialabels=array();
	$afiliavalores=array();
	$afiliagraficolabels="";
	$afiliagraficovalores="";

	$tipokey=0;
	$tipolabels=array();
	$tipovalores=array();
	$tipograficolabels="";
	$tipograficovalores="";	

	$fechakey=0;
	$fechalabels=array();
	$fechavalores=array();
	$fechagraficolabels="";
	$fechagraficovalores="";	

	$paiskey=0;
	$paislabels=array();
	$paisvalores=array();
	$paisgraficolabels="";
	$paisgraficovalores="";	
	
	$txt="Dato,";
	
	$csv = fopen("testfile.csv", "w") or die("Unable to open file!");
	$datos = fopen("datos.json", "w") or die("Unable to open file!");
	$xml=simplexml_load_file("/home/vigtech/shared/repository/".$ruta."/busqueda0.xml") or die("Error: Cannot create object");
	$palabras = array();
	$numdocumentos=0;
	$numcitas=0;
	$numautores=0;
	$numrevistas=0;
	$query=$xml->xpath('opensearch:Query');
	$query='"'.urldecode($query[0]['searchTerms']).'"'; 

		foreach($xml->children() as $dato){
						
			$namespaces = $dato->getNameSpaces(true);
			$dc = $dato->children($namespaces['dc']);
			$prism= $dato->children($namespaces['prism']);
			if($dc->title != "" and $dc->description != "" and $prism->publicationName!= "" and $dato->authkeywords!=""){
	
				$token = strtok($dato->authkeywords, "|");
				while ($token !== false){
						if(!in_array($token, $palabras)){
							array_push($palabras, $token);
						}
						$token = strtok("|");	
				} 	
	
			}
		}
	
		foreach($palabras as $palabra){
			$campo=str_replace(',', ' ', $palabra);
			$campo=str_replace(' ', '_', $campo);		
			$txt=$txt.$campo;
			if(end($palabras)!=$palabra){
				$txt=$txt.",";
			}else{
				$txt=$txt."\n";
			}
		}fwrite($csv, $txt);	
	




	 	foreach($xml->children() as $dato){
			$namespaces = $dato->getNameSpaces(true);
			$dc = $dato->children($namespaces['dc']);
			$prism= $dato->children($namespaces['prism']);
			
			//documento
			if($dc->title != "" and $dc->description != "" and $prism->publicationName!= "" and  $dato->authkeywords!=""){
				$numdocumentos=$numdocumentos+1;
				$numcitas=$numcitas + $dato->{"citedby-count"};
				array_push($citasaux,$dato->{"citedby-count"});


				

				//año
				
				if(!in_array('"'.substr(trim($prism->coverDate), 0, 4).'"', $fechalabels)){
						array_push($fechalabels,'"'.substr(trim($prism->coverDate), 0, 4).'"');
						array_push($fechavalores, 1);
						$fechalabels = array_map('trim', $fechalabels);
					}elseif (in_array('"'.substr(trim($prism->coverDate), 0, 4).'"', $fechalabels)) {
    						$fechakey=array_search('"'.substr(trim($prism->coverDate), 0, 4).'"', $fechalabels);
						$fechavalores[$fechakey]=$fechavalores[$fechakey]+1;
					}




				//autor
				foreach($dato->author as $author){
					if(!in_array_r(trim($author->authid), $autoraux)){
						$numautores=$numautores+1;					
						array_push($autoraux, $author->authid);
						array_push($autorlabels,'"'.$author->authname.'"');
						array_push($autorvalores, 1);
						$autoraux = array_map('trim', $autoraux);
					}elseif (in_array_r(trim($author->authid), $autoraux)) {
    						$autorkey=array_search($author->authid, $autoraux);
						$autorvalores[$autorkey]=$autorvalores[$autorkey]+1;
					}
				}
				//revista
				if(!in_array(trim($prism->issn), $revistaaux)){
						$numrevistas=$numrevistas+1;					
						array_push($revistaaux, $prism->issn);
						array_push($revistalabels, '"'.$prism->publicationName.'"');
						array_push($revistavalores, 1);
						$revistaaux = array_map('trim', $revistaaux);
					}elseif (in_array(trim($prism->issn), $revistaaux)) {
    						$revistakey=array_search($prism->issn, $revistaaux);
						$revistavalores[$revistakey]=$revistavalores[$revistakey]+1;
					}
				//afiliacion
				foreach($dato->affiliation as $afiliacion){
					//pais
					if(!in_array('"'.trim($afiliacion->{"affiliation-country"}).'"', $paislabels)){
						array_push($paislabels,'"'.$afiliacion->{"affiliation-country"}.'"');
						array_push($paisvalores, 1);
						$paislabels = array_map('trim', $paislabels);
					}elseif (in_array('"'.trim($afiliacion->{"affiliation-country"}).'"', $paislabels)) {
    						$paiskey=array_search('"'.$afiliacion->{"affiliation-country"}.'"', $paislabels);
						$paisvalores[$paiskey]=$paisvalores[$paiskey]+1;
					}
					//afiliacion
					if(!in_array(trim($afiliacion->afid), $afiliaaux)){											
						array_push($afiliaaux, $afiliacion->afid);
						array_push($afilialabels, '"'.$afiliacion->affilname.'"');
						array_push($afiliavalores, 1);
						$afiliaaux = array_map('trim', $afiliaaux);
					}elseif (in_array(trim($afiliacion->afid), $afiliaaux)) {
    						$afiliakey=array_search($afiliacion->afid, $afiliaaux);
						$afiliavalores[$afiliakey]=$afiliavalores[$afiliakey]+1;
					}
				}
				//tipo
				if(!in_array('"'.trim($dato->subtypeDescription).'"', $tipolabels)){
						array_push($tipolabels,'"'.$dato->subtypeDescription.'"');
						array_push($tipovalores, 1);
						$tipolabels = array_map('trim', $tipolabels);
					}elseif (in_array('"'.trim($dato->subtypeDescription).'"', $tipolabels)) {
    						$tipokey=array_search('"'.$dato->subtypeDescription.'"', $tipolabels);
						$tipovalores[$tipokey]=$tipovalores[$tipokey]+1;
					}
				
				//enlaces
				
			
				$nombre=$dc->title;
				array_push($doclabels, '"'.$nombre.'"');
				array_push($docvalores, '"'.$dato->link[2]['href'].'"');
				
				$nombre=str_replace(',', ' ', $nombre);
				$nombre=str_replace('	', '', $nombre);
				
			        $nombre=str_replace(' ', '_', $nombre);	
				$txt=$nombre.",";

				//keywords
				foreach($palabras as $clave){
					$valor=0;
					$titulo= substri_count($dc->title,$clave);
					$abstract= substri_count($dc->description,$clave);
					$revista= substri_count($prism->publicationName,$clave);
					$keywords= substri_count($dato->authkeywords, $clave);
					$total=$titulo+$abstract+$revista+$keywords;
					if($total != 0){
						$valor=1;
					}
					$txt=$txt.$valor;
					if(end($palabras)!=$clave){
						$txt=$txt.",";
					}else{
						$txt=$txt."\n";
					}
					
					
				}
				fwrite($csv, $txt);
				
				
			}
		}
	$hindex=hIndex($citasaux);	
	$proautdoc=round($numautores/$numdocumentos);
	$procitadoc=round($numcitas/$numdocumentos);

	array_multisort($revistavalores,SORT_DESC, $revistalabels);
	array_multisort($paisvalores,SORT_DESC, $paislabels);
	array_multisort($autorvalores,SORT_DESC, $autorlabels);
	array_multisort($tipovalores,SORT_DESC, $tipolabels);
	array_multisort($afiliavalores,SORT_DESC, $afilialabels);
	array_multisort($fechalabels, $fechavalores);
	array_multisort($doclabels, $docvalores);

	$revistagraficolabels=rtrim(implode(',', $revistalabels),',');
	$revistagraficovalores=rtrim(implode(',', $revistavalores),',');

	$paisgraficolabels=rtrim(implode(',', $paislabels),',');
	$paisgraficovalores=rtrim(implode(',', $paisvalores),',');

	$autorgraficolabels=rtrim(implode(',', $autorlabels),',');
	$autorgraficovalores=rtrim(implode(',', $autorvalores),',');

	$tipograficolabels=rtrim(implode(',', $tipolabels),',');
	$tipograficovalores=rtrim(implode(',', $tipovalores),',');

	$afiliagraficolabels=rtrim(implode(',', $afilialabels),',');
	$afiliagraficovalores=rtrim(implode(',', $afiliavalores),',');
	
	$fechagraficolabels=rtrim(implode(',', $fechalabels),',');
	$fechagraficovalores=rtrim(implode(',', $fechavalores),',');

	$docresponselabels=rtrim(implode(',', $doclabels),',');
	$docresponsevalores=rtrim(implode(',', $docvalores),',');

	//echo  "<a href="."http://172.17.9.195/vigtech/1/rest/testfile.csv".">CSV</a>" ;	
	//echo "<br><br>";
	//echo  "<a href="."http://172.17.9.195/vigtech/1/rest/nombres.txt".">nombres</a>" ;
	fwrite($datos, '{ "documentos": '.$numdocumentos.', "citas": '.$numcitas.', "autores":'.$numautores.', "revistas":'.$numrevistas.', "hindex":'.$hindex.', "promedioautdoc":'.$proautdoc.', "promediocitadoc":'.$procitadoc.', "query":'.$query.' }');
	fwrite($datos, ', "revistas":{ "labels": ['.$revistagraficolabels.'], "valores": ['.$revistagraficovalores.'] }');
	fwrite($datos, ', "paises":{ "labels": ['.$paisgraficolabels.'], "valores": ['.$paisgraficovalores.'] }');
	fwrite($datos, ', "autores":{ "labels": ['.$autorgraficolabels.'], "valores": ['.$autorgraficovalores.'] }');
	fwrite($datos, ', "tipos":{ "labels": ['.$tipograficolabels.'], "valores": ['.$tipograficovalores.'] }');
	fwrite($datos, ', "afiliaciones":{ "labels": ['.$afiliagraficolabels.'], "valores": ['.$afiliagraficovalores.'] }');
	fwrite($datos, ', "fechas":{ "labels": ['.$fechagraficolabels.'], "valores": ['.$fechagraficovalores.'] }');	
	fwrite($datos, ', "enlaces":{ "labels": ['.$docresponselabels.'], "valores": ['.$docresponsevalores.'] }');			
	fclose($csv);
	fclose($datos);
}
?>
