<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogBlocklist;
use App\Services\Blog\BlogBlockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BlogBlocklistController extends Controller
{
    public function __construct(private readonly BlogBlockService $blockService) {}

    public function index(Request $request): Response
    {
        $query = BlogBlocklist::with('admin:id,name')->latest();

        if ($request->filled('search')) {
            $term = '%' . trim((string) $request->input('search')) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('source_url', 'like', $term)
                    ->orWhere('source_title', 'like', $term)
                    ->orWhere('reason', 'like', $term);
            });
        }

        return Inertia::render('Admin/Blog/Blocklist', [
            'entries' => $query->paginate(20)->withQueryString(),
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'source_url' => ['required', 'url', 'max:500'],
            'source_title' => ['nullable', 'string', 'max:500'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $this->blockService->blockUrl(
            $data['source_url'],
            $data['source_title'] ?? null,
            $data['reason'] ?: 'Manually blocked by admin',
            auth('admin')->id()
        );

        return back()->with('success', 'URL added to blog blocklist.');
    }

    public function destroy(BlogBlocklist $blocklist): RedirectResponse
    {
        $blocklist->delete();

        return back()->with('success', 'Entry removed from blocklist (unblocked).');
    }
}
