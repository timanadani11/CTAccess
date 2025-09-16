<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Persona;
use App\Http\Requests\StorePersonaRequest;
use App\Http\Requests\UpdatePersonaRequest;
use App\Http\Resources\PersonaResource;
use App\Services\PersonaService;
use Illuminate\Support\Facades\DB;

class PersonaController extends Controller
{
    public function __construct(private PersonaService $service)
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $perPage = (int) request()->get('per_page', 15);
        $query = Persona::query()->orderByDesc('idPersona');

        if (request()->boolean('with_relations')) {
            $query->with(['portatiles', 'vehiculos']);
        }

        $paginator = $query->paginate($perPage);
        return PersonaResource::collection($paginator)
            ->additional(['status' => 'success'])
            ->response();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePersonaRequest $request): JsonResponse
    {
        try {
            $persona = $this->service->createWithRelations($request->validated());
            return (new PersonaResource($persona))
                ->additional(['status' => 'success', 'message' => 'Persona creada correctamente'])
                ->response()
                ->setStatusCode(201);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo crear la persona',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Persona $persona)
    {
        $persona->load(['portatiles', 'vehiculos']);
        return (new PersonaResource($persona))
            ->additional(['status' => 'success'])
            ->response();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePersonaRequest $request, Persona $persona): JsonResponse
    {
        try {
            $updated = $this->service->updateWithRelations($persona, $request->validated());
            return (new PersonaResource($updated))
                ->additional(['status' => 'success', 'message' => 'Persona actualizada correctamente'])
                ->response()
                ->setStatusCode(200);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo actualizar la persona',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Persona $persona): JsonResponse
    {
        try {
            DB::transaction(function () use ($persona) {
                // Delete related models first to respect FK constraints
                $persona->portatiles()->delete();
                $persona->vehiculos()->delete();
                $persona->delete();
            });
            return response()->json([
                'status' => 'success',
                'message' => 'Persona eliminada correctamente',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo eliminar la persona',
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}
