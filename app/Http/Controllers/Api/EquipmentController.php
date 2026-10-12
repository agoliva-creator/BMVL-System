<?php
namespace App\Http\Controllers\Api;

use App\Models\Equipment;

class EquipmentController extends CrudController
{
    protected string $model = Equipment::class;

    protected function rules(bool $updating = false): array
    {
        $r = $this->required($updating);
        return ['name' => "$r|string|max:255", 'price' => "$r|numeric|min:0", 'stock' => "$r|integer|min:0", 'status' => 'sometimes|string|max:50', 'image' => 'sometimes|nullable|string|max:2048'];
    }
}
