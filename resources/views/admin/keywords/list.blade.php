@extends('layouts.admin_list')
@section('content_list')
    <div class="page-breadcrumbs">
        <h1 class="section-title">{{ trans('keywords.list') }}</h1>
        <div class="world-nav cat-menu">
            <ul class="list-inline">
                <li class="active"><a href="{{ route('admin.keywords.create') }}" class=""><span class="fa fa-plus"></span> @lang('keywords.new')</a></li>
            </ul>
        </div>
    </div>

    <div class="section">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>{{ trans('keywords.image') }}</th>
                        <th>{{ trans('keywords.name') }}</th>
                        <th>{{ trans('keywords.weight') }}</th>
                        <th>{{ trans('form.action_column') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($keywords as $keyword)
                            <tr>
                                <td>
                                    @if(\File::exists($keyword->thumb_path) && \File::isFile($keyword->thumb_path))
                                        <img class="keyword-icon" src="{{ url($keyword->thumb_path) }}" alt="{{ $keyword->name }}">
                                    @else
                                        <img class="keyword-icon" src="" alt="{{ $keyword->name }}" style="display: none;">
                                    @endif
                                </td>
                                <td>{{ $keyword->name }}</td>
                                <td>{{ $keyword->weight }}</td>
                                <td class="action_column">
                                    <a href="{{ route('admin.keywords.edit', $keyword) }}" title="{{ trans('form.action_edit') }}">{{ Form::button('<i class="fa fa-edit"></i> '.trans('form.btn_edit'), ['class'=>'btn btn-primary btn-small']) }}</a>
                                    <a href="{{ route('admin.keywords.delete', $keyword) }}" title="{{ trans('form.action_delete') }}" class="btn-modal">{{ Form::button('<i class="fa fa-trash"></i> '.trans('form.btn_delete'), ['class'=>'btn btn-primary btn-small']) }}</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="pagination-wrapper">
                    {{ $keywords->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
