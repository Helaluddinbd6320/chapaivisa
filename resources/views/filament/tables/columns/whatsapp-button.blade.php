@php
    $phone = $getRecord()->phone1 ?? '';
    $name = $getRecord()->name ?? '';
    $balance = $getRecord()->calculated_balance ?? 0;

    if (empty($phone)) {
        echo '<div class="text-center py-1">
            <span class="text-gray-400 text-xs">No phone</span>
        </div>';
        return;
    }

    if ($balance >= 0) {
        echo '<div class="text-center py-1">
            <span class="text-green-500 text-xs font-medium">✅ Cleared</span>
        </div>';
        return;
    }

    $formattedBalance = number_format(abs($balance), 0);
    $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

    // Use direct emoji characters
    $message = "⭐ *Visa Office Chapai International* ⭐\n\n";
    $message .= "📋 *BALANCE REMINDER NOTIFICATION*\n\n";
    $message .= "Dear *{$name}*,\n\n";
    $message .= "Your account has an outstanding balance:\n\n";
    $message .= "💰 *Amount Due:* -{$formattedBalance}৳\n";
    $message .= "📊 *Status:* Payment Required\n";
    $message .= "📅 *Date:* " . date('d/m/Y') . "\n\n";
    $message .= "━━━━━━━━━━━━━━━━━━━━\n";
    $message .= "💳 *PAYMENT OPTIONS:*\n";
    $message .= "• Cash payment at our office\n";
    $message .= "• Bank transfer\n";
    $message .= "• Mobile banking (bKash, Nagad, Rocket)\n\n";
    $message .= "🏢 *OFFICE INFORMATION:*\n";
    $message .= "Visa Office Chapai International\n";
    $message .= "[Your Office Address]\n";
    $message .= "[Office Phone Number]\n\n";
    $message .= "━━━━━━━━━━━━━━━━━━━━\n";
    $message .= "Please clear your dues at the earliest to avoid any inconvenience.\n\n";
    $message .= "Thank you for your cooperation.\n\n";
    $message .= "Best regards,\n";
    $message .= "*Visa Office Chapai International*";

    // Convert to UTF-8
    $message = iconv('UTF-8', 'UTF-8//IGNORE', $message);
    
    // Use http_build_query for proper encoding
    $whatsappUrl = "https://wa.me/{$cleanPhone}?" . http_build_query(['text' => $message]);
@endphp

<div class="flex flex-col items-center gap-1 py-1">
    <a href="{{ $whatsappUrl }}" 
       onclick="window.open(this.href, '_blank', 'noopener,noreferrer'); return false;"
       class="whatsapp-btn inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all duration-200 shadow hover:shadow-lg transform hover:-translate-y-0.5 gap-2 w-full max-w-[160px]"
       title="Send WhatsApp reminder for -{{ $formattedBalance }}৳">

        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.67-1.612-.916-2.207-.242-.579-.487-.5-.67-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
        </svg>
        <span>Send Reminder</span>
    </a>
    <span class="text-xs text-red-600 font-medium">-{{ $formattedBalance }}৳ Due</span>
</div>