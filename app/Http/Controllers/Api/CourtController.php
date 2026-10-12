<?php
namespace App\Http\Controllers\Api;

use App\Models\Court;

class CourtController extends CrudController
{
    protected string $model = Court::class;

    protected function rules(bool $updating = false): array
    {
        $r = $this->required($updating);
        return ['name' => "$r|string|max:255", 'sport_type' => "$r|string|max:255", 'hourly_rate' => "$r|numeric|min:0", 'status' => 'sometimes|in:available,maintenance', 'description' => 'sometimes|nullable|string', 'image' => 'sometimes|nullable|string|max:2048'];
    }
}
