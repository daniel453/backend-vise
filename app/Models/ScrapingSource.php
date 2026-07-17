<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['group', 'source', 'coverage', 'domain', 'sort_order'])]
class ScrapingSource extends Model
{
    //
}
