<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::with(['media' => fn($q) => $q->where('media_type', 'photo')->orderByDesc('is_cover')->orderBy('order')->limit(1)])
            ->published();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('province', 'like', "%{$search}%")
                  ->orWhere('canton', 'like', "%{$search}%");
            });
        }

        if ($request->filled('province')) {
            $query->where('province', $request->province);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('min_area')) {
            $query->where('area_m2', '>=', $request->min_area);
        }

        if ($request->filled('max_area')) {
            $query->where('area_m2', '<=', $request->max_area);
        }

        match ($request->get('sort', 'created_at_desc')) {
            'price_asc'       => $query->orderBy('price', 'asc'),
            'price_desc'      => $query->orderBy('price', 'desc'),
            'views_desc'      => $query->orderBy('views', 'desc'),
            default           => $query->orderBy('created_at', 'desc'),
        };

        $perPage = min((int) $request->get('per_page', 12), 50);
        $properties = $query->paginate($perPage);

        return response()->json($properties);
    }

    public function show(Property $property)
    {
        $property->incrementViews();
        $property->load(['media' => fn($q) => $q->orderByDesc('is_cover')->orderBy('order')]);

        $data              = $property->toArray();
        $data['cover']     = $property->media->firstWhere('is_cover', true);
        $data['thumbnails'] = $property->media->where('is_cover', false)->where('media_type', 'photo')->values();

        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Property::class);

        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'type'             => 'required|in:urbano,rural,agricola,comercial,industrial',
            'province'         => 'required|string|max:100',
            'canton'           => 'required|string|max:100',
            'price'            => 'required|numeric|min:0',
            'area_m2'          => 'required|numeric|min:0',
            'status'           => 'in:disponible,reservado,vendido',
            'soil_type'        => 'nullable|string|max:100',
            'access_services'  => 'nullable|string',
            'legal_documents'  => 'nullable|string',
            'latitude'         => 'nullable|numeric',
            'longitude'        => 'nullable|numeric',
            'published'        => 'boolean',
        ]);

        $property = Property::create([...$data, 'created_by' => $request->user()->id]);

        return response()->json(['data' => $property], 201);
    }

    public function update(Request $request, Property $property)
    {
        $this->authorize('update', $property);

        $data = $request->validate([
            'title'            => 'sometimes|required|string|max:255',
            'description'      => 'nullable|string',
            'type'             => 'sometimes|required|in:urbano,rural,agricola,comercial,industrial',
            'province'         => 'sometimes|required|string|max:100',
            'canton'           => 'sometimes|required|string|max:100',
            'price'            => 'sometimes|required|numeric|min:0',
            'area_m2'          => 'sometimes|required|numeric|min:0',
            'status'           => 'in:disponible,reservado,vendido',
            'soil_type'        => 'nullable|string|max:100',
            'access_services'  => 'nullable|string',
            'legal_documents'  => 'nullable|string',
            'latitude'         => 'nullable|numeric',
            'longitude'        => 'nullable|numeric',
            'published'        => 'boolean',
        ]);

        $property->update($data);

        return response()->json(['data' => $property->fresh()]);
    }

    public function destroy(Property $property)
    {
        $this->authorize('delete', $property);
        $property->delete();
        return response()->json(null, 204);
    }

    public function uploadMedia(Request $request, Property $property)
    {
        $this->authorize('update', $property);

        $request->validate([
            'cover'          => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:10240',
            'thumbnails'     => 'nullable|array|max:10',
            'thumbnails.*'   => 'file|mimes:jpg,jpeg,png,gif,webp|max:10240',
        ]);

        $uploaded = [];

        // Portada: reemplaza la existente si ya había una
        if ($request->hasFile('cover')) {
            $existing = $property->media()->where('is_cover', true)->first();
            if ($existing) {
                $this->deleteFile($existing->url);
                $existing->delete();
            }

            $file = $request->file('cover');
            $path = $file->store("properties/{$property->id}", 'public');

            $uploaded[] = PropertyMedia::create([
                'property_id' => $property->id,
                'media_type'  => 'photo',
                'is_cover'    => true,
                'url'         => Storage::disk('public')->url($path),
                'filename'    => $file->getClientOriginalName(),
                'order'       => 0,
            ]);
        }

        // Miniaturas (hasta 10 en total por propiedad)
        if ($request->hasFile('thumbnails')) {
            $currentCount = $property->media()->where('is_cover', false)->where('media_type', 'photo')->count();
            $slots        = max(0, 10 - $currentCount);
            $order        = $property->media()->where('is_cover', false)->max('order') ?? 0;

            foreach (array_slice($request->file('thumbnails'), 0, $slots) as $file) {
                $path = $file->store("properties/{$property->id}", 'public');

                $uploaded[] = PropertyMedia::create([
                    'property_id' => $property->id,
                    'media_type'  => 'photo',
                    'is_cover'    => false,
                    'url'         => Storage::disk('public')->url($path),
                    'filename'    => $file->getClientOriginalName(),
                    'order'       => ++$order,
                ]);
            }
        }

        return response()->json(['data' => $uploaded], 201);
    }

    public function deleteMedia(Request $request, Property $property, PropertyMedia $media)
    {
        $this->authorize('update', $property);
        $this->deleteFile($media->url);
        $media->delete();
        return response()->json(null, 204);
    }

    private function deleteFile(string $url): void
    {
        // Funciona tanto con URLs absolutas como relativas (/storage/...)
        $base = Storage::disk('public')->url('');
        $relative = str_starts_with($url, 'http')
            ? ltrim(str_replace($base, '', $url), '/')
            : ltrim(str_replace('/storage/', '', $url), '/');
        Storage::disk('public')->delete($relative);
    }
}
