<?php
if (!isset($_GET['file'])) {
    die("Error: No city file specified.");
}

$cityFile = $_GET['file'];
$xmlPath = __DIR__ . "/xml/" . basename($cityFile);
$htmlPath = __DIR__ . "/city_pages/" . pathinfo($cityFile, PATHINFO_FILENAME) . ".html";

// Check if the XML file exists
if (!file_exists($xmlPath)) {
    die("Error: City XML file not found.");
}

// Load the XML file
$dom = new DOMDocument();
libxml_use_internal_errors(true);
if (!$dom->load($xmlPath)) {
    die("Error: Invalid XML file.");
}

// Extract city details from the XML
$city = $dom->documentElement;
$cityName = $city->getAttribute('nom');
$description = $city->getElementsByTagName('descriptif')->item(0)->nodeValue;
$sites = $city->getElementsByTagName('site');
$hotels = $city->getElementsByTagName('hotel');
$restaurants = $city->getElementsByTagName('restaurant');
$gares = $city->getElementsByTagName('gare');
$aeroports = $city->getElementsByTagName('aeroport');

// Generate the HTML content
$htmlContent = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$cityName}</title>
    <link rel="stylesheet" href="../css/city_css.css">
</head>
<body>
    <header>
        <h1>{$cityName}</h1>
        <p>{$description}</p>
    </header>
    <section>
        <h2>Sites</h2>
        <ul>
HTML;

foreach ($sites as $site) {
    $siteName = $site->getAttribute('nom');
    $sitePhoto = $site->getElementsByTagName('photo')->item(0)->nodeValue;
    $htmlContent .= "<li><strong>{$siteName}</strong>: <img src='../images/{$sitePhoto}' alt='{$siteName}'></li>";
}

$htmlContent .= <<<HTML
        </ul>
    </section>
    <section>
        <h2>Hotels</h2>
        <ul>
HTML;

foreach ($hotels as $hotel) {
    $htmlContent .= "<li>{$hotel->nodeValue}</li>";
}

$htmlContent .= <<<HTML
        </ul>
    </section>
    <section>
        <h2>Restaurants</h2>
        <ul>
HTML;

foreach ($restaurants as $restaurant) {
    $htmlContent .= "<li>{$restaurant->nodeValue}</li>";
}

$htmlContent .= <<<HTML
        </ul>
    </section>
    <section>
        <h2>Transport</h2>
        <h3>Gares</h3>
        <ul>
HTML;

foreach ($gares as $gare) {
    $htmlContent .= "<li>{$gare->nodeValue}</li>";
}

$htmlContent .= <<<HTML
        </ul>
        <h3>Aéroports</h3>
        <ul>
HTML;

foreach ($aeroports as $aeroport) {
    $htmlContent .= "<li>{$aeroport->nodeValue}</li>";
}

$htmlContent .= <<<HTML
        </ul>
    </section>
    <!-- Button to Generate PDF -->
    <button id="generate-pdf-btn">Generate PDF</button>

    <script>
        document.getElementById('generate-pdf-btn').addEventListener('click', function() {
            const cityFile = "$cityFile"; // Get the city filename
            window.location.href = '/tp_amir/generate_pdf.php?file=' + cityFile;
        });
    </script>

</body>
</html>
HTML;

// Save the generated HTML content to a file
if (file_put_contents($htmlPath, $htmlContent) === false) {
    die("Error: Unable to create HTML file.");
}

// Redirect to the generated HTML file
header("Location: city_pages/" . basename($htmlPath));
exit;
?>
