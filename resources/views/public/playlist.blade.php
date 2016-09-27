@extends('layouts.app')

@section('content')
    <div class="playlist-search text-center section">
        <div class="playlist-search-inner">
            <div class="row">
                <div class="col-md-6 col-md-offset-3 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1">
                    <h1>{{ trans('form.playlist_search_caption') }}</h1>
                    {{ Form::open(['url'=>url('public_playlist/search'), 'method'=>'get', 'class'=>'submit-ajax-getx']) }}
                    <div class="input-group">
                        {{ Form::text('q', old('q'), ['class'=>'form-control', 'placeholder'=> trans('form.eg_search_playlist')]) }}
                        <span class="input-group-btn">
                {{ Form::button(trans('form.btn_search'), ['type'=>'submit', 'class'=>'btn btn-primary form-control']) }}
              </span>
                    </div>

                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="section">
            <h5 class="section-title title">@lang('form.playlist_latest')</h5>
            <div class="scroll">
                @foreach($latest->chunk(4) as $latest_set)
                    <div class="row">
                        @foreach($latest_set as $latest_item)
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
                @endforeach
                <div class="load_more"><a href="{{ url('public_playlist/latest/more?page=2') }}">Load more</a></div>
            </div>
        </div>


        <div class="section">
            <h5 class="section-title title">@lang('form.playlist_most_viewed')</h5>
            <div class="scroll">
                @foreach($mostViewed->chunk(4) as $mv_set)
                    <div class="row">
                        @foreach($mv_set as $mv_item)
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
                @endforeach
                <div class="load_more"><a href="{{ url('public_playlist/mostviewed/more?page=2') }}">Load more</a></div>
            </div>
        </div>

    </div>
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('css/fontawesome-circle-o.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fontawesome-stars-o.css') }}">
@endsection

@section('script')
    <script src="{{ asset('/js/jquery.jscroll.min.js') }}"></script>
    <script src="{{ asset('js/jquery.barrating.min.js') }}"></script>
    <script>
        $('.scroll').jscroll({
            autoTrigger: false,
            loadingHtml: '<span class="label label-default">Loading...</span>',
        });

        $(document).ready(function () {

            ratingEnable();

            // $(".plRating").rateYo({
            //   starWidth: "18px",
            //   rating    : 0,
            //   readOnly: true
            // });

        });

        function ratingEnable() {
            $('.plRating').barrating('show', {
                theme: 'fontawesome-circle-o',
                showSelectedRating: true,
                readonly: true,
            });
        }

    </script>
@endsection

@section('meta')
    <meta property="fb:app_id" content="{{ env('FACEBOOK_CLIENT_ID') }}"/>
    <meta property="og:url" content="{{ Request::url() }}"/>
    <meta property="og:type" content="website"/>
    <!-- <meta property="og:image"         content="http://www.your-domain.com/path/image.jpg" /> -->
@endsection
