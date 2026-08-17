<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends \Konekt\AppShell\Models\User
{
    use HasFactory, Notifiable;
}
