<?php
namespace App\Traits;

use App\Models\ContactCustomFieldValue;

trait HandlesCustomFields
{
    protected function saveCustomFields($contact, $customFields)
    {
        foreach ($customFields as $fieldId => $value) {
            ContactCustomFieldValue::updateOrCreate(
                ['contact_id' => $contact->id, 'custom_field_id' => $fieldId],
                ['value' => $value]
            );
        }
    }

    public function uploadFile($request, string $fieldName, string $folderName): ?string
    {
        if ($request->hasFile($fieldName)) {
            $file = $request->file($fieldName);
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path($folderName), $filename);
            return $folderName . '/' . $filename;
        }

        return null;
    }
}
