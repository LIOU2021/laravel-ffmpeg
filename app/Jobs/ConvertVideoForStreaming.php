<?php

namespace App\Jobs;

use App\Models\Video;
use Carbon\Carbon;
use FFMpeg;
use FFMpeg\Format\Video\X264;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Exporters\HLSExporter;

class ConvertVideoForStreaming implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $video;

    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    public function handle()
    {


        // $encryptionKey = HLSExporter::generateEncryptionKey();

        // create some video formats...
        $lowBitrateFormat  = (new X264)->setKiloBitrate(500);
        $midBitrateFormat  = (new X264)->setKiloBitrate(1500);
        $highBitrateFormat = (new X264)->setKiloBitrate(3000);

        $m3u8PrefixPath = "/" . $this->video->id . "/";

        // open the uploaded video from the right disk...
        FFMpeg::fromDisk($this->video->disk)
            ->open($this->video->path)

            // call the 'exportForHLS' method and specify the disk to which we want to export...
            ->exportForHLS()
            // ->withEncryptionKey($encryptionKey)
            ->withRotatingEncryptionKey(function ($filename, $content) use ($m3u8PrefixPath) {
                Storage::disk('m3u8')->put($m3u8PrefixPath . $filename, $content);
            })
            ->toDisk('m3u8')

            // we'll add different formats so the stream will play smoothly
            // with all kinds of internet connections...
            ->addFormat($lowBitrateFormat)
            ->addFormat($midBitrateFormat)
            ->addFormat($highBitrateFormat)

            // call the 'save' method with a filename...
            ->save($m3u8PrefixPath . $this->video->id . '.m3u8');

        // update the database so we know the convertion is done!
        $this->video->update([
            'converted_for_streaming_at' => Carbon::now(),
        ]);
    }
}
