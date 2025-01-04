<!-- no need -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="css/styles.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

    <header>
        <h1 class="site-title">Search Results</h1>
    </header>

    <nav>
        <a href="index.php" class="nav-link">
            <i class="fa fa-home"></i> Home
        </a>
        <a href="form.php" class="nav-link">
            <i class="fa fa-plus-circle"></i> Add City
        </a>
    </nav>

    <main>
        <section class="search-results">
            <h2>Search Results</h2>

            <?php
            // Sample cities array for demonstration
            $cities = [
                ['name' => 'Alger', 'country' => 'Algeria', 'continent' => 'Africa'],
                ['name' => 'Tunis', 'country' => 'Tunisia', 'continent' => 'Africa'],
            ];

            if (!empty($cities)):
            ?>
                <ul class="city-list">
                    <?php foreach ($cities as $city): ?>
                        <li class="city-item">
                            <a href="city.php?city_name=<?php echo urlencode($city['name']); ?>">
                                <?php echo htmlspecialchars($city['name']); ?> (<?php echo htmlspecialchars($city['country']); ?>)
                            </a>
                            <div class="city-actions">
                                <a href="edit_city.php?city_name=<?php echo urlencode($city['name']); ?>" class="edit-btn">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <a href="delete_city.php?city_name=<?php echo urlencode($city['name']); ?>" class="delete-btn">
                                    <i class="fa fa-trash"></i> Delete
                                </a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No cities found. Please refine your search.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Travel Guide. Created by Toto Tutu, M2-TI, UMBB.</p>
    </footer>

</body>
</html>
