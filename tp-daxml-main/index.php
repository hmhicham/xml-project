<?php
// Create a new DOMDocument instance
$dom = new DOMDocument();
libxml_use_internal_errors(true);

// Suppress errors and load the XML file
libxml_use_internal_errors(true);  // Enable internal error handling
if ($dom->load('xml/Config.xml') === false) {
    echo "Error: Cannot load XML file.";
    foreach (libxml_get_errors() as $error) {
        echo "<br>Error: ", $error->message;
    }
    exit;
}

// Validate the XML against the DTD
if (!$dom->validate()) {
    echo "Error: XML does not validate against the DTD.<br>";
    foreach (libxml_get_errors() as $error) {
        echo "Line {$error->line}: {$error->message}<br>";
    }
    libxml_clear_errors(); // Clear errors after displaying
    exit;
}

// Proceed to parse the XML as you normally would
$header = $dom->getElementsByTagName('header')->item(0);
$nav = $dom->getElementsByTagName('nav')->item(0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Guide</title>
    <link rel="stylesheet" href="css/styles.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

    <div class="container">

        <!-- Sidebar Navigation -->
        <nav class="sidebar">
            <?php
            $students = $nav->getElementsByTagName('student');
            foreach ($students as $student):
            ?>
                <div class="student-info">
                    <h2>Etudiant</h2>
                    <p><strong>Nom:</strong> <?php echo $student->getElementsByTagName('nom')->item(0)->nodeValue; ?></p>
                    <p><strong>Prénom:</strong> <?php echo $student->getElementsByTagName('prenom')->item(0)->nodeValue; ?></p>
                    <p><strong>Spécialité:</strong> <?php echo $student->getElementsByTagName('specialite')->item(0)->nodeValue; ?></p>
                    <p><strong>Section:</strong> <?php echo $student->getElementsByTagName('section')->item(0)->nodeValue; ?></p>
                    <p><strong>Groupe:</strong> <?php echo $student->getElementsByTagName('groupe')->item(0)->nodeValue; ?></p>
                    <p><strong>Email:</strong> <a href="mailto:<?php echo $student->getElementsByTagName('email')->item(0)->nodeValue; ?>"><?php echo $student->getElementsByTagName('email')->item(0)->nodeValue; ?></a></p>
                    <hr>
                </div>
            <?php endforeach; ?>
            <button class="btn add-city-btn" onclick="window.location.href='form.php';">
                <i class="fa fa-plus-circle"></i> Ajouter Ville
            </button>
        </nav>

        <!-- Main Content -->
        <div class="main-content">

            <!-- Header Section -->
            <header class="header">
                <img src="<?php echo $header->getElementsByTagName('img')->item(0)->getAttribute('src'); ?>" alt="Travel Site Banner" class="banner-image">
                <h1 class="site-title"><?php echo $header->getElementsByTagName('h1')->item(0)->nodeValue; ?></h1>
            </header>

            <!-- Search Section -->
            <section class="search-section">
                <h2 class="search-title"><em>Recherche</em></h2>
                <form id="search-form" class="search-form">
                    <div class="form-row">
                        <div class="input-group">
                            <label for="continent">Continent:</label>
                            <input type="text" id="continent" name="continent" placeholder="Entrez un continent">
                        </div>
                        <div class="input-group">
                            <label for="country">Pays:</label>
                            <input type="text" id="country" name="country" placeholder="Entrez un pays">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="input-group">
                            <label for="city">Ville:</label>
                            <input type="text" id="city" name="city" placeholder="Entrez une ville">
                        </div>
                        <div class="input-group">
                            <label for="site">Site:</label>
                            <input type="text" id="site" name="site"  placeholder="Entrez un site">
                        </div>
                    </div>
                    <button type="submit" class="btn"><i class="fa fa-check"></i> Valider</button>
                </form>
            </section>

            <!-- Search Results Section -->
            <section class="results-section">
                <h2><em>Résultat de la recherche</em></h2>
                <ol class="city-list" id="results">
                    <!-- Results will be displayed here dynamically -->
                </ol>
            </section>

        </div>

    </div>

    <footer>
        <p>&copy; 2024 Travel Guide. Created by ghaith & hicham, M2-TI, UMBB.</p>
    </footer>

    <!-- JavaScript -->
    <script>
    document.getElementById('search-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Empêcher le comportement par défaut du formulaire

    // Collecter les valeurs des champs de recherche
    const continent = document.getElementById('continent').value.toLowerCase().trim();
    const country = document.getElementById('country').value.toLowerCase().trim();
    const city = document.getElementById('city').value.toLowerCase().trim();
    const site = document.getElementById('site').value.toLowerCase().trim();

    const results = document.getElementById('results');
    results.innerHTML = ''; // Réinitialiser les résultats

    // Charger et parser le fichier XML
    fetch('xml/Villes.xml')
        .then(response => response.text())
        .then(xmlText => {
            const parser = new DOMParser();
            const xmlDoc = parser.parseFromString(xmlText, 'application/xml');

            const villes = xmlDoc.getElementsByTagName('ville');
            let found = false;

            // Parcourir les villes pour trouver des correspondances
            Array.from(villes).forEach(ville => {
                const villeName = ville.getAttribute('nom')
                const countryNode = ville.parentNode.parentNode;
                const countryName = countryNode.getAttribute('nom').toLowerCase();
                const continentNode = xmlDoc.querySelector(`continent[no="${countryNode.getAttribute('no')}"]`);
                const continentName = continentNode ? continentNode.getAttribute('nom').toLowerCase() : '';
                const siteElements = ville.getElementsByTagName('site');
                const siteNames = Array.from(siteElements).map(site => site.getAttribute('nom').toLowerCase());

                // Vérifier si la ville correspond aux critères de recherche
                if (
                    (!city || villeName.startsWith(city)) &&
                    (!country || countryName.startsWith(country)) &&
                    (!continent || continentName.startsWith(continent)) &&
                    (!site || siteNames.some(siteName => siteName.startsWith(site)))
                ) {
                    found = true;

                    // Créer un élément de résultat
                    const resultItem = document.createElement('li');
                    resultItem.classList.add('city-item');
                    resultItem.innerHTML = `
                        <a href="generate_city.php?file=${villeName}.xml" class="city-link">${villeName} (${countryName}, ${continentName})</a>
                        <div class="city-actions">
                            <a href="#" class="edit-btn"><i class="fa fa-edit"></i></a>
                            <a href="#" class="delete-btn"><i class="fa fa-trash"></i></a>
                        </div>

                        

                         ${Array.from(ville.getElementsByTagName('site')).map(site => `
                                <li>
                                    <strong>${site.getAttribute('nom')}</strong>
                                </li>
                            `).join('')}
                        </ul>
                        
                    `;

                    

                    
                    results.appendChild(resultItem);
                }
            });

            // @add event listener to the edit and delete buttons
            Array.from(document.querySelectorAll('.edit-btn')).forEach(editBtn => {
                editBtn.addEventListener('click', function(event) {
    event.preventDefault();
    const cityName = this.closest('.city-item').querySelector('.city-link').textContent.split(' ')[0];
    window.location.href = `edit_city.php?city_name=${encodeURIComponent(cityName)}`;
});
            });

            Array.from(document.querySelectorAll('.delete-btn')).forEach(deleteBtn => {
    deleteBtn.addEventListener('click', function(event) {
        event.preventDefault();
        const cityName = this.closest('.city-item').querySelector('.city-link').textContent.split(' ')[0];
        if (confirm(`Are you sure you want to delete the city ${cityName}?`)) {
            fetch('delete_city.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `city_name=${encodeURIComponent(cityName)}`
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                location.reload(); // Reload the page to reflect changes
            })
            .catch(error => {
                console.error('Error deleting city:', error);
            });
        }
    });
});

            

            

            // Si aucune ville n'a été trouvée
            if (!found) {
                results.innerHTML = '<li>Aucun résultat trouvé.</li>';
            }

            
        })

        .catch(error => {
            console.error('Erreur lors du chargement du fichier XML:', error);
            results.innerHTML = '<li>Impossible de charger les données.</li>';
        });


        
    });



    </script>

</body>
</html>
