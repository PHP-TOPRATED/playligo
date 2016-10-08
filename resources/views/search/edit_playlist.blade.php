@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="page-breadcrumbs">
            <h1 class="section-title">Edit Playlist</h1>
            {{ Form::open(['url' => url('edit_keywords/' . $playlist->pl_id), 'class'=>'submit-ajaxx', 'method'=>'POST']) }}
            {{ Form::hidden('pl_id', $playlist->pl_id, ['id'=>'pl_id']) }}
            {{ Form::hidden('hiddenField')}}
            {{ Form::close() }}
        </div>

        <div class="section">
            <div class="entry-meta">
                <ul class="list-inline">
                    <li class="posted-by"><i class="fa fa-user"></i> by {{ $owner->name }}</li>
                    <li class="publish-date"><i
                                class="fa fa-clock-o"></i> {{ Carbon::parse($playlist->created_at)->diffForHumans() }}
                    </li>
                    <li class="views"><i class="fa fa-eye"></i> {{ $playlist->pl_view }} views</li>
                </ul>
            </div>
            <h3><span class="label label-info"><i class="fa fa-map-marker"></i> {{ $playlist->pl_location }}</span>
                @if(!empty($playlist->pl_title))
                    <div class="inline_edit_title"><i class="fa fa-edit"></i> {{ $playlist->pl_title }}</div>@endif
            </h3>
        </div>

        <div class="section">
            <div class="row">
                {{ Form::open(['url' => url('edit_keywords/' . $playlist->pl_id), 'class'=>'submit-ajaxx', 'method'=>'POST']) }}
                <div class="col-md-8 col-sm-6 col-xs-12 mb15">
                    {{ Form::text('search_keys', $keys_string, ['id'=>'tags', 'class'=>'form-control']) }}
                </div>
                <div class="col-md-2 col-sm-3 col-xs-6">
                    {{ Form::button(trans('form.btn_update'), ['type'=>'submit', 'class'=>'btn btn-success btn-wide', 'style'=>'height: 48px;']) }}
                </div>
                {{ Form::close() }}
                <div class="col-md-2 col-sm-3 col-xs-6">
                    @if (! $playlist->isPublished())
                        {{ Form::button(trans('form.btn_publish'), ['type'=>'button', 'class'=>'btn btn-success btn-wide btn-publish', 'style'=>'height: 48px;', 'data-pl_id' => $playlist->pl_id, 'data-url' => route('playlist.publish'), 'data-title' => (!empty($playlist->pl_title)) ? 'true' : 'false', 'data-title_url' => url('playlist/edit')]) }}
                    @else
                        <a target="_blank" href="{{ route('public_playlist.view', ['pl_slug' => $playlist->pl_slug]) }}"
                           style="height: 48px;">
                            {{ Form::button(trans('form.btn_view_live'), ['type'=>'button', 'class'=>'btn btn-success btn-wide', 'style'=>'height: 48px;']) }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    Click and drag the keywords to rearrange the order of the videos. Add more keywords and separate
                    them using "commas".
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
                            <?php $video_snippet = unserialize($video->plv_snippet) ?>
                            <li class="list-group-item">
                                <a href="#" id="{{ $video->plv_video_id }}" vorder="{{ $key }}" class="play_video">
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
                                                       data-key="{{$key}}"
                                                       href="{{ url('/playlist/video/delete') }}"
                                                       class="remove_video_button btn-remove">{{ trans('form.remove_from_playlist') }}
                                                        <i class="fa fa-minus-circle"></i></a>
                                                @else
                                                    <a id="video-{{ $item->id->videoId }}"
                                                       data-key="{{$key}}"
                                                       href="{{ url('playlist/video/add') }}"
                                                       class="add_video_button btn-add">{{ trans('form.add_to_playlist') }}
                                                        <i class="fa fa-plus"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                            <div class="load_more"><a
                                        href="{{ url('/edit_playlist/'.$playlist->pl_id.'/more?search_key=' . str_replace(' ', '+', $key)) }}">Load
                                                                                                                                               more</a>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
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
                playerVars: {'autoplay': 0, 'controls': 2, 'showinfo': 1, 'rel': 0},
                events: {
                    'onReady': onPlayerReady,
                    'onStateChange': onPlayerStateChange
                }
            });
        }

        function onPlayerReady(event) {
            var videos = [{!! $video_ids !!}];
            event.target.loadPlaylist(videos);
            setTimeout(function () {
                player.pauseVideo();
            }, 1000);

        }

        var done = false;
        function onPlayerStateChange(event) {
            if (event.data == YT.PlayerState.PLAYING && !done) {
                done = true;
            }
        }


        $('body').on('click', '.play_video', function (event) {
            event.preventDefault();
            var id = $(this).attr('id');
            var vorder = $(this).attr('vorder');
            player.playVideoAt(vorder);

            return false;
        });

        $('.scroll').jscroll({
            autoTrigger: false,
            loadingHtml: '<span class="label label-default">Loading...</span>',
        });
        $('body').on('click', '.add_video_button', function (event) {
            event.preventDefault();
            var id = $(this).attr('id');
            id = id.replace('video-', '');
            var keyword = $(this).data('key');
            var pl_id = $('#pl_id').val();
            var row_index = $(this).closest('.jscroll-added').index();
            var cell_index = $(this).closest('.select_video_thumbnail').index();
            var plv_order = (row_index * 4) + (cell_index + 1);
            $.ajax({
                url: $(this).attr('href'),
                type: 'POST',
                dataType: 'json',
                data: {
                    pl_id: pl_id,
                    id: id,
                    keyword: keyword,
                    plv_order: plv_order,
                    _token: "{{ csrf_token() }}"
                },
                success: function (data) {
                    getLatestSelected();
                    // Update clicked video link
                    $('#video-' + id).html('{{ trans('form.remove_from_playlist') }}' + '<i class="fa fa-minus-circle"></i>');
                    $('#video-' + id).attr('href', '{{ url('/playlist/video/delete') }}');
                    $('#video-' + id).removeClass('add_video_button btn-add').addClass('remove_video_button btn-remove');
                    $('#thumb' + id).addClass('selected_disable');
                }
            });

            return false;
        });

        // Remove video from selected list
        $('body').on('click', '.remove_video_button', function (event) {
            event.preventDefault();
            var id = $(this).attr('id');
            id = id.replace('video-', '');
            var pl_id = $('#pl_id').val();
            $.ajax({
                url: $(this).attr('href'),
                type: 'POST',
                dataType: 'json',
                data: {pl_id: pl_id, id: id, _token: "{{ csrf_token() }}"},
                success: function (data) {
                    getLatestSelected();
                    // Update clicked video link
                    $('#video-' + id).html('{{ trans('form.add_to_playlist') }}' + '<i class="fa fa-plus"></i>');
                    $('#video-' + id).attr('href', "{{ url('playlist/video/add') }}");
                    $('#video-' + id).removeClass('remove_video_button btn-remove').addClass('add_video_button btn-add');
                    $('#thumb' + id).removeClass('selected_disable');
                }
            });

            return false;
        });
        $(document).ready(function () {
            @if($show_tutorial_message)
                swal('', '{{ trans('messages.edit_playlist_guide') }}');
                    @endif

            var replaceWith = $('<input id="pl_title" name="pl_title" type="text" class="form-control" value="{{ $playlist->pl_title }}"/>'),
                    connectWith = $('input[name="hiddenField"]');

            $('.inline_edit_title').inlineEdit(replaceWith, connectWith);

            $('#tags').tagEditor({
                maxTags: {{ config('playligo.max_keyword_tags') }},
            });


            $('body').on('keydown', '#pl_title', function (event) {
                // event.preventDefault();
                if (event.which == 13) {
                    var pl_title = $(this).val();
                    var pl_id = $('#pl_id').val();
                    $.ajax({
                        url: '{{ url('playlist/edit') }}',
                        type: 'POST',
                        dataType: 'json',
                        data: {pl_title: pl_title, pl_id: pl_id, _token: "{{ csrf_token() }}"},
                        success: function (data) {
                            sweetAlert("Yay!", data.message, "success");
                        },
                        error: function (xhr, status, error) {
                            var err = jQuery.parseJSON(xhr.responseText);
                            var errStr = '';
                            $.each(err, function (key, value) {
                                errStr = errStr + value + "\n";
                            });
                            sweetAlert("Oops...", errStr, "error");
                        }
                    });

                    return false;
                }
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

        function getLatestSelected() {
            $.ajax({
                url: "{{ url('/edit_playlist/load_selected/' . $playlist->pl_id) }}",
                type: 'GET'
            }).done(function (data) {
                $('#playlist-scroll').html(data.html);
                player.loadPlaylist(data.videos);
                player.stopVideo()
            });
        }

        $.fn.inlineEdit = function (replaceWith, connectWith) {

            $(this).hover(function () {
                $(this).addClass('hover');
            }, function () {
                $(this).removeClass('hover');
            });

            $(this).click(function () {

                var elem = $(this);

                elem.hide();
                elem.after(replaceWith);
                replaceWith.focus();

                replaceWith.blur(function () {

                    if ($(this).val() != "") {
                        connectWith.val($(this).val()).change();
                        elem.innerHTML = '<i class="fa fa-edit"></i> ' + $(this).val();
                    }

                    $(this).remove();
                    elem.show();
                });
            });
        };
    </script>
@endsection
