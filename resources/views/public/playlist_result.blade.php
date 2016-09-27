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
    <h5 class="section-title title">@lang('form.playlist_result') - {{ $q }}</h5>
    @if($result)
      @foreach($result->chunk(4) as $result_set)
        <div class="row">
          @foreach($result_set as $result_item)
          <div class="col-md-3 col-sm-6 col-xs-6">
            <div class="post medium-post">
  						<div class="entry-header">
  							<div class="entry-thumbnail play_image_container">
  								<a href="{{ url('public_playlist/' . $result_item->pl_slug) }}"><img class="img-responsive" src="{{ $result_item->pl_thumb_path or asset(config('playligo.video_thumb_default')) }}" alt=""></a>
                  <div class="play_button"><i class="fa fa-play-circle-o"></i></div>
  							</div>
  						</div>
  						<div class="post-content">
  							<div class="entry-meta">
  								<ul class="list-inline">
  									<li class="views"><a href="#"><i class="fa fa-eye"></i>{{ $result_item->pl_view }}</a></li>
  									<li class="loves"><div class="plRating"></div></li>
  								</ul>
  							</div>
  							<h2 class="entry-title">
  								<a href="{{ url('public_playlist/' . $result_item->pl_slug) }}">{{ str_limit($result_item->pl_title, 100) }}</a>
  							</h2>
  						</div>
  					</div>
          </div>
          @endforeach
        </div>
      @endforeach
      <div class="pagination-wrapper">
  			{{ $result->links() }}
  		</div>
    @else
    @endif
  </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {

  // $(".plRating").rateYo({
  //   starWidth: "18px",
  //   rating    : 0,
  //   readOnly: true
  // });

});
</script>
@endsection

@section('meta')
 <meta property="fb:app_id" content="{{ env('FACEBOOK_CLIENT_ID') }}" />
  <meta property="og:url"           content="{{ Request::url() }}" />
	<meta property="og:type"          content="website" />
	<!-- <meta property="og:image"         content="http://www.your-domain.com/path/image.jpg" /> -->
@endsection
