@if(!empty($keywords))
    @foreach($keywords as $keyword)
        <div class="keyword @if($split) keyword-builder @endif clearfix" data-keyword="{{ strtolower(preg_replace("/[^ \w]+/", "", $keyword['name'])) }}" style="display: {{ in_array($keyword['name'], $default) ? 'none' : 'block' }}" >
            <div class="keyword-image">
                @if(isset($keyword['thumb_path']))
                    <img src="{{ url($keyword['thumb_path']) }}" alt="{{ $keyword['name'] }}">
                @elseif(isset($keyword['image_path']))
                    <img src="{{ url($keyword['image_path']) }}" alt="{{ $keyword['name'] }}">
                @else
                    <img src="http://placehold.it/200x100" alt="{{ $keyword['name'] }}">
                @endif
            </div>
            <div class="keyword-body">
                @if($split)
                    <div class="input-group keyword-temp">
                        <input type="text" class="form-control keyword-temp-text" readonly>
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-danger keyword-temp-btn-cancel"><i class="fa fa-times"></i></button>
                            <button type="button" class="btn btn-success keyword-temp-btn-add"><i class="fa fa-plus"></i></button>
                        </div>
                    </div>
                @endif
                <div class="keyword-name">
                    @if(!$split)
                        {{ preg_replace("/[^ \w]+/", "", $keyword['name']) }}
                    @else
                        @foreach(explode(' ', preg_replace("/[^ \w]+/", "", $keyword['name'])) as $key => $word)
                            <span class="keyword-part" data-order="{{ $key }}">{{ $word }}</span>
                        @endforeach
                    @endif
                </div>
                <div class="keyword-description">
                    {{ $keyword['description'] }}
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="alert alert-info" role="alert">
        Unfortunately, there are no tours or activities for this place
    </div>
@endif