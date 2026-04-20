# 📅 Setup Google Calendar & Gmail API

## Status: ❌ Not Configured

## What You Need
- Google Account
- 10-15 minutes

## Features Enabled
- ✅ Schedule meetings automatically
- ✅ Send email reminders via Gmail
- ✅ OAuth login with Google
- ✅ Sync calendar events
- ✅ Read/Send emails

---

## Step-by-Step Setup

### 1. Create Google Cloud Project

**a) Go to Google Cloud Console**
- URL: https://console.cloud.google.com/
- Sign in with your Google Account

**b) Create New Project**
- Click: **"Select a project"** (top navigation)
- Click: **"NEW PROJECT"**
- Project name: `Workflow AI` (or any name you like)
- Organization: Leave as default
- Click: **"CREATE"**
- Wait for project creation (10-30 seconds)

---

### 2. Enable Required APIs

**a) Open API Library**
- Click: **"≡"** menu → **"APIs & Services"** → **"Library"**

**b) Enable Google Calendar API**
- Search: `Google Calendar API`
- Click on: **Google Calendar API**
- Click: **"ENABLE"**
- Wait for activation

**c) Enable Gmail API**
- Click: **"< Go to Library"** (back button)
- Search: `Gmail API`
- Click on: **Gmail API**
- Click: **"ENABLE"**
- Wait for activation

---

### 3. Configure OAuth Consent Screen

**a) Open Consent Screen**
- Click: **"≡"** menu → **"APIs & Services"** → **"OAuth consent screen"**

**b) Choose User Type**
- Select: **"External"** (for testing with any Google account)
- Click: **"CREATE"**

**c) Fill App Information**

**OAuth consent screen (Page 1):**
- App name: `Workflow AI`
- User support email: (your email address)
- App logo: (optional - skip for now)
- Application home page: `http://localhost:3001` (optional)
- Authorized domains: (skip for development)
- Developer contact information: (your email address)
- Click: **"SAVE AND CONTINUE"**

**Scopes (Page 2):**
- Click: **"ADD OR REMOVE SCOPES"**
- Manually add these scopes:
  ```
  https://www.googleapis.com/auth/calendar
  https://www.googleapis.com/auth/gmail.send
  https://www.googleapis.com/auth/gmail.readonly
  https://www.googleapis.com/auth/userinfo.profile
  https://www.googleapis.com/auth/userinfo.email
  ```
- Click: **"UPDATE"**
- Click: **"SAVE AND CONTINUE"**

**Test users (Page 3):**
- Click: **"+ ADD USERS"**
- Enter your email address
- Click: **"ADD"**
- Click: **"SAVE AND CONTINUE"**

**Summary (Page 4):**
- Review and click: **"BACK TO DASHBOARD"**

---

### 4. Create OAuth Credentials

**a) Open Credentials Page**
- Click: **"≡"** menu → **"APIs & Services"** → **"Credentials"**

**b) Create OAuth Client ID**
- Click: **"+ CREATE CREDENTIALS"** (top)
- Select: **"OAuth client ID"**

**c) Configure OAuth Client**
- Application type: **"Web application"**
- Name: `Workflow AI Backend`

**Authorized JavaScript origins:**
- Click: **"+ ADD URI"**
- Enter: `http://localhost:5000`

**Authorized redirect URIs:**
- Click: **"+ ADD URI"**
- Enter: `http://localhost:5000/api/auth/google/callback`

- Click: **"CREATE"**

**d) Save Credentials**
- A popup will show your credentials
- **Copy and save:**
  - **Client ID**: `xxxxx.apps.googleusercontent.com`
  - **Client Secret**: `GOCSPX-xxxxx`
- Click: **"OK"**

---

### 5. Get Refresh Token

**a) Edit Token Generator Script**

Open file: `backend/get-google-token.js`

Replace these lines:
```javascript
const CLIENT_ID = 'YOUR_CLIENT_ID';  // Replace with your Client ID
const CLIENT_SECRET = 'YOUR_CLIENT_SECRET';  // Replace with your Client Secret
```

**b) Run Script**

```bash
cd backend
node get-google-token.js
```

**c) Follow Instructions**
1. Script will print a URL - Copy it
2. Open URL in browser
3. **Sign in** with Google Account
4. **Allow** all permissions
5. You'll be redirected to a URL like:
   ```
   http://localhost:5000/api/auth/google/callback?code=4/0AanRRrtX...
   ```
6. **Copy the code** parameter value (the long string after `code=`)
7. Paste it in terminal when asked: `Enter the authorization code:`
8. Press **Enter**

**d) Save Output**
- Script will print your credentials
- Copy all three values

---

### 6. Update .env File

Open `backend/.env` and update:

```properties
GOOGLE_CLIENT_ID=YOUR_CLIENT_ID_FROM_STEP_4
GOOGLE_CLIENT_SECRET=YOUR_CLIENT_SECRET_FROM_STEP_4
GOOGLE_REFRESH_TOKEN=YOUR_REFRESH_TOKEN_FROM_STEP_5
GOOGLE_REDIRECT_URI=http://localhost:5000/api/auth/google/callback
```

---

### 7. Test Configuration

**a) Create Test Script**

File: `backend/test-google-api.js`

```javascript
require('dotenv').config();
const { google } = require('googleapis');

async function testGoogleAPI() {
  try {
    console.log('🔍 Testing Google API...\n');

    const oauth2Client = new google.auth.OAuth2(
      process.env.GOOGLE_CLIENT_ID,
      process.env.GOOGLE_CLIENT_SECRET,
      process.env.GOOGLE_REDIRECT_URI
    );

    oauth2Client.setCredentials({
      refresh_token: process.env.GOOGLE_REFRESH_TOKEN
    });

    // Test Calendar API
    const calendar = google.calendar({ version: 'v3', auth: oauth2Client });
    const calendarList = await calendar.calendarList.list();
    
    console.log('✅ Google Calendar API: Working!');
    console.log(`Found ${calendarList.data.items.length} calendars\n`);

    // Test Gmail API
    const gmail = google.gmail({ version: 'v1', auth: oauth2Client });
    const profile = await gmail.users.getProfile({ userId: 'me' });
    
    console.log('✅ Gmail API: Working!');
    console.log(`Email: ${profile.data.emailAddress}\n`);

    console.log('🎉 All Google APIs configured successfully!');
    
  } catch (error) {
    console.error('❌ Error:', error.message);
  }
}

testGoogleAPI();
```

**b) Run Test**

```bash
node test-google-api.js
```

**Expected Output:**
```
✅ Google Calendar API: Working!
✅ Gmail API: Working!
🎉 All Google APIs configured successfully!
```

---

## Troubleshooting

### Error: "invalid_client"
- Check CLIENT_ID and CLIENT_SECRET are correct
- Make sure no extra spaces in .env file

### Error: "invalid_grant"
- Refresh token might be expired
- Run `node get-google-token.js` again to get new token

### Error: "redirect_uri_mismatch"
- Make sure redirect URI in Google Console exactly matches:
  `http://localhost:5000/api/auth/google/callback`

### Error: "access_denied"
- Make sure you added your email as test user in OAuth consent screen

---

## Production Deployment

When deploying to production:

1. **Update redirect URI:**
   - Add production URL in Google Console
   - Example: `https://your-domain.com/api/auth/google/callback`

2. **Publish OAuth Consent:**
   - Submit app for verification
   - Or keep in testing mode (max 100 users)

3. **Update .env:**
   ```
   GOOGLE_REDIRECT_URI=https://your-domain.com/api/auth/google/callback
   ```

---

## Quick Reference

**Google Cloud Console:** https://console.cloud.google.com/  
**OAuth Consent Screen:** APIs & Services → OAuth consent screen  
**Credentials:** APIs & Services → Credentials  
**API Library:** APIs & Services → Library

**Required Scopes:**
- `https://www.googleapis.com/auth/calendar`
- `https://www.googleapis.com/auth/gmail.send`
- `https://www.googleapis.com/auth/gmail.readonly`
- `https://www.googleapis.com/auth/userinfo.profile`
- `https://www.googleapis.com/auth/userinfo.email`

---

## Next Steps

After setup:
1. ✅ Restart backend server
2. ✅ Test calendar features in Chat
3. ✅ Try: "Jadwalkan meeting besok jam 10"
4. ✅ Try: "Kirim email ke test@example.com"
