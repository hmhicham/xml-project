<?php
// Get the city name from the URL parameter
$city_name = $_GET['city_name'];

// Load the XML file for the specific city
$xml = new DOMDocument();
$xml->load("xml/$city_name.xml");

// Load the XSLT stylesheet
$xsl = new DOMDocument();
$xsl->load('xsl/Ville.xsl');

// Configure the XSLT processor
$proc = new XSLTProcessor();
$proc->importStyleSheet($xsl);

// Output the transformed HTML for the city
echo $proc->transformToXML($xml);
?>
