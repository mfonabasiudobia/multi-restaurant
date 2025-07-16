<?php

namespace App\Repositories;

use App\Models\Video;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg as LaravelFFMpeg;
use FFMpeg;

class VideoRepository extends Repository
{
    /**
     * Get the model class for the repository.
     *
     * @return string
     */
    public static function model()
    {
        return Video::class;
    }

    /**
     * Store a video file and create a video record.
     *
     * @param UploadedFile $file
     * @param int $productId
     * @return Video
     */
    // public static function storeVideo(UploadedFile $file, int $productId): Video
    // {

    //     #error_reporting(E_ALL);
    //     # ini_set('display_errors', '1');

    //     // Set higher time limit for video processing (10 minutes)
    //     set_time_limit(600);
    //     ini_set('max_execution_time', '600');


    //     // $tempPath = $tempPath . $filename;
    //     try {
    //         // Create a safer temporary file path
    //         $filename = uniqid('video_', true) . '.mp4';
    //         $tempDir = storage_path('app');
    //         $tempPath = $tempDir . '/' . $filename;

    //         // Ensure temp directory exists
    //         if (!file_exists(dirname($tempPath))) {
    //             mkdir(dirname($tempPath), 0755, true);
    //         }

    //         $file->move($tempDir, $filename); // this moves the actual uploaded file

    //         // Upload to S3
    //         $localFile = new \Illuminate\Http\File($tempPath);

    //         // Upload to S3
    //         $videoPath = Storage::disk('s3')->putFileAs(
    //             'videos/products/' . $productId,
    //             $localFile,
    //             $filename
    //         );

    //         // Clean up
    //         // if (file_exists($tempPath)) {
    //         //     unlink($tempPath);
    //         // }
    //         \Log::info('Video uploaded to S3: ' . $videoPath);

    //         // 2. Stream just the first few seconds from S3 to generate thumbnail
    //         $thumbnailPath = self::generateThumbnailFromS3($videoPath, $productId);

    //         // 3. Create and return the video record
    //         return self::create([
    //             'product_id' => $productId,
    //             'src' => $videoPath,
    //             'thumbnail' => $thumbnailPath,
    //             'type' => "video/mp4", // Assuming MP4 for simplicity, adjust as needed
    //             'title' => $filename,
    //             'size' => $localFile->getSize()
    //         ]);
    //     } catch (\Exception $e) {
    //         dd($e->getMessage());
    //         // If something fails, cleanup any uploaded files
    //         if (isset($videoPath) && Storage::disk('s3')->exists($videoPath)) {
    //             Storage::disk('s3')->delete($videoPath);
    //         }
    //         if (isset($thumbnailPath) && Storage::disk('s3')->exists($thumbnailPath)) {
    //             Storage::disk('s3')->delete($thumbnailPath);
    //         }
    //         \Log::error('Failed to store video: ' . $e->getMessage());
    //         throw $e;
    //     }
    // }


    public static function storeVideo(UploadedFile $file, int $productId): Video
    {
        set_time_limit(600);
        ini_set('max_execution_time', '600');

        try {
            // Step 1: Save file temporarily
            $filename = uniqid('video_', true) . '.mp4';
            $tempDir = storage_path('app/temp');
            $tempPath = $tempDir . '/' . $filename;

            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $file->move($tempDir, $filename);
            $localFile = new \Illuminate\Http\File($tempPath);

            // Step 2: Generate HLS renditions (adaptive)
            $outputDirName = pathinfo($filename, PATHINFO_FILENAME);
            $hlsTempPath = storage_path("app/hls/{$outputDirName}");

            if (!file_exists($hlsTempPath)) {
                mkdir($hlsTempPath, 0755, true);
            }

            $videoPath = Storage::disk('s3')->putFileAs(
                'videos/products/' . $productId,
                new \Illuminate\Http\File($tempPath),
                $filename
            );

            $lowFormat = (new \FFMpeg\Format\Video\X264)
                ->setKiloBitrate(400)
                ->setAdditionalParameters(['-an']); // 400 kbps, no audio

            $mediumFormat = (new \FFMpeg\Format\Video\X264)
                ->setKiloBitrate(1000)
                ->setAdditionalParameters(['-an']); // 1000 kbps, no audio

            $highFormat = (new \FFMpeg\Format\Video\X264)
                ->setKiloBitrate(1500)
                ->setAdditionalParameters(['-an']); // 1500 kbps, no audio


            LaravelFFMpeg::fromDisk('local')
                ->open("temp/{$filename}")
                ->exportForHLS()
                ->addFormat($lowFormat)
                ->addFormat($mediumFormat)
                ->addFormat($highFormat)
                ->toDisk('local')
                ->save("hls/{$outputDirName}/master.m3u8");

            \Log::info("HLS adaptive export complete: hls/{$outputDirName}/master.m3u8");

            // Step 3: Upload to S3
            $localHlsPath = storage_path("app/hls/{$outputDirName}");
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($localHlsPath),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($files as $fileInfo) {
                if (!$fileInfo->isFile()) continue;

                $filePath = $fileInfo->getPathname();
                $keyName = "videos/products/{$productId}/hls/{$outputDirName}/" . $fileInfo->getFilename();
                Storage::disk('s3')->put($keyName, file_get_contents($filePath));
            }

            $hlsMasterPlaylist = "videos/products/{$productId}/hls/{$outputDirName}/master.m3u8";

            // Step 4: Generate thumbnail
            $thumbnailPath = self::generateThumbnailFromS3($videoPath, $productId);

            // Step 5: Return video record
            return self::create([
                'product_id' => $productId,
                'src' => $hlsMasterPlaylist,
                'thumbnail' => $thumbnailPath,
                'type' => "application/x-mpegURL",
                'title' => $filename,
                'size' => $localFile->getSize()
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to store video: ' . $e->getMessage());
            throw $e;
        } finally {
            // Clean up
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }

            if (isset($hlsTempPath)) {
                \File::deleteDirectory($hlsTempPath);
            }
        }
    }


    /**
     * Generate a thumbnail from a video stored in S3 by streaming a small segment.
     *
     * @param string $videoPath
     * @param int $productId
     * @return string
     * @throws \Exception
     */
    private static function generateThumbnailFromS3(string $videoPath, int $productId): string
    {
        // Set higher time limit for thumbnail generation (5 minutes)
        set_time_limit(300);
        ini_set('max_execution_time', '300');

        // Create temporary directory for processing
        $tempDir = storage_path('app/temp/thumbnails');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        try {
            // Create temporary files
            $tempVideoPath = $tempDir . '/' . uniqid() . '.mp4';
            $tempThumbnailPath = $tempDir . '/' . uniqid() . '.jpg';

            // Download just the first 2 seconds of the video using ffmpeg
            $s3Url = Storage::disk('s3')->url($videoPath);
            $ffmpegCommand = "ffmpeg -y -ss 0 -t 2 -i \"{$s3Url}\" \"{$tempVideoPath}\" 2>&1";
            exec($ffmpegCommand, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \Exception("Failed to download video segment: " . implode("\n", $output));
            }

            // Generate thumbnail from the downloaded segment
            $ffmpeg = FFMpeg\FFMpeg::create();
            $video = $ffmpeg->open($tempVideoPath);
            $video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(1))
                ->save($tempThumbnailPath);

            // Upload thumbnail to S3
            $thumbnailS3Path = 'thumbnails/products/' . $productId . '/' . basename($tempThumbnailPath);
            Storage::disk('s3')->put($thumbnailS3Path, file_get_contents($tempThumbnailPath));

            // Cleanup temporary files
            @unlink($tempVideoPath);
            @unlink($tempThumbnailPath);

            return $thumbnailS3Path;
        } catch (\Exception $e) {
            // Cleanup on error
            @unlink($tempVideoPath ?? '');
            @unlink($tempThumbnailPath ?? '');
            throw new \Exception("Failed to generate thumbnail: " . $e->getMessage());
        }
    }

    /**
     * Store multiple videos for a product.
     *
     * @param array $files
     * @param int $productId
     * @return array
     */
    public static function storeMultipleVideos(array $files, int $productId): array
    {
        $videos = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $videos[] = self::storeVideo($file, $productId);
            }
        }

        return $videos;
    }

    /**
     * Delete a video and its file.
     *
     * @param Video $video
     * @return bool
     */
    public static function deleteVideo(Video $video): bool
    {
        // Delete the video file if it exists on S3
        if (Storage::disk('s3')->exists($video->src)) {
            Storage::disk('s3')->delete($video->src);
        }

        // Delete the thumbnail file if it exists on S3
        if (Storage::disk('s3')->exists($video->thumbnail)) {
            Storage::disk('s3')->delete($video->thumbnail);
        }

        // Delete the video record
        return $video->delete();
    }
    /**
     * Remove a video and its associated thumbnail using video ID.
     *
     * @param int $id
     * @return bool
     */
    public static function removeVideo($id): bool
    {
        try {
            $video = self::find($id);
            \Log::info('Remove video: ' . $id);
            \Log::info('Remove video: ' . $video);
            if (!$video) {
                return false;
            }

            // Delete the video file if it exists on S3
            if ($video->src && Storage::disk('s3')->exists($video->src)) {
                Storage::disk('s3')->delete($video->src);
            }

            // Delete the thumbnail file if it exists on S3
            if ($video->thumbnail && Storage::disk('s3')->exists($video->thumbnail)) {
                Storage::disk('s3')->delete($video->thumbnail);
            }

            // Delete the video record
            return $video->delete();
        } catch (\Exception $e) {
            \Log::error('Error removing video: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete multiple videos by IDs for a specific product.
     *
     * @param array $videoIds
     * @param int $productId
     * @return int Number of videos deleted
     */
    public static function deleteMultipleVideos(array $videoIds, int $productId): int
    {
        $count = 0;
        foreach ($videoIds as $videoId) {
            try {
                $video = self::find($videoId);
                if ($video && $video->product_id === $productId) {
                    if (self::deleteVideo($video)) {
                        $count++;
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Error deleting video with ID ' . $videoId . ': ' . $e->getMessage());
            }
        }
        return $count;
    }

    /**
     * Update a video's metadata.
     *
     * @param Video $video
     * @param array $data
     * @return Video
     */
    public static function updateVideoMetadata(Video $video, array $data): Video
    {
        $updateData = array_intersect_key($data, array_flip(['title', 'type']));
        return self::update($video, $updateData);
    }

    /**
     * Replace a video file while keeping the same record.
     *
     * @param Video $video
     * @param UploadedFile $newFile
     * @return Video
     */
    public static function replaceVideoFile(Video $video, UploadedFile $newFile): Video
    {
        // Delete the old video file if it exists on S3
        if (Storage::disk('s3')->exists($video->src)) {
            Storage::disk('s3')->delete($video->src);
        }

        // Delete the old thumbnail file if it exists on S3
        if (Storage::disk('s3')->exists($video->thumbnail)) {
            Storage::disk('s3')->delete($video->thumbnail);
        }

        // Store the new video file on S3
        $videoPath = Storage::disk('s3')->putFile(
            'videos/products/' . $video->product_id,
            $newFile
        );

        // Generate and upload the new thumbnail
        $thumbnailPath = self::generateThumbnail($newFile, $video->product_id);

        // Update the video record
        return self::update($video, [
            'src' => $videoPath,
            'thumbnail' => $thumbnailPath,
            'type' => $newFile->getMimeType(),
            'title' => $newFile->getClientOriginalName(),
            'size' => $newFile->getSize()
        ]);
    }

    /**
     * Get all videos for a product.
     *
     * @param int $productId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getProductVideos(int $productId)
    {
        return self::query()->where('product_id', $productId)->get();
    }

    /**
     * Check if a product has reached its video limit.
     *
     * @param int $productId
     * @param int $limit
     * @return bool
     */
    public static function hasReachedVideoLimit(int $productId, int $limit = 5): bool
    {
        return self::query()
            ->where('product_id', $productId)
            ->count() >= $limit;
    }

    /**
     * Get total size of all videos for a product.
     *
     * @param int $productId
     * @return int Total size in bytes
     */
    public static function getTotalVideoSize(int $productId): int
    {
        return self::query()
            ->where('product_id', $productId)
            ->sum('size');
    }

    /**
     * Generate a thumbnail for the video using FFmpeg.
     *
     * @param UploadedFile $videoFile
     * @param int $productId
     * @return string
     */
    public static function generateThumbnail(UploadedFile $videoFile, int $productId): string
    {
        // Temporary path for storing the generated thumbnail
        $thumbnailDir = storage_path('app/public/thumbnails/');
        if (!is_dir($thumbnailDir)) {
            mkdir($thumbnailDir, 0777, true); // Ensure directory exists
        }

        $thumbnailPath = $thumbnailDir . uniqid() . '.jpg';

        // Get the video path
        $videoPath = $videoFile->getPathname();

        // Ensure the video file exists
        if (!file_exists($videoPath)) {
            throw new \Exception("Video file does not exist at path: " . $videoPath);
        }

        // FFmpeg command to generate a thumbnail at 1 second from the video
        try {
            $ffmpeg = FFMpeg\FFMpeg::create();
            $video = $ffmpeg->open($videoPath);

            // Capture a frame from the video to create a thumbnail
            $video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(1))
                ->save($thumbnailPath); // Save the thumbnail locally
        } catch (\Exception $e) {
            \Log::error("FFmpeg error: " . $e->getMessage());
            throw new \Exception("Error generating thumbnail: " . $e->getMessage());
        }

        // Upload the generated thumbnail to S3
        $thumbnailS3Path = 'thumbnails/products/' . $productId . '/' . basename($thumbnailPath);
        Storage::disk('s3')->put($thumbnailS3Path, fopen($thumbnailPath, 'r'));

        // Delete the local thumbnail file after upload to S3
        if (file_exists($thumbnailPath)) {
            unlink($thumbnailPath);
        } else {
            \Log::warning("Failed to delete temporary thumbnail file: " . $thumbnailPath);
        }

        return $thumbnailS3Path;
    }
}
