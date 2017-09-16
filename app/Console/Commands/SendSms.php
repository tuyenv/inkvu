<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\NotifyHelper;
use App\Models\Link;
use Aws\Sns\SnsClient;

class SendSms extends Command
{
    const PER_PROCESS = 10;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send notification sms';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $deliver = NotifyHelper::getNotifyDelivery('mobile', self::PER_PROCESS);
        if ($deliver->isEmpty()) {
            $this->info('Empty');
            return;
        }

        try {
            $client = SnsClient::factory(
                array(
                    'region'  => 'us-east-1',
                    'version' => 'latest',
                    'credentials' => [
                        'key'     => env('SNS_KEY'),
                        'secret'  => env('SNS_SECRET')
                    ]
                )
            );

            foreach ($deliver as $obj) {
                $this->updateNotifyDelivery($obj, 'process');
                $linkObj = Link::where('id', $obj->link_id)->first();
                if (empty($linkObj)) {
                    $this->info('Deleted Link');
                    $this->updateNotifyDelivery($obj, 'deleted');
                    continue;
                }

                $message = 'Your subscribed user published new url: ' . env('APP_PROTOCOL').env('APP_ADDRESS')."/{$linkObj->creator}/{$linkObj->short_url}";
                $payload = array(
                    'Message' => $this->truncate($message),
                    'MessageStructure' => 'string',
                    'PhoneNumber' => $obj->mobile,
                    'SenderID' => "Inkvu",
                    'SMSType' => "Promotional",
                );
                $client->publish($payload);

                $this->updateNotifyDelivery($obj, 'done');
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            $this->updateNotifyDelivery($obj, 'failed');
        }
    }

    private function updateNotifyDelivery($notifyDeliver, $status)
    {
        $notifyDeliver->status = $status;
        return $notifyDeliver->save();
    }

    private function truncate($string, $length = 160, $append = "...")
    {
        $string = trim($string);
        if (strlen($string) > $length) {
            $string = wordwrap($string, $length);
            $string = explode("\n", $string, 2);
            $string = $string[0] . $append;
        }
        return $string;
    }
}