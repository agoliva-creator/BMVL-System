<?php
namespace App\Http\Controllers\Api;

use App\Models\Membership;

class MembershipController extends CrudController
{
    protected string $model = Membership::class;

    protected function rules(bool $updating = false): array
    {
        $r = $this->required($updating);
        return ['user_id' => "$r|exists:users,id", 'plan_type' => "$r|in:monthly,quarterly,annual", 'amount' => "$r|numeric|min:0", 'start_date' => "$r|date", 'end_date' => "$r|date|after_or_equal:start_date", 'status' => 'sometimes|in:pending,active,expired', 'receipt_image' => 'sometimes|nullable|string|max:2048'];
    }
}
