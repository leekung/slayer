<?php

namespace App\Models;

use App\Models\Traits\Timestampable;
use App\Models\Traits\SoftDeletable;

class PasswordHistory extends Model
{
    use Timestampable;
    use SoftDeletable;

    public function getSource()
    {
        return 'password_history';
    }
}