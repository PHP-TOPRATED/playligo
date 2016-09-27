@extends('layouts.admin_page')

@section('content_page')
    <div class="section details-news">
        <div class="row">
            <div class="col-md-6">
                {!! Form::model($keyword, [
                        'class' => 'submit-ajax',
                        'action' => $keyword->id == null ? 'KeywordsController@store' : ['KeywordsController@update', $keyword],
                        'files' => true,
                        'role' => 'form'
                    ]) !!}
                    <div class="form-group">
                        {{ Form::label('name', trans('keywords.name'), ['class'=>'control-label']) }}
                        {{ Form::text('name', null, ['class'=>'form-control']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('weight', trans('keywords.weight'), ['class'=>'control-label']) }}
                        {{ Form::number('weight', null, ['class'=>'form-control', 'min' => 0]) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('image', trans('keywords.image'), ['class'=>'control-label']) }}
                        {{ Form::file('image', array('class' => 'filestyle', 'data-value' => null, 'data-icon' => 'true') ) }}
                    </div>
                    <div class="form-group">
                        @if(\File::exists($keyword->image_path) && \File::isFile($keyword->image_path))
                            <img src="{{ url($keyword->image_path) }}" alt="{{ $keyword->name }}">
                        @else
                            <img src="" alt="{{ $keyword->name }}" style="display: none;">
                        @endif
                    </div>
                    <div class="form-group">
                        {{ Form::label('description', trans('keywords.description'), ['class'=>'control-label']) }}
                        {{ Form::textarea('description', null, ['class'=>'form-control', 'rows' => '5']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::button(trans('form.btn_submit'), ['type'=>'submit', 'class'=>'btn btn-primary']) }}
                    </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript" src="{{ URL::asset('js/bootstrap-filestyle.min.js')}}"></script>
    <script>
        $(":file").filestyle();
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('.form-group img').show();
                    $('.form-group img').attr("src", e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(document).ready(function() {
            // update logo container on file input change event
            $('input[name="image"]').change(function () {
                readURL(this);
            });
        });
    </script>
@endpush