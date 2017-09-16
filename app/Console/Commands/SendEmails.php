<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\NotifyHelper;
use Mail;
use App\Models\Link;

class SendEmails extends Command
{
    const PER_PROCESS = 10;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send notification email';

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
        $deliver = NotifyHelper::getNotifyDelivery('email', self::PER_PROCESS);
        if ($deliver->isEmpty()) {
            $this->info('Empty');
            return;
        }

        try {
            foreach ($deliver as $obj) {
                $this->updateNotifyDelivery($obj, 'process');
                $linkObj = Link::where('id', $obj->link_id)->first();
                if (empty($linkObj)) {
                    $this->info('Deleted Link');
                    $this->updateNotifyDelivery($obj, 'deleted');
                    continue;
                }

                Mail::send('emails.notification', ['linkObj' => $linkObj], function ($m) use ($obj) {
                    $m->from('notification@ink.vu', 'Inkvu Notification');
                    $m->to($obj->email, $obj->email)->subject('Your subscribed user published new url');
                });

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
}