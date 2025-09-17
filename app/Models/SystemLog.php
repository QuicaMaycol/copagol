<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'event_type',
        'description',
        'subject_id',
        'subject_type',
        'metadata',
        'ip_address',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'metadata' => 'json',
    ];

    /**
     * Get the user that performed the action.
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Sistema', // Show 'Sistema' if user is deleted or action was automated
        ]);
    }

    /**
     * Get the parent subject model (the model that was changed).
     */
    public function subject()
    {
        return $this->morphTo();
    }

    /**
     * Helper function to add a new log entry.
     *
     * @param string $eventType
     * @param string $description
     * @param Model|null $subject
     * @param array|null $metadata
     */
    public static function add(string $eventType, string $description, ?Model $subject = null, ?array $metadata = null)
    {
        self::create([
            'user_id' => auth()->id(),
            'event_type' => $eventType,
            'description' => $description,
            'subject_id' => $subject ? $subject->id : null,
            'subject_type' => $subject ? get_class($subject) : null,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
        ]);
    }
}
