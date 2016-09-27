@extends('layouts.email')
@section('email_content')
  <p>Dear {{ $owner->name }},</p>
  <p>Good job! Here’s the link:<br/> <a href="{{ url('public_playlist/' . $playlist->pl_slug) }}">{{ url('public_playlist/' . $playlist->pl_slug) }}</a></p>
  <img src="{{ asset('img/jump.gif') }}">
  <p>Have you shared it with your friends?</p>
  @include('email.footer')
@endsection
`