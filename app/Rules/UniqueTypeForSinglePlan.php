<?php

namespace App\Rules;

use Closure;
use DB;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueTypeForSinglePlan implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //
    }
    public function passes($attribute, $value)
    {
        // Check if a record with the same "type" and "type" being "single" exists
        $count = DB::table('tbl_plan')
            ->where('type', $value)
            ->where('type', 'single')
            ->count();

        return $count === 0;
    }

    public function message()
    {
        return 'The :attribute must be unique for single plans.';
    }
    
}
