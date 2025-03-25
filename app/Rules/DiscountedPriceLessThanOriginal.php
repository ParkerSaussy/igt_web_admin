<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DiscountedPriceLessThanOriginal implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $originalPrice = request()->input('price');

        // Check if the discounted price is less than the original price
        return $value < $originalPrice;
    }
    
    public function passes($attribute, $value)
    {
        // Retrieve the value of the "original_price" input field from the request data
      
    }
    public function message()
    {
        return 'The discounted price must be less than the original price.';
    }

    
}
