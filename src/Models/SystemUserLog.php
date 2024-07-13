<?php

namespace Module\System\Models;

use ReflectionClass;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Module\System\Traits\HasMeta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\System\Traits\Filterable;
use Module\System\Traits\Searchable;
use Module\System\Traits\HasPageSetup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemUserLog extends Model
{
    use Filterable;
    use HasMeta;
    use HasPageSetup;
    use Searchable;

    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'datalog';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'system_user_logs';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'dirties' => 'array',
        'origins' => 'array',
        'coords' => 'array'
    ];

    /**
     * The default key for the order.
     *
     * @var string
     */
    protected $defaultOrder = 'updated_at:desc';

    /**
     * toFilterableArray function
     * #[FilterUsingAge(['age'])]
     *
     * @return array
     */
    protected function toFilterableArray(): array
    {
        return [
            'month' => 'month::updated_at'
        ];
    }

    /**
     * mapFilters function
     * type: Combobox, DateInput, NumberInput, Select, Textfield, TimeInput
     *
     * @return array
     */
    public static function mapFilters(): array
    {
        return [
            'month' => [
                'title' => 'Bulan',
                'data' => [
                    ['title' => 'Januari', 'value' => 1],
                    ['title' => 'Februari', 'value' => 2],
                    ['title' => 'Maret', 'value' => 3],
                    ['title' => 'April', 'value' => 4],
                    ['title' => 'Mei', 'value' => 5],
                    ['title' => 'Juni', 'value' => 6],
                    ['title' => 'Juli', 'value' => 7],
                    ['title' => 'Agustus', 'value' => 8],
                    ['title' => 'September', 'value' => 9],
                    ['title' => 'Oktober', 'value' => 10],
                    ['title' => 'Nopember', 'value' => 11],
                    ['title' => 'Desember', 'value' => 12],
                ],
                'used' => false,
                'operators' => ['=', '<', '>'],
                'operator' => ['='],
                'type' => 'Select',
                'value' => null,
            ],
        ];
    }

    /**
     * mapHeaders function
     *
     * readonly value?: SelectItemKey<any>
     * readonly title?: string | undefined
     * readonly align?: 'start' | 'end' | 'center' | undefined
     * readonly width?: string | number | undefined
     * readonly minWidth?: string | undefined
     * readonly maxWidth?: string | undefined
     * readonly nowrap?: boolean | undefined
     * readonly sortable?: boolean | undefined
     *
     * @param Request $request
     * @return array
     */
    public static function mapHeaders(Request $request): array
    {
        return [
            ['title' => 'Event', 'value' => 'event'],
            ['title' => 'Module', 'value' => 'module'],
            ['title' => 'Model', 'value' => 'modelName'],
            ['title' => 'ID', 'value' => 'modelId'],
            ['title' => 'Updated', 'value' => 'updated_at', 'sortable' => false, 'width' => '170'],
        ];
    }

    /**
     * mapColor function
     *
     * @param [type] $event
     * @return string
     */
    protected static function mapColor($event): string
    {
        switch ($event) {
            case 'created':
                return 'blue';

            case 'updated':
                return 'green';

            case 'trashed':
                return 'orange';

            case 'deleted':
                return 'deep-orange';

            case 'restored':
                return 'green';
        }
    }

    /**
     * mapIcon function
     *
     * @param [type] $event
     * @return string
     */
    protected static function mapIcon($event): string
    {
        switch ($event) {
            case 'created':
                return 'add';

            case 'updated':
                return 'edit';

            case 'trashed':
                return 'delete';

            case 'deleted':
                return 'delete';

            case 'restored':
                return 'restore';
        }
    }

    /**
     * mapResource function
     *
     * @param Request $request
     * @return array
     */
    public static function mapActivities(Request $request, $model): array
    {
        return [
            'id' => $model->id,
            'color' => static::mapColor($model->event),
            'event' => $model->event,
            'module' => $model->module,
            'icon' => static::mapIcon($model->event),
            'username' => $model->user_name,
            'dirties' => $model->dirties,
            'origins' => $model->origins,
            'state' => false,
            'updated_at' => (string) $model->updated_at,
        ];
    }

    /**
     * mapResource function
     *
     * @param Request $request
     * @return array
     */
    public static function mapResource(Request $request, $model): array
    {
        return [
            'id' => $model->id,
            'event' => $model->event,
            'module' => $model->module,
            'modelName' => $model->subjectable ? (new ReflectionClass($model->subjectable))->getShortName() : null,
            'modelId' => $model->subjectable_id,
            'username' => $model->user_name,
            'dirties' => $model->dirties,
            'origins' => $model->origins,
            'state' => false,
            'updated_at' => (string) $model->updated_at,
        ];
    }

    /**
     * scopeForCurrentUser function
     *
     * @param Builder $query
     * @param [type] $request
     * @return void
     */
    public function scopeForCurrentUser(Builder $query, $request)
    {
        return $query
            ->where('user_id', $request->user()->id);
    }

    /**
     * The subjectable function
     *
     * @return MorphTo
     */
    public function subjectable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The user function
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(SystemUser::class, 'user_id');
    }

    /**
     * The eventLog function
     *
     * @param string $event
     * @param Model $model
     * @return void
     */
    public static function eventLog(string $event, Model $subject): void
    {
        $model = new static();

        DB::connection($model->connection)->beginTransaction();

        try {
            if ($event === 'deleted') {
                static::where('subjectable_type', get_class($subject))
                    ->where('subjectable_id', optional($subject)->id)
                    ->delete();
            } else {
                $model->id = (string) Str::uuid();
                $model->subjectable_type = get_class($subject);
                $model->subjectable_id = optional($subject)->id;
                $model->event = $event;
                $model->module = str($model->subjectable_type)->after('Module\\')->before('\Models');
                $model->user_id = optional(request()->user())->id ?: 1;
                $model->user_name = optional(request()->user())->name ?: 'superadmin';
                // $model->impersonate = true;
                // $model->impersonate_id = 1;
                // $model->impersonate_name = 'superadmin';
                $model->coords = null;
                
                if (request()->has('coords')) {
                    $model->coords = request('coords');
                }

                switch ($event) {
                    case 'updated':
                        $origins = [];

                        foreach ($dirties = $subject->getDirty() as $key => $value) {
                            $origins[$key] = $subject->getOriginal($key);
                        }

                        $model->dirties = $dirties;
                        $model->origins = $origins;
                        break;
                }

                $model->save();
            }

            DB::connection($model->connection)->commit();
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            DB::connection($model->connection)->rollBack();
        }
    }
}
