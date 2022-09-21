<!DOCTYPE html>
<html>

<head>
    <meta charset=utf-8 />
    <title>轉檔server</title>

    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <link href="https://unpkg.com/video.js/dist/video-js.css" rel="stylesheet">
    <script src="https://unpkg.com/video.js/dist/video.js"></script>
    <script src="https://unpkg.com/videojs-contrib-hls/dist/videojs-contrib-hls.js"></script>

</head>

<body>
    <h1>影片串流範例</h1>
    
    <video id=example-video width=960 height=400 class="video-js vjs-default-skin" controls>
        <source src="/test.m3u8" type="application/x-mpegURL">
    </video>

    <form id=load-url>
        <label>
            Video URL:
            <input id=url type=url value="https://d2zihajmogu5jn.cloudfront.net/bipbop-advanced/bipbop_16x9_variant.m3u8">
        </label>
        <button type=submit>Load</button>
    </form>
    <script>
    </script>

    <script>
        (function(window, videojs) {
            var player = window.player = videojs('example-video');

            // hook up the video switcher
            var loadUrl = document.getElementById('load-url');
            var url = document.getElementById('url');
            loadUrl.addEventListener('submit', function(event) {
                event.preventDefault();
                player.src({
                    src: url.value,
                    type: 'application/x-mpegURL'
                });
                return false;
            });
        }(window, window.videojs));
    </script>

</body>

</html>