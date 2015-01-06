<?php
header("Content-Type: application/rss+xml");
include 'include/config.php';
$expira = time() - $timecache;
if (file_exists('backend.xml') && $expira < filemtime($urlcache)) {
include 'backend.xml';
exit;
}
function convertir($cadena) 
{$cadena= stripslashes($cadena); 
 $buscar = array('<br>', '<p>', '</p>', '<br />','&nbsp;','@','"','�','&iexcl;',
 '<','&lt;','&amp;','&','�');
 $reemplazar = '';
 $cadena = str_replace($buscar, $reemplazar, $cadena);
 $buscar = array('�', '�', '�', '�','�','�');
 $reemplazar = array('a', 'e', 'i', 'o','u','n');
 $cadena = str_replace($buscar, $reemplazar, $cadena);
 $buscar = array('&aacute;', '&eacute;', '&iacute;', '&oacute;', '&uacute;','&ntilde;');
 $cadena_arreglada = str_replace($buscar, $reemplazar, $cadena);
 return ($cadena_arreglada);
}
function RSS($url,$imagen,$leer_cant_feed,$largo_lectura)
{ global $entries;
$noticias = simplexml_load_file($url);
$largo=$largo_lectura; 
$lee=$leer_cant_feed;
$ciclo = 1;
foreach ($noticias as $noticia) {
foreach($noticia as $reg){ 
if(!empty($reg->title) && $ciclo<$lee&& !empty($reg->description) && !empty($reg->pubDate)){
$pubdate =$reg->pubDate;
$title = utf8_decode(strip_tags(convertir($reg->title)));
 $link=$reg->link;
$description = utf8_decode(convertir(substr(strip_tags($reg->description),0,$largo))).'...';
$timestamp =strtotime(substr($reg->pubDate,0,25));
$entries[$timestamp]['pubdate'] = $timestamp;
$entries[$timestamp]['title'] = $title;
$entries[$timestamp]['link']= $link;
$entries[$timestamp]['image'] = $imagen;
$entries[$timestamp]['description'] = $description;
$ciclo++;
}}}
krsort($entries);
return $entries;
}
$urlplanet = substr($urlplanet,0,strlen($urlplanet)-1);
$ahora = time();
$fecha = date("r",$ahora);
$year= date("Y",$ahora);
$xml = '<?xml version="1.0" encoding="ISO-8859-1"  standalone= "yes" ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
$xml .= "<channel>\r
<title>$nombre_sitio</title>\r
<link>$urlplanet</link>\r
<description>$descripcion</description>\r
<language>$lang</language>\r
<copyright>Copyleft 2014 -$year, $nombre_sitio</copyright>\r
<pubDate>$fecha</pubDate>\r
<lastBuildDate>$fecha</lastBuildDate>\r
<docs>$urlplanet</docs>\r
<generator>Script ViSeRProject http://viserproject.com</generator>\r
<webMaster>$emailinfo ($nombre_sitio)</webMaster>\r
<managingEditor>$emailinfo (($nombre_sitio)</managingEditor>\r
<image>\r
<title>$nombre_sitio</title>\r
<url>$urlplanet.'themes/'.$theme.'/img/rss.png</url>\r
<link>$urlplanet</link>\r
<description>$descripcion</description>\r
</image>\r
<ttl>120</ttl>\r
<atom:link href=\"http://'.$urlplanet.'backend.php\" rel=\"self\" type=\"application/rss+xml\">";
foreach ($feeds as $imagen => $url)
{RSS($url,$imagen,$leer_cant_feed,$largo_lectura);}
foreach ($entries as $timestamp => $entry) {
$fecha = date("r",$entry['pubdate']);
$entry['title'] = $entry['title'];
$entry['description'] = $entry['description'];
$xml .= "\n<item>\n\r
<title>$entry[title]</title>\r
<link>$entry[link]</link>\r
<guid>$entry[link]</guid>\r
<pubDate>$fecha</pubDate>\r
<description>\r
<![CDATA[<img src=\"$urlplanet/img/avatar/$entry[image].png\" alt=\"$entry[image]\" align=\"left\" style=\"float:left; width:95px; height:95px;\">$entry[description]]]></description>\r
</item>\r";
}
$xml .= "\n\r</channel>\n\r
</rss>\n\r";
$xml = trim($xml);
$filexml = fopen('backend.xml', 'w+');
fwrite($filexml, $xml);
fclose($filexml);
include 'backend.xml';
?>
