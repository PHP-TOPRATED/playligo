@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="page-breadcrumbs">
            <h1 class="section-title">Edit Playlist</h1>
        </div>

        <div class="section">
            <div class="row">
                <div class="col-md-8 col-sm-6 col-xs-12 mb15">
                    {{ Form::text('search_keys', $keys_string, ['id'=>'tags', 'class'=>'form-control']) }}
                </div>
                <div class="col-md-2 col-sm-3 col-xs-6">
                    <a href="{{ url('/login') }}" class="btn btn-success btn-wide"
                       style="height:48px;line-height:48px;padding:0;">{{ trans('form.btn_update') }}</a>
                </div>
                <div class="col-md-2 col-sm-3 col-xs-6">
                    <a href="{{ url('/login') }}" class="btn btn-success btn-wide"
                       style="height:48px;line-height:48px;padding:0;">{{ trans('form.btn_publish') }}</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="video_wrapper">
                    <div id="player"></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="section edit_playlist">
                    <h5 class="section-title title">Playlist</h5>
                    <ul class="list-group playlist-scroll" id="playlist-scroll">
                        @foreach ($videos as $key => $video)
                            <?php $video_snippet = unserialize($video['snippet']) ?>
                            <li class="list-group-item">
                                <a href="#" id="{{ $video['video_id'] }}" vorder="{{ $key }}" class="play_video">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-4 col-xs-4">
                                            <div class="play_image_container">
                                                <img src="{{ $video_snippet->thumbnails->medium->url }}"
                                                     class="img-rounded" width="100%">
                                                <div class="play_button"><i class="fa fa-play-circle-o"></i></div>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-sm-8 col-xs-8">
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

        <div class="section">
            <div class="col-md-12">
                @foreach($resultsets as $key => $result)
                    <h5><span class="label label-info">{{ $key }}</span></h5>
                    @if(empty($result))
                        <div class="alert alert-info" role="alert">
                            {{ trans('keywords.no_videos') }}
                        </div>
                    @endif
                    @if(!empty($result))
                        <div class="scroll keyword_selection">
                            @foreach(array_chunk($result, 4) as $item_set)
                                <div class="row jscroll-added">
                                    @foreach($item_set as $item)
                                        <div class="col-md-3 col-sm-6 col-xs-12 select_video_thumbnail">
                                            <a href="{{ url('search/preview/' . $item->id->videoId) }}"
                                               class="btn-modal">
                                                <div class="play_image_container">
                                                    <img id="thumb{{ $item->id->videoId }}"
                                                         src="{{ $item->snippet->thumbnails->medium->url }}"
                                                         class="video_thumbnail @if (in_array($item->id->videoId, $selected)) selected_disable @endif"
                                                         width="100%">
                                                    <div class="play_button"><i class="fa fa-play-circle-o"></i></div>
                                                </div>
                                                <div class="description">
                                                    <div class='description_content'>{{ $item->snippet->title }}</div>
                                                </div>
                                            </a>
                                            <div class="select_video_control">
                                                @if (in_array($item->id->videoId, $selected))
                                                    <a id="video-{{ $item->id->videoId }}"
                                                       href="{{ url('/login') }}"
                                                       class="remove_video_button btn-remove">{{ trans('form.remove_from_playlist') }}
                                                        <i class="fa fa-minus-circle"></i></a>
                                                @else
                                                    <a id="video-{{ $item->id->videoId }}"
                                                       href="{{ url('/login') }}"
                                                       class="add_video_button btn-add">{{ trans('form.add_to_playlist') }}
                                                        <i class="fa fa-plus"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                            <div class="load_more"><a href="{{ url('/login') }}">Load more</a></div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('style')
    <link href="{{ asset('css/jquery.tag-editor.css') }}" rel="stylesheet">
    <style>
        .tag-editor {
            padding: 10px 10px;
        }
    </style>
@endsection

@section('script')
    <script src="{{ asset('js/jquery.tag-editor.min.js') }}"></script>
    <script src="{{ asset('/js/jquery.caret.min.js') }}"></script>
    <script src="{{ asset('/js/jquery.jscroll.min.js') }}"></script>
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
                    <?php $vid[] = $item['video_id']; ?>
                    @endforeach
            var videos = {!! json_encode($vid) !!};
            event.target.loadPlaylist(videos);
            setTimeout(function () {
                player.pauseVideo();
            }, 900);
        }

        var done = false;
        function onPlayerStateChange(event) {
            if (event.data == YT.PlayerState.PLAYING && !done) {
                done = true;
            }
        }

        function stopVideo() {
            player.stopVideo();
        }

        function loadVideo() {
            player.loadVideoById("bHQqvYy5KYo", 5, "large");
        }

        $('body').on('click', '.play_video', function (event) {
            event.preventDefault();
            var id = $(this).attr('id');
            var vorder = $(this).attr('vorder');
            player.playVideoAt(vorder);

            return false;
        });

        $(document).ready(function () {
            @if($show_tutorial_message)
                swal('', '{{ trans('messages.edit_playlist_guide') }}');
            @endif


            $('#tags').tagEditor({
                maxTags: {{ config('playligo.max_keyword_tags') }},
            });


            // Video thumbs description hover
            $(document).on({
                mouseenter: function () {
                    $('div.description').css('width', $('.video_thumbnail').width());

                    $('div.description').css('height', $('.video_thumbnail').height());

                    $(this).children('.description').stop().fadeTo(500, 0.7);
                },
                mouseleave: function () {
                    $(this).children('.description').stop().fadeTo(500, 0);
                }
            }, '.select_video_thumbnail a');

        });

    </script>
@endsection
