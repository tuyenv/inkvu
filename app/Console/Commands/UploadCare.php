<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Contracts\Filesystem\Filesystem;
use Storage;
use Unirest;

class UploadCare extends Command
{

    const UC_STATUS_PROCESS = 2;
    const UC_STATUS_FAIL = 3;
    const UC_STATUS_DONE = 4;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uploadcare:s3';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'uploadcare files to s3';

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
        try {
            $links = Link::where('image', 'like', '%ucarecdn.com%')
                ->where(function($q) {
                    $q->where('ucstatus', 0)
                        ->orWhere('ucstatus', 1);
                })->orderBy('id', 'asc')
                ->limit(10)->get();

            if (empty($links)) {
                $this->info('Empty');
                return false;
            }

            $s3 = \Storage::disk('s3');
            foreach ($links as $link) {
                // processing
                $this->updateUcStatus($link['id'], self::UC_STATUS_PROCESS);
                $filePath = time() . '_' . $link['id']. '.png';
                $uploadS3 = $s3->put($filePath, file_get_contents($link['image']), 'public');

                if ($uploadS3) {
                    $this->info($filePath);
                    $newImage = env('S3_URL') . env('S3_BUCKET') . '/' . $filePath;
                    $this->updateUcStatus($link['id'], self::UC_STATUS_DONE, $newImage); // done

                    // delete uploadcare file
                    $regex = '/ucarecdn\.com\/([a-zA-Z0-9-]*)/';
                    preg_match_all($regex, $link['image'], $matches);
                    if (!empty($matches)) {
                        $uuid = $matches[1][0];
                        $this->deleteUploadCare($uuid);
                    }
                } else {
                    $this->updateUcStatus($link['id'], self::UC_STATUS_FAIL); // something wrong
                }
            }

        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    private function updateUcStatus($id, $status, $newImage = '')
    {
        $objLink = Link::where('id', $id)->first();
        $objLink->ucstatus = $status;
        if ($newImage != '') {
            $objLink->image = $newImage;
        }
        $objLink->save();
    }

    private function deleteUploadCare($uuid)
    {
        $headers = array(
            'Authorization' => 'Uploadcare.Simple '.env('UPLOADCARE_PUBLIC_KEY').':'.env('UPLOADCARE_SECRET_KEY'),
        );

        $endpoint = 'https://api.uploadcare.com/files/' . $uuid . '/';
        $client = new \GuzzleHttp\Client(array('headers' => $headers));
        $client->request('DELETE', $endpoint);
    }
}