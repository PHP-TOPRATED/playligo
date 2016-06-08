@extends('layouts.app_home')

@section('content')
<div class="fullscreen-bg">
    <video loop muted autoplay poster="img/videoframe.jpg" class="fullscreen-bg__video">
        <source src="{{ asset('video/playligo-visualize-your-travel.mp4') }}" type="video/mp4">
    </video>
</div>

<div class="text-center home-content">
  <div class="home-content-inner">
    <div class="section"><a href="#"><i class="fa semi-transparent fa-lg fa-play-circle"></i></a></div>
    <h1>Be inspired. Decide with confidence.</h1>
    <h2 style="margin-bottom: 40px">Watch short curated destination video playlists before you go.</h2>
    <div class="section">
      <div class="row">
          <div class="col-md-10 col-md-offset-1">
            <a href="{{ url('search') }}#discover">{{ Form::button(trans('form.btn_discover'), ['class'=>'btn-transparent-hero']) }}</a>&nbsp;&nbsp;&nbsp;
            <a href="{{ url('search') }}">{{ Form::button(trans('form.btn_search_hero'), ['class'=>'btn-transparent-hero']) }}</a>
          </div>
      </div>
    </div>
  </div>
</div>
@endsection
