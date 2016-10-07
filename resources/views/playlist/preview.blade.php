@extends('layouts.app')

@section('content')
<div class="container">
    <h1><span class="label label-success">{{ $playlist->pl_title }}</span></h1>
    <div class="row">
        <div class="col-md-8">
          <div class="video_wrapper">
            <div id="player"></div>
          </div>
        </div>

        <div class="col-md-4" class="">
          <a href="{{ url('edit_playlist/' . $playlist->pl_id . '?' . $_SERVER['QUERY_STRING']) }}">{{ Form::button('Edit playlist', ['class'=>'btn btn-primary']) }}</a>
          <a href="{{ url('search') }}">{{ Form::button(trans('form.btn_another_playlist'), ['class'=>'btn btn-primary']) }}</a>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="http://www.youtube.com/player_api"></script>
<script>

    var tag = document.createElement('script');
    tag.src = "https://www.youtube.com/iframe_api";
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
    var player;
    function onYouTubeIframeAPIReady() {
      player = new YT.Player('player', {
        playerVars: { 'autoplay': 1, 'controls': 2, 'showinfo': 1},
        events: {
          'onReady': onPlayerReady,
          'onStateChange': onPlayerStateChange
        }
      });
    }


    function onPlayerReady(event) {
      <?php $vid = [] ?>
      @foreach ($videos as $item)
      <?php $vid[] = $item->vc_id; ?>
      @endforeach
      var videos = {!! json_encode($vid) !!};
      event.target.loadPlaylist(videos);
      event.target.playVideo();
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

</script>
<script src="{{ asset('/js/jquery.jscroll.min.js') }}"></script>
<script>
$('.scroll').jscroll({
    autoTrigger: false,
    loadingHtml: '<span class="label label-default">Loading...</span>',
});

$(document).ready(function() {
  // getLatestSelected();

  $('body').on('click', '.play_video', function (event) {
			event.preventDefault();
      var id = $(this).attr('id');
      player.loadVideoById(id, 5, "large");

			return false;
	});

	$('body').on('click', '.add_video_button', function (event) {
			event.preventDefault();
      var id = $(this).attr('id');
			$.ajax({
					url: $(this).attr('href'),
					type: 'POST',
					dataType: 'json',
					data: {id: id, _token: "{{ csrf_token() }}"},
					success: function (data) {
              // Update selected videos section
              $('#selected_videos').hide().fadeIn('fast');
              getLatestSelected();

              // Update clicked video link
              $('#' + id).html('<i class="fa fa-check-circle fa-3"></i> Added');
              $('#' + id).attr('href', '#');
              $('#' + id).removeClass('add_video_button');
              $('#thumb' + id).addClass('selected_disable');
              // location.reload();
					}
			});

			return false;
	});

  // Remove video from selected list
  $('body').on('click', '.remove_video_button', function (event) {
			event.preventDefault();
      var id = $(this).attr('id');
			$.ajax({
					url: $(this).attr('href'),
					type: 'POST',
					dataType: 'json',
					data: {id: id, _token: "{{ csrf_token() }}"},
					success: function (data) {
              // Update selected videos section
              $('#selected_videos').hide().fadeIn('fast');
              getLatestSelected();

              // Update clicked video link
              $('#' + id).html('<i class="fa fa-check-circle fa-3"></i> Add to playlist');
              $('#' + id).addClass('add_video_button');
              $('#' + id).attr('href', "{{ url('search/add_video') }}");
              $('#thumb' + id).removeClass('selected_disable');
              // location.reload();
					}
			});

			return false;
	});

});


function getLatestSelected()
{
  $.ajax({
      url: "{{ url('/search/load_selected') }}",
      type: 'GET',
    }).done(function( data ) {
      $('#selected_videos').html(data);
  });

}
</script>
@endsection
