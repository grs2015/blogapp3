<?php

namespace App\Rules;

use App\Models\Category;
use Illuminate\Contracts\Validation\InvokableRule;

class ParentCategory implements InvokableRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        if ($value && !Category::find($value)) {
            $fail('The :attribute must correspond to existing categories.');
        }
    }
}
