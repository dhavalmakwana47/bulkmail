<?php

namespace App\Services;

use App\Enums\ContactType;
use App\Models\Contact;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Crypt;

class SubscriptionService
{
    public function generateUnsubscribeUrl(Contact $contact, int $expirationDays = 30): string
    {
        return URL::signedRoute(
            'unsubscribe',
            ['token' => Crypt::encryptString($contact->id)],
            now()->addDays($expirationDays)
        );
    }

    public function unsubscribeContact(Contact $contact): bool
    {
        if ($contact->type === ContactType::UNSUBSCRIBED) {
            return false; // Already unsubscribed
        }

        return $contact->unsubscribe();
    }

    public function isSubscribed(Contact $contact): bool
    {
        return $contact->isSubscribed();
    }

    public function getSubscribedContactsCount(int $userId): int
    {
        return Contact::where('user_id', $userId)
            ->subscribed()
            ->count();
    }

    public function getUnsubscribedContactsCount(int $userId): int
    {
        return Contact::where('user_id', $userId)
            ->where('type', ContactType::UNSUBSCRIBED)
            ->count();
    }
}
