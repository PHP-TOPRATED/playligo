@extends('layouts.app')

@section('content')
{{ Form::open(['url'=> url(''), 'method'=>'post']) }}
<div class="container">
  <div class="page-breadcrumbs">
    <h1 class="section-title">{{ $poll->pol_title }}</h1>
  </div>

  <div class="section">
    <div class="entry-meta">
			<ul class="list-inline">
				<li class="posted-by"><i class="fa fa-user"></i> by <a href="#">{{ $owner->name }}</a></li>
				<li class="publish-date"><a href="#"><i class="fa fa-clock-o"></i> {{ Carbon::parse($poll->created_at)->diffForHumans() }} </a></li>
				<li class="views"><a href="#"><i class="fa fa-eye"></i> {{ $poll->pol_view }} views</a></li>
        <li class="views"><a href="#"><i class="fa fa-thumbs-o-up"></i> {{ $poll->pol_votes }} votes</a></li>
			</ul>
		</div>
  </div>

  <div class="section">
    <h4>Vote for your favorite by  clicking on the “love” button…</h4>
    <div class="row">
      <div class="col-md-8">
        <ul class="list-group poll_playlist_group">
        @foreach($poll_playlists as $playlist)
          <?php //$snippet = unserialize($playlist->vc_snippet); ?>
          <li class="list-group-item">
            <div class="row">
              <div class="col-md-3">
                <div class="play_image_container">
                  <a href="{{ url('/public_playlist/popup/' . $playlist->pl_slug) }}" class="btn-modal"><img src="{{ $playlist->pl_thumb_path or asset(config('playligo.video_thumb_default')) }}" class="img-rounded" width="100%"></a>
                  <div class="play_button"><i class="fa fa-play-circle-o"></i></div>
                </div>
              </div>
              <div class="col-md-7">
                <div class="selected_video_title">{{ $playlist->pl_title }}</div>
                <div class="progress">
                  <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="40"
                  aria-valuemin="0" aria-valuemax="100" style="width:{{ $poll->pol_votes > 0 ? round(($playlist->polp_vote / $poll->pol_votes) * 100) : 0 }}%;">
                    {{ $poll->pol_votes > 0 ? round(($playlist->polp_vote / $poll->pol_votes) * 100) : 0 }}%
                  </div>
                </div>
              </div>
              <div class="col-md-2 text-center">
                <div class="vote_count">{{ number_format($playlist->polp_vote) }}</div>
                @if (isset($voted) && $voted > 0)
                  {{ Form::button('<i class="fa fa-heart fa-1"></i>', ['class'=>'btn btn-default', 'disabled']) }}
                  @if($voted == $playlist->polp_id)
                  <div class="voted"><i class="fa fa-check"></i> Voted</div>
                  @endif
                @else
                  <a href="{{ url('/pollplaylist/'.$playlist->polp_id.'/vote') }}" class="vote_playlist">{{ Form::button('<i class="fa fa-heart"></i>', ['class'=>'btn btn-default']) }}</a>
                @endif
              </div>
            </div>

            <!-- <div class="post video-post small-post">
              <div class="entry-header">
                <img src="{{ $playlist->pl_thumb_path or asset(config('playligo.video_thumb_default')) }}" width="100%">
              </div>
              <div class="post-content">
                <a href="{{ url('load_playlist/' . $playlist->pl_id) }}" class="load_playlist">{{ $playlist->pl_title  }}</a>
                <div class="progress">
                  <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="40"
                  aria-valuemin="0" aria-valuemax="100" style="width:{{ $poll->pol_votes > 0 ? round(($playlist->polp_vote / $poll->pol_votes) * 100) : 0 }}%">
                    {{ $poll->pol_votes > 0 ? round(($playlist->polp_vote / $poll->pol_votes) * 100) : 0 }}%
                  </div>
                </div>
                <a href="{{ url('/pollplaylist/'.$playlist->polp_id.'/vote') }}" class="vote_playlist">{{ Form::button('<i class="fa fa-heart fa-1"></i>', ['class'=>'btn btn-default']) }}</a>
                <span class="vote_count">{{ number_format($playlist->polp_vote) }}</span>

              </div>
            </div> -->
          </li>
        @endforeach
        </ul>
        <div class="fb-comments hidden-sm hidden-xs" data-href="{{ request()->url() }}" data-numposts="5" data-width="100%"></div>

      </div>
      <div class="col-md-4">
        <div class="section">
          <h5 class="section-title title">Latest Votes</h5>
          <ul class="list-group">
          @foreach ($voters as $voter)
          <li class="list-group-item">
            <div class="row">
              <div class="col-md-3 col-sm-3 col-xs-3">
                <div class="image-cropper">
                  <img class="rounded" src="{{ !empty($voter->avatar) ? $voter->avatar : asset(config('playligo.avatar_default')) }}" >
                </div>
              </div>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <div class="voter_description">{{ $voter->name }} voted <a href="{{ url('public_poll/' . $voter->pov_poll) }}">{{ $pl_titles[$voter->pov_poll_playlist] or '' }}</a></div>
                <div class="date_time">{{ Carbon::parse($voter->created_at)->diffForHumans() }}</div>
              </div>
            </div>
          </li>
          @endforeach
          </ul>
        </div>
        <div id="playlist_videos">
        </div>
      </div>
    </div>
  </div>
</div>
{{ Form::close() }}
@endsection

@section('script')
<script>

$(document).ready(function() {
	$('body').on('click', '.load_playlist', function (event) {
			event.preventDefault();
			$.ajax({
					url: $(this).attr('href'),
					type: 'GET',
			}).done(function( data ) {
        $('#playlist_videos').html(data);
    });
			return false;
	});

  $('body').on('click', '.vote_playlist', function (event) {
			event.preventDefault();
      @if(!auth()->check())
      window.location = "{{ url('login') }}";
      @else
      $.ajax({
          url: $(this).attr('href'),
          type: 'POST',
          dataType: 'json',
          data: {_token: $("input[name='_token']").val()},
          success: function (data) {
            sweetAlert("Yay!", data.message, "success");
            setTimeout(function () {
              if (data.redirect) {
                window.location = data.redirect;
              } else {
                location.reload();
              }
            }, 2000);
          },
          error: function(data){
    			}
      });
      @endif
			return false;
	});
});

</script>
@endsection

@section('meta')
@endsection
