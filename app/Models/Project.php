<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
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
        'user_id',
    ];

    /**
     * User of the project.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Project categories.
     *
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'projects_categories', 'project_id', 'category_id');
    }

    /**
     * Project situations.
     *
     * @return HasMany
     */
    public function situations(): HasMany
    {
        return $this->hasMany(Situation::class);
    }

    /**
     * Turn project to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $result = parent::toArray();
        $result['categories'] = $this->categories()->get();
        $result['situation'] = $this->situations()->get();
        return $result;
    }

}
