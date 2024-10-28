<?php declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Core\Domains\Access\RoleLocator;
use Core\Domains\Access\Services\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class TemplateController extends Controller
{
    private RoleService $roleService;

    public function __construct()
    {
        $this->roleService = RoleLocator::RoleService();
    }

    public function get(Request $request): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        $template = $request->get('template');
        if (view()->exists($template)) {
            $content = File::get(resource_path('views/' . Str::replace('.', '/', $template) . '.blade.php'));

            return response()->json($content);
        }

        abort(422);
    }

    public function update(Request $request): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        $template = $request->get('template');
        $content  = $request->get('content');
        if (view()->exists($template) && $content) {
            File::put(resource_path('views/' . Str::replace('.', '/', $template) . '.blade.php'), $content);

            return response()->json(true);
        }

        return response()->json(false);
    }

    private function canEdit(): bool
    {
        return \app::roleDecorator()->canEditTemplates();
    }
}
