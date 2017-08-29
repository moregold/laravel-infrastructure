<?php namespace Moregold\Infrastructure;

use \Exception;
use Illuminate\Support\Facades\DB,
    Illuminate\Support\Facades\Log,
    Illuminate\Support\Facades\Validator,
    Illuminate\Database\Eloquent\Model as Eloquent;
use Moregold\Infrastructure\Helpers\UniqueValuesTrait,
    Moregold\Infrastructure\Helpers\AttributeTrait,
    Moregold\Infrastructure\Messages\ErrorMessageTrait,
    Moregold\Infrastructure\Messages\SuccessMessageTrait,
    Moregold\Infrastructure\Validators\RulesInterface,
    Moregold\Infrastructure\Validators\Factory as ValidatorFactory;

abstract class Model extends Eloquent implements RulesInterface
{
    use AttributeTrait,
        ErrorMessageTrait,
        SuccessMessageTrait,
        UniqueValuesTrait;

    /**
     * Attributes to append to model during serialization
     *
     * @var array
     */
    protected $appends = [
        'hasErrors',
        'getErrorsAsString',
        'hasSuccesses',
        'getSuccessesAsString'
    ];

    /**
     * Disable GUID generation when creating model
     *
     * @var boolean
     */
    protected $disableGuid = false;

    /**
     * Handle dynamic method calls into the method after attempting to
     * dynamically create get attribute append method
     *
     * @param  string $func   Name of method
     * @param  array  $params Method parameters
     *
     * @return mixed          Good luck!
     */
    public function __call($func, $params)
    {
        try {
            $prefix = 'get';
            $suffix = 'Attribute';
            $length = strlen($func) - strlen($prefix) - strlen($suffix);

            preg_match("/^".$prefix."([A-Za-z]{".$length."})".$suffix."$/", $func, $matches);

            if (!empty($matches) && isset($matches[1])) {
                if (method_exists($this, $matches[0])) {
                    return call_user_func_array([$this, $matches[0]], $params);
                }
                if (method_exists($this, lcfirst($matches[1]))) {
                    return call_user_func_array([$this, $matches[1]], $params);
                }
            }
            return parent::__call($func, $params);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }

    /**
     * The "booting" method of the model. Boots parent model,
     * then attaches hook to saving event for model.
     */
    public static function boot()
    {
        parent::boot();

        /**
         * Attach to the 'creating' Model Event to provide a GUID
         * for the `id` field (provided by $model->getKeyName())
         */
        static::creating(function ($model) {
            if (!($model->incrementing || $model->disableGuid || $model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string)$model->makeGuid();
            }
        });
    }

    /**
     * Add property to appends array
     *
     * @param string $append Property to add to appends array
     *
     * @return \Moregold\Infrastructure\Model  Updated model
     */
    protected function addAppends($append)
    {
        if (!property_exists($this, 'appends') || !is_array($this->appends)) {
            $this->appends = [];
        }
        if (!empty($append) && !in_array($append, $this->appends)) {
            array_push($this->appends, $append);
        }
        return $this;
    }

    /**
     * Retrieves attributes to append to model during serialization
     *
     * @var array
     */
    public function getAppends()
    {
        return $this->appends;
    }

    public function load($relations)
    {
        $relations = $this->addAppendsGroup($relations);
        return parent::load($relations);
    }

    protected function addAppendsGroup($relations)
    {
        if (!is_array($relations)) {
            $relations = [$relations];
        }
        $indices_to_remove = [];
        $prefix = 'appends';
        for ($i = 0; $i < count($relations); $i++) {
            $relation = $relations[$i];
            if (is_string($relations[$i]) && strrpos($relation, $prefix) === 0) {
                preg_match("/^".$prefix."([A-Za-z]+)$/", $relation, $matches);
                if (!empty($matches) && isset($matches[1])) {
                    $group = explode('group', strtolower($matches[1]));
                    if (count($group) == 2) {
                        $method = $group[0].'Group';
                        if (method_exists($this, $method)) {
                            $this->$method($group[1]);
                            array_push($indices_to_remove, $i);
                        }
                    }
                }
            }
        }
        foreach ($indices_to_remove as $i) {
            unset($relations[$i]);
        }
        return $relations;
    }

    /**
     * Count related models in bulk
     *
     * @param string $model Full name of the model, e.g. Moregold\Infrastructure\Model
     * @param string $foreign_key Foreign key to search, group by (e.g. community_id)
     * @param array $where Associative array of additional parameters to filter the count by
     *
     * @return mixed Single row with the count
     */
    protected function countRelation($model, $foreign_key, $where = [], $local_key = null)
    {
        // We use "hasOne" instead of "hasMany" because we only want to return one row.
        $query = $this->hasOne($model, $foreign_key, $local_key)->select(DB::raw($foreign_key.', count(*) as aggregate'));
        foreach ($where as $key => $value) {
            $query = $query->where($key, $value);
        }
        return $query->groupBy($foreign_key);
    }

    /**
     * The attribute to get the value of the relation
     *
     * @param string|\Illuminate\Database\Eloquent\Relations\Relation name to get the count of
     *
     * @return int Count of defined relation
     */
    protected function countRelationAttribute($countRelation, $lazy_load = false)
    {
        if (!$this->relationLoaded($countRelation)) {
            if (!$lazy_load) {
                return null;
            }
            $this->load($countRelation);
        }
        $related = $this->getRelation($countRelation);
        return ($related) ? (int) $related->aggregate : 0;
    }

    /**
     * Default validation rules for model
     *
     * @var array
     */
    public $validation_rules = [];

    /**
     * Retrieve default or overloaded validation rules for current model,
     * attempts to make appropriate replacements for macros inside rules
     *
     * @return array Collection of rules for current model
     */
    public function getValidationRules()
    {
        $replace = '';
        if (!is_null($this->getKey()) && $this->getKey() > 0) {
            $replace = ','.$this->getKey().','.$this->getKeyName();
        }
        foreach ($this->validation_rules as $key => $rule) {
            $this->validation_rules[$key] = str_replace(',:id,:key', $replace, $rule);
        }
        return $this->validation_rules;
    }

    /**
     * Performs a check of current model against current model's validation
     * rules, if validation check fails, adds validation error messages to
     * error collection
     *
     * @uses Moregold\Infrastructure\Helpers\ErrorMessageAble
     *
     * @return boolean Model is valid
     */
    public function isValid()
    {
        $v = ValidatorFactory::model($this);
        if ($v->fails()) {
            $this->addErrors($v->messages()->getMessages());
        }
        return $v->passes();
    }

    /**
     * Update the attributes of the model
     *
     * @param array $attributes New model attributes
     * @return \Moregold\Infrastructure\Model  Updated model
     */
    public function mergeAttributes($attributes = [])
    {
        $merged = [];
        foreach ($this->getAttributes() as $key => $value) {
            $merged[$key] = $value;
            if (isset($attributes[$key]) && !is_null($attributes[$key])) {
                $merged[$key] = $attributes[$key];
            }
        }
        return $this->fill($merged);
    }

    /**
     * Add skip, take, ordering to a query
     *
     * @param \Illuminate\Database\Query\Builder $query The query to add filters to
     * @param array $filters Pagination filters
     *
     * @return \Illuminate\Database\Query\Builder  Query with additional parameters
     */
    public function scopeWithPaginationFilters($query, $filters = [])
    {
        if (isset($filters['skip'])) {
            $query->skip($filters['skip']);
        }
        if (isset($filters['take'])) {
            $query->take($filters['take']);
        }
        if (isset($filters['order_by'])) {
            $query->orderBy($filters['order_by']['field'], $filters['order_by']['order']);
        }
        return $query;
    }
}
