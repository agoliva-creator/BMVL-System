<?php
namespace App\Http\Controllers\Api;

use App\Models\Coach;

class CoachController extends CrudController
{
    protected string $model = Coach::class;

    protected function rules(bool $updating = false): array
    {
        $r = $this->required($updating);
        return ['name' => "$r|string|max:255", 'sport_type' => "$r|string|max:255", 'hourly_rate' => "$r|numeric|min:0", 'photo_path' => 'sometimes|nullable|string|max:2048', 'status' => 'sometimes|in:available,on_leave'];
    }
}
