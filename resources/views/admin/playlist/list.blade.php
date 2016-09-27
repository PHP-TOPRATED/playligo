@extends('layouts.admin_list')
@section('content_list')
    <div class="section">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th class="col-md-6">{{ trans('playlist.pl_title') }}</th>
                        <th>{{ trans('playlist.pl_user') }}</th>
                        <th>{{ trans('form.created_at') }}</th>
                        <th>{{ trans('form.keyword_clicks') }}</th>
                        <th>{{ trans('form.keyword_clicks_stats') }}</th>
                        <th>{{ trans('form.action_column') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($playlists as $pl)
                        <tr>
                            <td>{{ $pl->pl_title }}
                                <div>
                                    <span class="label2 label-success"><i class="fa fa-star"></i> {{ $pl->pl_rating }}
                                        / {{ $pl->pl_rating_count }}</span>
                                    <span class="label2 label-success"><i
                                                class="fa fa-eye"></i> {{ $pl->pl_view }}</span>
                                </div>
                            </td>
                            <td><a href="{{ url('admin/user/popup/' . $pl->pl_user) }}" class="btn-modal"><i
                                            class="fa fa-eye"></i></a> {{ $pl->name }}</td>
                            <td>{{ $pl->created_at }}</td>
                            <td>{{ $pl->pl_keyword_clicks }}</td>
                            <td>{{ $pl->pl_visits == 0 ? '-' : number_format($pl->pl_keyword_clicks / $pl->pl_visits, 2) }}</td>
                            <td class="action_column">
                                <a href="{{ url('playlist/edit/' . $pl->pl_id) }}"
                                   title="{{ trans('form.action_edit') }}"><i class="fa fa-edit"></i></a>
                                <a href="{{ url('playlist/delete/' . $pl->pl_id) }}"
                                   title="{{ trans('form.action_delete') }}" class="btn-modal"><i
                                            class="fa fa-trash"></i></a>
                                <a href="{{ url('poll/add/' . $pl->pl_id) }}"
                                   title="{{ trans('form.action_add_poll') }}" class="btn-modal"><i
                                            class="fa fa-plus-square"></i></a>
                                <a href="{{ url('public_playlist/' . $pl->pl_slug) }}"
                                   title="{{ trans('form.action_view') }}"><i class="fa fa-eye"></i></a>
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
