
# CineStream

**CineStream** is a web-based application that allows users to browse and stream movies. It integrates with the CineStream API via RapidAPI, providing access to movie data, including genres, popular movies, and movie details, with options to search and filter by genre.

## Table of Contents

- [Features](#features)
- [Technologies](#technologies)
- [Installation](#installation)
- [Usage](#usage)
- [API Integration](#api-integration)
- [Contributing](#contributing)
- [License](#license)

---

## Features

- **Watch Movies**: Stream and enjoy movies within the application.
- **Browse Movies by Genre**: View and filter movies by genres with a responsive layout.
- **Search Functionality**: Search for movies based on user input.
- **Detailed Movie Pages**: See comprehensive information for each movie, including description, release date, rating, and runtime.
- **Responsive Design**: Optimized for mobile and desktop.



## Technologies

- **Frontend**: PHP, HTML, CSS (Bootstrap)
- **Backend**: RapidAPI (for fetching movie data)
- **API**: CineStream API (via RapidAPI)

## Installation

To get a local copy of CineStream running, follow these steps:

1. **Clone the repository:**
   ```bash
   git clone https://github.com/hamashyah/Cinestream.git
   cd Cinestream
   ```

2. **Install Composer Dependencies**:  
   Run Composer to install the necessary PHP packages.
   ```bash
   composer install
   ```

3. **Set Up Environment Variables:
You can use the default API Key provided in the .env file, or create your own by signing up on RapidAPI at CineStream API (recommended)https://rapidapi.com/dataforge-dataforge-default/api/cinestream-api      (Is free) To set it up, add a .env file in the root directory with the following variable:
  
```bash
  API_KEY=your_rapidapi_key
   ```

4. **Run a Local Server**:  
   Use PHP’s built-in server or a local server environment like XAMPP or MAMP.  
   Example command:
   ```bash
   php -S localhost:8000
   ```

5. **Access the App**:  
   Go to [http://localhost:8000](http://localhost:8000) in your browser.

## Usage

1. **Browse Movies**: The home page lists popular movies and provides genre filters.
2. **Search**: Use the search bar to find movies by title.
3. **View Movie Details**: Click on a movie to view detailed information on a dedicated page and watch the movie.

## API Integration

CineStream integrates with the **CineStream API** via RapidAPI to fetch movie data. Below are the main API requests made in the application:

- **Popular Movies**:  
  Request popular movies by default on the main page.
- **Genres**:  
  Fetches genres to allow users to filter by movie genre.
- **Search**:  
  Searches movies based on user queries entered in the search bar.
- **Movie Details**:  
  Provides details for individual movies, including an embedded player.
-**Watch Movie**
Create a player with the selected movie.

API requests are handled by PHP functions like `fetchAPI()`, which manage GET requests using cURL with the RapidAPI key.

## Contributing

Contributions are welcome! To contribute:

1. Fork the project.
2. Create a feature branch (`git checkout -b feature/YourFeature`).
3. Commit changes (`git commit -m 'Add YourFeature'`).
4. Push to the branch (`git push origin feature/YourFeature`).
5. Open a pull request.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---
