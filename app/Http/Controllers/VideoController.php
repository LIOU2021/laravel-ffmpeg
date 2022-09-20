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

    public function show($id)
    {
        // $downloadUrl = Storage::disk('downloadable_videos')->url($id . '.mp4');
        // $streamUrl = Storage::disk('m3u8')->url($id . '.m3u8');

        // return $streamUrl;
    }
}
