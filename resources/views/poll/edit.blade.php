@extends('layouts.app')

@section('content')
<div class="container">
	<div class="page-breadcrumbs">
		<h1 class="section-title">{{ trans('poll.edit_title') }}</h1>
		<div class="world-nav cat-menu">
			<ul class="list-inline">
				<li class="active"><a href="{{ url('poll') }}" class=""><span class="fa fa-angle-double-left"></span> @lang('form.back_to_list')</a></li>
			</ul>
		</div>
	</div>

	<div class="section details-news">
		<div class="entry-meta">
			<ul class="list-inline">
				<li class="posted-by"><i class="fa fa-user"></i> by <a href="#">{{ $owner->name }}</a></li>
				<li class="publish-date"><a href="#"><i class="fa fa-clock-o"></i> {{ $poll->created_at }} </a></li>
				<li class="views"><a href="#"><i class="fa fa-eye"></i> {{ $poll->pol_view }}</a></li>
				<li class="views"><a href="#"><i class="fa fa-bar-chart"></i> {{ $poll->pol_votes }}</a></li>
				<!-- <li class="loves"><a href="#"><i class="fa fa-heart-o"></i>278</a></li>
				<li class="comments"><i class="fa fa-comment-o"></i><a href="#">189</a></li> -->
			</ul>
		</div>
		{{ Form::open(['url'=>url('poll/edit'), 'method'=>'post', 'class'=>'submit-ajax']) }}
		{{ Form::hidden('pol_id', $poll->pol_id, ['id'=>'pol_id']) }}
		<div class="row">
				<div class="col-md-6">
						<div class="form-group">
	          	{{ Form::label('pol_title', trans('poll.pol_title'), ['class'=>'control-label']) }}
          		{{ Form::text('pol_title', $poll->pol_title, ['class'=>'form-control']) }}
          	</div>
						<div class="form-group">
	          	{{ Form::label('pol_expiry', trans('poll.pol_expiry_alt'), ['class'=>'control-label']) }}
							<div class="input-group">
	          		{{ Form::text('pol_expiry', date('d-m-Y', strtotime($poll->pol_expiry)), ['class'=>'form-control datepicker']) }}
								<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							</div>
          	</div>
						<div class="form-group">
	          	{{ Form::label('pol_description', trans('poll.pol_description'), ['class'=>'control-label']) }}
          		{{ Form::textarea('pol_description', $poll->pol_description, ['class'=>'form-control', 'rows'=>5]) }}
          	</div>
						<div class="form-group">
								{{ Form::button(trans('form.btn_save'), ['type'=>'submit', 'class'=>'btn btn-primary']) }}
						</div>
				</div>
				<div class="col-md-6">
					<div id="sort_list" class="video-post-list">
						<h5>Total votes: {{ $poll->pol_votes }}</h5>
						<ul class="ui-sortable list-group poll_playlist_group">
							@foreach($poll_playlists as $pl)
							<li class="list-group-item" id="{{ $pl->polp_id }}">
								<div class="row">
									<div class="col-md-3">
										<div class="play_image_container">
											<a href="{{ url('/public_playlist/popup/' . $pl->pl_id) }}" class="btn-modal"><img src="{{ $pl->pl_thumb_path or asset(config('playligo.video_thumb_default')) }}" class="img-rounded" width="100%"></a>
											<div class="play_button"><i class="fa fa-play-circle-o"></i></div>
										</div>
									</div>
									<div class="col-md-7">
										<div class="selected_video_title">{{ $pl->pl_title }}</div>
										<div class="progress">
											<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="40"
											aria-valuemin="0" aria-valuemax="100" style="width:{{ $poll->pol_votes > 0 ? round(($pl->polp_vote / $poll->pol_votes) * 100) : 0 }}%;">
												{{ $poll->pol_votes > 0 ? round(($pl->polp_vote / $poll->pol_votes) * 100) : 0 }}%
											</div>
										</div>
									</div>
									<div class="col-md-2 text-center">
										<div class="vote_count">{{ number_format($pl->polp_vote) }}</div>
										<a href="{{ url('poll/playlist/' . $pl->polp_id . '/delete') }}" class="btn-modal"><i class="fa fa-times-circle"></i></a>
									</div>
								</div>
							</li>
							<!-- <li class="poll_playlist_group" id="{{ $pl->polp_id }}">
								<div class="post video-post small-post">
									<div class="entry-header">
										<div class="entry-thumbnail embed-responsive embed-responsive-16by9">
											<a href="{{ url('public_playlist/popup/' . $pl->pl_slug) }}" class="btn-modal"><img src="{{ $pl->pl_thumb_path or asset(config('playligo.video_thumb_default')) }}" width="100%"></a>
										</div>
									</div>
									<div class="post-content">
										<h2 class="entry-title pull-left">
											<div class="row">
												<div class="col-md-9">
													<div>
														{{ $pl->pl_title }}
														<div class="progress">
														  <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="40"
														  aria-valuemin="0" aria-valuemax="100" style="width:{{ $poll->pol_votes > 0 ? round(($pl->polp_vote / $poll->pol_votes) * 100) : 0 }}%;">
														    {{ $poll->pol_votes > 0 ? round(($pl->polp_vote / $poll->pol_votes) * 100) : 0 }}%
														  </div>
														</div>
													</div>
												</div>
												<div class="col-md-3 action_column">
													<a href="{{ url('poll/playlist/' . $pl->polp_id . '/delete') }}" class="btn-modal"><i class="fa fa-times-circle"></i></a>
												</div>
											</div>
										</h2>
									</div>
								</div>
							</li> -->
							@endforeach
						</ul>
					</div>
				</div>
		</div>
		{{ Form::close() }}
	</div>
</div>
@endsection

@section('style')
<link href="{{ asset('css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
@endsection

@section('script')
<script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
<script>
$('.datepicker').datetimepicker({
	format: 'DD-MM-YYYY',
});

$("#sort_list ul").sortable({ opacity: 0.6, cursor: 'move',
	start: function(event, ui) {
				ui.item.startPos = ui.item.index();
		},
	update: function(event, ui) {
	var end_pos = ui.item.index() + 1;
	var start_pos = ui.item.startPos + 1;
	var pol_id = $('#pol_id').val();
	var id = ui.item.attr("id");
	$.ajax({
					data: {start_pos:start_pos, end_pos:end_pos, pol_id:pol_id, id:id, _token: "{{ csrf_token() }}"},
					type: 'POST',
					url: '{{url('poll/sort_item')}}'
			});
}
});
</script>
@endsection
