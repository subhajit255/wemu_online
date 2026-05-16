<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Details</title>
</head>

<body
    style="margin: 0; padding: 24px; background-color: rgb(249, 250, 251); font-family: system-ui, -apple-system, sans-serif;">
    <div style="max-width: 896px; margin: 0 auto;">
        <!-- Header Card -->
        <div
            style="background-color: white; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05); margin-bottom: 24px;">
            <div style="background: linear-gradient(45deg, #b1c3cd, #97a049); padding: 24px; display: flex; align-items: center;">
                <img style="width: 225px;" alt="Logo" src="{{ asset('assets/media/logos/logo.png') }}" class="h-40px app-sidebar-logo-default" style="margin-right: 16px;"/>
                <div style="margin-left: 200px;">
                    <h1 style="color: white; font-size: 24px; font-weight: bold; margin: 0;">Invoice Details</h1>
                    <p style="color: rgb(219, 234, 254); margin-top: 4px;">Transaction #{{ $detail->transaction_id ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- User Details Section -->
            <div style="padding: 24px;">
                <div style="display: flex; align-items: center; margin-bottom: 16px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                    <h2 style="margin: 0 0 0 8px; font-size: 18px; color: rgb(31, 41, 55);">User Information</h2>
                </div>
                <div style="background-color: rgb(249, 250, 251); border-radius: 8px; padding: 16px;">
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
                        <div>
                            <p style="color: rgb(107, 114, 128); font-size: 14px; margin: 0 0 4px 0;">Name</p>
                            <p style="font-weight: 500; color: rgb(17, 24, 39); margin: 0;">
                                {{ $detail->user->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p style="color: rgb(107, 114, 128); font-size: 14px; margin: 0 0 4px 0;">Phone</p>
                            <p style="font-weight: 500; color: rgb(17, 24, 39); margin: 0;">{{ getPhoneCode($detail->user?->id) }}
                                {{ $detail->user->mobile_number ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p style="color: rgb(107, 114, 128); font-size: 14px; margin: 0 0 4px 0;">Email</p>
                            <p style="font-weight: 500; color: rgb(17, 24, 39); margin: 0;">
                                {{ $detail->user->email ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p style="color: rgb(107, 114, 128); font-size: 14px; margin: 0 0 4px 0;">Subscription</p>
                            <p style="font-weight: 500; color: rgb(17, 24, 39); margin: 0;">
                                {{ $detail->subscription->title ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction Details Card -->
        <div
            style="background-color: white; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);">
            <div style="padding: 24px;">
                <div style="display: flex; align-items: center; margin-bottom: 16px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1-2-1Z" />
                    </svg>
                    <h2 style="margin: 0 0 0 8px; font-size: 18px; color: rgb(31, 41, 55);">Transaction Details</h2>
                </div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;">
                    <div style="background-color: rgb(249, 250, 251); border-radius: 8px; padding: 16px;">
                        <div style="display: flex; align-items: center; margin-bottom: 8px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <rect width="20" height="14" x="2" y="5" rx="2" />
                                <line x1="2" x2="22" y1="10" y2="10" />
                            </svg>
                            <p style="color: rgb(107, 114, 128); font-size: 14px; margin: 0 0 0 8px;">Transaction ID</p>
                        </div>
                        <p style="font-weight: 500; color: rgb(17, 24, 39); margin: 0;">
                            {{ $detail->transaction_id ?? 'N/A' }}</p>
                    </div>
                    <div style="background-color: rgb(249, 250, 251); border-radius: 8px; padding: 16px;">
                        <div style="display: flex; align-items: center; margin-bottom: 8px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1-2-1Z" />
                            </svg>
                            <p style="color: rgb(107, 114, 128); font-size: 14px; margin: 0 0 0 8px;">Amount</p>
                        </div>
                        <p style="font-weight: 500; color: rgb(17, 24, 39); margin: 0;">$ {{ $detail->amount ?? 'N/A' }}
                            {{ $detail->payment_status == 4 ? '(refunded)' : '' }}</p>
                    </div>
                    <div style="background-color: rgb(249, 250, 251); border-radius: 8px; padding: 16px;">
                        <div style="display: flex; align-items: center; margin-bottom: 8px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                                <line x1="16" x2="16" y1="2" y2="6" />
                                <line x1="8" x2="8" y1="2" y2="6" />
                                <line x1="3" x2="21" y1="10" y2="10" />
                            </svg>
                            <p style="color: rgb(107, 114, 128); font-size: 14px; margin: 0 0 0 8px;">Date & Time</p>
                        </div>
                        <p style="font-weight: 500; color: rgb(17, 24, 39); margin: 0;">
                            {{ \Carbon\Carbon::parse($detail->created_at)->format('M d, Y h:i A') ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div
                style="border-top: 1px solid rgb(243, 244, 246); padding: 24px; background-color: rgb(249, 250, 251);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="color: rgb(107, 114, 128); font-size: 14px;">
                        Thank you
                    </div>
                    <div>
                        <button onclick="window.print()"
                            style="margin-left: 8px; padding: 8px 16px; background-color: rgb(229, 231, 235); color: rgb(55, 65, 81); border: none; border-radius: 8px; cursor: pointer; font-size: 14px;">Print</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
