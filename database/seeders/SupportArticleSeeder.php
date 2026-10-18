<?php

namespace Database\Seeders;

use App\Models\SupportArticle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SupportArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $articles = [
            'Payment & billing' => [
                [
                    'question' => 'Subscription plan',
                    'answer' => '<p>WEMU offers a range of subscription plans designed for music lovers, artists, and families:</p><ul><li><strong>Free Plan:</strong> Ad-supported streaming with access to our entire music catalog, curated playlists, and standard audio quality.</li><li><strong>Individual Premium:</strong> Ad-free listening, unlimited skips, high-fidelity audio, and offline downloads.</li><li><strong>Family Plan:</strong> All Premium features for up to 6 individual accounts living under the same household with shared billing.</li><li><strong>Artist Tier:</strong> Dedicated artist dashboard, analytics, stream tracking, and direct distribution tools.</li></ul><p>You can view and switch your plan anytime under <strong>Settings &gt; Subscription</strong>.</p>',
                    'sort_order' => 1,
                ],
                [
                    'question' => 'How to change payment method details',
                    'answer' => '<p>To update or change your payment method:</p><ol><li>Log in to your WEMU account on web or mobile app.</li><li>Navigate to <strong>Settings</strong> &gt; <strong>Subscription &amp; Billing</strong>.</li><li>Under <strong>Payment Method</strong>, click on <strong>Edit / Change Payment Method</strong>.</li><li>Provide your new Credit/Debit card information or PayPal account.</li><li>Click <strong>Save &amp; Update</strong> to confirm.</li></ol><p>Your next subscription renewal billing cycle will automatically use this updated payment method.</p>',
                    'sort_order' => 2,
                ],
                [
                    'question' => 'How to cancel premium plans',
                    'answer' => '<p>You can cancel your active Premium subscription at any time without extra fees:</p><ol><li>Go to your <strong>Profile</strong> &gt; <strong>Settings</strong>.</li><li>Select the <strong>Subscription</strong> tab.</li><li>Scroll down to your active plan details and select <strong>Cancel Subscription</strong>.</li><li>Choose a reason for cancellation and confirm your request.</li></ol><p><em>Note:</em> You will continue enjoying full Premium benefits until the end of your current prepaid billing cycle. Afterward, your account will smoothly transition to the standard Free tier without losing your saved playlists.</p>',
                    'sort_order' => 3,
                ],
                [
                    'question' => 'How to change plan',
                    'answer' => '<p>If you would like to upgrade or switch between plans (e.g. Monthly to Annual, or Individual to Family):</p><ol><li>Visit <strong>Settings</strong> &gt; <strong>Subscription</strong>.</li><li>Click on <strong>Change Plan</strong> to view available tiers.</li><li>Select the plan you wish to switch to and review the price adjustment.</li><li>Confirm the upgrade. Any remaining days on your current billing cycle will be prorated automatically.</li></ol>',
                    'sort_order' => 4,
                ],
                [
                    'question' => 'How to view purchase history',
                    'answer' => '<p>To review all your past charges and download official invoices:</p><ol><li>Go to <strong>Settings</strong> &gt; <strong>Billing &amp; Transactions</strong>.</li><li>You will see a list of all successful and pending transactions, including dates, plan details, and transaction IDs.</li><li>Click the <strong>Download Invoice</strong> button beside any payment to save a PDF copy for your personal records or tax filing.</li></ol>',
                    'sort_order' => 5,
                ],
            ],

            'Manage your account' => [
                [
                    'question' => 'How to change email id',
                    'answer' => '<p>To update the primary email address linked to your WEMU account:</p><ol><li>Navigate to <strong>Account Settings</strong> &gt; <strong>Security &amp; Login</strong>.</li><li>Click <strong>Edit</strong> next to your current email address.</li><li>Enter your new email address and confirm with your account password.</li><li>A verification link will be sent to your new email. Click the verification link within 24 hours to finalize the change.</li></ol>',
                    'sort_order' => 1,
                ],
                [
                    'question' => 'How to change password',
                    'answer' => '<p>Keeping your password fresh and secure is essential:</p><ol><li>Navigate to <strong>Settings</strong> &gt; <strong>Password &amp; Security</strong>.</li><li>Enter your current password.</li><li>Type in your new password (minimum 8 characters, containing uppercase, lowercase, numbers, and special characters).</li><li>Confirm the new password and click <strong>Update Password</strong>.</li></ol><p>If you have forgotten your password, use the <em>Forgot Password</em> link on the login screen to receive a secure password reset link via email.</p>',
                    'sort_order' => 2,
                ],
                [
                    'question' => 'How to enable/disable notification',
                    'answer' => '<p>You have full control over the notifications you receive from WEMU:</p><ol><li>Go to <strong>Settings</strong> &gt; <strong>Notification Preferences</strong>.</li><li>Toggle switches on or off for:<ul><li><strong>Push Notifications:</strong> New song releases from followed artists and playlist updates.</li><li><strong>Email Digests:</strong> Weekly top charts, music recommendations, and artist spotlights.</li><li><strong>Billing &amp; Account Alerts:</strong> Payment receipts and important security notices (recommended to remain active).</li></ul></li><li>Click <strong>Save Preferences</strong> to apply.</li></ol>',
                    'sort_order' => 3,
                ],
                [
                    'question' => 'How to update your profile',
                    'answer' => '<p>To personalize your WEMU listener or artist profile:</p><ol><li>Click on your profile avatar in the navigation bar and select <strong>My Profile</strong> &gt; <strong>Edit Profile</strong>.</li><li>Update your display name, username handle, bio, country, and gender.</li><li>Upload a new profile picture or banner image (supported formats: JPG, PNG, WEBP).</li><li>Click <strong>Save Changes</strong> to instantly update your public profile.</li></ol>',
                    'sort_order' => 4,
                ],
                [
                    'question' => 'How to logout of all devices',
                    'answer' => '<p>If you used a shared computer or suspect unauthorized activity on your account:</p><ol><li>Go to <strong>Settings</strong> &gt; <strong>Security</strong> &gt; <strong>Connected Devices</strong>.</li><li>Review the list of devices, browser types, and last active locations.</li><li>Click the red <strong>Log Out Of All Devices</strong> button.</li><li>Confirm with your password. All active sessions across other phones, tablets, and web browsers will be terminated immediately.</li></ol>',
                    'sort_order' => 5,
                ],
                [
                    'question' => 'How to delete your account',
                    'answer' => '<p>We are sorry to see you go. If you want to permanently delete your WEMU account:</p><ol><li>Navigate to <strong>Settings</strong> &gt; <strong>Privacy &amp; Data</strong> &gt; <strong>Delete Account</strong>.</li><li>Review the warning: Deleting your account is irreversible and removes your playlists, listening history, favorites, and active subscriptions.</li><li>Type your password to verify your identity.</li><li>Click <strong>Permanently Delete My Account</strong>.</li></ol><p>You can also reach out to our support team at <a href="mailto:support@wemu.online">support@wemu.online</a> for assistance.</p>',
                    'sort_order' => 6,
                ],
            ],

            'Security & privacy' => [
                [
                    'question' => 'Data rights and privacy choices',
                    'answer' => '<p>Under international data protection regulations including GDPR and CCPA, you have fundamental rights over your personal data:</p><ul><li><strong>Right of Access:</strong> Request a full digital copy of all personal data WEMU holds about you.</li><li><strong>Right to Rectification:</strong> Correct any inaccurate or incomplete information.</li><li><strong>Right to Erasure:</strong> Request permanent deletion of your data when no longer needed.</li><li><strong>Right to Opt-Out:</strong> Disable personalized advertising and telemetry tracking.</li></ul><p>Manage your choices anytime under <strong>Settings &gt; Privacy Center</strong>.</p>',
                    'sort_order' => 1,
                ],
                [
                    'question' => 'Understanding your data',
                    'answer' => '<p>WEMU collects data strictly to personalize and improve your music streaming experience:</p><ul><li><strong>Account Information:</strong> Name, email address, date of birth, and preferred language.</li><li><strong>Usage Information:</strong> Songs played, skip frequency, playlists created, and search queries.</li><li><strong>Technical Data:</strong> Device model, operating system, and IP address for session security.</li></ul><p>We never sell your personal data to third parties. For full details, review our <a href="/privacy-policy" target="_blank">Privacy Policy</a>.</p>',
                    'sort_order' => 2,
                ],
                [
                    'question' => 'Safety and privacy center',
                    'answer' => '<p>The WEMU Safety &amp; Privacy Center is your centralized hub for platform security tools:</p><ul><li>Monitor real-time login activity and active sessions.</li><li>Configure Multi-Factor Authentication (MFA / 2FA) via email OTP or authenticator apps.</li><li>Report copyrighted content, explicit violations, or fraudulent artist impersonations.</li><li>Set explicit content filters for family-friendly listening.</li></ul>',
                    'sort_order' => 3,
                ],
                [
                    'question' => 'Data privacy and cookies',
                    'answer' => '<p>Cookies help our website and web player remember who you are and maintain high playback performance:</p><ul><li><strong>Essential Cookies:</strong> Required for secure login authentication, CSRF protection, and stream caching.</li><li><strong>Performance Cookies:</strong> Measure streaming latency and audio buffer quality.</li><li><strong>Functional Cookies:</strong> Remember your volume level, dark/light theme choice, and repeat/shuffle settings.</li></ul><p>You can adjust your cookie consent settings via the Cookie banner in the website footer or within your web browser settings.</p>',
                    'sort_order' => 4,
                ],
                [
                    'question' => 'Terms and privacy',
                    'answer' => '<p>Our legal terms govern user conduct and platform operations:</p><ul><li><strong>Terms of Service:</strong> Outlines licensing agreements, copyright rules, forbidden activities (such as ripping or redistributing audio), and subscription terms. Visit <a href="/term-and-conditions" target="_blank">Terms and Conditions</a>.</li><li><strong>Privacy Policy:</strong> Explains data protection standards, data retention periods, and cross-border transfers. Visit <a href="/privacy-policy" target="_blank">Privacy Policy</a>.</li></ul>',
                    'sort_order' => 5,
                ],
                [
                    'question' => 'Security best practices',
                    'answer' => '<p>Follow these essential tips to keep your WEMU account and music collection safe:</p><ol><li><strong>Use a strong, unique password:</strong> Avoid using the same password that you use for other online services.</li><li><strong>Never share OTP verification codes:</strong> WEMU support staff will never ask for your password or one-time verification codes.</li><li><strong>Log out on shared computers:</strong> Always click Log Out when using public libraries, internet cafes, or shared devices.</li><li><strong>Beware of phishing attempts:</strong> Official emails from WEMU will only come from our verified domain (<code>@wemu.online</code>).</li></ol>',
                    'sort_order' => 6,
                ],
            ],
        ];

        foreach ($articles as $category => $items) {
            foreach ($items as $item) {
                SupportArticle::updateOrCreate(
                    [
                        'category' => $category,
                        'question' => $item['question'],
                    ],
                    [
                        'uuid' => (string) Str::uuid(),
                        'answer' => $item['answer'],
                        'sort_order' => $item['sort_order'],
                        'is_active' => 1,
                    ]
                );
            }
        }
    }
}
