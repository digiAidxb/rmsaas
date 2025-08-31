<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Hashing\Hasher;

class TenantUserProvider extends EloquentUserProvider
{
    /**
     * Create a new tenant user provider.
     *
     * @param  \Illuminate\Contracts\Hashing\Hasher  $hasher
     * @param  string  $model
     * @return void
     */
    public function __construct(Hasher $hasher, $model)
    {
        parent::__construct($hasher, $model);
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        $model = $this->createModel();

        return $this->newModelQuery($model)
                    ->where($model->getAuthIdentifierName(), $identifier)
                    ->first();
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) ||
           (count($credentials) === 1 &&
            str_contains(array_keys($credentials)[0], 'password'))) {
            return;
        }

        $query = $this->newModelQuery();

        foreach ($credentials as $key => $value) {
            if (str_contains($key, 'password')) {
                continue;
            }

            if (is_array($value) || $value instanceof \Arrayable) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }

        // Note: is_active field doesn't exist in our schema, removing this constraint

        return $query->first();
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        if (! $user instanceof User) {
            return false;
        }

        // Note: is_active field doesn't exist in our schema, skipping this check

        // Check password
        return $this->hasher->check($credentials['password'] ?? '', $user->getAuthPassword());
    }

    /**
     * Create a new instance of the model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createModel()
    {
        $class = '\\'.ltrim($this->model, '\\');

        $model = new $class;

        // Use landlord connection for user queries - users are stored in landlord database
        $model->setConnection('landlord');

        return $model;
    }

    /**
     * Get a new query builder for the model instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model|null  $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function newModelQuery($model = null)
    {
        return is_null($model)
                ? $this->createModel()->newQuery()
                : $model->newQuery();
    }
}