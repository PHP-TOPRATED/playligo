<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel='shortcut icon' href='{{ asset('img/favicon-32x32.png') }}' type='image/x-icon'/>
    <link href='https://fonts.googleapis.com/css?family=Signika+Negative:400,300,600,700' rel='stylesheet'
          type='text/css'>
    <link href="{{ asset('embed/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('embed/css/slider.min.css') }}" rel="stylesheet">
    <title>{{ $page_title }}</title>
</head>
<body>
<div id="content-wrapper">
    <header class="clearfix">
        <a target="_blank" href="{{ url('/') }}" class="logo">
            <img src="{{ asset('img/logo_playligo_md.png') }}" alt="Playligo">
        </a>
        <a target="_blank" href="{{ url('search') }}" class="btn btn-success">Visualize Your Travel</a>
    </header>
    <main>
        <div class="video_wrapper">
            <div class="link">
                <a href="{{ route('public_playlist.view', ['playlist' => $playlist->pl_slug]) }}" target="_blank">{{ $playlist->pl_title }}</a>
            </div>
            <div id="player"></div>
        </div>
    </main>
    <footer>
        {{-- Keywords --}}
        <div class="clearfix">
            <div class="keywords-tabs">
                <ul class="nav nav-tabs clearfix" role="tablist" id="keywords-list">
                    @foreach ($playlist->keys as $i => $key)
                        <li role="presentation" class="{{ $i === 0 ? 'active ' : '' }}">
                            <a href=""
                               class="keyword-tab"
                               aria-controls="tab-general"
                               role="tab"
                               data-keyword="{{$key->plk_key}}"
                               data-toggle="tab">
                                {{ $key->plk_key }}
                            </a>
                        </li>
                    @endforeach
                </ul>
                <div class="controls">
                    <a class="prev"><i class="fa fa-chevron-circle-left" aria-hidden="true"></i></a>
                    <a class="next"><i class="fa fa-chevron-circle-right" aria-hidden="true"></i></a>
                </div>
            </div>
        </div>
        {{--End of keywords--}}
        {{--Playlist--}}
        <ul class="playlist">
            @foreach ($videos as $key => $video)
                <?php $video_snippet = unserialize($video->plv_snippet) ?>
                <li>
                    <a href="#" id="{{ $video->plv_video_id }}" vorder="{{ $key }}" class="play_video"></a>
                </li>
            @endforeach
        </ul>
        {{--End of Playlist--}}
    </footer>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="{{ asset('embed/js/tabs.min.js') }}"></script>
<script src="{{ asset('embed/js/slider.min.js') }}"></script>
<script>
    var tag = document.createElement('script');
    tag.src = "https://www.youtube.com/iframe_api";
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
    var player;
    function onYouTubeIframeAPIReady() {
        player = new YT.Player('player', {
            playerVars: {'autoplay': 0, 'controls': 2, 'showinfo': 1},
            events: {
                'onReady': onPlayerReady,
                'onStateChange': onPlayerStateChange
            }
        });
    }
    function onPlayerReady(event) {
                <?php $vid = [] ?>
                @foreach ($videos as $item)
                <?php $vid[] = $item->plv_video_id; ?>
                @endforeach
        var videos = {!! json_encode($vid) !!};
        event.target.loadPlaylist(videos);
        event.target.playVideo();
    }
    var done = false;
    function onPlayerStateChange(event) {
        if (event.data == YT.PlayerState.PLAYING && !done) {
            // setTimeout(stopVideo, 6000);
            done = true;
        }
    }
    $(document).ready(function () {
        var slider = $("#keywords-list").lightSlider({
            autoWidth: true,
            pager: false,
            auto: false,
            controls: false
        });
        $('body').on('click', '.play_video', function (event) {
            event.preventDefault();
            var id = $(this).attr('id');
            var vorder = $(this).attr('vorder');
            player.playVideoAt(vorder);

            return false;
        });
        $('.prev').click(function () {
            slider.goToPrevSlide();
        });
        $('.next').click(function () {
            slider.goToNextSlide();
        });
        var $head = $("iframe").contents().find("head");
        $head.append($("<link/>",
                { rel: "stylesheet", href: "{{ asset('embed/css/youtube.css') }}", type: "text/css" }));
    });
    /**
     * Handling click on keyword tab
     */
    $('a.keyword-tab').click(function () {
        if ($(this).parent().hasClass('active')) return;
        var videos = $('a.play_video');
        var keyword = $(this).attr('data-keyword');
        var q = "{{ $playlist->pl_location }}" + ' ' + keyword;
        // search videos by specified keyword
        $.get('/search/get_by_keyword', {
            query: q,
            pl_id: "{{$playlist->pl_id}}"
        }).done(function (data) {
            // data contains array of youtbue videos ids which match specified query
            var results = videos.filter(function (index) {
                // if video id from page is in array returned from server - that's the video we need
                return data.indexOf($(this).attr('id')) !== -1;
            });
            results[0].click();
        });
    });
</script>
</body>
</html>