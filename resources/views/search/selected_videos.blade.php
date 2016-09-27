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