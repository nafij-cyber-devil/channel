<html>
<head>
<title>AynaOTT | FREDFLIX</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<script src="https://cdn.jsdelivr.net/clappr/latest/clappr.min.js"></script>
<script src="https://cdn.jsdelivr.net/clappr.level-selector/latest/level-selector.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@clappr/hlsjs-playback@1.0.1/dist/hlsjs-playback.min.js"></script>
<link rel="icon" href="https://i.postimg.cc/rmw0zVvB/logo-2.png"/>
<link rel="shortcut icon" href="https://i.postimg.cc/rmw0zVvB/logo-2.png"/>
<style>body { background-color: #000000; }</style>
</head>
<style>
     .watermark {
            position: absolute;
            bottom: 10px;
            right: 10px;
            color: #fff;
            font-size: 12px;
            font-weight: bold;
            z-index: 1; 
        }
        </style>
<body>
<div id="player" style="height: 100%; width: 100%;"></div>
<div class="watermark">@fredflixceo</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>document.write(atob("PHNjcmlwdCB0eXBlPSd0ZXh0L2phdmFzY3JpcHQnIHNyYz0nLy9wbDI0NDQ3OTM4LnByb2ZpdGFibGVjcG1yYXRlLmNvbS85My81ZC9lNC85MzVkZTRlNGI5NWExMmQwMWNhMGMzNTYwZWZiYmE0Ni5qcyc+PC9zY3JpcHQ+"));</script>
<script>
var player = new Clappr.Player({
    source: 'live.php?id=' + getURLParameterByName('id') + '&e=.m3u8',
    width: '100%',
    height: '100%',
    autoPlay: true,
    plugins: [HlsjsPlayback, LevelSelector],
    mimeType: "application/x-mpegURL",
    mediacontrol: { seekbar: "#ff0000", buttons: "#eee" },
    parentId: "#player",
});
function getURLParameterByName(name)
{
    const url = window.location.href;
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
}
</script>
<script>document.write(atob("PHNjcmlwdCB0eXBlPSd0ZXh0L2phdmFzY3JpcHQnIHNyYz0nLy9wbDI0NDQ3OTM4LnByb2ZpdGFibGVjcG1yYXRlLmNvbS85My81ZC9lNC85MzVkZTRlNGI5NWExMmQwMWNhMGMzNTYwZWZiYmE0Ni5qcyc+PC9zY3JpcHQ+"));</script>
</body>
</html>