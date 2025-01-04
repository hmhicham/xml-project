<?php
// Path to the Villes.xml file
$villesFile = 'xml/Villes.xml';

// Check if the file exists
if (!file_exists($villesFile)) {
    die("Error: File not found.");
}

// Load the XML file
$dom = new DOMDocument();
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->load($villesFile);

// Get the city name from the POST request
$city_name = $_POST['city_name'] ?? null;

if (!$city_name) {
    die("Error: Missing required city name.");
}

// Locate and delete the city
$xpath = new DOMXPath($dom);
$villeNode = $xpath->query("//ville[@nom='$city_name']")->item(0);

if ($villeNode) {
    $parentNode = $villeNode->parentNode; // Get the parent node of the city (villes)
    $parentNode->removeChild($villeNode); // Remove the city
    $dom->save($villesFile); // Save the updated XML file
    echo "City '$city_name' deleted successfully.";
} else {
    echo "Error: City '$city_name' not found.";
}
?>
