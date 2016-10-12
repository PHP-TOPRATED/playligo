@extends('layouts.app')

@section('content')
    <div class="container">
        <div id="head-section">
            <div class="page-breadcrumbs">
                <h1 class="section-title">
                    <img src="{{ !empty($owner->avatar) ? $owner->avatar : asset(config('playligo.avatar_default')) }}">
                    {{ $playlist->pl_title }}
                </h1>
            </div>
            @if (isset($share) && $share)
                @include('layouts.partials.sharing_modal', ['message' => 'Spread the travel bug now!'])
            @endif
            @include('layouts.partials.embed_modal', ['title' => 'Spread the travel bug now!'])
            <div class="section">
                <div class="entry-meta">
                    <ul class="list-inline">
                        <li class="posted-by"><i class="fa fa-user"></i> by <a href="#">{{ $owner->name }}</a></li>
                        <li class="publish-date"><a href="#"><i
                                        class="fa fa-clock-o"></i> {{ Carbon::parse($playlist->created_at)->diffForHumans() }}
                            </a></li>
                        <li class="views"><a href="#"><i class="fa fa-eye"></i> {{ $playlist->pl_view }} views</a></li>
                        <li>
                            {{ FormError::rating('plRating', 0) }}
                        </li>
                        <li>
                            {{ Form::button('Rate', ['type'=>'button', 'class'=>'btn-xs btn-success btn ratingPopUp_open']) }}
                            @if(auth()->check())
                                <div id="ratingPopUp" class="well text-center">
                                    Enter your rating
                                    {{ FormError::rating('newPlRating', 0) }}
                                </div>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Keywords --}}
            <div class="row">
                <div class="keywords-tabs">
                    <ul class="nav nav-tabs row playlist-keywords" role="tablist">
                        @foreach ($playlist->keys as $i => $key)
                            {{--{{ dd($key) }}--}}
                            <li role="presentation" class="{{ $i === 0 ? 'active ' : '' }} text-center">
                                <a href=""
                                   class="keyword-tab green"
                                   aria-controls="tab-general"
                                   role="tab"
                                   data-keyword="{{$key->plk_key}}"
                                   data-toggle="tab">
                                    {{ $key->plk_key }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            {{--End of keywords--}}
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="video_wrapper">
                    <div id="player"></div>
                </div>
                <div class="search-box">
                    @include('public.partials.search-box', ['playlist' => $playlist])
                </div>
            </div>
            <div class="col-md-6">
                <div class="section playback_queue">
                    <ul class="list-group playlist-scroll">
                        @foreach ($videos as $key => $video)
                            <?php $video_snippet = unserialize($video->plv_snippet) ?>
                            <li class="list-group-item">
                                <a href="#" id="{{ $video->plv_video_id }}" vorder="{{ $key }}" class="play_video">
                                    <div class="row">
                                        <div class="col-md-5 col-sm-5 col-xs-5">
                                            <div class="play_image_container">
                                                <img src="{{ $video_snippet->thumbnails->medium->url }}"
                                                     class="img-rounded" width="100%">
                                                <div class="play_button"><i class="fa fa-play-circle-o"></i></div>
                                            </div>
                                        </div>
                                        <div class="col-md-7 col-sm-7 col-xs-7">
                                            <div class="selected_video_title">{{ $video_snippet->title }}</div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        {{--<div class="row">--}}
        {{--<div class="col-md-8">--}}
        {{--<div class="hidden-sm hidden-xs">--}}
        {{--@include('public.playlist.desc_column')--}}
        {{--</div>--}}
        {{--<div class="fb-comments hidden-sm hidden-xs" data-href="{{ request()->url() }}" data-numposts="5"--}}
        {{--data-width="100%"></div>--}}
        {{--</div>--}}
        {{--</div>--}}
        {{--<div class="section white-background">
            <div class="search-box">
                @include('public.partials.search-box', ['playlist' => $playlist])
            </div>
        </div>--}}
        <div class="section white-background">
            <h5 class="section-title title">Book Tours & Activities in {{$playlist->pl_location}}</h5>
            <div id="gyg-widget"></div>
        </div>
        <div class="section white-background">
            <h5 class="section-title title">@lang('form.playlist_latest')</h5>
            <div class="scroll">
                <div class="row">
                    @foreach($latest as $latest_item)
                        <div class="col-md-3 col-sm-6 col-xs-6">
                            <div class="post medium-post">
                                <div class="entry-header">
                                    <div class="entry-thumbnail play_image_container">
                                        <a href="{{ url('public_playlist/' . $latest_item->pl_slug) }}"><img
                                                    class="img-responsive"
                                                    src="{{ $latest_item->pl_thumb_path or asset(config('playligo.video_thumb_default')) }}"
                                                    alt="">
                                            <div class="play_button"><i class="fa fa-play-circle-o"></i></div>
                                        </a>
                                    </div>
                                </div>
                                <div class="post-content">
                                    <div class="entry-meta">
                                        <ul class="list-inline">
                                            <li class="views"><i class="fa fa-eye"></i>{{ $latest_item->pl_view }}
                                            </li>
                                            <li class="loves">{{ FormError::rating('plRating', $latest_item->pl_rating) }}</li>
                                            <li class="loves">{{ $latest_item->pl_rating }}</li>
                                        </ul>
                                    </div>
                                    <h2 class="entry-title">
                                        <a href="{{ url('public_playlist/' . $latest_item->pl_slug) }}">{{ str_limit($latest_item->pl_title, 100) }}</a>
                                    </h2>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="load_more"><a target="_blank" href="{{ url('public_playlist') }}">Show more</a></div>
            </div>
        </div>

        <div class="section white-background">
            <h5 class="section-title title">@lang('form.playlist_most_viewed')</h5>
            <div class="scroll">
                <div class="row">
                    @foreach($mostViewed as $mv_item)
                        <div class="col-md-3 col-sm-6 col-xs-6">
                            <div class="post medium-post">
                                <div class="entry-header">
                                    <div class="entry-thumbnail play_image_container">
                                        <a href="{{ url('public_playlist/' . $mv_item->pl_slug) }}"><img
                                                    class="img-responsive"
                                                    src="{{ $mv_item->pl_thumb_path or asset(config('playligo.video_thumb_default')) }}"
                                                    alt="">
                                            <div class="play_button"><i class="fa fa-play-circle-o"></i></div>
                                        </a>
                                    </div>
                                </div>
                                <div class="post-content">
                                    <div class="entry-meta">
                                        <ul class="list-inline">
                                            <li class="views"><a href="#"><i
                                                            class="fa fa-eye"></i>{{ $mv_item->pl_view }}</a></li>
                                            <li class="loves">{{ FormError::rating('plRating', $mv_item->pl_rating) }}</li>
                                            <li class="loves">{{ $mv_item->pl_rating }}</li>
                                        </ul>
                                    </div>
                                    <h2 class="entry-title">
                                        <a href="{{ url('public_playlist/' . $mv_item->pl_slug) }}">{{ str_limit($mv_item->pl_title, 100) }}</a>
                                    </h2>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="load_more"><a target="_blank" href="{{ url('public_playlist') }}">Show more</a></div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="fb-comments" data-href="{{ request()->url() }}"
                     data-numposts="5" data-width="100%"></div>
            </div>
        </div>

    </div>

    <!-- </div> -->
    @if(auth()->check() && $playlist->pl_user == auth()->user()->id)
        <div class="action_section">
            <div class="action_section_inner">
                <div class="container">
                    <div class="action_buttons">
                        <a href="{{ url('playlist/edit/' . $playlist->pl_id) }}"
                           class="btn btn-success">{{ trans('form.btn_edit_playlist') }}</a>
                        <a class="btn btn-success" data-toggle="modal" data-target="#embed_modal">Embed & Share</a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="action_section">
            <div class="action_section_inner">
                <div class="container">
                    <a href="{{ url('search') }}" class="btn btn-success">Visualize Your Travel</a>&nbsp;&nbsp;
                    <a data-toggle="modal" data-target="#embed_modal" class="btn btn-success">Embed & Share</a>
                </div>
            </div>
        </div>
    @endif

@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('css/fontawesome-circle-o.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fontawesome-stars-o.css') }}">
@endsection

@section('script')
    <script type="text/javascript"
            src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-579ca464be1308db"></script>
    <script>
        @if (isset($share) && $share)
            $('#sharing-modal').modal('show');
        $('#skip-sharing').click(function () {
            $('#sharing-modal').modal('hide');
            $('#feedback-comment-modal').modal('show');
        });
        @endif
    </script>
    <script src="https://cdn.rawgit.com/vast-engineering/jquery-popup-overlay/1.7.13/jquery.popupoverlay.js"></script>
    <script src="{{ asset('js/jquery-scrolltofixed.js') }}"></script>
    <script src="{{ asset('js/jquery.barrating.min.js') }}"></script>
    @if($playlist->coordinates)
        <script async defer src="//widget.getyourguide.com/v2/core.js"
                onload="GYG.Widget(document.getElementById('gyg-widget'),{'currency':'USD','lat':'{{$playlist->coordinates['lat']}}','lon':'{{$playlist->coordinates['lng']}}','localeCode':'en-US','numberOfItems':'4','partnerId':'HUBIIMY','type':'tour'});"></script>
    @endif
    <script type="text/javascript">
        $(document).ready(function () {
            var $wrapper = $('#content-wrapper');
            var background = "{{ isset($background) ? $background->url : '' }}";
            if (background != '') {
                $('#head-section').toggleClass('head-section');
                $wrapper.css('background-image', 'url("' + background + '")');
            }
        });

        $(function () {

            ratingEnable({{ $playlist->pl_rating }});

            $('.newPlRating').barrating({
                theme: 'fontawesome-circle-o',
                initialRating: {{ $my_rating }},
                showSelectedRating: true,
                onSelect: function (value, text, event) {
                    if (typeof(event) !== 'undefined') {
                        $('#ratingPopUp').popup('hide');
                        $.ajax({
                            url: "{{ url('playlist/rating/add') }}",
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                plr_playlist: {{ $playlist->pl_id }},
                                plr_rating: value,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function (data) {
                                sweetAlert("Yay!", data.message, "success");
                                $('.plRating').barrating('destroy');
                                ratingEnable(data.rating);
                            }
                        });
                    } else {
                        // rating was selected programmatically
                        // by calling `set` method
                    }
                }
            });

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

        function ratingEnable(rating) {
            $('.plRating').barrating('show', {
                theme: 'fontawesome-circle-o',
                initialRating: rating,
                showSelectedRating: true,
                readonly: true
            });

        }
    </script>

    <!--FB comment plugin-->
    <div id="fb-root"></div>
    <script>(function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s);
            js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.6&appId=1070932692967173";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));</script>
    <!--FB comment plugin ends-->

    <!-- <script src="http://www.youtube.com/player_api"></script> -->
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
            setTimeout(function () {
                player.pauseVideo();
            }, 1000);
        }

        var done = false;
        function onPlayerStateChange(event) {
            if (event.data == YT.PlayerState.PLAYING && !done) {
                // setTimeout(stopVideo, 6000);
                done = true;
            }
        }

        function stopVideo() {
            player.stopVideo();
        }

        function loadVideo() {
            player.loadVideoById("bHQqvYy5KYo", 5, "large");
        }

        $(document).ready(function () {
            // getLatestSelected();
            $('.action_section').scrollToFixed({
                bottom: 0,
                limit: $('.action_section').offset().top,
            });

            @if(!auth()->check())
            $('body').on('click', '.ratingPopUp_open', function (event) {
                window.location = "{{ url('login') }}";
                return false;
            });
            @endif

            $('#ratingPopUp').popup({
                transition: 'all 0.3s',
                scrolllock: true,
                blur: true,
                openelement: '.ratingPopUp_open',
                type: 'tooltip',
                offsettop: -60,
            });

            $('body').on('click', '.play_video', function (event) {
                event.preventDefault();
                var id = $(this).attr('id');
                var vorder = $(this).attr('vorder');
                player.playVideoAt(vorder);

                return false;
            });

        });


        function getLatestSelected() {
            $.ajax({
                url: "{{ url('/search/load_selected') }}",
                type: 'GET',
                // dataType: 'json',
                // data: {_token: "{{ csrf_token() }}"},
                // success: function (data) {
                //     // Update selected videos section
                //     // $('#selected_videos').hide().fadeIn('fast');
                //     $('#selected_videos').html();
                // }
            }).done(function (data) {
                $('#selected_videos').html(data);
                // console.log( data );
            });

        }

    </script>
@endsection

@section('meta')
    @if ($playlist->pl_rating < config('playligo.min_rating_show_index'))
        <!-- <meta name="robots" content="noindex, follow"> -->
    @endif
    <!-- <meta property="og:image"         content="http://www.your-domain.com/path/image.jpg" /> -->
@endsection
