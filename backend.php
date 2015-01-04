<?php
header("Content-Type: application/rss+xml");
include 'include/config.php';
$expira = time() - $timecache;
if (file_exists('backend.xml') && $expira < filemtime($urlcache)) {
    include 'backend.xml';    
    exit;
}
function convertir($cadena) 
{  $cadena= stripslashes($cadena); 
   $buscar = array('<br>', '<p>', '</p>', '<br />','&nbsp;','@','"');
   $reemplazar = array();
   $cadena = str_replace($buscar, $reemplazar, $cadena);
   $buscar = array('á', 'é', 'í', 'ó','ú');
   $reemplazar = array('a', 'e', 'i', 'o','u');
   $cadena = str_replace($buscar, $reemplazar, $cadena);
   $buscar = array('&aacute;', '&eacute;', '&iacute;', '&oacute;', '&uacute;');
   $cadena_arreglada = str_replace($buscar, $reemplazar, $cadena);
   return ($cadena_arreglada);
}

function RSS($url,$imagen,$leer_cant_feed)
{ global $entries;
  $noticias = simplexml_load_file($url);
  $largo=550; 
  $lee=$leer_cant_feed;
  $ciclo = 1;
  foreach ($noticias as $noticia) {  
	foreach($noticia as $reg){ 
		if(!empty($reg->title) && $ciclo<$lee&& !empty($reg->description) && !empty($reg->pubDate)){
		        $pubdate =  $reg->pubDate;
		        $title   =  $reg->title;
	 			$link    =  $reg->link;
		        $description =  convertir(strip_tags(substr($reg->description,0,$largo)).'...');
		        $timestamp   =  strtotime(substr($reg->pubDate,0,25));
		        $entries[$timestamp]['pubdate'] = $timestamp;
		        $entries[$timestamp]['title']   = $title;
		        $entries[$timestamp]['link']    = $link;
		        $entries[$timestamp]['image']   = $imagen;
		        $entries[$timestamp]['description'] = $description;
		        $ciclo++;    
	    } 
   }
}
krsort($entries);
return $entries;
}
$urlplanet = substr($urlplanet,0,strlen($urlplanet)-1);
$ahora = time();
$fecha = date("r",$ahora);
$year  = date("Y",$ahora);
$xml = '';
$xml = "
<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\r
<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\">\n\r
 <channel>\r\n
  <title>$nombre_sitio</title>\r
  <link>$urlplanet</link>\r
  <description>$descripcion</description>\r
  <language>$lenguaje</language>\r
  <copyright>Copyleft 2014 -$year, $nombre_sitio</copyright>\r
  <pubDate>$fecha</pubDate>\r
  <lastBuildDate>$fecha</lastBuildDate>\r
  <docs>$urlplanet</docs>\r
  <generator>Script ViSeRProject http://viserproject.com</generator>\r
  <webMaster>$emailinfo ($nombre_sitio)</webMaster>\r
  <managingEditor>$emailinfo</managingEditor>\r
  <image>\r
	  <title>$nombre_sitio</title>\r
	  <url>$urlplanet/img/rss.png</url>\r
	  <link>$urlplanet</link>\r
	  <description>$descripcion</description>\r
  </image>\r
  <ttl>120</ttl>\r
  <atom:link href=\"$urlplanet/backend.php\" rel=\"self\" type=\"application/rss+xml\" />\n\r";
   foreach ($feeds as $imagen => $url) 
	 {  RSS($url,$imagen,$leer_cant_feed);  }
   foreach ($entries as $timestamp => $entry) {
     $fecha = date("r",$entry['pubdate']);
     $entry['title']       = utf8_decode($entry['title']);
     $entry['description'] = utf8_decode($entry['description']);
     $xml .= "\n\r<item>\r
		        <title>$entry[title]</title>r
		    	<link>$entry[link]</link>\r
		    	<guid>$entry[link]</guid>\r
		    	<pubDate>$fecha</pubDate>\r 
 		    	<description>\r
                  <![CDATA[<img src=\"$urlplanet/img/avatar/$entry[image].png\" alt=\"$entry[image]\" align=\"left\" style=\"float:left; width:95px; height:95px;\">$entry[description]]]></description>\r 
		  </item>\r";
  }
$xml .= "
 </channel>\r\n
</rss>\n\r";
$filexml = fopen('backend.xml', 'w');
fwrite($filexml, $xml);
fclose($filexml);
include 'backend.xml';
?>