<?php

declare(strict_types=1);

namespace App\Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Services\AdminOperationsService;
use App\Modules\Client\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class ClientController extends Controller
{
    public function __construct(private readonly AdminOperationsService $service) {}

    public function index(): View
    {
        return view('admin::pages.clients.index', $this->service->clientsPageData());
    }

    public function create(): View
    {
        return view('admin::pages.clients.create');
    }

    public function store(): RedirectResponse
    {
        return redirect()->route('admin.clients.index')->with('success', 'Client created successfully.');
    }

    public function show(Client $client): View
    {
        return view('admin::pages.clients.show', compact('client'));
    }

    public function edit(Client $client): View
    {
        return view('admin::pages.clients.edit', compact('client'));
    }

    public function update(Client $client): RedirectResponse
    {
        return redirect()->route('admin.clients.show', $client)->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        return redirect()->route('admin.clients.index')->with('success', 'Client archived successfully.');
    }

    public function duePdf(Client $client): RedirectResponse
    {
        return back()->with('success', 'Due PDF generation requested.');
    }
}
