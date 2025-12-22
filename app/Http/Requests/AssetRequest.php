<?php

namespace App\Http\Requests;

use App\AssetComplianceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    // Bersihkan data sebelum divalidasi
    protected function prepareForValidation()
    {
        // Cek apakah ada input harga
        if ($this->has("price")) {
            $this->merge([
                "price" => $this->sanitizePrice($this->price),
            ]);
        }
    }

    // Helper function biar rapi
    private function sanitizePrice($value)
    {
        // Hapus Rp, Titik, Spasi -> Jadi angka murni
        return (int) str_replace(["Rp", ".", " "], "", $value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $asset = $this->route("asset");

        return [
            "serialNumber" => [
                "required",
                "string",
                "max:255",
                Rule::unique("assets", "serial_number")->ignore($asset),
            ],
            "registerCode" => [
                "required",
                "string",
                "max:255",
                Rule::unique("assets", "register_code")->ignore($asset),
            ],
            "hostname" => [
                "required",
                "string",
                "max:255",
                Rule::unique("assets", "hostname")->ignore($asset),
            ],
            "brand" => ["required", "string", "max:255"],
            "model" => ["required", "string", "max:255"],
            "room" => ["required", "exists:rooms,id"],
            "condition" => ["required", "string", "max:255"],
            "deploymentStatus" => ["required", "string", "max:255"],
            "ipVlan" => ["nullable", "string", "max:255"],
            "vlan" => ["nullable", "string", "max:255"],
            "portCapacity" => ["nullable", "string", "max:255"],
            "portTrunk" => ["nullable", "string", "max:255"],
            "osVersion" => ["nullable", "string", "max:255"],
            "compliance_status" => [Rule::enum(AssetComplianceStatus::class)],
            "eosDate" => ["nullable", "date"],
            "purchaseYear" => [
                "nullable",
                "integer",
                "min:1900",
                "max:" . (date("Y") + 10),
            ],
            "price" => [
                "nullable",
                "integer",
                "min:0",
                "max:999999999999999", // 15 Digit
            ],
        ];
    }
}
