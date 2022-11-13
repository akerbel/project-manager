<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 *     title="Situation",
 *     description="Situation model",
 *     @OA\Xml(
 *         name="Situation"
 *     )
 * )
 */
class Situation extends Model
{
    use HasFactory;

    const STATUS_PLANNED = 0;
    const STATUS_ONGOING = 1;
    const STATUS_COMPLETED = 2;

    public $timestamps = false;

    /**
     * @var int[] Array of allowed status numbers.
     */
    public static array $allowedStatuses = [
        self::STATUS_PLANNED => 'Planned',
        self::STATUS_ONGOING => 'Ongoing',
        self::STATUS_COMPLETED => 'Completed',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    /**
     * Project of situation.
     *
     * @return BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Check if status number is allowed number.
     *
     * @param int $status
     *
     * @return bool
     */
    public static function isStatusAllowed(int $status): bool
    {
        return in_array($status, array_keys(self::$allowedStatuses));
    }

}
