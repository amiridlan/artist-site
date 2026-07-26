# Cloudflare R2 Setup Guide for KLP48

This guide explains how to set up two Cloudflare R2 buckets for your KLP48 project.

## Overview

- **klp48-media-public**: Public media (member photos, news images, release covers)
- **klp48-media-fanclub**: Protected fan club content (paywall-protected)

## Step 1: Create R2 Buckets

### 1.1 Access Cloudflare R2
1. Log into your Cloudflare account
2. Navigate to **R2 Object Storage** from the sidebar
3. Click **Create bucket**

### 1.2 Create Public Bucket
1. **Bucket name**: `klp48-media-public`
2. **Location**: Automatic (APAC for best performance in Malaysia)
3. **Storage Class**: Standard
4. Click **Create bucket**

### 1.3 Create Protected Bucket
1. **Bucket name**: `klp48-media-fanclub`
2. **Location**: Automatic (same as public bucket)
3. **Storage Class**: Standard
4. Click **Create bucket**

## Step 2: Configure Public Access & Custom Domain

### 2.1 Set Up Custom Domain for Public Bucket
1. Open `klp48-media-public` bucket
2. Go to **Settings** → **Public Access**
3. Click **Connect Domain**
4. Enter your custom domain: `media.klp48.com` (or subdomain of your choice)
5. Add the required CNAME record to your DNS:
   ```
   media.klp48.com → CNAME → <bucket-name>.<account-id>.r2.cloudflarestorage.com
   ```
6. Wait for DNS propagation (5-60 minutes)

### 2.2 Keep Fan Club Bucket Private
- **DO NOT** enable public access for `klp48-media-fanclub`
- This bucket will only be accessed via temporary signed URLs

## Step 3: Create API Tokens

### 3.1 Generate R2 API Token(s)
1. Go to R2 → **Manage R2 API Tokens**
2. Click **Create API token**

**Option A: Single Token (Simpler)**
- **Token name**: `klp48-media-all`
- **Permissions**: Object Read & Write
- **Buckets**: Select both buckets
- Copy the **Access Key ID** and **Secret Access Key**

**Option B: Separate Tokens (More Secure)**

Create two tokens:

**Token 1: Public Media**
- **Token name**: `klp48-media-public`
- **Permissions**: Object Read & Write
- **Buckets**: `klp48-media-public` only
- Copy credentials

**Token 2: Fan Club Media**
- **Token name**: `klp48-media-fanclub`
- **Permissions**: Object Read & Write
- **Buckets**: `klp48-media-fanclub` only
- Copy credentials

## Step 4: Configure Laravel Environment

### 4.1 Copy Environment Variables
Copy `.env.example` to `.env` if you haven't already:
```bash
cd backend
cp .env.example .env
```

### 4.2 Update .env File

Find your **Account ID** in Cloudflare dashboard (R2 Overview page).

**If using single token (Option A):**
```env
# Media Storage
MEDIA_DISK=r2-public
FANCLUB_DISK=r2-fanclub

# Public Bucket
R2_PUBLIC_ACCESS_KEY_ID=your_access_key_id
R2_PUBLIC_SECRET_ACCESS_KEY=your_secret_access_key
R2_PUBLIC_BUCKET=klp48-media-public
R2_PUBLIC_ENDPOINT=https://YOUR_ACCOUNT_ID.r2.cloudflarestorage.com
R2_PUBLIC_URL=https://media.klp48.com

# Fan Club Bucket (use same credentials)
R2_FANCLUB_ACCESS_KEY_ID=your_access_key_id
R2_FANCLUB_SECRET_ACCESS_KEY=your_secret_access_key
R2_FANCLUB_BUCKET=klp48-media-fanclub
R2_FANCLUB_ENDPOINT=https://YOUR_ACCOUNT_ID.r2.cloudflarestorage.com
```

**If using separate tokens (Option B):**
```env
# Media Storage
MEDIA_DISK=r2-public
FANCLUB_DISK=r2-fanclub

# Public Bucket
R2_PUBLIC_ACCESS_KEY_ID=public_token_access_key
R2_PUBLIC_SECRET_ACCESS_KEY=public_token_secret
R2_PUBLIC_BUCKET=klp48-media-public
R2_PUBLIC_ENDPOINT=https://YOUR_ACCOUNT_ID.r2.cloudflarestorage.com
R2_PUBLIC_URL=https://media.klp48.com

# Fan Club Bucket
R2_FANCLUB_ACCESS_KEY_ID=fanclub_token_access_key
R2_FANCLUB_SECRET_ACCESS_KEY=fanclub_token_secret
R2_FANCLUB_BUCKET=klp48-media-fanclub
R2_FANCLUB_ENDPOINT=https://YOUR_ACCOUNT_ID.r2.cloudflarestorage.com
```

### 4.3 Install AWS SDK
Laravel uses the AWS S3 SDK for R2 (they're S3-compatible):
```bash
cd backend
composer require league/flysystem-aws-s3-v3 "^3.0"
```

## Step 5: Test the Connection

### 5.1 Test Public Bucket
```bash
cd backend
php artisan tinker
```

In the Tinker console:
```php
// Upload test file to public bucket
Storage::disk('r2-public')->put('test.txt', 'Hello from R2 Public!');

// Get public URL
$url = Storage::disk('r2-public')->url('test.txt');
echo $url; // Should return: https://media.klp48.com/test.txt

// Verify you can access it in browser
// Visit the URL above - it should show "Hello from R2 Public!"

// Clean up
Storage::disk('r2-public')->delete('test.txt');
```

### 5.2 Test Fan Club Bucket (Signed URLs)
```php
// Upload test file to protected bucket
Storage::disk('r2-fanclub')->put('test-protected.txt', 'Protected content!');

// Generate temporary signed URL (valid for 60 minutes)
$signedUrl = Storage::disk('r2-fanclub')->temporaryUrl(
    'test-protected.txt',
    now()->addMinutes(60)
);
echo $signedUrl;

// Visit this URL in browser - it should work!
// After 60 minutes, the URL will expire

// Clean up
Storage::disk('r2-fanclub')->delete('test-protected.txt');
```

## Step 6: Usage in Your Application

### 6.1 Public Media (News, Members, Releases)
Use the existing `MediaStorageService` or `ImageProcessingService`:

```php
// In your controllers
use App\Services\MediaStorageService;

public function uploadMemberPhoto(Request $request, MediaStorageService $storage)
{
    $path = $storage->store($request->file('photo'), 'members');
    $url = $storage->url($path); // Returns: https://media.klp48.com/members/xyz.jpg

    // Save $path to database, return $url to frontend
}
```

### 6.2 Protected Fan Club Content
Use the new `FanClubMediaService`:

```php
use App\Services\FanClubMediaService;

public function uploadFanClubContent(Request $request, FanClubMediaService $fanClub)
{
    // Upload to protected bucket
    $path = $fanClub->store($request->file('content'), 'exclusive-photos');

    // Save $path to database (NOT the URL!)
    // Users must request temporary URLs through API
}

public function getProtectedContent(Request $request, FanClubMediaService $fanClub)
{
    // Verify user has active subscription
    if (!$request->user()->hasActiveSubscription()) {
        abort(403, 'Active subscription required');
    }

    $content = FanClubContent::findOrFail($id);

    // Generate temporary signed URL (60 minute expiry)
    $signedUrl = $fanClub->getTemporaryUrl($content->file_path, expirationMinutes: 60);

    return response()->json(['url' => $signedUrl]);
}
```

## Step 7: Migration from Local Storage

If you have existing files in local `storage/app/public/`:

### 7.1 Migrate Public Files to R2
```bash
cd backend
php artisan tinker
```

```php
// Get all files from local public disk
$files = Storage::disk('public')->allFiles();

foreach ($files as $file) {
    // Get file contents
    $contents = Storage::disk('public')->get($file);

    // Upload to R2 public bucket
    Storage::disk('r2-public')->put($file, $contents);

    echo "Migrated: {$file}\n";
}
```

### 7.2 Update Database URLs
After migration, update database records to use new R2 URLs:

```sql
-- Example: Update member photo paths
UPDATE members
SET photo_url = REPLACE(photo_url, 'http://localhost:8000/storage', 'https://media.klp48.com');
```

## Security Best Practices

### For Public Bucket (klp48-media-public)
✅ Use custom domain with HTTPS
✅ Enable Cloudflare caching
✅ Set appropriate cache headers
✅ Never store sensitive data

### For Fan Club Bucket (klp48-media-fanclub)
✅ **NEVER** enable public access
✅ Use short-lived signed URLs (60 minutes recommended)
✅ Always verify subscription before generating URLs
✅ Log access attempts for security auditing
✅ Consider adding watermarks to images
✅ Implement download limits per user

## Cost Estimates

Cloudflare R2 pricing (as of 2024):
- **Storage**: $0.015 per GB/month
- **Class A Operations** (writes): $4.50 per million
- **Class B Operations** (reads): $0.36 per million
- **Egress**: FREE (no bandwidth charges!)

Example monthly cost for:
- 100 GB storage
- 1M reads, 10K writes
= ~$1.50/month + $0.36 + $0.045 = **~$2/month**

## Troubleshooting

### "Invalid signature" error
- Check your Access Key ID and Secret Access Key
- Ensure endpoint URL matches your account ID
- Verify bucket name is correct

### "Bucket not found" error
- Confirm bucket names are spelled correctly
- Check that API token has access to the bucket

### Signed URLs not working
- Ensure bucket does NOT have public access enabled
- Check URL hasn't expired
- Verify clock sync on your server (`ntpdate` on Linux)

### CORS issues (for direct frontend uploads)
Add CORS policy to your bucket:
```json
{
  "AllowedOrigins": ["https://klp48.com", "http://localhost:3000"],
  "AllowedMethods": ["GET", "PUT"],
  "AllowedHeaders": ["*"],
  "MaxAgeSeconds": 3600
}
```

## Next Steps

1. ✅ Create both R2 buckets
2. ✅ Set up custom domain for public bucket
3. ✅ Generate API tokens
4. ✅ Configure `.env` file
5. ✅ Test both buckets
6. ✅ Migrate existing files
7. ✅ Implement subscription verification for fan club content
8. 🔄 Update frontend to handle signed URLs
9. 🔄 Add caching headers for public content
10. 🔄 Monitor usage and costs in Cloudflare dashboard

## Support

For issues:
- **Cloudflare R2 Docs**: https://developers.cloudflare.com/r2/
- **Laravel Filesystem**: https://laravel.com/docs/filesystem
- **AWS S3 SDK**: https://docs.aws.amazon.com/sdk-for-php/
