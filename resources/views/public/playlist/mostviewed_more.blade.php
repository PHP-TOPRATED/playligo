@foreach($mostViewed->chunk(4) as $mv_set)
    <div class="row playlist-items clearfix">
        @foreach($mv_set as $mv_item)
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="post medium-post playlist-item">
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
                                <li>
                                    @if(auth()->check())
                                        <button type="button"
                                                class="btn-like @if($mv_item->isLiked()) active @endif"
                                                data-playlist="{{ $mv_item->pl_slug }}"
                                                onclick="likePlaylist(this)">
                                            <i class="fa fa-thumbs-up" aria-hidden="true"></i>
                                        </button>
                                    @endif
                                </li>
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
<div class="load_more"><a href="{{ url('public_playlist/mostviewed/more?page=' . ($page + 1)) }}">Load more</a></div>
