<?php

namespace App\Enums\Foundation; // <-- CORRECTED

enum UserType: string
{
    case ADMIN = 'admin';
    case STAFF = 'staff';
    case OWNER = 'owner';
    case VENDOR = 'vendor';
    case TECHNICIAN = 'technician';
    case SUPER_ADMIN = 'super-admin';
}