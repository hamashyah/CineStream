<?php
// Load variables from the .env file
require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$apiKey = $_ENV['API_KEY'];
$apiBaseUrl = 'https://cinestream-api.p.rapidapi.com';

// Helper function to make requests to the API
function fetchAPI($endpoint) {
    global $apiKey, $apiBaseUrl;
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $apiBaseUrl . $endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'X-RapidAPI-Key: ' . $apiKey
        ],
    ]);
    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response, true);
}

// Get the movie ID from the URL
$movieId = isset($_GET['movie_id']) ? (int)$_GET['movie_id'] : null;

// If there isn't a valid ID, redirect to the home page
if (!$movieId) {
    header('Location: index.php');
    exit;
}

// Fetch movie details
$movieDetails = fetchAPI("/movie/$movieId");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($movieDetails['title'] ?? 'Unknown Title') ?> - Movie Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
   <link rel="stylesheet" href="css/movie-details.css">
   
</head>
<body>
<style>
        body { background-color: #141414; color: white; }
        .navbar { background-color: #1c1c1c; padding: 20px; }
        .brand-name { font-size: 36px; color: white; margin-left: 10px; vertical-align: middle; }
        .mobile-menu-button { display: none; background-color: #1c1c1c; color: white; padding: 10px 20px; border: none; width: 100%; font-size: 1.5rem; cursor: pointer; }
        .mobile-menu { display: none; background-color: #1c1c1c; position: absolute; width: 100%; top: 70px; left: 0; z-index: 100; padding: 20px; }
        @media (max-width: 992px) {
            .mobile-menu-button { display: block; }
        }
        .navbar { background-color: #1c1c1c; padding: 20px; }
        .navbar-brand img { width: 50px; height: 50px; }
</style>
</head>
<body>

    <!-- Responsive Header -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="images/logo.png" alt="Logo">
                <span class="brand-name">CineStream</span>
            </a>
        </div>
    </nav>

    <!-- JavaScript -->
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <div id="adblock-warning">
        We detected that you are using an ad blocker. Please disable the ad blocker to access all features of this page.
    </div>

    <div class="iframe-container">
        <iframe id="player" allowfullscreen></iframe>
    </div>

    <div class="info-container details-container">
        <h1 class="movie-title"><?= htmlspecialchars($movieDetails['title'] ?? 'Unknown Title') ?></h1>
        <div class="description">
            <p><i class="fas fa-film icon"></i><?= htmlspecialchars($movieDetails['overview'] ?? 'Description not available') ?></p>
            <p><i class="fas fa-calendar-alt icon"></i><strong>Release Date:</strong> <?= htmlspecialchars($movieDetails['release_date'] ?? 'Not available') ?></p>
            <p><i class="fas fa-star icon"></i><strong>Rating:</strong> <?= htmlspecialchars($movieDetails['vote_average'] ?? 'N/A') ?>/10</p>
            <p><i class="fas fa-clock icon"></i><strong>Runtime:</strong> <?= htmlspecialchars($movieDetails['runtime'] ?? 'Not available') ?> minutes</p>
            <p><i class="fas fa-globe icon"></i><strong>Language:</strong> <?= htmlspecialchars($movieDetails['spoken_languages'][0] ?? 'Not available') ?></p>
            <p><i class="fas fa-tags icon"></i><strong>Genres:</strong> <?= isset($movieDetails['genres']) && is_array($movieDetails['genres']) ? htmlspecialchars(implode(', ', $movieDetails['genres'])) : 'Genres not available' ?></p>
        </div>
    </div>

    <div class="back-button text-center">
        <a href="index.php" class="btn btn-light">Back to Movies</a>
    </div>

    <script>
        // URL for the API endpoint for the player
        const playerApiUrl = '<?= $apiBaseUrl ?>/player/<?= $movieId ?>';

        fetch(playerApiUrl, {
            method: 'GET',
            headers: {
                'X-RapidAPI-Key': '<?= $apiKey ?>'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(html => {
            const playerIframe = document.getElementById('player');
            playerIframe.srcdoc = html;
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        });

        // Detect ad blocker
        window.onload = function() {
            const adElement = document.createElement('div');
            adElement.className = 'adsbox';
            adElement.style.display = 'none';
            document.body.appendChild(adElement);

            setTimeout(() => {
                if (!document.querySelector('.adsbox')) {
                    document.getElementById('adblock-warning').style.display = 'block';
                }
                adElement.remove();
            }, 100);
        };
    </script>
</body>
</html>
