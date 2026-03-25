<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Infra\SentEmail;
use Illuminate\Http\Request;

class SentEmailController extends Controller
{
    public function index(Request $request)
    {
        $query = SentEmail::query();

        // Фильтр по email
        if ($request->filled('email')) {
            $query->where('recipient_email', 'like', '%' . $request->email . '%');
        }

        // Фильтр по статусу
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Фильтр по дате отправки (начало)
        if ($request->filled('sent_from')) {
            $query->whereDate('sent_at', '>=', $request->sent_from);
        }

        // Фильтр по дате отправки (конец)
        if ($request->filled('sent_to')) {
            $query->whereDate('sent_at', '<=', $request->sent_to);
        }

        // Сортировка
        $sortBy    = $request->get('sort_by', 'sent_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $emails = $query->paginate(20)->withQueryString();

        // Список доступных статусов для фильтра
        $statuses = [
            SentEmail::STATUS_SENT,
            SentEmail::STATUS_DELIVERED,
            SentEmail::STATUS_OPENED,
            SentEmail::STATUS_CLICKED,
            SentEmail::STATUS_FAILED,
        ];

        return view('admin.emails.index', compact('emails', 'statuses'));
    }

    public function show(SentEmail $email)
    {
        return view('admin.emails.show', compact('email'));
    }

    public function destroy(SentEmail $email)
    {
        $email->delete();

        return redirect()->route('admin.emails.index')->with('success', 'Письмо удалено');
    }
}