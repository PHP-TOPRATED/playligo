{{ Form::open(['url'=>route('admin.keywords.search'), 'method'=>'post' ]) }}
<div class="row">
  <div class="col-md-3">
    <div class="form-group">
    {{ Form::label('name', trans('keywords.name'), ['class'=>'control-label']) }}
    {{ Form::text('name', array_get($search, 'name'), ['class'=>'form-control']) }}
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-group">
    {{ Form::label('description', trans('keywords.description'), ['class'=>'control-label']) }}
    {{ Form::text('description', array_get($search, 'description'), ['class'=>'form-control']) }}
    </div>
  </div>
</div>
<div class="form-group">
{{ Form::button(trans('form.btn_search'), ['type'=>'submit', 'class'=>'btn btn-primary']) }}
{{ Form::button(trans('form.btn_clear_search'), ['type'=>'submit', 'class'=>'btn btn-primary btn-clear']) }}
</div>
{{ Form::close() }}
