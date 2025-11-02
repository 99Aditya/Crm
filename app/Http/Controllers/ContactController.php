<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\CustomField;
use App\Traits\HandlesCustomFields;


class ContactController extends Controller
{
    
    use HandlesCustomFields;

    public function index()
    {
        $contacts = Contact::where('is_active',1)->get();
        $customFields = CustomField::all();
        return view('contacts.index', compact('contacts', 'customFields'));
    }

    public function store(Request $request)
    {
        $data = $request->except(['custom_fields']);
        
        if ($path = $this->uploadFile($request, 'profile_image', 'profiles')) {
            $data['profile_image'] = $path;
        }

        if ($path = $this->uploadFile($request, 'additional_file', 'files')) {
            $data['additional_file'] = $path;
        }

        $contact = Contact::updateOrCreate(['id' => $request->id], $data);

        if ($request->has('custom_fields')) {
            $this->saveCustomFields($contact, $request->custom_fields);
        }

        return response()->json(['success' => true, 'message' => 'Contact saved successfully']);
    }

    public function destroy($id=0)
    {
        $contact = Contact::findOrFail($id);
         $contact->delete();
        return response()->json(['success' => true, 'message' => 'Contact deleted']);
    }

    public function edit($id =0)
    {
        $contact = Contact::with('customFieldValues')->findOrFail($id);
        $customValues = $contact->customFieldValues->pluck('value', 'custom_field_id');
        $responseData = [
            'contact' => [
                'id' => $contact->id,
                'name' => $contact->name,
                'email' => $contact->email,
                'phone' => $contact->phone,
                'gender' => $contact->gender,
                'profile_image_url' => $contact->profile_image_url,
                'additional_file_url' => $contact->additional_file_url,
                'additional_file_name' => $contact->additional_file_name
            ],
            'customValues' => $customValues
        ];
        return response()->json($responseData);
    }

    public function getContact(Request $request) {
        $query = Contact::query();

        if ($request->name) {
            $query->where('name', 'like', "%{$request->name}%");
        }

        if ($request->email) {
            $query->where('email', 'like', "%{$request->email}%");
        }

        if ($request->gender) {
            $query->where('gender', $request->gender);
        }
        $contacts = $query->where('is_active',1)->get();

        return response()->json([
            'contacts' => $contacts
        ]);   
    }

    public function checkEmail(Request $request){
        $email = $request->input('email');
        $contactId = $request->input('id');

        $query = Contact::where('email', $email);
        if ($contactId) {
            $query->where('id', '!=', $contactId);
        }

        $exists = $query->exists();

        return response()->json(['exists' => $exists]);
        
    }

}
