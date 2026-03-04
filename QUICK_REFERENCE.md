# Subscription Management - Quick Reference

## Installation

```bash
# 1. Run migrations
php artisan migrate

# 2. Clear caches
php artisan config:clear
php artisan route:clear
```

## Key Files Modified

### Models
- `app/Models/Contact.php` - Added subscription methods and scope
- `app/Models/UnsubscribeLog.php` - New model for tracking

### Enums
- `app/Enums/ContactType.php` - Changed to SUBSCRIBED/UNSUBSCRIBED

### Controllers
- `app/Http/Controllers/ContactController.php` - Import defaults to SUBSCRIBED
- `app/Http/Controllers/UnsubscribeController.php` - New controller

### Services
- `app/Services/MailService.php` - Filters subscribed, adds footer
- `app/Services/SubscriptionService.php` - New service

### Views
- `resources/views/emails/partials/unsubscribe-footer.blade.php` - Email footer
- `resources/views/unsubscribe/success.blade.php` - Success page
- `resources/views/unsubscribe/error.blade.php` - Error page

### Routes
- `routes/web.php` - Added public unsubscribe route

### Migrations
- `2026_03_04_104038_update_contacts_type_to_subscription_status.php`
- `2026_03_04_104341_add_unsubscribe_logs_table.php`

## Code Examples

### Filter Subscribed Contacts
```php
$contacts = Contact::subscribed()->get();
```

### Check Status
```php
if ($contact->isSubscribed()) {
    // Send email
}
```

### Unsubscribe
```php
$contact->unsubscribe(); // Logs automatically
```

### Generate Link
```php
$service = app(SubscriptionService::class);
$url = $service->generateUnsubscribeUrl($contact);
```

### Get Statistics
```php
$service = app(SubscriptionService::class);
$subscribed = $service->getSubscribedContactsCount($userId);
$unsubscribed = $service->getUnsubscribedContactsCount($userId);
```

## Security Features

✓ Signed URLs (30-day expiration)
✓ Rate limiting (10/minute)
✓ No ID enumeration
✓ Activity logging
✓ IP tracking

## Testing URLs

```
# Valid unsubscribe (generated from email)
https://yourdomain.com/unsubscribe/123?signature=...

# Invalid signature
https://yourdomain.com/unsubscribe/123?signature=invalid
→ Shows error page

# Expired link
→ Shows error page

# Already unsubscribed
→ Shows "already unsubscribed" message
```

## Database Schema

### contacts table
- `type` column: SUBSCRIBED (default) | UNSUBSCRIBED
- Indexed for performance

### unsubscribe_logs table
- `contact_id` (foreign key)
- `ip_address`
- `user_agent`
- `unsubscribed_at`

## Important Notes

1. **All imports default to SUBSCRIBED** - No need to specify type
2. **Emails only sent to subscribed** - Automatic filtering
3. **Footer added automatically** - No manual intervention
4. **Unsubscribe is permanent** - No resubscribe (can be added)
5. **Logs are immutable** - Audit trail preserved

## Troubleshooting

### Issue: Unsubscribe link not working
**Solution**: Check APP_URL in .env matches your domain

### Issue: Emails still sending to unsubscribed
**Solution**: Verify `subscribed()` scope is used in queries

### Issue: Migration fails
**Solution**: Install doctrine/dbal: `composer require doctrine/dbal`

## Performance Tips

- Use `subscribed()` scope for query-level filtering
- Index on `type` column already exists
- Chunked imports handle 100k+ records
- Eager load relationships to prevent N+1

## Compliance

✓ CAN-SPAM Act compliant
✓ GDPR compliant (right to opt-out)
✓ CASL compliant
✓ Immediate processing
✓ Clear unsubscribe mechanism
