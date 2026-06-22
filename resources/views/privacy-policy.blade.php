<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - SaaS Commerce</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .prose h2 { font-size: 1.25rem; font-weight: 800; margin-top: 2rem; margin-bottom: 0.75rem; color: #111827; }
        .prose h3 { font-size: 1.1rem; font-weight: 700; margin-top: 1.5rem; margin-bottom: 0.5rem; color: #1f2937; }
        .prose p { margin-bottom: 0.75rem; line-height: 1.75; color: #374151; }
        .prose ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1rem; }
        .prose li { margin-bottom: 0.5rem; line-height: 1.75; color: #374151; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- Header -->
<div class="bg-white border-b border-gray-200">
    <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="/" class="flex items-center gap-2">
            <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <span class="font-black text-gray-900">SaaS Commerce</span>
        </a>
        <a href="/" class="text-sm text-gray-500 font-bold hover:text-gray-700">&larr; Back to Home</a>
    </div>
</div>

<!-- Content -->
<div class="max-w-4xl mx-auto px-4 py-12">
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 md:p-12">

        <h1 class="text-3xl font-black text-gray-900 mb-2">Privacy Policy</h1>
        <p class="text-sm text-gray-400 font-semibold mb-8">Effective Date: {{ date('d-m-Y') }}</p>

        <div class="prose">

            <h2>1. Introduction</h2>
            <p>Welcome to SaaS Commerce ("Platform", "we", "us", or "our"). We operate a multi-tenant Software-as-a-Service (SaaS) e-commerce platform that enables merchants to create and manage their online stores. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our platform, including our WhatsApp Business messaging integration.</p>
            <p>By accessing or using our Platform, you agree to the collection and use of information in accordance with this policy. If you do not agree with the terms of this Privacy Policy, please do not access the Platform.</p>

            <h2>2. Information We Collect</h2>
            <p>We collect information to provide and improve our services. The types of information we collect include:</p>

            <h3>a. Account Information</h3>
            <ul>
                <li>Name, email address, and password (when you register as a merchant or store admin)</li>
                <li>Business name, business type, and contact information</li>
                <li>Phone number (for account verification and WhatsApp messaging)</li>
            </ul>

            <h3>b. Store & Customer Data</h3>
            <ul>
                <li>Product listings, pricing, and inventory data</li>
                <li>Customer names, phone numbers, email addresses, and shipping addresses (entered by merchants)</li>
                <li>Order details, payment method, and transaction history</li>
            </ul>

            <h3>c. WhatsApp Messaging Data</h3>
            <ul>
                <li>WhatsApp Business API credentials (access tokens, phone number IDs) stored securely in encrypted format</li>
                <li>Messages sent and received through the Platform's WhatsApp CRM (order confirmations, shipping updates, customer support conversations)</li>
                <li>Phone numbers used for sending and receiving WhatsApp messages</li>
                <li>Message delivery status and read receipts</li>
            </ul>

            <h3>d. Automatically Collected Information</h3>
            <ul>
                <li>Device information (browser type, operating system, device type)</li>
                <li>IP address, time zone, and geographic location</li>
                <li>Usage data (pages visited, features used, time spent on the Platform)</li>
                <li>Cookies and similar tracking technologies</li>
            </ul>

            <h2>3. How We Use Your Information</h2>
            <p>We use the collected information for the following purposes:</p>

            <ul>
                <li><strong>Platform Operation:</strong> To provide, maintain, and improve the SaaS e-commerce platform and its features</li>
                <li><strong>Order Fulfillment:</strong> To process orders, arrange shipping, and send order confirmations/invoices</li>
                <li><strong>WhatsApp Messaging:</strong> To send transactional messages including order status updates, shipping notifications, and customer support responses via WhatsApp Business API</li>
                <li><strong>Account Management:</strong> To create and manage merchant accounts, authenticate users, and provide customer support</li>
                <li><strong>Communication:</strong> To send important platform updates, security alerts, and service notifications</li>
                <li><strong>Analytics:</strong> To analyze usage patterns and improve the Platform's performance and user experience</li>
                <li><strong>Legal Compliance:</strong> To comply with applicable laws, regulations, and legal processes</li>
            </ul>

            <h2>4. WhatsApp Business Messaging</h2>
            <p>Our Platform integrates with the WhatsApp Business Cloud API (provided by Meta Platforms, Inc.) to enable merchants to communicate with their customers. Regarding WhatsApp messaging:</p>

            <ul>
                <li>Messages are sent only to customers who have placed orders or initiated conversations with the merchant</li>
                <li>Message types include: order confirmations, shipping updates, delivery notifications, and customer support responses</li>
                <li>We do not send unsolicited marketing messages via WhatsApp</li>
                <li>WhatsApp message data is stored in our secure database and is associated with the relevant merchant's tenant</li>
                <li>Your use of WhatsApp messaging is also subject to <a href="https://www.whatsapp.com/legal/business-terms/" target="_blank" class="text-green-600 font-bold hover:underline">WhatsApp Business Terms of Service</a></li>
            </ul>

            <h2>5. Data Storage & Security</h2>
            <p>We implement industry-standard security measures to protect your information:</p>

            <ul>
                <li>All data is stored in encrypted databases with access controls</li>
                <li>WhatsApp API credentials are encrypted at rest</li>
                <li>We use HTTPS/TLS encryption for all data in transit</li>
                <li>Regular security audits and vulnerability assessments</li>
                <li>Multi-tenant architecture ensures data isolation between merchants</li>
            </ul>

            <h2>6. Data Sharing & Disclosure</h2>
            <p>We do not sell your personal information. We may share your information only in the following circumstances:</p>

            <ul>
                <li><strong>Service Providers:</strong> Third-party hosting providers (e.g., Render), payment processors, and delivery services that help us operate the Platform</li>
                <li><strong>Meta Platforms:</strong> WhatsApp Business API data is processed by Meta Platforms, Inc. in accordance with their <a href="https://www.whatsapp.com/legal/privacy-policy/" target="_blank" class="text-green-600 font-bold hover:underline">Privacy Policy</a></li>
                <li><strong>Legal Requirements:</strong> When required by law, subpoena, or other legal processes</li>
                <li><strong>Business Transfers:</strong> In connection with a merger, acquisition, or sale of assets</li>
            </ul>

            <h2>7. Your Rights</h2>
            <p>You have the following rights regarding your personal information:</p>

            <ul>
                <li><strong>Access:</strong> Request a copy of the personal data we hold about you</li>
                <li><strong>Correction:</strong> Request correction of inaccurate or incomplete data</li>
                <li><strong>Deletion:</strong> Request deletion of your personal data (subject to legal retention requirements)</li>
                <li><strong>Opt-Out:</strong> Unsubscribe from non-essential communications at any time</li>
                <li><strong>Data Portability:</strong> Request your data in a structured, machine-readable format</li>
            </ul>

            <h2>8. Data Retention</h2>
            <p>We retain your information for as long as your account is active or as needed to provide services. Specifically:</p>

            <ul>
                <li>Account data is retained while the account is active and for 30 days after deletion request</li>
                <li>WhatsApp message logs are retained for 90 days for quality and support purposes</li>
                <li>Order data is retained as required by applicable tax and business laws</li>
                <li>Anonymized analytics data may be retained indefinitely</li>
            </ul>

            <h2>9. Cookies</h2>
            <p>We use cookies and similar technologies to:</p>
            <ul>
                <li>Maintain your login session</li>
                <li>Remember your preferences and settings</li>
                <li>Analyze Platform usage and performance</li>
                <li>Detect and prevent fraud</li>
            </ul>
            <p>You can control cookies through your browser settings. Disabling cookies may affect Platform functionality.</p>

            <h2>10. Children's Privacy</h2>
            <p>Our Platform is not intended for use by individuals under the age of 18. We do not knowingly collect personal information from children. If we become aware that we have collected data from a child, we will take steps to delete it promptly.</p>

            <h2>11. Changes to This Policy</h2>
            <p>We may update this Privacy Policy from time to time. We will notify you of any material changes by posting the new policy on this page and updating the "Effective Date" at the top. Your continued use of the Platform after changes constitutes acceptance of the updated policy.</p>

            <h2>12. Contact Us</h2>
            <p>If you have questions about this Privacy Policy or wish to exercise your rights, please contact us:</p>
            <ul>
                <li><strong>Platform:</strong> SaaS Commerce</li>
                <li><strong>Website:</strong> <a href="https://saas-ecommerce-xx7e.onrender.com" target="_blank" class="text-green-600 font-bold hover:underline">saas-ecommerce-xx7e.onrender.com</a></li>
                <li><strong>Email:</strong> privacy@saascommerce.com</li>
            </ul>

        </div>
    </div>
</div>

<!-- Footer -->
<div class="max-w-4xl mx-auto px-4 pb-8">
    <p class="text-center text-xs text-gray-400">&copy; {{ date('Y') }} SaaS Commerce. All rights reserved.</p>
</div>

</body>
</html>
