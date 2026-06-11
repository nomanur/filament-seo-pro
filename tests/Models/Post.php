<?php

declare(strict_types=1);

namespace Nomanur\FilamentSeoPro\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Nomanur\FilamentSeoPro\Traits\HasSeo;

/**
 * Test model for HasSeo trait testing.
 */
class Post extends Model
{
    use HasSeo;

    protected $fillable = ['title', 'slug', 'content'];
}
