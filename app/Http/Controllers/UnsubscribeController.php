<?php

namespace App\Http\Controllers;

use App\Enums\ActionType;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class UnsubscribeController extends Controller
{
    public function unsubscribe(Request $request, string $token)
    {
        if (!$request->hasValidSignature()) {
            return view('unsubscribe.error', [
                'message' => 'This unsubscribe link is invalid or has expired.'
            ]);
        }

        try {
            $contactId = Crypt::decryptString($token);
            $contact = Contact::findOrFail($contactId);
        } catch (\Exception $e) {
            return view('unsubscribe.error', [
                'message' => 'Invalid unsubscribe link.'
            ]);
        }

        $contactType = is_string($contact->type) ? $contact->type : $contact->type->value;

        if ($contactType === 'UNSUBSCRIBED') {
            return view('unsubscribe.success', [
                'message' => 'You are already unsubscribed from our mailing list.',
                'alreadyUnsubscribed' => true
            ]);
        }

        $contact->unsubscribe();

        activity_log('Contact', ActionType::UPDATE, $contact, null, [
            'action' => 'unsubscribed',
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        Log::info('Contact unsubscribed', [
            'contact_id' => $contact->id,
            'email' => $contact->email,
            'ip' => $request->ip()
        ]);

        return view('unsubscribe.success', [
            'message' => 'You have been successfully unsubscribed from our mailing list.',
            'alreadyUnsubscribed' => false
        ]);
    }
}
