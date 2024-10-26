<?php
// Load variables from the .env file
require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$apiKey = $_ENV['API_KEY'];
$apiBaseUrl = 'https://cinestream-api.p.rapidapi.com';

// Helper function to make requests to the API
function fetchAPI($endpoint, $params = []) {
    global $apiKey, $apiBaseUrl;
    
    $url = $apiBaseUrl . $endpoint;
    
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'X-RapidAPI-Key: ' . $apiKey
        ],
    ]);
    
    $response = curl_exec($curl);
    curl_close($curl);

    return json_decode($response, true);
}

// Capture the query from the URL
$query = isset($_GET['query']) ? $_GET['query'] : '';

// If there is a query, perform the search
if ($query) {
    $movies = fetchAPI('/search/movie', ['query' => $query]);
} else {
    // Otherwise, show popular movies
    $movies = fetchAPI('/movie/popular');
}

// Sort movies by IMDb rating (in descending order)
usort($movies, function($a, $b) {
    return $b['rating'] - $a['rating'];
});

// Function to limit the description
function limitDescription($description, $limit = 150) {
    if (strlen($description) > $limit) {
        return substr($description, 0, $limit) . '...';
    }
    return $description;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Search</title>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
   <link rel="stylesheet" href="css/search.css">
</head>
<body>
    <!-- Responsive Header -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="images/logo.png" alt="Logo">
                <span class="brand-name">CineStream</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>
    <div class="container">
        <h1 class="text-center my-5">Search for Movies</h1>

        <!-- Search Bar -->
        <div class="mb-4">
            <form action="search.php" method="get" class="search-container">
                <input type="text" class="form-control" name="query" placeholder="Search for a movie..." value="<?= htmlspecialchars($query) ?>">
                <button type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <!-- Movie List -->
        <div id="moviesContainer" class="movie-list">
            <?php if (!empty($movies)): ?>
                <?php foreach ($movies as $movie): ?>
                    <div class="movie-list-item d-flex">
                        <img src="<?= $movie['poster'] ?>" alt="<?= $movie['title'] ?>">
                        <div>
                            <h5>
                                <a href="movie-details.php?movie_id=<?= $movie['tmdb_id'] ?>" class="text-white"><?= $movie['title'] ?></a>
                            </h5>
                            <p><?= limitDescription($movie['description'], 150) ?></p>
                            <p>Rating: <?= $movie['rating'] ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No movies found for your search.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
