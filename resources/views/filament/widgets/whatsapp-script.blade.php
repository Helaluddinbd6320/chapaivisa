<div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // WhatsApp button click handler
            document.addEventListener('click', function(event) {
                const button = event.target.closest('.whatsapp-btn');
                if (!button) return;
                
                event.preventDefault();
                
                const phone = button.dataset.phone.replace(/[^0-9]/g, '');
                const name = button.dataset.name;
                const balance = button.dataset.balance;
                
                // Professional WhatsApp message format
                const message = `🌟 *Visa Office Chapai International* 🌟
                
📋 *BALANCE REMINDER NOTIFICATION*

Dear *${name}*,

Your account has an outstanding balance:

💰 *Amount Due:* -${balance}৳
📊 *Status:* Payment Required
📅 *Date:* ${new Date().toLocaleDateString('en-GB')}

━━━━━━━━━━━━━━━━━━━━
💳 *PAYMENT OPTIONS:*
• Cash payment at our office
• Bank transfer
• Mobile banking (bKash, Nagad, Rocket)

🏢 *OFFICE INFORMATION:*
Visa Office Chapai International
[Your Office Address]
[Office Phone Number]

━━━━━━━━━━━━━━━━━━━━
Please clear your dues at the earliest to avoid any inconvenience.

Thank you for your cooperation.

Best regards,
*Visa Office Chapai International*`;
                
                const whatsappUrl = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
                window.open(whatsappUrl, '_blank');
            });
        });
    </script>
</div>