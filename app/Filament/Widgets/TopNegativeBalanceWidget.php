Tables\Columns\TextColumn::make('whatsapp')
    ->label('Send Message')
    ->formatStateUsing(function ($state, $record) {
        if (empty($record->phone1) || $record->calculated_balance >= 0) {
            return '';
        }
        
        $cleanPhone = preg_replace('/[^0-9]/', '', $record->phone1);
        $formattedBalance = number_format(abs($record->calculated_balance), 0);
        
        $message = "Dear {$record->name}, your current balance is -{$formattedBalance}৳. Please clear your due as soon as possible. Thank you.";
        $whatsappUrl = "https://wa.me/{$cleanPhone}?text=" . urlencode($message);
        
        return <<<HTML
<div style="display: flex; justify-content: center;">
    <a href="{$whatsappUrl}" 
       target="_blank" 
       style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; background-color: #22c55e; color: white; font-size: 0.75rem; font-weight: 500; border-radius: 0.5rem; text-decoration: none; gap: 0.5rem;"
       title="Send WhatsApp reminder">
        <svg style="width: 1rem; height: 1rem;" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.67-1.612-.916-2.207-.242-.579-.487-.5-.67-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
        </svg>
        WhatsApp
    </a>
</div>
HTML;
    })
    ->html()
    ->alignCenter(),