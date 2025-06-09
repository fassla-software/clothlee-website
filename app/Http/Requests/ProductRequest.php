<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
          	'fabric'           => 'nullable|string|max:255',
            'name'           => 'required|max:255',
            'category_id'    => ['required', 'exists:categories,id'], // Ensure category_id exists
            'parent_category' => [
                'required',
                'exists:categories,id',
                function ($attribute, $value, $fail) {
                    // Ensure parent_category is the actual parent of category_id
                    $selectedCategory = \App\Models\Category::find($this->category_id);
                    if (!$selectedCategory || $selectedCategory->parent_id != $value) {
                        $fail('The selected parent category is invalid.');
                    }
                }
            ],
            'unit'           => 'sometimes|required',
            'min_qty'        => 'sometimes|required|numeric',
            'unit_price'     => 'sometimes|required|numeric|gt:0',
            'discount'       => [
                'sometimes',
                'nullable',
                'numeric',
                $this->get('discount_type') == 'amount' ? 'lt:unit_price' : 'lt:100'
            ],
            'current_stock'  => 'sometimes|required|numeric',
            'starting_bid'   => 'sometimes|required|numeric|min:1',
            'auction_date_range' => 'sometimes|required',
        ];
    }

    /**
     * Get the validation messages of rules that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required'          => translate('Product name is required'),
            'category_id.required'   => translate('You must select a category'),
            'category_id.exists'     => translate('Invalid category selected'),
            'parent_category.required' => translate('A parent category is required'),
            'parent_category.exists' => translate('Invalid parent category selected'),
            'parent_category.*'      => translate('The selected parent category does not match the selected category'),
            'unit.required'          => translate('Product unit is required'),
            'min_qty.required'       => translate('Minimum purchase quantity is required'),
            'min_qty.numeric'        => translate('Minimum purchase must be numeric'),
            'unit_price.gt'          => translate('The unit price must be greater than 0'),
            'unit_price.required'    => translate('Unit price is required'),
            'unit_price.numeric'     => translate('Unit price must be numeric'),
            'discount.numeric'       => translate('Discount must be numeric'),
            'discount.lt'            => translate('Discount should be less than unit price'),
            'current_stock.required' => translate('Current stock is required'),
            'current_stock.numeric'  => translate('Current stock must be numeric'),
            'starting_bid.required'  => translate('Starting Bid is required'),
            'starting_bid.numeric'   => translate('Starting Bid must be numeric'),
            'starting_bid.min'       => translate('Minimum Starting Bid is 1'),
            'auction_date_range.required' => translate('Auction Date Range is required'),
        ];
    }

    /**
     * Handle failed validation response
     */
    public function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            throw new HttpResponseException(response()->json([
                'message' => $validator->errors()->all(),
                'result' => false
            ], 422));
        } else {
            throw (new ValidationException($validator))
                ->errorBag($this->errorBag)
                ->redirectTo($this->getRedirectUrl());
        }
    }
}