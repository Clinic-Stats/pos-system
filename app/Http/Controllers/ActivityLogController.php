<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::query();

        if ($request->filled('from_date')) $query->whereDate('created_at', '>=', $request->from_date);
        if ($request->filled('to_date'))   $query->whereDate('created_at', '<=', $request->to_date);
        if ($request->filled('user_id'))   $query->where('user_id', $request->user_id);
        if ($request->filled('action'))    $query->where('action', $request->action);
        if ($request->filled('type'))      $query->where('type', $request->type);
        if ($request->filled('q')) {
            $q = '%' . $request->q . '%';
            $query->where(fn($w) => $w->where('label', 'like', $q)->orWhere('party', 'like', $q)->orWhere('user_name', 'like', $q));
        }

        $counts = (clone $query)->selectRaw('action, COUNT(*) as c')->groupBy('action')->pluck('c', 'action');
        $logs   = $query->latest('created_at')->latest('id')->paginate(30)->withQueryString();
        $users  = User::orderBy('name')->get(['id', 'name']);

        return view('audit.index', compact('logs', 'users', 'counts'));
    }
}