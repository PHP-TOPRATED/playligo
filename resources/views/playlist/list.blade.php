@extends('layouts.list')
@section('content_list')
    <div class="page-breadcrumbs">
        <h1 class="section-title">{{ trans('playlist.list') }}</h1>
        <div class="world-nav cat-menu">
            <ul class="list-inline">
                <li class="active"><a href="{{ url('search') }}" class=""><span
                                class="fa fa-plus"></span> @lang('playlist.new')</a></li>
            </ul>
        </div>
    </div>

    <div class="section">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th class="col-md-4 col-sm-4 col-xs-4" colspan="2">{{ trans('playlist.pl_title') }}</th>
                        <th class="col-md-4 col-sm-4 col-xs-4">{{ trans('playlist.pl_location') }}</th>
                        <th class="col-md-4 col-sm-4 col-xs-4">{{ trans('form.action_column') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($playlists as $pl)
                        <tr>
                            <td>
                                <img class="video_thumb img-rounded"
                                     src="{{ $pl->pl_thumb_path or asset(config('playligo.video_thumb_default')) }}">
                            </td>
                            <td>
                                {{ $pl->pl_title }}
                                <div>
                                    <span class="label2 label-success"><i class="fa fa-eye"></i> {{ $pl->pl_view }}
                                        views</span>
                                    <span class="label2 label-success"><i
                                                class="fa fa-star"></i> {{ $pl->pl_rating }}</span>
                                    <span class="label2 label-success"><i
                                                class="fa fa-clock-o"></i> {{Carbon::parse($pl->created_at)->diffForHumans()}}</span>
                                </div>
                            </td>
                            <td>
                                {{ $pl->pl_location }}
                            </td>
                            <td class="action_column">
                                <a href="{{ url('playlist/edit/' . $pl->pl_id) }}"
                                   title="{{ trans('form.action_edit') }}">{{ Form::button('<i class="fa fa-edit"></i> '.trans('form.btn_edit'), ['class'=>'btn btn-success btn-small']) }}</a>
                                <a href="{{ url('playlist/delete/' . $pl->pl_id) }}"
                                   title="{{ trans('form.action_delete') }}"
                                   class="btn-modal">{{ Form::button('<i class="fa fa-trash"></i> '.trans('form.btn_delete'), ['class'=>'btn btn-success btn-small']) }}</a>
                                {{--<a href="{{ url('poll/add/' . $pl->pl_id) }}" title="{{ trans('form.action_add_poll') }}" class="btn-modal">{{ Form::button('<i class="fa fa-plus"></i> '.trans('form.btn_add_to_poll_alt'), ['class'=>'btn btn-success btn-small']) }}</a>--}}
                                @if (! $pl->isPublished())
                                    {{ Form::button(trans('form.btn_publish'), ['type'=>'button', 'class'=>'btn btn-success btn-small btn-publish', 'data-pl_id' => $pl->pl_id, 'data-url' => route('playlist.publish'), 'data-title' => (!empty($pl->pl_title)) ? 'true' : 'false', 'data-title_url' => url('playlist/edit')]) }}
                                @else
                                    <a target="_blank"
                                       href="{{ route('public_playlist.view', ['playlist' => $pl->pl_slug]) }}">
                                        {{ Form::button(trans('form.btn_view_live'), ['class'=>'btn btn-success btn-small']) }}
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="pagination-wrapper">
                    {{ $playlists->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
