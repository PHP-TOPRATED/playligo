@extends('layouts.admin')

@section('content_admin')
    <div class="section">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>User name</th>
                        <th>Score</th>
                        <th>Comment</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($feedbacks as $feedback)
                        <tr>
                            <td>{{ $feedback->user->name }}</td>
                            <td>{{ $feedback->mark }}</td>
                            <td>{{ $feedback->comment }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{--<div class="pagination-wrapper">--}}
                    {{--{{ $feedbacks->links() }}--}}
                {{--</div>--}}
            </div>
        </div>
    </div>
@endsection
