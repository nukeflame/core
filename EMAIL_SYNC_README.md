# Real-Time Email Synchronization Implementation

## Overview
This implementation provides real-time Outlook email synchronization using Microsoft Graph API delta queries with Laravel Reverb WebSocket broadcasting for live updates.

## Features Implemented

### 1. **Microsoft Graph Delta Query Integration**
- ✅ Incremental sync using delta tokens
- ✅ Supports all folders (not just inbox)
- ✅ Handles pagination automatically
- ✅ Tracks new, updated, and deleted messages
- ✅ Stores delta token for next sync

**Location**: `app/Services/OutlookService.php:4012`
```php
public function getDeltaMessages($user, ?string $deltaLink = null): array
```

### 2. **Background Email Sync Job**
- ✅ Queue-based sync using Laravel Horizon
- ✅ Broadcasts real-time progress via WebSocket
- ✅ Handles sync state management
- ✅ Prevents concurrent syncs for same user
- ✅ Broadcasts new email events

**Location**: `app/Jobs/SyncUserEmails.php`

### 3. **Real-Time WebSocket Events**

#### Email Sync Progress Event
**Location**: `app/Events/EmailSyncProgress.php`
- Broadcasts sync progress (processed, total, inserted, updated, deleted)
- Channel: `private-email-sync.{userId}`
- Event: `sync.progress`

#### Email Sync Completed Event
**Location**: `app/Events/EmailSyncCompleted.php`
- Broadcasts when sync finishes successfully
- Channel: `private-email-sync.{userId}`
- Event: `sync.completed`

#### New Email Received Event (NEW)
**Location**: `app/Events/NewEmailReceived.php`
- Broadcasts when new emails arrive
- Channel: `private-email-sync.{userId}`
- Event: `email.new`
- Payload includes email preview data

### 4. **Automatic Background Sync Command**
**Location**: `app/Console/Commands/SyncAllUsersEmails.php`

```bash
# Sync all users with active Outlook connections
php artisan emails:sync-all

# Sync specific user
php artisan emails:sync-all --user=123

# Only sync users with active tokens
php artisan emails:sync-all --active-only
```

**Schedule in `app/Console/Kernel.php`:**
```php
$schedule->command('emails:sync-all --active-only')->everyFiveMinutes();
```

### 5. **Folder-Based Email Organization**
**Location**: `app/Services/EmailStorageService.php`

Supports filtering by:
- `inbox` - Inbox folder
- `sent` - Sent items
- `drafts` - Draft messages
- `spam` - Spam/Junk folder
- `trash` - Deleted items
- `archive` - Archived messages
- `starred` - Flagged/starred emails
- `important` - High importance emails
- `all` - All folders

### 6. **Enhanced Mail Controller**

#### New Endpoint: Trigger Manual Sync
**Route**: `POST /mail/sync/trigger`
**Controller**: `app/Http/Controllers/MailController.php:388`

```javascript
fetch('/mail/sync/trigger', {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': csrfToken
    }
})
.then(response => response.json())
.then(data => console.log(data));
```

#### Enhanced Endpoint: Check New Emails
**Route**: `GET /mail/check-new`
**Returns**: Unread email count from database

### 7. **Frontend Real-Time Integration**
**Location**: `resources/views/mail/index.blade.php:170-247`

Features:
- ✅ Listens to sync progress events
- ✅ Listens to new email events
- ✅ Shows browser notifications for new emails
- ✅ Updates unread count badge automatically
- ✅ Shows refresh banner when new emails arrive
- ✅ Automatic background sync every 5 minutes
- ✅ Smooth loading overlay during sync

## Usage Guide

### Setting Up Automatic Sync

#### 1. Configure Laravel Scheduler
Add to `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    // Sync emails every 5 minutes for active users
    $schedule->command('emails:sync-all --active-only')
        ->everyFiveMinutes()
        ->withoutOverlapping()
        ->runInBackground();
}
```

#### 2. Start Required Services
```bash
# Terminal 1: Start queue worker (Horizon)
php artisan horizon

# Terminal 2: Start WebSocket server (Reverb)
php artisan reverb:start

# Terminal 3: Start scheduler (for automatic sync)
php artisan schedule:work
```

### Manual Sync Triggers

#### From Frontend (Button Click)
The sync button in `resources/views/mail/partials/email-list.blade.php:18` triggers:
```javascript
$('#syncEmailsBtn').on('click', function() {
    mailApp.syncEmails(); // Calls fetchEmails with force_refresh: true
});
```

#### From Command Line
```bash
# Sync all active users
php artisan emails:sync-all

# Sync specific user
php artisan emails:sync-all --user=5
```

#### From Code
```php
use App\Jobs\SyncUserEmails;

// Dispatch sync job
SyncUserEmails::dispatch($userId);
```

### Frontend Real-Time Updates

#### Listening to Sync Events
```javascript
// In resources/views/mail/index.blade.php
window.Echo.private(`email-sync.${userId}`)
    .listen('.sync.progress', (data) => {
        // data.status, data.processed, data.total, data.percentage
    })
    .listen('.sync.completed', (data) => {
        // data.processed, data.inserted, data.updated, data.deleted
    })
    .listen('.email.new', (data) => {
        // data.email contains new email preview
        showNewEmailNotification(data.email);
    });
```

#### New Email Notification Handler
The `handleNewEmail(data)` function:
1. Shows browser notification
2. Plays optional sound
3. Updates unread count badge
4. Shows refresh banner if on current folder

## Database Schema

### Required Tables

#### `fetched_emails`
Stores synced emails:
- `id` - Primary key
- `user_id` - Foreign key to users
- `uid` - Message UID from Outlook
- `message_id` - Internet message ID
- `user_email` - User's email address
- `subject` - Email subject
- `from_name`, `from_email` - Sender info
- `body_preview`, `body_text`, `body_html` - Content
- `date_received`, `date_sent` - Timestamps
- `is_read` - Read status
- `has_attachments` - Attachment flag
- `folder` - Folder name (inbox, sent, etc.)
- `importance` - Priority level
- `to_recipients`, `cc_recipients`, `bcc_recipients` - JSON arrays
- `conversation_id` - Thread ID

#### `email_sync_states`
Tracks sync state per user:
- `user_id` - Foreign key to users
- `delta_token` - Microsoft Graph delta token
- `is_syncing` - Sync in progress flag
- `is_locked` - Lock flag
- `last_synced_at` - Last successful sync
- `last_attempt_at` - Last attempt timestamp
- `sync_attempts` - Attempt counter
- `last_error` - Error message if failed

## Performance Optimizations

1. **Delta Queries**: Only fetches changed messages since last sync
2. **Batch Processing**: Processes messages in chunks of 50
3. **Pagination Handling**: Automatically follows nextLink/deltaLink
4. **Database Upserts**: Uses efficient upsert for insert/update
5. **Folder Enrichment**: Batches folder name lookups
6. **WebSocket Broadcasting**: Real-time updates without polling
7. **Background Jobs**: Non-blocking queue-based processing

## Best Practices

### 1. Delta Token Management
- Store delta token after each successful sync
- Use delta token for incremental syncs
- Handle token expiration gracefully (start fresh sync)

### 2. Sync Frequency
- **Recommended**: Every 5 minutes for active users
- **Minimum**: Every 2 minutes (avoid rate limiting)
- **Maximum**: Every 30 minutes (for inactive users)

### 3. Error Handling
- Retry failed syncs with exponential backoff
- Log errors to database (`email_sync_states.last_error`)
- Broadcast failure events to frontend
- Reset sync locks after failures

### 4. Rate Limiting
- Max 50 requests per sync cycle
- Max 10,000 messages per sync
- 200ms delay between pagination requests
- Track API usage per user

## Troubleshooting

### Sync Not Working
1. Check Horizon is running: `php artisan horizon:status`
2. Check Reverb is running: `ps aux | grep reverb`
3. Check OAuth token: `SELECT * FROM oauth_tokens WHERE provider='outlook'`
4. Check sync state: `SELECT * FROM email_sync_states`

### WebSocket Not Connecting
1. Verify Reverb config in `.env`:
   ```
   BROADCAST_DRIVER=reverb
   REVERB_APP_ID=your-app-id
   REVERB_APP_KEY=your-app-key
   REVERB_APP_SECRET=your-app-secret
   ```
2. Check browser console for connection errors
3. Verify channel authorization in `routes/channels.php`

### No New Email Notifications
1. Check event is being broadcast: `tail -f storage/logs/laravel.log`
2. Verify WebSocket channel is subscribed
3. Check browser notification permissions
4. Verify `NewEmailReceived` event is dispatched in `SyncUserEmails.php:281`

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/mail` | Mail index view with folder filtering |
| GET | `/mail/folder/{folder}` | Filter emails by folder |
| GET | `/mail/email/{id}` | Get single email details |
| GET | `/mail/check-new` | Check for unread emails |
| POST | `/mail/sync/trigger` | Trigger manual sync |
| POST | `/mail/send` | Send new email |
| POST | `/mail/reply/{id}` | Reply to email |
| POST | `/emails/fetch_emails` | Fetch emails (legacy) |

## Files Modified/Created

### New Files
- ✅ `app/Events/NewEmailReceived.php` - New email event
- ✅ `app/Console/Commands/SyncAllUsersEmails.php` - Sync command
- ✅ `EMAIL_SYNC_README.md` - This documentation

### Modified Files
- ✅ `app/Jobs/SyncUserEmails.php` - Added new email broadcasting
- ✅ `app/Services/OutlookService.php` - Changed delta query to all folders
- ✅ `app/Services/EmailStorageService.php` - Enhanced folder filtering
- ✅ `app/Http/Controllers/MailController.php` - Added triggerSync method
- ✅ `routes/mail.php` - Added sync trigger route
- ✅ `resources/views/mail/index.blade.php` - Added real-time listeners

## Next Steps (Optional Enhancements)

1. **Email Search**: Add full-text search across all fields
2. **Folder Counts**: Show unread count per folder in navigation
3. **Email Actions**: Implement move, archive, delete via API
4. **Attachment Sync**: Download and store attachments during sync
5. **Thread View**: Group emails by conversation_id
6. **Push Notifications**: Use Microsoft webhooks for instant updates
7. **Offline Mode**: Cache emails in IndexedDB for offline access
8. **Read Receipts**: Sync read status back to Outlook

## Support

For issues or questions:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check Horizon dashboard: `/horizon`
3. Review Microsoft Graph API docs: https://docs.microsoft.com/graph/api/
4. Check Laravel Reverb docs: https://laravel.com/docs/reverb
