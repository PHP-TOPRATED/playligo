@extends('layouts.app')
@section('content')
    <div class="text-center search-keywords">
        <div class="search-keywords-inner">
            <div class="section">
                <h1><span class="label label-primary">{{ $location }}</span></h1>
                <div class="row">
                    <div class="col-lg-6 col-md-8 col-sm-10 col-xs-10 col-center">
                        <h2>What do you want to SEE?</h2>
                        {{ Form::open(['url'=>url('autogen'), 'method'=>'get', 'class'=>'submit-ajax-get']) }}
                        <div class="form-group">
                            <input name="search_keys" id="tags" value="{{ implode(",", $default) }}"
                                   class="form-control"/>
                            Add more keywords by clicking on discovery ideas listed below
                        </div>
                        <div class="keywords-tabs">
                            <ul class="nav nav-tabs row" role="tablist">
                                <li role="presentation" class="active col-md-4 col-sm-6 col-xs-12"><a
                                            href="#tab-general"
                                            aria-controls="tab-general"
                                            role="tab"
                                            data-toggle="tab">General</a>
                                </li>
                                <li role="presentation" class="col-md-4 col-sm-6 col-xs-12"><a href="#tab-interests"
                                                                                               aria-controls="tab-interests"
                                                                                               role="tab"
                                                                                               data-toggle="tab">Places
                                        of Interest</a></li>
                                <li role="presentation" class="col-md-4 col-sm-6 col-xs-12"><a href="#tab-tours"
                                                                                               aria-controls="tab-tours"
                                                                                               role="tab"
                                                                                               data-toggle="tab">Tours &
                                        Activities</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab-general">
                                    <div class="keywords">
                                        @include('search.partials._keywords', [
                                            'keywords' => $general_keywords,
                                            'default' => $default
                                        ])
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab-interests">
                                    <div id="interests-hidden" style="display: none;"></div>
                                    <div class="keywords" id="interests">
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab-tours">
                                    <div class="keywords" id="tours">
                                        <div class="alert alert-danger alert-dismissible" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span></button>
                                            Please click any words from titles of the Tours and Activities to use as
                                            keyword(s) for your video search
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{ Form::hidden('location', $location) }}
                        {{ Form::button(trans('form.btn_start_visualizing'), ['type'=>'submit', 'class'=>'btn btn-primary col-xs-10 col-center', 'id' => 'keywords-submit']) }}
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('style')
    <link href="{{ asset('css/jquery.tag-editor.css') }}" rel="stylesheet">
    <style>
        .tag-editor {
            padding: 10px 10px;
        }
    </style>
@endsection

@section('script')
    <script src="{{ asset('/js/jquery.caret.min.js') }}"></script>
    <script src="{{ asset('js/jquery.tag-editor.min.js') }}"></script>
    <script async defer type="text/javascript"
            src="https://maps.googleapis.com/maps/api/js?key={{ config('googlemaps.key') }}&libraries=places&callback=initialize"></script>
    <script>
        var geocoder, service, places = 0, placesHTML = '';
        var placesContainer = document.getElementById('interests');
        var placesHiddenContainer = document.getElementById('interests-hidden');
        var cutKeywords = [];
        var address = "{{ $location }}";
        var tempPlacesCount = 0;
        $(document).ready(function () {
            $('#tags').tagEditor({
                maxTags: parseInt({{ config('playligo.max_keyword_tags') }}),
                beforeTagDelete: function (field, editor, tags, val) {
                    $('.keywords').find('[data-keyword="' + val + '"]').show('fast');
                    $('.keywords').find('[data-keyword="' + cutKeywords[val] + '"]').show('fast');
                }
            });
        });
        $('body').on('click', '.keyword:not(.keyword-builder)', function () {
            $('#tags').tagEditor('addTag', $(this).data('keyword'));
            $(this).hide('fast');
        });
        $('body').on('click', '.keyword-part', function (event) {
            event.stopPropagation();
            var word = $(this).text().toLowerCase();
            var keyword = $(this).closest('.keyword');
            var tempKeywordContainer = $(this).parent().prev('.keyword-temp');
            var tempKeyword = $.merge(tempKeywordContainer.children('.keyword-temp-text').val().split(' '), []);
            $(this).toggleClass('active');

            if ($.inArray(word, tempKeyword) == -1) {
                tempKeyword.push(word);
            } else {
                tempKeyword.splice($.inArray(word, tempKeyword), 1);
            }
            if (tempKeyword.length != 0) {
                tempKeywordContainer.css('display', 'table');
            } else {
                tempKeywordContainer.hide();
            }
            tempKeywordContainer.children('.keyword-temp-text').val(tempKeyword.join(' '));
        });
        $('body').on('click', '.keyword-temp', function (event) {
            event.stopPropagation();
        });
        $('body').on('click', '.keyword-temp-btn-cancel', function (event) {
            $(this).closest('.keyword-temp').hide('fast');
            $(this).parent().prev().val('');
            $('.keyword-part').removeClass('active');
        });
        $('body').on('click', '.keyword-temp-btn-add', function (event) {
            var tempValue = $(this).parent().prev().val();
            $('#tags').tagEditor('addTag', tempValue);
            $(this).closest('.keyword').hide('fast');
            cutKeywords[tempValue.substring(1, tempValue.length)] = $(this).closest('.keyword').data('keyword');
        });
        function initialize() {
            $('#tab-interests .keywords').hide();
            loader($('#tab-interests'), true);
            geocoder = new google.maps.Geocoder();
            service = new google.maps.places.PlacesService(placesHiddenContainer);
            geocodeAddress();
        }
        function geocodeAddress() {
            geocoder.geocode({'address': address}, function (results, status) {
                if (status === 'OK') {
                    var lat = results[0].geometry.location.lat();
                    var lng = results[0].geometry.location.lng();
                    getTours(lat, lng);
                    getPlacesOfInterest(lat, lng);
                } else {
                    console.error('Geocode was not successful for the following reason: ' + status);
                }
            });
        }
        function getPlacesOfInterest(lat, lng) {
            var requestPoint = {
                query: 'point of interest ' + address,
                location: new google.maps.LatLng(lat, lng),
                language: 'en',
                types: ['point_of_interest']
            };
            var requestPlaces = {
                query: 'place of interest ' + address,
                location: new google.maps.LatLng(lat, lng),
                language: 'en',
                types: ['point_of_interest']
            };
            service.textSearch(requestPlaces, googleSearchCallback);
            service.textSearch(requestPoint, googleSearchCallback);
        }

        function googleSearchCallback(results, status, pagination) {
            loader($('#tab-interests'), false);
            $('#tab-interests .keywords').show();
            processResults(results, status, pagination);
        }
        function processResults(results, status, pagination) {
            switch (status) {
                case google.maps.places.PlacesServiceStatus.OK:
                    places += results.length;
                    results.forEach(function (result) {
                        placesContainer.innerHTML += outputPlace(result);
                    });
                    if (places < 50 && pagination.hasNextPage) {
                        pagination.nextPage();
                    }
                    break;
                case google.maps.places.PlacesServiceStatus.ZERO_RESULTS:
                    placesContainer.innerHTML = '<div class="alert alert-info" role="alert">' +
                            'Unfortunately, there are no places of interest for this place' +
                            '</div>';
                    break;
                default:
                    placesContainer.innerHTML += '<div class="alert alert-warning" role="alert">' +
                            'Something weird happened' +
                            '</div>';
                    break;
            }
        }

        function outputPlace(keyword) {
            var name = keyword.name.replace("/[^ \w]+/", "");

            var description = keyword.formatted_address;
            var image_path = getPlacePhotoUrl(keyword);
            return '<div class="keyword clearfix" data-keyword="' + name + '">' +
                    '<div class="keyword-image">' +
                    '<img src="' + image_path + '" alt="' + name + '">' +
                    '</div>' +
                    '<div class="keyword-body">' +
                    '<div class="keyword-name">' + name + '</div>' +
                    '<div class="keyword-description">' + description + '</div>' +
                    '</div>' +
                    '</div>';
        }

        function getPlacePhotoUrl(keyword) {
            var photo_path = null;
            var photos = keyword.photos;
            var width = parseInt({{ \App\Keyword::KEYWORD_ICON_WIDTH }});
            var height = parseInt({{ \App\Keyword::KEYWORD_ICON_HEIGHT }});
            if (!photos) {
                photo_path = keyword.icon;
            } else {
                photo_path = photos[0].getUrl({
                    'maxWidth': width,
                    'maxHeight': height
                });
            }
            return photo_path;
        }

        function getTours(lat, lng) {
            $('#tab-tours .keywords').hide();
            loader($('#tab-tours'), true);
            $.ajax({
                method: "GET",
                url: "{{ url('tours') }}",
                data: {lat: lat, lng: lng}
            })
                    .done(function (data) {
                        $('#tours').append(data);
                        $('#tab-tours .keywords').show();
                        loader($('#tab-tours'), false);
                    });
        }
    </script>
@endsection
