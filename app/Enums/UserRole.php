<?php

namespace App\Enums;

enum UserRole : string
{
    case User = 'user';
    case Clerk = 'clerk';
    case Admin = 'admin';
}