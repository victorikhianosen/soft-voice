<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class ValidRecipients implements Rule
{
    protected $attribute;

    protected $value;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->attribute = $attribute;
        $this->value = $value;
        if (gettype($value) === 'array') {
            $usersCount = User::whereIn('id', $value)->count();
            return count($value) > 1 && count($value) === $usersCount;
        }
        return in_array($value, ['all_users', 'all_verified_users']);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if (gettype($this->value) === 'array') {
            $usersCount = User::whereIn('id', $this->value)->count();
            if (count($this->value) != $usersCount) {
                return "Some recipients not found";
            }
        }
        return "The {$this->attribute} field must be an array of valid user ids or string (all_users, all_verified_users)";
    }
}
