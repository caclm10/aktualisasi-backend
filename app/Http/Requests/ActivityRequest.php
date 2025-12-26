<?php

namespace App\Http\Requests;

use App\Models\Enums\ActivityCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActivityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "category" => ["required", Rule::enum(ActivityCategory::class)],
            "property" => ["required", "string", "max:255"],
            "old" => ["required", "string", "max:255"],
            "new" => ["required", "string", "max:255"],
            "assetId" => ["required", "exists:assets,id"],
            "roomId" => ["required", "exists:rooms,id"],
            "remarks" => ["nullable", "string"],
        ];
    }
}
