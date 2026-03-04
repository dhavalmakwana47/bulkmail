# Subscription Management System - Implementation Guide

## Overview
Production-grade subscription management system for Laravel bulk mail application with secure unsubscribe functionality.

## Features Implemented

### 1. Contact Type Enum (PHP 8.1)
- **Location**: `app/Enums/ContactType.php`
- **Values**: `SUBSCRIBED`, `UNSUBSCRIBED`
- **Default**: `SUBSCRIBED`

### 2. Database Changes
- **Migration**: `2026_03_04_104038_update_contacts_type_to_subscription_status.php`
- Updates all existing contacts to `SUBSCRIBED`
- Sets default value on `type` column
- Maintains index on `type` column for performance

### 3. Contact Model Enhancements
- **Default value**: `type = 'SUBSCRIBED'`
- **Query scope**: `subscribed()` - filters only subscribed contacts
- **Helper methods**:
  - `isSubscribed()`: Check subscription status
  - `unsubscribe()`: Unsubscribe contact with logging

### 4. Bulk Import Behavior
- All CSV/Excel imports default to `SUBSCRIBED`
- Removed `type` column from sample CSV
- Optimized for 100k+ records with chunking

### 5. Email Sending Restrictions
- **MailService**: Filters contacts at query level using `subscribed()` scope
- Applies to:
  - Bulk mail campaigns
  - Queued mail jobs
  - Resend functionality
- Prevents sending to unsubscribed contacts

### 6. Unsubscribe Footer
- **Partial**: `resources/views/emails/partials/unsubscribe-footer.blade.php`
- Automatically appended to all outgoing emails
- Uses Laravel signed URLs (30-day expiration)
- Prevents tampering and ID enumeration

### 7. Unsubscribe Route & Controller
- **Route**: `GET /unsubscribe/{contact}`
- **Rate limiting**: 10 requests per minute
- **Security**:
  - Signed URL validation
  - No authentication required
  - Protected against enumeration attacks
- **Logging**: IP address, user agent, timestamp

### 8. Unsubscribe Views
- **Success page**: Professional confirmation message
- **Error page**: Handles invalid/expired links
- **Already unsubscribed**: Graceful handling

### 9. Unsubscribe Logging
- **Table**: `unsubscribe_logs`
- **Model**: `UnsubscribeLog`
- Tracks: contact_id, ip_address, user_agent, timestamp

## Installation Steps

```bash
# Run migrations
php artisan migrate

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Usage Examples

### Query Only Subscribed Contacts
```php
// Using scope
$contacts = Contact::subscribed()->get();

// In MailService (already implemented)
$contacts = Contact::where('user_id', $userId)->subscribed()->get();
```

### Check Subscription Status
```php
if ($contact->isSubscribed()) {
    // Send email
}
```

### Manual Unsubscribe
```php
$contact->unsubscribe(); // Automatically logs to unsubscribe_logs
```

### Generate Unsubscribe Link
```php
$url = URL::signedRoute('unsubscribe', ['contact' => $contact->id], now()->addDays(30));
```

## Security Features

1. **Signed URLs**: Prevents tampering, includes expiration
2. **Rate Limiting**: 10 requests/minute prevents abuse
3. **No ID Enumeration**: Signed URLs hide contact IDs
4. **Activity Logging**: Tracks all unsubscribe events
5. **IP & User Agent**: Forensic tracking
6. **No Authentication**: Public endpoint, user-friendly

## Performance Optimizations

1. **Database Index**: `type` column indexed for fast filtering
2. **Query-Level Filtering**: Uses `WHERE` clause, not collection filtering
3. **Chunked Imports**: Handles 100k+ records efficiently
4. **Eager Loading**: Prevents N+1 queries in mail sending

## Testing Checklist

- [ ] Bulk import defaults to SUBSCRIBED
- [ ] Only subscribed contacts receive emails
- [ ] Unsubscribe link appears in all emails
- [ ] Valid unsubscribe link works correctly
- [ ] Expired link shows error message
- [ ] Already unsubscribed shows appropriate message
- [ ] Rate limiting prevents abuse
- [ ] Unsubscribe logs are created
- [ ] Activity logs track unsubscribe events
- [ ] Resend mail checks subscription status

## Future Enhancements (Optional)

### Double Opt-In System
```php
// Add to ContactType enum
case PENDING = 'PENDING';
case CONFIRMED = 'CONFIRMED';

// Workflow:
// 1. Import → PENDING
// 2. Send confirmation email
// 3. User clicks → CONFIRMED
// 4. Only send to CONFIRMED contacts
```

### Email Preference Management
```php
// New table: contact_preferences
Schema::create('contact_preferences', function (Blueprint $table) {
    $table->id();
    $table->foreignId('contact_id')->constrained();
    $table->string('preference_type'); // newsletter, promotions, updates
    $table->boolean('is_enabled')->default(true);
    $table->timestamps();
});

// Allow granular control:
// - Marketing emails
// - Transactional emails
// - Newsletter
// - Product updates
```

### Resubscribe Functionality
```php
// Add route
Route::get('/resubscribe/{contact}', [UnsubscribeController::class, 'resubscribe'])
    ->name('resubscribe')
    ->middleware('throttle:10,1');

// Add method to Contact model
public function resubscribe(): bool
{
    return $this->update(['type' => ContactType::SUBSCRIBED]);
}
```

### Unsubscribe Reasons
```php
// Add to unsubscribe_logs table
$table->string('reason')->nullable(); // too_frequent, not_relevant, etc.
$table->text('feedback')->nullable();

// Collect feedback on unsubscribe page
// Analyze reasons to improve email strategy
```

## API Endpoints (If Needed)

```php
// routes/api.php
Route::post('/api/unsubscribe', [UnsubscribeController::class, 'apiUnsubscribe'])
    ->middleware('throttle:10,1');

// For email clients that don't support HTML links
// POST with email parameter
```

## Compliance Notes

- **CAN-SPAM Act**: Unsubscribe link in every email ✓
- **GDPR**: Right to opt-out ✓
- **CASL**: Clear unsubscribe mechanism ✓
- **Link Expiration**: 30 days (configurable)
- **Processing Time**: Immediate unsubscribe

## Troubleshooting

### Unsubscribe Link Not Working
- Check APP_URL in .env
- Verify signed route configuration
- Check rate limiting settings

### Emails Still Sending to Unsubscribed
- Verify `subscribed()` scope is used
- Check Contact model casts
- Clear application cache

### Migration Fails
- Ensure doctrine/dbal is installed: `composer require doctrine/dbal`
- Check database connection
- Verify column exists before modifying

## Support

For issues or questions, refer to:
- Laravel Signed URLs: https://laravel.com/docs/urls#signed-urls
- Laravel Rate Limiting: https://laravel.com/docs/routing#rate-limiting
- Laravel Enums: https://laravel.com/docs/eloquent-mutators#enum-casting
