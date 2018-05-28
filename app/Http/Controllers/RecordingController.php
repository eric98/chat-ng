<?php

namespace App\Http\Controllers;

use App\Recording;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Audio\Aac;
use Illuminate\Http\Request;

class RecordingController extends Controller
{
    public function postRecording(Request $request)
    {
        // Get the POSTed file
        $file = $request->file('recording');
        // Create a random file name
        $fileName = hash('md5', uniqid(mt_rand(), true), false) . '.ogg';
        // Move the file to the storage directory
        $file->move(storage_path(), $fileName);
        // Initialize FFMpeg
        $ffmpeg = FFMpeg::create([
            'ffmpeg.binaries'   => '/usr/bin/avconv',
            'ffprobe.binaries'  => '/usr/bin/avprobe',
            'timeout'           => 3600,
            'ffmpeg.threads'    => 12,
        ]);
        // Open our OGG file
//        $oggAudio = $ffmpeg->open(storage_path() . '/' . $fileName);
//        // Set our AAC format
//        $format = new Aac();
//        $format->setAudioCodec('aac')
//            ->setAudioKiloBitrate(96)
//            ->setAudioChannels(1);
//        // Create a new random file name
//        $fileName = hash('md5', uniqid(mt_rand(), true), false) . '.aac';
//        $newFilePath = storage_path() . '/' . $fileName;
//        // Save our file as AAC on the new file path
//        $oggAudio->save($format, $newFilePath);
//        // Save our recording
        $recording = Recording::create([
            'filename'      => $fileName,
            'path'          => storage_path(),
            'mime_type'     => $file->getClientMimeType(),
            'content_size'  => $file->getClientSize(),
        ]);

        // Return success
        return response()->json($recording, 201);
    }

    public function getRecording(Request $request, $id)
    {
        // Fetch our recording by ID, or throw an exception
        $recording = Recording::findOrFail($id);
        // Return our recording data
        return response()->json($recording, 200);
    }
}
