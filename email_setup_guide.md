# Custom Domain Email Setup Guide

## Option 1: Google Workspace (Recommended for Business)

### Setup Steps:
1. **Purchase Google Workspace** ($6/month per user)
   - Go to workspace.google.com
   - Add your domain (e.g., yourdomain.com)
   - Verify domain ownership

2. **DNS Configuration**
   Add these records to your domain DNS:
   ```
   MX Records:
   1 ASPMX.L.GOOGLE.COM
   5 ALT1.ASPMX.L.GOOGLE.COM
   5 ALT2.ASPMX.L.GOOGLE.COM
   10 ALT3.ASPMX.L.GOOGLE.COM
   10 ALT4.ASPMX.L.GOOGLE.COM
   ```

3. **Laravel Configuration**
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=your-email@yourdomain.com
   MAIL_PASSWORD=your-app-password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=your-email@yourdomain.com
   MAIL_FROM_NAME="UCUA Reporting System"
   ```

## Option 2: Mailgun (For Transactional Emails)

### Setup Steps:
1. **Sign up at mailgun.com**
2. **Add your domain**
3. **Configure DNS records** (provided by Mailgun)
4. **Laravel Configuration**
   ```env
   MAIL_MAILER=mailgun
   MAILGUN_DOMAIN=yourdomain.com
   MAILGUN_SECRET=your-mailgun-secret
   MAIL_FROM_ADDRESS=noreply@yourdomain.com
   MAIL_FROM_NAME="UCUA Reporting System"
   ```

## Option 3: SendGrid

### Setup Steps:
1. **Sign up at sendgrid.com**
2. **Verify your domain**
3. **Create API key**
4. **Laravel Configuration**
   ```env
   MAIL_MAILER=sendgrid
   SENDGRID_API_KEY=your-sendgrid-api-key
   MAIL_FROM_ADDRESS=noreply@yourdomain.com
   MAIL_FROM_NAME="UCUA Reporting System"
   ```

## Option 4: Custom SMTP Server

### If you have your own email server:
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=your-email@yourdomain.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@yourdomain.com
MAIL_FROM_NAME="UCUA Reporting System"
```

## Domain Examples for Your System

Based on your project, here are some domain suggestions:
- ucua-reporting.com
- ucua-system.com
- port-safety.com
- maritime-ucua.com
- ucua-portal.com

## Email Addresses You Might Want:
- admin@yourdomain.com
- noreply@yourdomain.com
- support@yourdomain.com
- ucua@yourdomain.com
- reports@yourdomain.com

## Testing Your Email Setup

After configuration, test with:
```bash
php artisan tinker
Mail::raw('Test email', function($msg) {
    $msg->to('test@example.com')->subject('Test');
});
```

## Security Considerations

1. **SPF Record**: Add to DNS
   ```
   TXT: v=spf1 include:_spf.google.com ~all
   ```

2. **DKIM**: Enable in your email provider

3. **DMARC**: Add policy record
   ```
   TXT: v=DMARC1; p=quarantine; rua=mailto:dmarc@yourdomain.com
   ```

## Cost Comparison

| Service | Cost | Features |
|---------|------|----------|
| Google Workspace | $6/month | Full email suite, calendar, drive |
| Mailgun | $0.80/1000 emails | Transactional focused |
| SendGrid | $14.95/month | 40,000 emails/month |
| Zoho Mail | $1/month | Basic email hosting |

## Next Steps

1. Choose your preferred option
2. Purchase/configure domain
3. Set up email service
4. Update Laravel .env file
5. Test email functionality
