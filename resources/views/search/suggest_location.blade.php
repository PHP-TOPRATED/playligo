@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="section text-center">
            <h1>Let's explore {{ $region }}! <a href="{{ url('search#discover') }}"><span class="label label-info">Try another region?</span></a>
            </h1>
        </div>
    <!-- {{ count($cities) }} -->
        @if(!$cities->isEmpty())
            <div class="section text-center">
                <?php $divider = $cities[count($cities) - 1]->cit_hotels; ?>
                @foreach($cities->chunk($chunk_size) as $index => $city_set)
                    <a class="switchSet" set="{{ $index }}" href="#"><span
                                class="label label-success">Set {{ $index + 1 }}</span></a>
                    <div id="tags{{ $index }}" class="tags">
                        <ul>
                            @foreach($city_set as $city)
                                <?php $size = ($city->cit_hotels / $divider) <= 3 ? $city->cit_hotels / $divider : 2.5 ?>
                                <?php $size = 3; ?>
                                <li> <a href="{{ url('/new_search_keywords?location=' . $city->cit_name . ", " . $city->coun_name) }}" data-weight="{{ $size*10 }}">{{"$city->cit_name"}}, {{ "$city->coun_name" }}</a> </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="section text-center">
            Click on one of the locations to begin.
            <div class="row">
                <div class="col-md-10 col-sm-10 col-xs-10 col-md-offset-1 col-sm-offset-1 col-xs-offset-1">
                    <div id="canvasSection">
                        <div id="myCanvasContainer">
                            <canvas width="400" height="400" id="myCanvas">
                                <p>Anything in here will be replaced on browsers that support the canvas element</p>
                            </canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script src="{{ asset('js/jquery.tagcanvas.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            prepareCanvas('tags0');

            $('body').on('click', '.switchSet', function (event) {
                event.preventDefault();
                var set = $(this).attr('set');
                prepareCanvas('tags' + set);
            });

        });


        function prepareCanvas(tagid) {
            $('#myCanvasContainer').fadeIn(1000, function () {
                $("#myCanvas").attr("width", $("#canvasSection").width());
                if (!$('#myCanvas').tagcanvas({
                            textColour: '#333333',
                            outlineColour: '#cccccc',
                            bgOutline: '#6fa806',
                            reverse: true,
                            depth: 0.1,
                            maxSpeed: 0.05,
                            clickToFront: 100,
                            freezeDecel: true,
                            weight: true,
                            weightMode: "size",
                            weightFrom: "data-weight",
                            weightSize: 1,
                            pinchZoom: true,
                            shape: "sphere",
                            shuffleTags: true,
                            initial: [0.1, 0.1]
                        }, tagid)) {
                    // something went wrong, hide the canvas container
                    $('#myCanvasContainer').hide();
                }
            });

        }
    </script>
@endsection
