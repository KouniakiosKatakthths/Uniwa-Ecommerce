<?php

namespace Database\Seeders;

use App\Models\Showtime;
use App\Models\Movie;
use Illuminate\Database\Seeder;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 10 movies with past showtimes (archived)
        Movie::factory()
            ->count(10)
            ->has(Showtime::factory()->count(5)->in_past())
            ->create();

        // 10 movies with future showtimes (upcoming)
        Movie::factory()
            ->count(10)
            ->has(Showtime::factory()->count(5)->in_future())
            ->create();

        // 10 movies with both past and future showtimes (now playing)
        Movie::factory()
            ->count(10)
            ->has(Showtime::factory()->count(3)->in_past())
            ->has(Showtime::factory()->count(5)->in_future())
            ->create();
    }

    // private function get_movies()
    // {
    //     return [
    //         $this->create_track(
    //             "Pulp Fiction",
    //             "The lives of two mob hitmen, a boxer, a gangster and his wife, and a pair of diner bandits intertwine in four tales of violence and redemption.",
    //             "https://m.media-amazon.com/images/M/MV5BYTViYTE3ZGQtNDBlMC00ZTAyLTkyODMtZGRiZDg0MjA2YThkXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg",
    //             "https://www.youtube.com/watch?v=yMXB9u4z8Ic",
    //             "154",
    //             "R",
    //             true,
    //             fake()->dateTime($max = 'now', $timezone = null),
    //         ),
    //         $this->create_track(
    //             "The Lord of the Rings: The Fellowship of the Ring",
    //             "A meek Hobbit from the Shire and eight companions set out on a journey to destroy the powerful One Ring and save Middle-earth from the Dark Lord Sauron.",
    //             "https://m.media-amazon.com/images/I/71TZ8BmoZqL.jpg",
    //             "https://www.youtube.com/watch?v=yMXB9u4z8Ic",
    //             "154",
    //             "PG-13",
    //             true,
    //             fake()->dateTime($max = 'now', $timezone = null),
    //         ),
    //         $this->create_track(
    //             "The Lord of the Rings: The Return of the King",
    //             "Gandalf and Aragorn lead the World of Men against Sauron's army to draw his gaze from Frodo and Sam as they approach Mount Doom with the One Ring.",
    //             "https://atthemovies.uk/cdn/shop/products/LORthereturnoftheking2003us27x40in135.jpg?v=1621381407&width=1090",
    //             "https://www.youtube.com/watch?v=yMXB9u4z8Ic",
    //             "201",
    //             "PG-13",
    //             true,
    //             fake()->dateTime($max = 'now', $timezone = null),
    //         ),
    //         $this->create_track(
    //             "La La Land",
    //             "When Sebastian, a pianist, and Mia, an actress, follow their passion and achieve success in their respective fields, they find themselves torn between their love for each other and their careers.",
    //             "https://i1.sndcdn.com/artworks-000202679424-ivrw3g-t1080x1080.jpg",
    //             "https://www.youtube.com/watch?v=yMXB9u4z8Ic",
    //             "128",
    //             "PG-13",
    //             true,
    //             fake()->dateTime($max = 'now', $timezone = null),
    //         ),
    //         $this->create_track(
    //             "House",
    //             "Using a crack team of doctors and his wits, an antisocial maverick doctor specializing in diagnostic medicine does whatever it takes to solve puzzling cases that come his way.",
    //             "https://media-cache.cinematerial.com/p/500x/6ds0wadd/house-md-french-dvd-movie-cover.jpg?v=1456289239",
    //             "https://www.youtube.com/watch?v=yMXB9u4z8Ic",
    //             "45",
    //             "TV-14",
    //             false,
    //             fake()->dateTime($max = 'now', $timezone = null),
    //         ),
    //         $this->create_track(
    //             "The Matrix",
    //             "A computer hacker discovers that his life is nothing more than an elaborate simulation run by an evil AI.",
    //             "https://m.media-amazon.com/images/I/613ypTLZHsL._AC_UF1000,1000_QL80_.jpg",
    //             "https://www.youtube.com/watch?v=yMXB9u4z8Ic",
    //             "136",
    //             "R",
    //             false,
    //             fake()->dateTime($max = 'now', $timezone = null),
    //         ),
    //         $this->create_track(
    //             "The Matrix",
    //             "A computer hacker discovers that his life is nothing more than an elaborate simulation run by an evil AI.",
    //             "https://m.media-amazon.com/images/I/613ypTLZHsL._AC_UF1000,1000_QL80_.jpg",
    //             "https://www.youtube.com/watch?v=yMXB9u4z8Ic",
    //             "136",
    //             "R",
    //             false,
    //             fake()->dateTime($max = 'now', $timezone = null),
    //         ),
    //         $this->create_track(
    //             "Once Upon a Time in America",
    //             "A former Prohibition-era Jewish gangster returns to the Lower East Side of Manhattan 35 years later, where he must once again confront the ghosts and regrets of his old life.",
    //             "https://m.media-amazon.com/images/I/613ypTLZHsL._AC_UF1000,1000_QL80_.jpg",
    //             "https://www.youtube.com/watch?v=yMXB9u4z8Ic",
    //             "229",
    //             "R",
    //             true,
    //             fake()->dateTime($max = 'now', $timezone = null),
    //         ),
    //         $this->create_track(
    //             "The Godfather",
    //             "The aging patriarch of an organized crime dynasty transfers control of his clandestine empire to his reluctant son.",
    //             "https://m.media-amazon.com/images/M/MV5BNGEwYjgwOGQtYjg5ZS00Njc1LTk2ZGEtM2QwZWQ2NjdhZTE5XkEyXkFqcGc@._V1_.jpg",
    //             "https://www.youtube.com/watch?v=yMXB9u4z8Ic",
    //             "175",
    //             "R",
    //             true,
    //             fake()->dateTime($max = 'now', $timezone = null),
    //         ),
    //         $this->create_track(
    //             "Blade Runner 2049",
    //             "Young Blade Runner K's discovery of a long-buried secret leads him to track down former Blade Runner Rick Deckard, who's been missing for thirty years.",
    //             "https://m.media-amazon.com/images/M/MV5BNzA1Njg4NzYxOV5BMl5BanBnXkFtZTgwODk5NjU3MzI@._V1_.jpg",
    //             "https://www.youtube.com/watch?v=yMXB9u4z8Ic",
    //             "164",
    //             "R",
    //             false,
    //             fake()->dateTime($max = 'now', $timezone = null),
    //         ),
    //         $this->create_track(
    //             "Interstellar",
    //             "In a dystopian future where Earth has become near-uninhabitable, a team of astronauts embark on a mission to find a new home for humanity.",
    //             "https://hqcovers.net/wp-content/uploads/2014/11/interstellar121.jpg",
    //             "https://www.youtube.com/watch?v=yMXB9u4z8Ic",
    //             "169",
    //             "PG-13",
    //             false,
    //             fake()->dateTime($max = 'now', $timezone = null),
    //         ),
    //     ];
    // }

    // private function create_track(
    //     $title,
    //     $description,
    //     $poster_url,
    //     $trailer_url,
    //     $duration,
    //     $rating,
    //     $featured,
    //     $release_date
    // )
    // {
    //     return [
    //         'title' => $title,
    //         'description' => $description,
    //         'poster_url' => $poster_url,
    //         'trailer_url' => $trailer_url,  
    //         'duration' => $duration,
    //         'rating' => $rating,
    //         'featured' => $featured,
    //         'release_date' => $release_date,
    //     ];
    // }

}
