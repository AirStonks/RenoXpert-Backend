<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendLarkBookingNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 10;

    public function __construct(private array $campaignData)
    {
    }

    public function handle(): void
    {
        $campaignId = $this->campaignData['id'] ?? null;
        $campaignName = $this->campaignData['name'] ?? null;
        $campaignPackageName = $this->campaignData['package_name'] ?? null;
        $bookingNumber = $this->campaignData['booking_number'] ?? null;
        $ownerName = $this->campaignData['owner_name'] ?? null;
        $bookingFee = $this->campaignData['booking_fee'] ?? null;

        $bodyData = [
            "msg_type" => "interactive",
            "card" => [
                "elements" => [
                    [
                        "tag" => "markdown",
                        "content" => "**Congratulations to the Owner Sales Team!**\nWe've just locked in a new booking for **{$campaignName}**!\nThis milestone highlights your outstanding hustle, collaboration, and client-winning skills. Let's keep the momentum going."
                    ],
                    [
                        "tag" => "column_set",
                        "flex_mode" => "none",
                        "background_style" => "default",
                        "columns" => [
                            [
                                "tag" => "column",
                                "width" => "weighted",
                                "weight" => 1,
                                "vertical_align" => "top",
                                "elements" => [
                                    [
                                        "tag" => "div",
                                        "text" => [
                                            "content" => "**🔴 Campaign Package:**\n{$campaignPackageName}",
                                            "tag" => "lark_md"
                                        ]
                                    ]
                                ]
                            ],
                            [
                                "tag" => "column",
                                "width" => "weighted",
                                "weight" => 1,
                                "vertical_align" => "top",
                                "elements" => [
                                    [
                                        "tag" => "div",
                                        "text" => [
                                            "content" => "**👤 Owner Name:**\n{$ownerName}",
                                            "tag" => "lark_md"
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        "tag" => "column_set",
                        "flex_mode" => "none",
                        "background_style" => "default",
                        "columns" => [
                            [
                                "tag" => "column",
                                "width" => "weighted",
                                "weight" => 1,
                                "vertical_align" => "top",
                                "elements" => [
                                    [
                                        "tag" => "div",
                                        "text" => [
                                            "content" => "**🧾 Booking Number:**\n{$bookingNumber}",
                                            "tag" => "lark_md"
                                        ]
                                    ]
                                ]
                            ],
                            [
                                "tag" => "column",
                                "width" => "weighted",
                                "weight" => 1,
                                "vertical_align" => "top",
                                "elements" => [
                                    [
                                        "tag" => "div",
                                        "text" => [
                                            "content" => "**💵 Booking Fee:**\nRM {$bookingFee}",
                                            "tag" => "lark_md"
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        "tag" => "hr"
                    ],
                    [
                        "tag" => "div",
                        "text" => [
                            "content" => "🙋🏼 [View Campaign](https://app.renoxpert.my/campaigns/{$campaignId})",
                            "tag" => "lark_md"
                        ]
                    ]
                ],
                "header" => [
                    "template" => "red",
                    "title" => [
                        "tag" => "plain_text",
                        "content" => "New Booking - {$campaignName}"
                    ]
                ]
            ]
        ];

        $body = json_encode($this->convertEmptyArrayToObject($bodyData), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $client = new Client();
        $url = env('APP_BYPASS') === true ? env('LARK_MSG_TEST_OS_URL') : env('LARK_MSG_OS_URL');

        $req = $client->request('POST', $url, [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => $body,
            'timeout' => 10,
        ]);

        Log::info('Lark booking notification sent', [
            'booking_number' => $bookingNumber,
            'status' => $req->getStatusCode(),
            'response' => json_decode($req->getBody(), true),
        ]);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('Lark booking notification failed', [
            'booking_number' => $this->campaignData['booking_number'] ?? null,
            'error' => $e->getMessage(),
        ]);
    }

    private function convertEmptyArrayToObject($data)
    {
        if (is_array($data)) {
            if (empty($data)) {
                return (object) [];
            }
            foreach ($data as $key => $value) {
                $data[$key] = $this->convertEmptyArrayToObject($value);
            }
        }
        return $data;
    }
}
