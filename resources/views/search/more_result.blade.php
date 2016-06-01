@foreach($resultsets as $key => $result)
@if(!empty($result))
<div>
  @foreach(array_chunk($result, 4) as $item_set)
      <div class="row">
      @foreach($item_set as $item)
          <div class="col-md-3 col-sm-3 col-xs-3 select_video_thumbnail">
              <a href="{{ url('search/preview/' . $item->id->videoId) }}" class="btn-modal">
                <div class="play_image_container">
                  <img id="thumb{{ $item->id->videoId }}" src="{{ $item->snippet->thumbnails->medium->url }}" class="video_thumbnail @if (key_exists($item->id->videoId, $selected)) selected_disable @endif" width="100%">
                  <div class="play_button"><i class="fa fa-play-circle-o"></i></div>
                </div>
                <div class="description"><div class='description_content'>{{ $item->snippet->title }}</div></div>
              </a>
              <div class="select_video_control">
                  @if (key_exists($item->id->videoId, $selected))
                      <a href="#"><i class="fa fa-check-circle fa-3"></i> Added</a>
                  @else
                      <a id="{{ $item->id->videoId }}" href="{{ url('playlist/video/add') }}" class="add_video_button"><i class="fa fa-plus-circle fa-3"></i> {{ trans('form.add_to_playlist') }}</a>
                  @endif
              </div>
          </div>
      @endforeach
      </div>
  @endforeach
</div>
<div class="load_more"><a href="{{ url('/edit_playlist/'.$playlist->pl_id.'/more?search_key=' . str_replace(' ', '+', $key)) }}">Load more</a></div>
@endif
@endforeach
