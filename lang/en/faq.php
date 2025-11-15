<?php

return [
    'intro_title' => 'Need help?',
    'intro_text' => 'Here are the most common questions about orders, payments, and product warranties. Pick a category below or reach out to our support team if you need extra assistance.',
    'support_title' => 'Still need help?',
    'support_text' => 'Our support team is ready 7 days a week via live chat, email, or the contact page.',
    'groups' => [
        [
            'category' => 'Orders & Shipping',
            'items' => [
                [
                    'question' => 'How long does shipping take?',
                    'answer' => 'Orders are processed within 1–2 business days. Deliveries within Greater Bangkok usually take 2–4 days, while other provinces take 3–7 days depending on the courier.',
                ],
                [
                    'question' => 'How can I track my order status?',
                    'answer' => 'Once your parcel leaves our warehouse we email the tracking number. You can monitor it from your account page or the courier’s website.',
                ],
                [
                    'question' => 'Can I pick up the order in person?',
                    'answer' => 'We currently focus on nationwide delivery. You are welcome to visit our Bangkok showroom for product demos before purchasing.',
                ],
            ],
        ],
        [
            'category' => 'Payments',
            'items' => [
                [
                    'question' => 'Which payment methods are available?',
                    'answer' => 'We accept bank transfers, virtual accounts, major e-wallets, credit cards, and 0% installments with selected banks.',
                ],
                [
                    'question' => 'Is my transaction secure?',
                    'answer' => 'Yes. Every payment goes through PCI-DSS certified gateways with full encryption and fraud monitoring.',
                ],
                [
                    'question' => 'Can I change the payment method after checkout?',
                    'answer' => 'For security reasons the payment method can’t be edited once an order is submitted. Please place a new order or contact support for guidance.',
                ],
            ],
        ],
        [
            'category' => 'Products & Warranty',
            'items' => [
                [
                    'question' => 'Do the products include official warranties?',
                    'answer' => 'All hardware we sell comes with an official distributor warranty for at least 1 year. Warranty details are listed on every product page.',
                ],
                [
                    'question' => 'How do I submit a warranty claim?',
                    'answer' => 'Contact our support team with your proof of purchase plus photos or videos that show the issue. We’ll coordinate the claim with the authorized service center.',
                ],
                [
                    'question' => 'Can I return an item if it doesn’t match my order?',
                    'answer' => 'Returns are accepted within 7 days of delivery as long as the seal is intact and all accessories are complete. We’ll help you organize the pickup.',
                ],
            ],
        ],
    ],
];
