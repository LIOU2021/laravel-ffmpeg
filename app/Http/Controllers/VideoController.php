<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Requests\StoreVideoRequest;
use App\Jobs\ConvertVideoForDownloading;
use App\Jobs\ConvertVideoForStreaming;
use App\Models\Video;

class VideoController extends Controller
{
    /**
     * 上傳檔案與轉檔作業
     * 
     */
    public function store(StoreVideoRequest $request)
    {
        $video = Video::create([
            'disk'          => 'local',
            'original_name' => $request->video->getClientOriginalName(),
            'path'          => $request->video->store('videos', 'local'),
            'title'         => $request->title,
        ]);

        $this->dispatch(new ConvertVideoForDownloading($video));
        $this->dispatch(new ConvertVideoForStreaming($video));

        return response()->json([
            'id' => $video->id,
        ], 201);
    }

    /**
     * 獲取影片m3u8公開的連結位置
     * 
     */
    public function show($id)
    {
        $streamUrl = Storage::disk('m3u8')->url($id . '.m3u8');
        return $streamUrl;
    }
}
