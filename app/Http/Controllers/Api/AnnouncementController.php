<?php
namespace App\Http\Controllers\Api;

use App\Models\Announcement;

class AnnouncementController extends CrudController
{
    protected string $model = Announcement::class;

    protected function rules(bool $updating = false): array
    {
        $r = $this->required($updating);
        return ['title' => "$r|string|max:255", 'content' => "$r|string", 'status' => 'sometimes|string|max:50', 'target_audience' => 'sometimes|in:all,athlete,staff', 'pinned' => 'sometimes|boolean'];
    }
}
