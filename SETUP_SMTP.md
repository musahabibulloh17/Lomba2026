# 📧 Setup SMTP Email (Gmail App Password)

## Status: ❌ Not Configured (Optional)

## What You Need
- Gmail account
- 5 minutes

## Features Enabled
- ✅ Send email notifications directly
- ✅ Alternative to Gmail API
- ✅ Simpler setup for basic email

---

## Gmail App Password Setup

### 1. Enable 2-Factor Authentication

**a) Go to Google Account Security**
- URL: https://myaccount.google.com/security
- Sign in with your Gmail account

**b) Find 2-Step Verification**
- Scroll to **"How you sign in to Google"**
- Click: **"2-Step Verification"**

**c) Turn On 2-Step Verification**
- Click: **"GET STARTED"**
- Follow the steps to enable
- Choose verification method (phone recommended)

---

### 2. Generate App Password

**a) Go to App Passwords**
- URL: https://myaccount.google.com/apppasswords
- Or: Google Account → Security → 2-Step Verification → App passwords

**b) Create App Password**
- **Select app:** Mail
- **Select device:** Other (Custom name)
- **Name:** `Workflow AI` or `Workflow Automation`
- Click: **"GENERATE"**

**c) Copy Password**
- You'll see a 16-character password like:
  ```
  abcd efgh ijkl mnop
  ```
- **Copy this password** (remove spaces when pasting)
- Click: **"DONE"**

**Note:** You can't view this password again, but you can generate a new one anytime.

---

### 3. Update .env File

Open `backend/.env` and update:

```properties
# SMTP Email Configuration
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your-email@gmail.com
SMTP_PASSWORD=abcdefghijklmnop
```

**Example:**
```properties
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=john.doe@gmail.com
SMTP_PASSWORD=abcdefghijklmnop
```

**Important:** Remove spaces from app password!

---

### 4. Test Configuration

**a) Create Test Script**

File: `backend/test-smtp.js`

```javascript
require('dotenv').config();
const nodemailer = require('nodemailer');

async function testSMTP() {
  try {
    console.log('📧 Testing SMTP Email...\n');

    // Create transporter
    const transporter = nodemailer.createTransporter({
      host: process.env.SMTP_HOST,
      port: parseInt(process.env.SMTP_PORT),
      secure: false, // true for 465, false for other ports
      auth: {
        user: process.env.SMTP_USER,
        pass: process.env.SMTP_PASSWORD
      }
    });

    // Verify connection
    await transporter.verify();
    console.log('✅ SMTP connection successful!\n');

    // Send test email
    const info = await transporter.sendMail({
      from: `"Workflow AI" <${process.env.SMTP_USER}>`,
      to: process.env.SMTP_USER, // Send to yourself
      subject: '🤖 Workflow AI - Test Email',
      text: 'Hello! This is a test email from Workflow AI.',
      html: `
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
          <h2 style="color: #1976d2;">🤖 Workflow AI</h2>
          <p>Hello!</p>
          <p>This is a test email from <strong>Workflow AI</strong>.</p>
          <p>If you received this, your SMTP configuration is working correctly! ✅</p>
          <hr style="border: 1px solid #eee; margin: 20px 0;">
          <p style="color: #666; font-size: 12px;">
            This is an automated message from Workflow AI
          </p>
        </div>
      `
    });

    console.log('✅ Test email sent!');
    console.log(`   Message ID: ${info.messageId}`);
    console.log(`   To: ${process.env.SMTP_USER}\n`);

    console.log('🎉 SMTP configured successfully!');
    console.log('   Check your inbox for the test email.');

  } catch (error) {
    console.error('❌ Error:', error.message);
    
    if (error.message.includes('Invalid login')) {
      console.log('\n💡 Tip: Check your SMTP_USER and SMTP_PASSWORD');
      console.log('   Make sure you\'re using an App Password, not your regular password');
    }
  }
}

testSMTP();
```

**b) Install nodemailer (if not installed)**

```bash
cd backend
npm install nodemailer
```

**c) Run Test**

```bash
node test-smtp.js
```

**Expected Output:**
```
✅ SMTP connection successful!
✅ Test email sent!
🎉 SMTP configured successfully!
```

**Check your Gmail inbox** for the test email!

---

## Alternative: Other Email Providers

### Outlook/Hotmail

```properties
SMTP_HOST=smtp-mail.outlook.com
SMTP_PORT=587
SMTP_USER=your-email@outlook.com
SMTP_PASSWORD=your-password
```

### Yahoo Mail

```properties
SMTP_HOST=smtp.mail.yahoo.com
SMTP_PORT=587
SMTP_USER=your-email@yahoo.com
SMTP_PASSWORD=your-app-password
```

### Custom SMTP Server

```properties
SMTP_HOST=mail.yourdomain.com
SMTP_PORT=587
SMTP_USER=noreply@yourdomain.com
SMTP_PASSWORD=your-password
```

---

## Troubleshooting

### Error: "Invalid login"

**Problem:** Wrong email or password

**Solutions:**
1. ✅ Use App Password, not regular Gmail password
2. ✅ Remove spaces from App Password
3. ✅ Enable 2-Factor Authentication first
4. ✅ Check email address is correct

---

### Error: "Connection timeout"

**Problem:** Can't connect to SMTP server

**Solutions:**
1. ✅ Check SMTP_HOST is correct
2. ✅ Check SMTP_PORT (usually 587 or 465)
3. ✅ Check firewall settings
4. ✅ Try port 465 with `secure: true`

---

### Error: "Self-signed certificate"

**Problem:** SSL certificate issue

**Solution:** Add to transporter config:
```javascript
tls: {
  rejectUnauthorized: false
}
```

---

### Emails Going to Spam

**Solutions:**
1. ✅ Add SPF record to your domain
2. ✅ Use verified sender address
3. ✅ Don't send too many emails quickly
4. ✅ Include unsubscribe link
5. ✅ Use HTML formatting

---

## Email Templates

### Task Reminder

```javascript
const mailOptions = {
  from: `"Workflow AI" <${process.env.SMTP_USER}>`,
  to: user.email,
  subject: '🎯 Task Reminder: ' + task.title,
  html: `
    <h2>🎯 Task Reminder</h2>
    <p>Hi ${user.name},</p>
    <p>This is a reminder for your task:</p>
    <div style="background: #f5f5f5; padding: 15px; border-radius: 5px;">
      <h3>${task.title}</h3>
      <p><strong>Priority:</strong> ${task.priority}</p>
      <p><strong>Due:</strong> ${task.dueDate}</p>
      <p>${task.description}</p>
    </div>
    <a href="http://localhost:3001/tasks/${task.id}" 
       style="display:inline-block; padding:10px 20px; background:#1976d2; 
              color:white; text-decoration:none; border-radius:5px; margin-top:15px;">
      View Task
    </a>
  `
};
```

### Meeting Invitation

```javascript
const mailOptions = {
  from: `"Workflow AI" <${process.env.SMTP_USER}>`,
  to: meeting.attendees.join(','),
  subject: '📅 Meeting Invitation: ' + meeting.title,
  html: `
    <h2>📅 Meeting Invitation</h2>
    <div style="background: #f5f5f5; padding: 15px; border-radius: 5px;">
      <h3>${meeting.title}</h3>
      <p><strong>📍 Location:</strong> ${meeting.location}</p>
      <p><strong>🕐 Time:</strong> ${meeting.startTime}</p>
      <p><strong>⏱️ Duration:</strong> ${meeting.duration} minutes</p>
      <p>${meeting.description}</p>
    </div>
    ${meeting.meetingLink ? `
      <a href="${meeting.meetingLink}" style="...">Join Meeting</a>
    ` : ''}
  `
};
```

---

## Production Best Practices

### 1. Use Environment Variables
Never hardcode credentials in code

### 2. Rate Limiting
Don't send too many emails quickly
```javascript
// Max 10 emails per minute
const RATE_LIMIT = 10;
const RATE_WINDOW = 60000; // 1 minute
```

### 3. Queue System
Use job queue for bulk emails (Bull, Bee-Queue)

### 4. Email Validation
Validate email addresses before sending
```javascript
const validator = require('email-validator');
if (!validator.validate(email)) {
  throw new Error('Invalid email');
}
```

### 5. Unsubscribe Link
Include option to opt-out
```html
<p style="font-size: 12px; color: #666;">
  <a href="http://localhost:3001/settings">Unsubscribe</a> from notifications
</p>
```

### 6. Monitor Sending
Log all emails sent
```javascript
logger.info('Email sent', {
  to: recipient,
  subject: subject,
  messageId: info.messageId
});
```

---

## Comparison: SMTP vs Gmail API

| Feature | SMTP Email | Gmail API |
|---------|-----------|-----------|
| Setup | ⭐⭐⭐⭐⭐ Easy | ⭐⭐⭐ Medium |
| Reliability | ⭐⭐⭐⭐ Good | ⭐⭐⭐⭐⭐ Excellent |
| Rate Limit | 500/day (free) | 1B/day (free) |
| Rich Features | ⭐⭐⭐ Basic | ⭐⭐⭐⭐⭐ Advanced |
| OAuth | ❌ No | ✅ Yes |
| Best For | Simple emails | Complex integration |

**Recommendation:**
- Use **SMTP** for quick setup and simple email notifications
- Use **Gmail API** for production and advanced features

---

## Quick Reference

**Gmail App Passwords:** https://myaccount.google.com/apppasswords  
**SMTP Settings:** host=smtp.gmail.com, port=587  
**Daily Limit:** 500 emails/day (free Gmail)  
**Best Port:** 587 (TLS) or 465 (SSL)

---

## Next Steps

After setup:
1. ✅ Restart backend server
2. ✅ Create task with reminder
3. ✅ Check email for notification
4. ✅ Customize email templates in `gmailService.js`
