<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('layouts.partials.meta')
    @yield('meta')
    <title>{{ $page_title or config('playligo.title') }}</title>

    <!-- Fonts -->
    <link rel='shortcut icon' href='{{ asset('img/favicon-32x32.png') }}' type='image/x-icon'/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
    <!-- <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700" rel='stylesheet' type='text/css'> -->
    <link href='https://fonts.googleapis.com/css?family=Signika+Negative:400,300,600,700' rel='stylesheet' type='text/css'>

    <!-- Styles -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.1.1/jquery.rateyo.min.css"> -->
    <link href="{{ asset('css/jquery-ui.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">
    <link href="{{ asset('css/presets/preset3.css') }}" rel="stylesheet">
    <link href="{{ asset('css/responsive.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sweetalert.css') }}" rel="stylesheet">
    <link href="{{ elixir('css/app.css') }}" rel="stylesheet">
    @yield('style')
    <script src="//load.sumome.com/" data-sumo-site-id="12f06233c59661b6520eb33ff694b42a0caa863dcc1b7c72527912614ad97be2" async="async"></script>
    @yield('head_script')
</head>
<body>
    {{-- if user has to leave feedback --}}
    @if (Auth::user() && isset($shouldFeedback) && $shouldFeedback && request()->input('share') != 1)
        <link href="{{ asset('css/bars-square.css') }}" rel="stylesheet">

        <div class="modal fade" tabindex="-1" role="dialog" id="feedback-modal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div style="padding: 35px 15px;" class="modal-body">
                        <p style="font-size: 24px;" class="text-center">Please rate how effective is playligo in helping you discover and visualize new destinations</p>
                        <select id="example" class="text-center">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                        </select>
                        <a href="#" id="skip-feedback" class="pull-right" data-dismiss="modal">skip</a>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        @include('layouts.partials.sharing_modal', ['message' => 'Please take a moment to share Playligo with your friends.'])

        <div class="modal fade" tabindex="-1" role="dialog" id="feedback-comment-modal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div style="padding: 35px 15px 25px 15px;" class="modal-body">
                        <p style="font-size: 24px;" class="text-center">
                            What will you say to friend to recommend Playligo.com?
                        </p>
                        <textarea id='feedback-comment' class="form-control" rows="10"></textarea>
                        <div class="pull-right" style="margin-top: 20px;">
                            <button class="btn btn-danger send-feedback">Skip</button>
                            <button class="btn btn-success send-feedback" style="padding: 6px 12px">Submit</button>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="{{ URL::asset('js/jquery.barrating.min.js')}}"></script>
        <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-579c836873a36a19"></script>
        <script>
            var mark;
            $(function() {
                $('#feedback-modal').modal();
            });

            // Handle addthis sharing event
            function eventHandler(evt) {
                if (evt.type == 'addthis.menu.share') {
                    $(".modal-backdrop").remove();
                    $('#sharing-modal').hide();
                    $('#feedback-comment-modal').modal('show');
                }
            }
            addthis.addEventListener('addthis.menu.share', eventHandler);

            $('#example').barrating({
                theme: 'bars-square',
                showSelectedRating: false,
                showValues: true,
                onSelect: function (value, text, event) {
                    if (value <= 5) {
                        sendRequest(value, '');
                    } else {
                        $(".modal-backdrop").remove();
                        $('#feedback-modal').hide();
                        $('#sharing-modal').modal('show');
                        mark = value;
                        {{--sendRequest("{{ Auth::user()->id }}", value, 'asd');--}}
                    }
                }
            });

            $('#skip-sharing').click(function () {
                $(".modal-backdrop").remove();
                $('#sharing-modal').hide();
                $('#feedback-comment-modal').modal('show');
            });

            $('#skip-feedback').click(function () {
                $(".modal-backdrop").remove();
                $('#feedback-modal').hide();
            });

            $('.send-feedback').click(function () {
                sendRequest(mark, $('#feedback-comment').val());
            });

            function sendRequest(mark, comment) {
                $.ajax({
                    url: "{{ url('feedback') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        user: "{{ Auth::user()->id }}",
                        mark: mark,
                        comment: comment
                    },
                    success: function (data) {
                        $(".modal-backdrop").remove();
                        $('#feedback-modal').hide();
                        $('#feedback-comment-modal').hide();
                        sweetAlert("Thanks for your feedback!");
                    }
                });
            }
        </script>
    @endif

    @yield('master_content')
    @include('layouts.partials.modal')

    <!-- JavaScripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.1.1/jquery.rateyo.min.js"></script> -->
    <!-- <script type="text/javascript" src="{{ URL::asset('js/jquery.rateyo.js')}}"></script> -->
    <script type="text/javascript" src="{{ URL::asset('js/jquery-ui.min.js')}}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/moment.js')}}"></script>
    @stack('scripts')
    {{-- <script src="{{ elixir('js/app.js') }}"></script> --}}
    <script type="text/javascript" src="{{ URL::asset('js/playligo-main.js')}}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/main.js')}}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/sweetalert.min.js')}}"></script>
    <div id="fb-root"></div>
    <script>(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.6&appId=266649633362050";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>

    <!--Start of Zopim Live Chat Script-->
    <script type="text/javascript">
    window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
    d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
    _.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute("charset","utf-8");
    $.src="//v2.zopim.com/?3tJY4ySCgNDah77yIJSnKlD2M8YviqKH";z.t=+new Date;$.
    type="text/javascript";e.parentNode.insertBefore($,e)})(document,"script");
    </script>
    <!--End of Zopim Live Chat Script-->

    <!--Start of Google Analytics Script-->
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-77339981-1', 'auto');
      ga('send', 'pageview');

    </script>
    <!--End of Google Analytics Script-->

    <script>
    $('body').on('click', '.popupchat', function(event){
      event.preventDefault();
      $zopim(function() {
        $zopim.livechat.window.show();
      });
    });
    </script>

    @yield('script')
    @stack('scripts')
    <div class="loading-overlay"></div>
</body>
</html>
