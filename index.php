<?php
// Load the variables from the .env file
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
        $url .= '?' . http_build_query($params); // Adds query string parameters
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

// Get the current page (default is the first page)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Fetch movies by genre or popular
$genreId = isset($_GET['genre']) ? (int)$_GET['genre'] : null;

// Fetch genres
$genres = fetchAPI('/genre/movie/list');

// Fetch movies based on the genre
if ($genreId) {
    $popularMovies = fetchAPI("/genre/$genreId/movies", ['page' => $page]); 
} else {
    $popularMovies = fetchAPI("/movie/popular", ['page' => $page]); 
}

// Find the name of the selected genre
$genreName = 'Movies'; // Default value if no genre is selected
if ($genreId && $genres) {
    foreach ($genres as $genre) {
        if ($genre['id'] == $genreId) {
            $genreName = $genre['name'];
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Streaming</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="css/index.css">
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

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <form class="d-flex" action="search.php" method="get">
                            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="query">
                            <button class="btn btn-outline-light" type="submit">Search</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Mobile dropdown menu button -->
    <button class="mobile-menu-button" onclick="toggleMobileMenu()">Genres</button>
    
    <!-- Mobile dropdown menu -->
    <div class="mobile-menu" id="mobileMenu">
        <ul>
            <li><a href="?genre=">All Genres</a></li>
            <?php foreach ($genres as $genre): ?>
                <li><a href="?genre=<?= $genre['id'] ?>" <?= ($genreId == $genre['id']) ? 'class="active"' : '' ?>><?= $genre['name'] ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Main content with sidebar -->
    <div class="main-content">
        <!-- Genre menu in the sidebar for larger screens -->
        <div class="sidebar">
            <h3>Genres</h3>
            <ul>
                <li><a href="?genre=">All Genres</a></li>
                <?php foreach ($genres as $genre): ?>
                    <li><a href="?genre=<?= $genre['id'] ?>" <?= ($genreId == $genre['id']) ? 'class="active"' : '' ?>><?= $genre['name'] ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Movie grid -->
        <div class="movie-container">
            <h1 class="text-center my-5"><?= $genreName ?></h1>

            <div id="moviesContainer" class="movie-grid">
                <?php foreach ($popularMovies as $movie): ?>
                    <div class="movie-item">
                        <a href="movie-details.php?movie_id=<?= $movie['tmdb_id'] ?>">
                            <img class="movie-poster" src="<?= $movie['poster'] ?>" alt="<?= $movie['title'] ?>">
                            <h5 class="text-center mt-2"><?= $movie['title'] ?></h5>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation" class="my-5">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page - 1 ?>&genre=<?= $genreId ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="page-item"><a class="page-link" href="?page=<?= $page ?>&genre=<?= $genreId ?>"><?= $page ?></a></li>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page + 1 ?>&genre=<?= $genreId ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Function to toggle the dropdown menu on mobile
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            if (menu.style.display === 'block') {
                menu.style.display = 'none';
            } else {
                menu.style.display = 'block';
            }
        }
    </script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
