<?php

declare(strict_types=1);

namespace App\Modules\Manager\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Modules\Client\Models\Client;
use App\Modules\Manager\Http\Requests\Client\StoreClientRequest;
use App\Modules\Manager\Http\Requests\Client\UpdateClientRequest;
use App\Modules\Manager\Services\ClientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ClientController extends Controller
{
    public function __construct(private readonly ClientService $clientService)
    {
        $this->authorizeResource(Client::class, 'client');
    }

    public function index(Request $request): View|JsonResponse
    {
        $filters = $request->only(['search', 'client_type', 'status']);
        $clients = $this->clientService->getFilteredPaginated($filters);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('manager::pages.clients.partials._table_with_pagination', [
                    'clients' => $clients,
                ])->render(),
            ]);
        }

        return view('manager::pages.clients.index', [
            'clients' => $clients,
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('manager::pages.clients.create');
    }

    public function store(StoreClientRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $plainPassword = (string) ($validated['password'] ?? Str::password(14));

        $client = $this->clientService->create($validated + [
            'password' => $plainPassword,
            'created_by' => (int) $request->user()->id,
        ])->loadMissing('user:id,email');

        return response()->json([
            'message' => 'Client created successfully.',
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
            ],
            'credentials' => [
                'email' => $client->user?->email,
                'phone' => $validated['phone_number'] ?? null,
                'password' => $plainPassword,
            ],
        ], 201);
    }

    public function show(Client $client): View
    {
        $clientWithStats = $this->clientService->getClientWithStats($client);

        return view('manager::pages.clients.show', [
            'client' => $clientWithStats,
            'stats' => $this->clientService->getStats($clientWithStats),
        ]);
    }

    public function edit(Client $client): View
    {
        return view('manager::pages.clients.edit', [
            'client' => $client->load(['user:id,name,phone', 'category:id,name']),
        ]);
    }

    public function update(UpdateClientRequest $request, Client $client): JsonResponse
    {
        $this->clientService->update($client, $request->validated());

        return response()->json([
            'message' => 'Client updated successfully.',
        ]);
    }

    public function destroy(Client $client): JsonResponse
    {
        try {
            $this->clientService->delete($client);
        } catch (HttpException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->getStatusCode());
        }

        return response()->json([
            'message' => 'Client deleted successfully.',
        ]);
    }

    public function toggleStatus(Client $client): JsonResponse
    {
        $this->authorize('toggleStatus', $client);

        $updatedClient = $this->clientService->toggleStatus($client);

        return response()->json([
            'message' => 'Client status updated successfully.',
            'status' => $updatedClient->status,
        ]);
    }
}
