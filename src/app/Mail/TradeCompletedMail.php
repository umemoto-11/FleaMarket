<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Blade;

class TradeCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $seller;
    public $buyer;
    public $trade;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($seller, $buyer, $trade)
    {
        $this->seller = $seller;
        $this->buyer  = $buyer;
        $this->trade  = $trade;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $html = Blade::render(
            file_get_contents(base_path('src/resources/views/trade/completed.blade.php')),
            [
                'seller' => $this->seller,
                'buyer'  => $this->buyer,
                'trade'  => $this->trade,
            ]
        );

        return $this->subject('取引が完了しました')
                    ->from(config('mail.from.address'), config('mail.from.name'))
                    ->html($html);
    }
}
