<?php
require('header.php');
require('perlConfig.php');
require_once 'vendor/autoload.php'; 

$DEVELOPER_KEY = 'Add your key here !'; 
$client = new Google_Client();
$client->setDeveloperKey($DEVELOPER_KEY);
$youtube = new Google_Service_YouTube($client);

$videoDetails = '';
$downloadMessage = '';
$videoheading = ''; 


if (isset($_POST['url'])) {
    $videoUrl = $_POST['url'];

    // Extract video ID from URL
    parse_str(parse_url($videoUrl, PHP_URL_QUERY), $urlParams);
    $videoId = $urlParams['v'] ?? '';

    if ($videoId) {
        try {
            // Fetch video details
            $response = $youtube->videos->listVideos('snippet,contentDetails,statistics', [
                'id' => $videoId,
            ]);

            if (!empty($response['items'])) {
                $video = $response['items'][0];
                $title = $video['snippet']['title'];
                $description = $video['snippet']['description'];
                $thumbnail = $video['snippet']['thumbnails']['high']['url'];

                $videoheading = $title; 
                $videoDetails = "
                    <h4>{$title}</h4>
                    <img src='{$thumbnail}' alt='{$title}' width='100%'class='img-fluid mb-3'>
                ";               
            } else {
                $videoDetails = "No video found with this Link.";
            }
        } catch (Google_Service_Exception $e) {
            $videoDetails = 'API Error: ' . htmlspecialchars($e->getMessage());
        } catch (Exception $e) {
            $videoDetails = 'Error: ' . htmlspecialchars($e->getMessage());
        }
    } else {
        $videoDetails = "Invalid video URL.";
    }
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mt-5 shadow-sm">
                <div class="card-body">
                    <h1 class="card-title text-center">YouTube Thumbnail Downloader</h1>
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="url" class="form-label">YouTube thumbnail URL:</label>
                            <input type="text" name="url" id="url" class="form-control" placeholder="Enter YouTube URL" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Download</button>
                        </div>
                    </form>
                    <div>                    
                        <?php echo $videoDetails; ?>
                    </div>
                    <div>
                        <?php echo htmlspecialchars($downloadMessage); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require('footer.php');
?>
