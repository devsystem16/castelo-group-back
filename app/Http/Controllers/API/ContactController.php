<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'phone'       => 'required|string|max:20',
            'email'       => 'required|email',
            'message'     => 'required|string|max:2000',
            'property_id' => 'nullable|exists:properties,id',
        ]);

        $contact = Contact::create($data);

        return response()->json(['data' => $contact, 'message' => 'Mensaje enviado correctamente.'], 201);
    }

    public function index(Request $request)
    {
        $contacts = Contact::with('property')
            ->latest()
            ->paginate(20);

        return response()->json($contacts);
    }

    public function markRead(Contact $contact)
    {
        $contact->update(['read' => true]);
        return response()->json(['data' => $contact]);
    }
}
