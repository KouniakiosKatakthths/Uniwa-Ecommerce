<?php

namespace App\Enums;

enum MovieGenre : string
{
    case Action = 'Action';
    case Comedy = 'Comedy'; 
    case Drama = 'Drama'; 
    case Horror = 'Horror';
    case Thriller = 'Thriller';
    case Sci_Fi = 'Sci-Fi';
    case Romance = 'Romance';
    case Animation = 'Animation';
    case Documentary = 'Documentary';
    case Fantasy = 'Fantasy';
}