<?php
namespace App\Http\Controllers\Api;

use App\Models\Sale;

class SaleController extends CrudController
{
    protected string $model = Sale::class;

    protected function rules(bool $updating = false): array
    {
        $r = $this->required($updating);
        return ['processed_by' => "$r|exists:users,id", 'booking_id' => 'sometimes|nullable|exists:bookings,id', 'membership_id' => 'sometimes|nullable|exists:memberships,id', 'transaction_type' => "$r|in:court_rental,membership,walk_in", 'customer_name' => 'sometimes|nullable|string|max:255', 'amount_paid' => "$r|numeric|min:0", 'payment_status' => 'sometimes|in:paid,refunded'];
    }
}
