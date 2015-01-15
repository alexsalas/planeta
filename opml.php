<?php
header('Content-Type: text/xml');
header("Content-Type: application/rss+xml"); 
include 'include/config.php';
$xml = '';
$xml .= "
<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n
<opml version=\"1.0\">\r\n
<head>\r\n
<title>Feed List $nombre_sitio</title>\r\n
</head>\r\n
<body>\r\n\r\n";
foreach ($feeds as $imagen => $enlace) {
   $xml .= "<outline text=\"$imagen\" title=\"$imagen\" type=\"rss\" xmlUrl=\"$enlace\"/>\r\n";
  }
$xml .= "\r\n</body>\r\n
</opml>";
$xml = trim($xml);
$file = "opml-planeta.opml";
$filexml = fopen($file, 'w+');
fwrite($filexml, $xml);
fclose($filexml);
header("Content-Disposition: attachment; filename=" . urlencode($file));    
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
header("Content-Description: File Transfer");             
header("Content-Length: " . filesize($file));
flush(); // this doesn't really matter.
$fp = fopen($file, "r"); 
while (!feof($fp))
{
    echo fread($fp, 65536); 
    flush(); // this is essential for large downloads
}  
fclose($fp); 

/* header("Cache-Control: no-cache, must-revalidate");
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
header("Content-Description: File Transfer");
header("Content-Disposition: attachment; filename=opml-planeta.opml"); */
?>