<?php

namespace App\Http\Controllers;
use App\Models\CustomField;
use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\MergeHistory;

class CustomFieldController extends Controller
{
    public function index()
    {
        $fields = CustomField::all();
        return view('custom_fields.index', compact('fields'));
    }

    public function store(Request $request)
    {
        try {
            $fields = $request->input('fields', []);

            if (empty($fields)) {
                return response()->json([
                    'status' => false,
                    'message' => 'No fields provided.'
                ], 422);
            }

            $savedFields = [];

            foreach ($fields as $field) {
                if (empty($field['field_name'])) {
                    continue;
                }

                $saved = CustomField::create([
                    'field_name' => $field['field_name'],
                ]);

                $savedFields[] = $saved;
            }

            return response()->json([
                'status' => true,
                'message' => 'Custom fields created successfully.',
                'fields' => $savedFields
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $th->getMessage()
            ], 500);
        }
    }

    public function merge(Request $request){
        $primary = Contact::with('meta')->findOrFail($request->primary_id);
        $secondary = Contact::with('meta')->findOrFail($request->secondary_id);
   
        foreach ($secondary->meta as $meta) {
            $existing = $primary->meta->where('custom_field_id', $meta->custom_field_id)->first();
            if(empty($existing->value)){
                $existing->value = $meta->value;
                $existing->save();
            }
        }

        $secondary->update(['merged_into' => $primary->id, 'is_active' => false]);

        MergeHistory::create([
            'primary_id' => $primary->id,
            'secondary_id' => $secondary->id,
            'merged_at' => now(),
            'details' => json_encode(['action' => 'merge']),
        ]);

        return response()->json([
            'message' => 'Contacts merged successfully!',
            'primary_id' => $primary->id
        ]);
    }

   
}
