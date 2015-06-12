<?php
function substri_count($haystack, $needle)
{
    return substr_count(strtoupper($haystack), strtoupper($needle));
}
$myfile = fopen("testfile.txt", "w") or die("Unable to open file!");
$xml=simplexml_load_file("datos.xml") or die("Error: Cannot create object");
$palabras = array();
	foreach($xml->children() as $dato){

		if($dato->authkeywords != ""){

			$token = strtok($dato->authkeywords, "|");
			while ($token !== false){
					if(!in_array($token, $palabras)){
						array_push($palabras, $token);
					}
					$token = strtok("|");	
			} 

		}
	}
	

 	foreach($xml->children() as $dato){
		$anteriortitulo=0;
		$anteriorabstract=0;
		$anteriorrevista=0;
		$predominantetitulo="";
		$predominanteabstract="";
		$predominanterevusta="";
		$namespaces = $dato->getNameSpaces(true);
		$dc = $dato->children($namespaces['dc']);
		$prism= $dato->children($namespaces['prism']);
		if($dc->title != "" and $dc->description != "" and $prism->publicationName!= ""){
			foreach($palabras as $clave){
				$titulo= substri_count($dc->title,$clave);
				$abstract= substri_count($dc->description,$clave);
				$revista= substri_count($prism->publicationName,$clave);
				$total=$titulo+$abstract+$revista;
				if($titulo>$anteriortitulo){				
					$predominantetitulo=$clave;
					$anteriortitulo=$titulo;
					
				}
				if($abstract>$anteriorabstract){				
					$predominanteabstract=$clave;
					$anteriorabstract=$abstract;
					
				}
				if($revista>$anteriorrevista){				
					$predominanterevista=$clave;
					$anteriorrevista=$revista;
					
				}
				
				echo "dato ".$dc->title." palabra: ".$clave." titulo:".$titulo." abstract:".$abstract." revista: ".$revista." Total = ".$total."<br><br>";
			}
			echo "<br>predominantes<br>".$predominantetitulo."<br>".$predominanteabstract."<br>".$predominanterevista."<br><br>";
		}
	}

?> 

