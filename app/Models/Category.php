<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Validation\ValidationException;

class Category extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Projects with this category.
     *
     * @return BelongsToMany
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'projects_categories', 'category_id', 'project_id');
    }

    /**
     * Find at least one category or fail.
     *
     * @param $ids
     *
     * @return mixed
     *
     * @throws ValidationException
     */
    public static function findManyOrFail($ids) {
        $categories = self::findMany($ids);
        if (count($categories) < 1) {
            throw ValidationException::withMessages(['categories' => 'Categories not found']);
        }
        return $categories;
    }

}
