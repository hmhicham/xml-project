<?php

// Function to validate XML against XSD
function validateXML($xmlFile, $xsdFile) {
    $dom = new DOMDocument();
    $dom->load($xmlFile);

    // Validate the XML against the XSD schema
    if ($dom->schemaValidate($xsdFile)) {
        return true;
    } else {
        return false;
    }
}

// Processing form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Set the paths to your XML and XSD files
    $xmlDir = 'xml/';
    $xsdDir = 'xml/';

    // Validate the XML files
    $isVillesValid = validateXML($xmlDir . 'Villes.xml', $xsdDir . 'Villes.xsd');
    $isVilleValid = validateXML($xmlDir . 'Alger.xml', $xsdDir . 'Ville.xsd');

    if ($isVillesValid && $isVilleValid) {
        // If validation is successful, process form data
        $cityName = $_POST['city_name'];
        $country = $_POST['country'];
        $continent = $_POST['continent'];
        $description = $_POST['description'];

        // Process sites and their photos
        $sites = $_POST['sites'];
        $sitePhotos = $_FILES['site_photos'];

        // Process hotels, restaurants, stations, airports
        $hotels = isset($_POST['hotels']) ? $_POST['hotels'] : [];
        $restaurants = isset($_POST['restaurants']) ? $_POST['restaurants'] : [];
        $stations = isset($_POST['stations']) ? $_POST['stations'] : [];
        $airports = isset($_POST['airports']) ? $_POST['airports'] : [];

        // Example: Create the XML for the new city (e.g., hama.xml)
        $cityXML = new SimpleXMLElement('<ville/>');
        $cityXML->addAttribute('nom', $cityName);
        $cityXML->addChild('descriptif', $description);

        // Add sites and their photos
        $sitesXML = $cityXML->addChild('sites');
        foreach ($sites as $index => $site) {
            $siteXML = $sitesXML->addChild('site', $site);
            $siteXML->addAttribute('photo', 'Photos/' . $sitePhotos['name'][$index]);
        }

        // Add hotels
        $hotelsXML = $cityXML->addChild('hotels');
        foreach ($hotels as $hotel) {
            $hotelsXML->addChild('hotel', $hotel);
        }

        // Add restaurants
        $restaurantsXML = $cityXML->addChild('restaurants');
        foreach ($restaurants as $restaurant) {
            $restaurantsXML->addChild('restaurant', $restaurant);
        }

        // Add stations (gares)
        $stationsXML = $cityXML->addChild('gares');
        foreach ($stations as $station) {
            $stationsXML->addChild('gare', $station);
        }

        // Add airports
        $airportsXML = $cityXML->addChild('aéroports');
        foreach ($airports as $airport) {
            $airportsXML->addChild('aéroport', $airport);
        }

        // Save the city-specific XML file (e.g., hama.xml)
        $cityXML->asXML($xmlDir . $cityName . '.xml');

        // Handle file uploads (e.g., saving the site photos)
        if (!empty($sitePhotos['tmp_name'][0])) {
            foreach ($sitePhotos['tmp_name'] as $index => $tmpName) {
                $uploadDir = 'images/';
                $uploadFile = $uploadDir . basename($sitePhotos['name'][$index]);

                // Check if file already exists
                if (file_exists($uploadFile)) {
                    echo "Error: The file " . $sitePhotos['name'][$index] . " already exists.<br>";
                } else {
                    if (move_uploaded_file($tmpName, $uploadFile)) {
                        echo "File " . $sitePhotos['name'][$index] . " uploaded successfully.<br>";
                    } else {
                        echo "Error uploading file " . $sitePhotos['name'][$index] . "<br>";
                    }
                }
            }
        }

        // Update the Villes.xml file with the new city info
        $villesXML = simplexml_load_file($xmlDir . 'Villes.xml');
        $newCity = $villesXML->pays->addChild('ville', $cityName);
        $newCity->addAttribute('nom', $cityName);

        // Save the modified Villes.xml
        $villesXML->asXML($xmlDir . 'Villes.xml');

        echo "City and related information added successfully!";
    } else {
        // If XML validation fails
        echo "Error: The XML files are invalid!";
    }
}

?>
