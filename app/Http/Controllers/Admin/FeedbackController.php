<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FeedbackController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Feedback::query()->with('student:id,name,email')->latest();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        return Inertia::render('Admin/Feedback/Index', [
            'feedback' => $query->paginate(20)->withQueryString(),
            'filters' => $request->only('status', 'type'),
        ]);
    }

    public function update(Request $request, Feedback $feedback): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:new,reviewed,resolved'],
            'admin_note' => ['nullable', 'string', 'max:5000'],
        ]);

        $feedback->update($data);

        return back()->with('success', app()->getLocale() === 'bn' ? 'ফিডব্যাক আপডেট হয়েছে।' : 'Feedback updated.');
    }
}
