<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class GeometryPointRule implements Rule
{
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
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $result = json_decode($value);

        return property_exists($result, 'lng')
            && property_exists($result, 'lat')
            && is_float($result->lng)
            && is_float($result->lat);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Неверный параметр :attribute - не геометрическая точка.';
    }
}
