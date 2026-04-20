# 💬 Setup Slack API

## Status: ❌ Not Configured

## What You Need
- Slack Workspace (free or paid)
- Admin access to create apps
- 5-10 minutes

## Features Enabled
- ✅ Send notifications to Slack channels
- ✅ Task creation alerts
- ✅ Meeting reminders
- ✅ Custom bot messages

---

## Step-by-Step Setup

### 1. Create Slack App

**a) Go to Slack API**
- URL: https://api.slack.com/apps
- Sign in with your Slack account

**b) Create New App**
- Click: **"Create New App"**
- Choose: **"From scratch"**

**c) Fill App Details**
- **App Name:** `Workflow AI Bot`
- **Pick a workspace:** Select your workspace
- Click: **"Create App"**

---

### 2. Configure Bot Permissions

**a) Open OAuth & Permissions**
- In sidebar, click: **"OAuth & Permissions"**

**b) Scroll to "Scopes" Section**
- Find: **"Bot Token Scopes"**

**c) Add Required Scopes**
Click **"Add an OAuth Scope"** and add:

| Scope | Description | Why Needed |
|-------|-------------|------------|
| `chat:write` | Send messages as bot | Send notifications |
| `chat:write.public` | Send to channels without joining | Post to public channels |
| `channels:read` | View basic channel info | List channels |
| `users:read` | View people in workspace | Get user info |
| `incoming-webhook` | Post messages to specific channels | Webhooks (optional) |

---

### 3. Install App to Workspace

**a) Install App**
- Scroll to top of **"OAuth & Permissions"** page
- Click: **"Install to Workspace"**

**b) Authorize App**
- Review permissions
- Click: **"Allow"**

**c) Copy Bot Token**
- After installation, you'll see: **"Bot User OAuth Token"**
- It starts with: `xoxb-`
- **Copy this token** - Example:
  ```
  xoxb-1234567890-1234567890123-AbCdEfGhIjKlMnOpQrStUvWx
  ```

---

### 4. Get Signing Secret

**a) Open Basic Information**
- In sidebar, click: **"Basic Information"**

**b) Find App Credentials**
- Scroll to: **"App Credentials"** section

**c) Copy Signing Secret**
- Click: **"Show"** next to "Signing Secret"
- **Copy the secret** - Example:
  ```
  a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6
  ```

---

### 5. Invite Bot to Channel

**a) Open Slack Workspace**
- Go to your Slack workspace

**b) Go to Channel**
- Open the channel where you want bot notifications
- Default: `#general`

**c) Invite Bot**
Type in channel:
```
/invite @Workflow AI Bot
```

Or:
- Click channel name → **"Integrations"** tab
- Click **"Add apps"**
- Select **"Workflow AI Bot"**

---

### 6. Update .env File

Open `backend/.env` and update:

```properties
SLACK_BOT_TOKEN=xoxb-your-actual-bot-token-here
SLACK_SIGNING_SECRET=your-actual-signing-secret-here
SLACK_DEFAULT_CHANNEL=#general
```

**Example:**
```properties
SLACK_BOT_TOKEN=xoxb-1234567890-1234567890123-AbCdEfGhIjKlMnOpQrStUvWx
SLACK_SIGNING_SECRET=a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6
SLACK_DEFAULT_CHANNEL=#general
```

---

### 7. Test Configuration

**a) Create Test Script**

File: `backend/test-slack.js`

```javascript
require('dotenv').config();
const { WebClient } = require('@slack/web-api');

async function testSlack() {
  try {
    console.log('🔍 Testing Slack API...\n');

    const client = new WebClient(process.env.SLACK_BOT_TOKEN);

    // Test 1: Auth test
    const auth = await client.auth.test();
    console.log('✅ Authentication successful!');
    console.log(`   Bot name: ${auth.user}`);
    console.log(`   Team: ${auth.team}\n`);

    // Test 2: Send test message
    const result = await client.chat.postMessage({
      channel: process.env.SLACK_DEFAULT_CHANNEL,
      text: '🤖 Hello! Workflow AI Bot is now connected!',
      blocks: [
        {
          type: 'section',
          text: {
            type: 'mrkdwn',
            text: '*🤖 Workflow AI Bot*\n\nSuccessfully connected to Slack! 🎉'
          }
        },
        {
          type: 'section',
          text: {
            type: 'mrkdwn',
            text: '✅ Task notifications\n✅ Meeting reminders\n✅ Email alerts'
          }
        }
      ]
    });

    console.log('✅ Test message sent!');
    console.log(`   Channel: ${result.channel}`);
    console.log(`   Timestamp: ${result.ts}\n`);

    console.log('🎉 Slack API configured successfully!');

  } catch (error) {
    console.error('❌ Error:', error.message);
    
    if (error.data?.error === 'invalid_auth') {
      console.log('\n💡 Tip: Check your SLACK_BOT_TOKEN in .env file');
    } else if (error.data?.error === 'channel_not_found') {
      console.log('\n💡 Tip: Make sure bot is invited to the channel');
      console.log('   Type in Slack: /invite @Workflow AI Bot');
    }
  }
}

testSlack();
```

**b) Run Test**

```bash
cd backend
node test-slack.js
```

**Expected Output:**
```
✅ Authentication successful!
✅ Test message sent!
🎉 Slack API configured successfully!
```

**Check Slack:** You should see a message from bot in your channel!

---

## Configure Notification Preferences

### In User Settings (Database)

Users can enable/disable Slack notifications:

```javascript
{
  preferences: {
    slackNotifications: true,  // Enable Slack
    emailNotifications: true
  }
}
```

### Set Different Channels

You can send to specific channels:

**In `.env`:**
```properties
SLACK_DEFAULT_CHANNEL=#general
SLACK_URGENT_CHANNEL=#urgent
SLACK_TEAM_CHANNEL=#team-notifications
```

**In code:**
```javascript
await slackService.sendMessage('#urgent', 'High priority task!');
```

---

## Slack Message Examples

### Task Created
```
🎯 New Task Created

Title: Review Documentation
Priority: High
Due: Tomorrow, 10:00 AM
Assigned: @john

View details → http://localhost:3001/tasks/123
```

### Meeting Reminder
```
📅 Meeting in 15 minutes!

Sprint Planning
Time: Today, 2:00 PM
Duration: 1 hour
Location: Conference Room A

Join → http://meet.google.com/abc-defg-hij
```

### Custom Notification
```
💡 John completed task: "Review Code"
Status: Completed ✅
```

---

## Troubleshooting

### Error: "invalid_auth"
**Problem:** Bot token is incorrect

**Solution:**
- Go to: https://api.slack.com/apps
- Select your app → **"OAuth & Permissions"**
- Copy **"Bot User OAuth Token"** (starts with `xoxb-`)
- Update `SLACK_BOT_TOKEN` in `.env`

---

### Error: "channel_not_found"
**Problem:** Bot not invited to channel

**Solution:**
In Slack, type:
```
/invite @Workflow AI Bot
```

Or manually add bot:
- Channel name → **Integrations** → **Add apps** → Select bot

---

### Error: "not_in_channel"
**Problem:** Bot can't post to channel

**Solution:**
Add scope `chat:write.public` (allows posting without joining)

Or invite bot to channel first

---

### Messages Not Sending
**Problem:** Code runs but no message appears

**Check:**
1. ✅ Bot token correct?
2. ✅ Bot invited to channel?
3. ✅ Channel name correct? (include `#`)
4. ✅ Slack notifications enabled in user preferences?

---

## Advanced Features

### Rich Message Formatting

```javascript
await client.chat.postMessage({
  channel: '#general',
  blocks: [
    {
      type: 'header',
      text: {
        type: 'plain_text',
        text: '🚀 New Feature Released'
      }
    },
    {
      type: 'section',
      fields: [
        {
          type: 'mrkdwn',
          text: '*Type:*\nTask Management'
        },
        {
          type: 'mrkdwn',
          text: '*Priority:*\nHigh'
        }
      ]
    },
    {
      type: 'actions',
      elements: [
        {
          type: 'button',
          text: {
            type: 'plain_text',
            text: 'View Details'
          },
          url: 'http://localhost:3001/tasks'
        }
      ]
    }
  ]
});
```

### Interactive Buttons
- Add buttons for quick actions
- Handle button clicks with events

### Scheduled Messages
- Schedule messages for future delivery
- Requires additional scope: `chat:write.customize`

---

## Production Deployment

### 1. Update Redirect URLs
- Add production URL in Slack app settings
- **OAuth & Permissions** → **Redirect URLs**

### 2. Distribute App (Optional)
- Submit to Slack App Directory
- Or keep it private for your workspace

### 3. Environment Variables
```properties
SLACK_BOT_TOKEN=xoxb-production-token
SLACK_SIGNING_SECRET=production-secret
SLACK_DEFAULT_CHANNEL=#production-alerts
```

---

## Quick Reference

**Slack API Portal:** https://api.slack.com/apps  
**Documentation:** https://api.slack.com/docs  
**Block Kit Builder:** https://app.slack.com/block-kit-builder

**Bot Token:** Starts with `xoxb-`  
**User Token:** Starts with `xoxp-` (not used in this app)

**Common Scopes:**
- `chat:write` - Send messages
- `chat:write.public` - Post to any public channel
- `channels:read` - View channels
- `users:read` - View users

---

## Next Steps

After setup:
1. ✅ Restart backend server
2. ✅ Create a task via Chat
3. ✅ Check Slack for notification
4. ✅ Customize message templates in `slackService.js`

**Happy Slacking! 💬✨**
