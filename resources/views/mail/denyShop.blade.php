<!DOCTYPE html>
<html>
<head>
    <title>Shop Denied</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="max-w-2xl mx-auto p-6 bg-white shadow-md rounded-lg">
        <h1 class="text-2xl font-bold mb-4">Shop Denied</h1>
        @if(is_array($messageContent))
            <p class="mb-4">Dear {{ $messageContent['shopOwnerName'] }},</p>
            <p class="mb-4">We regret to inform you that your shop, {{ $messageContent['shopName'] }}, has been denied for the following reason(s):</p>
            <ul class="list-disc list-inside mb-4">
                @foreach($messageContent['reasons'] as $reason)
                    <li>{{ $reason }}</li>
                @endforeach
            </ul>
        @else
            <p class="mb-4">There was an error processing your request. Please contact support.</p>
        @endif
        <p class="mb-4">If you have any questions or need further assistance, please contact our support team.</p>
        <p class="mb-4">Thank you,</p>
        <p class="font-bold">The Multi Restaurant Team</p>
    </div>
</body>
</html>