<div class="post">
  <div class="post-content">
    <div class="row">
      <div class="col-md-2 text-center">
        <div class="image-cropper">
          <img class="rounded" src="{{ !empty($owner->avatar) ? $owner->avatar : asset(config('playligo.avatar_default')) }}" >
        </div>
        {{ $owner->name }}
      </div>
      <div class="col-md-10">
        <div class="row meta-list">
          <div class="col-md-3 col-sm-3 col-xs-3">
            <i class="fa fa-clock-o"></i> {{ Carbon::parse($playlist->created_at)->diffForHumans() }}
          </div>
          <div class="col-md-3 col-sm-3 col-xs-3">
            <i class="fa fa-eye"></i>{{ $playlist->pl_view }} views
          </div>
          <div class="col-md-3 col-sm-3 col-xs-3">{{ FormError::rating('plRating', 0) }}</div>
          <div class="col-md-1 col-sm-1 col-xs-1">
            {{ Form::button('Rate', ['type'=>'button', 'class'=>'btn-xs btn-success btn ratingPopUp_open']) }}
            @if(auth()->check())
            <div id="ratingPopUp" class="well text-center">
              Enter your rating
              {{ FormError::rating('newPlRating', 0) }}
            </div>
            @endif
          </div>
        </div>
        <div class="entry-content">
          <div>{{ $playlist->pl_description }}</div>
          Tags: {{$playlist_keys}}
        </div>
      </div>
    </div>
  </div>
</div>
