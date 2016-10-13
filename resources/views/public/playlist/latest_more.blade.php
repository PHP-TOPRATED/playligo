@foreach($latest->chunk(4) as $latest_set)
    <div class="row playlist-items clearfix">
        @foreach($latest_set as $latest_item)
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="post medium-post playlist-item">
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
                                <li class="views"><a href="#"><i
                                                class="fa fa-eye"></i>{{ $latest_item->pl_view }}</a></li>
                                <li>
                                    @if(auth()->check())
                                        <button type="button"
                                                class="btn-like @if($latest_item->isLiked()) active @endif"
                                                data-playlist="{{ $latest_item->pl_slug }}"
                                                onclick="likePlaylist(this)">
                                            <i class="fa fa-thumbs-up" aria-hidden="true"></i>
                                        </button>
                                    @endif
                                </li>
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
<div class="load_more"><a href="{{ url('public_playlist/latest/more?page=' . ($page + 1)) }}">Load more</a></div>

