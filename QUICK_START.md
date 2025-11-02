# Real-Time Email Sync - Quick Start Guide

## 🚀 Get Started in 5 Minutes

### Step 1: Run Migrations
```bash
php artisan migrate
```

This creates:
- `graph_subscriptions` - Tracks Microsoft Graph subscriptions
- `webhook_deliveries` - Logs webhook notifications

### Step 2: Configure Environment
Add to your `.env` file:

```bash
# Microsoft Azure (Already configured)
AZURE_CLIENT_ID=your-existing-client-id
AZURE_CLIENT_SECRET=your-existing-secret
AZURE_TENANT_ID=common
AZURE_REDIRECT_URI=https://your-domain.com/mail/outlook/callback

# NEW: Webhook Configuration
AZURE_WEBHOOK_URL=https://your-domain.com/api/webhook/subscriptionNotification
AZURE_WEBHOOK_CLIENT_STATE=RBAcentria-SecretToken-12345

# Broadcasting (Already should be set)
BROADCAST_DRIVER=reverb
QUEUE_CONNECTION=database
```

**For Local Development:**
```bash
# Start ngrok
ngrok http 8000

# Copy the HTTPS URL and update .env
AZURE_WEBHOOK_URL=https://abc123.ngrok-free.app/api/webhook/subscriptionNotification
```

### Step 3: Start Required Services
Open 3 terminals:

**Terminal 1 - Queue Worker:**
```bash
php artisan horizon
```

**Terminal 2 - WebSocket Server:**
```bash
php artisan reverb:start
```

**Terminal 3 - App Server (if using artisan serve):**
```bash
php artisan serve
```

### Step 4: Test Your Setup
```bash
php artisan emails:test-realtime
```

Expected output:
```
✓ All database tables exist
✓ Environment configured correctly
✓ Horizon is running
✓ Reverb is running
✓ Outlook connections are valid
✅ All tests passed!
```

### Step 5: Create a Subscription
```bash
# Replace '1' with your user ID
php artisan emails:test-realtime --user=1 --create-subscription
```

Expected output:
```
━━━ Creating Test Subscription ━━━━━━━━━━━━━━━━━━━━━━
✓ Subscription created successfully!
Subscription ID: abc-123-def-456
Expires: 2025-01-05T12:00:00Z
```

## ✅ Verify It's Working

### 1. Check Subscription Status
```bash
php artisan tinker
```
```php
>>> App\Models\GraphSubscription::active()->get()
```

### 2. Send Test Email
- Send an email to the connected Outlook account
- Check terminal logs for webhook delivery
- Frontend should show notification

### 3. Monitor Webhooks
```bash
# View recent webhook deliveries
php artisan tinker
```
```php
>>> App\Models\WebhookDelivery::latest()->limit(5)->get()
```

## 🔧 Common Issues & Fixes

### Issue: "Webhook URL not accessible"
**Solution:**
```bash
# For local development, use ngrok
ngrok http 8000
# Update AZURE_WEBHOOK_URL in .env with ngrok URL
```

### Issue: "Horizon not running"
**Solution:**
```bash
php artisan horizon
# Keep this terminal open
```

### Issue: "Reverb not running"
**Solution:**
```bash
php artisan reverb:start
# Keep this terminal open
```

### Issue: "Subscription creation failed"
**Solution:**
```bash
# Check OAuth token is valid
php artisan emails:test-realtime --user=1

# Re-authenticate if needed
# Visit: http://localhost:8000/mail
# Click "Connect Outlook"
```

## 📊 How to Monitor

### View Horizon Dashboard
```
http://localhost:8000/horizon
```

### Check Active Subscriptions
```bash
php artisan tinker
```
```php
>>> App\Models\GraphSubscription::active()->count()
>>> App\Models\GraphSubscription::expiringSoon(24)->get()
```

### Check Webhook Log
```bash
tail -f storage/logs/laravel.log | grep webhook
```

## 🎯 Testing Real-Time Features

### Test 1: Manual Sync
1. Visit http://localhost:8000/mail
2. Click the refresh button (top right)
3. Watch progress indicator
4. Emails load via WebSocket

### Test 2: New Email Notification
1. Keep mail page open
2. Send email to your Outlook account
3. Within 1-2 minutes:
   - Browser notification appears
   - Refresh banner shows
   - Unread count updates

### Test 3: Folder Navigation
1. Click different folders (Inbox, Sent, etc.)
2. Emails filter correctly
3. Reply/Edit/View buttons work

## 📋 Daily Commands

```bash
# Test configuration
php artisan emails:test-realtime

# Sync all users
php artisan emails:sync-all

# Check subscriptions
php artisan tinker
>>> App\Models\GraphSubscription::active()->get()
```

## 🔄 Automatic Renewal (Optional)

Add to `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    // Sync emails every 5 minutes
    $schedule->command('emails:sync-all --active-only')
        ->everyFiveMinutes();

    // Note: Subscriptions auto-renew via webhook lifecycle
}
```

Then start scheduler:
```bash
php artisan schedule:work
```

## ✨ Features Now Available

- ✅ **Real-Time Email Sync** - Delta queries every 5 min
- ✅ **WebSocket Notifications** - Instant browser alerts
- ✅ **Folder Organization** - Inbox, Sent, Drafts, etc.
- ✅ **Clickable Actions** - Reply, Edit, View, Archive
- ✅ **Webhook Subscriptions** - Microsoft Graph API
- ✅ **Automatic Renewal** - Subscriptions auto-renew
- ✅ **Full Diagnostics** - Test command verifies everything

## 📚 Documentation

- **Complete Guide**: `REALTIME_EMAIL_IMPLEMENTATION.md`
- **Original Setup**: `EMAIL_SYNC_README.md`
- **This Guide**: `QUICK_START.md`

## 🆘 Need Help?

1. Run diagnostics:
   ```bash
   php artisan emails:test-realtime
   ```

2. Check logs:
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. Verify services:
   ```bash
   # Horizon
   ps aux | grep horizon

   # Reverb
   ps aux | grep reverb
   ```

## 🎉 You're Done!

Visit **http://localhost:8000/mail** and enjoy real-time email sync!

---

**Next Steps:**
1. Connect your Outlook account if not already
2. Watch for new email notifications
3. Test folder navigation
4. Try reply/edit features

**Production Deployment:**
- Replace ngrok with your actual domain
- Set up SSL certificate
- Configure firewall for webhooks
- Enable automatic backups
