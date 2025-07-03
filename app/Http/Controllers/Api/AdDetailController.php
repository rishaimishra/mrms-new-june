<?php

namespace App\Http\Controllers\Api;


use App\Models\AdDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;



class AdDetailController extends ApiController
{

    public function processAds($ad_category, $ad_type)
{
    // Retrieve ads based on category and type
    $ads = AdDetail::where('ad_category', $ad_category)
                   ->where('ad_type', $ad_type)
                   ->get();
    // Iterate over each ad to process videos and images
    foreach ($ads as $ad) {
        // Process Video
        if (isset($ad->ad_video)) {
            // Log the ad video path before transformation
            \Log::info("Ad Video Path Before asset(): " . $ad->ad_video);

            // Convert relative path to full URL using the asset function
            $ad->ad_video = asset('storage/' . $ad->ad_video); // Transform relative path to full URL

            // Log the final video URL
            \Log::info("Ad Video Path After asset(): " . $ad->ad_video);

            // If the video is an HLS stream (m3u8 file), use FFmpeg to extract dimensions
            if (strpos($ad->ad_video, '.m3u8') !== false) {
                $videoUrl = $ad->ad_video; // Full URL of the video

                // Run FFmpeg command to fetch video details (dimensions, etc.)
                $command = "ffmpeg -i {$videoUrl} 2>&1";  // Use the URL directly with FFmpeg
                $output = [];
                $status = null;

                // Execute the command and capture output
                exec($command, $output, $status);

                // Log the FFmpeg output for debugging purposes
                \Log::info('FFMpeg Output: ' . implode("\n", $output));

                // Search for dimensions in the FFmpeg output
                foreach ($output as $line) {
                    if (preg_match('/, ([0-9]+)x([0-9]+)/', $line, $matches)) {
                        // Extract width and height from the output
                        $ad->ad_video_width = $matches[1];  // Width
                        $ad->ad_video_height = $matches[2]; // Height
                    }
                }
            }
        }

        // Process Image
        if (isset($ad->ad_image)) {
            // Generate the full URL for the image
            $ad->ad_image = asset('storage/' . $ad->ad_image);

            // If it's an image URL, fetch the image data
            if (filter_var($ad->ad_image, FILTER_VALIDATE_URL)) {
                // Fetch image data
                $imageData = file_get_contents($ad->ad_image);

                // Get image size
                $imageSize = getimagesizefromstring($imageData);

                // Set image width and height
                $ad->ad_image_width = $imageSize[0];
                $ad->ad_image_height = $imageSize[1];
            } else {
                // If it's a local file, use the local path to fetch the image
                $imageData = file_get_contents(storage_path('app/public/' . $ad->ad_image));

                // Get image size
                $imageSize = getimagesizefromstring($imageData);

                // Set image width and height
                $ad->ad_image_width = $imageSize[0];
                $ad->ad_image_height = $imageSize[1];
            }
        }
    }

    // Return the processed ads
    return $ads;
}


    public function index(Request $request)
    {
        // Retrieve all ads from the database
        $ads = AdDetail::get();
    
        foreach ($ads as $ad) {
            // Check if the ad has a video and fetch its dimensions
            if (isset($ad->ad_video)) {
                // Assuming ad_video contains the relative path to the video
                $videoPath = $ad->ad_video; // This could be a URL like 'ads/hls/file-1280x720.m3u8'
                $ad->ad_video = asset('storage/' . $ad->ad_video);
                // If the video is an HLS stream (m3u8 file), use FFmpeg to extract dimensions
                if (strpos($videoPath, '.m3u8') !== false) {
                    // Construct the full URL for the video (ensure the base URL is correct)
                    $videoUrl = asset('storage/' . $videoPath); // Assuming the video is accessible through this URL

                    // Log the video URL for debugging purposes
                    \Log::info("Video URL: " . $videoUrl);
    
                    // Run FFmpeg command to fetch video details (dimensions, etc.)
                    $command = "ffmpeg -i {$videoUrl} 2>&1";  // Use the URL directly with FFmpeg
                    $output = [];
                    $status = null;
    
                    // Execute the command and capture output
                    exec($command, $output, $status);
                    // dd($output);
                    // Log the FFmpeg output for debugging purposes
                    \Log::info('FFMpeg Output: ' . implode("\n", $output));
    
                    // Search for dimensions in the FFmpeg output
                    foreach ($output as $line) {
                        if (preg_match('/, ([0-9]+)x([0-9]+)/', $line, $matches)) {
                            // Extract width and height from the output
                            $ad->ad_video_width = $matches[1];  // Width
                            $ad->ad_video_height = $matches[2]; // Height
                        }
                    }
                }
            }
    
            // Check if the ad has an image and fetch dimensions
            if (isset($ad->ad_image)) {
                // Generate the full URL for the image
                $ad->ad_image = asset('storage/' . $ad->ad_image);
    
                // If it's an image URL, fetch the image data
                if (filter_var($ad->ad_image, FILTER_VALIDATE_URL)) {
                    // Fetch image data
                    $imageData = file_get_contents($ad->ad_image);
    
                    // Get image size
                    $imageSize = getimagesizefromstring($imageData);
    
                    // Set image width and height
                    $ad->ad_image_width = $imageSize[0];
                    $ad->ad_image_height = $imageSize[1];
                } else {
                    // If it's a local file, use the local path to fetch the image
                    $imageData = file_get_contents(storage_path('app/public/' . $ad->ad_image));
    
                    // Get image size
                    $imageSize = getimagesizefromstring($imageData);
    
                    // Set image width and height
                    $ad->ad_image_width = $imageSize[0];
                    $ad->ad_image_height = $imageSize[1];
                }
            }
        }
    
        // Return the success response with ads and their generated video and image URLs
        return $this->success('success', [
            'ads' => $ads
        ]);
    }
    

    

    

    
    public function shopAds()
{
    // Call the common function with 'Shop' category and appropriate ad types
    $banner_ads = $this->processAds('Shop', 2);   // Banner type
    $small_large_ads = $this->processAds('Shop', 1); // Small Ad (Large)
    $small_medium_ads = $this->processAds('Shop', 3); // Small Ad (Medium)
    $small_small_ads = $this->processAds('Shop', 4); // Small Ad (Small)
    
    // Return the success response with the processed ads
    return $this->success('success', [
        'bannerads' => $banner_ads,
        'small_large_ads' => $small_large_ads,
        'small_medium_ads' => $small_medium_ads,
        'small_small_ads' => $small_small_ads
    ]);
}

    
    public function realestateAds()
    {
        // Call the common function with 'RealEstate' category and appropriate ad types
        $banner_ads = $this->processAds('RealEstate', 1);
        $small_ads = $this->processAds('RealEstate', 2);
    
        // Return the success response with the processed ads
        return $this->success('success', [
            'bannerads' => $banner_ads,
            'smallads' => $small_ads
        ]);
    }
    

    public function autoAds()
    {
        // Call the common function with 'Auto' category and appropriate ad types
        $banner_ads = $this->processAds('Auto', 1);
        $small_ads = $this->processAds('Auto', 2);
    
        // Return the success response with the processed ads
        return $this->success('success', [
            'bannerads' => $banner_ads,
            'smallads' => $small_ads
        ]);
    }
    
    public function utilitiesAds()
    {
        // Call the common function with 'Utilities' category and appropriate ad types
        $banner_ads = $this->processAds('Utilities', 1);
        $small_ads = $this->processAds('Utilities', 2);
    
        // Return the success response with the processed ads
        return $this->success('success', [
            'bannerads' => $banner_ads,
            'smallads' => $small_ads
        ]);
    }
    
}
