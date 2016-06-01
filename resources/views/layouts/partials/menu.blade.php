<header id="navigation">
      <div class="navbar" role="banner">
        <div class="container">
          <a class="secondary-logo" href="{{ url('/') }}">
            <img class="img-responsive" src="{{ asset('img/logo_playligo_md.png') }}" alt="logo">
          </a>
        </div>
        <div class="topbar">
          <div class="container">
            <div id="topbar" class="navbar-header">
              <a class="navbar-brand" href="{{ url('/') }}">
                <img class="img-responsive" src="{{ asset('img/logo_playligo_md.png') }}" alt="logo">
              </a>
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
            </div>
          </div>
        </div>
        <div id="menubar" class="container">
          <nav id="mainmenu" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="{{ url('/home') }}">Home</a></li>
                <li><a href="{{ url('/search') }}">Search</a></li>
                <li><a href="{{ url('/public_playlist') }}">Playlists</a></li>
                <li><a href="{{ url('/public_poll') }}">Polls</a></li>
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <!-- Authentication Links -->
                @if (Auth::guest())
                    <li><a href="{{ url('/login') }}">Login</a></li>
                    <li><a href="{{ url('/register') }}">Register</a></li>
                @else
                    <li><a href="{{ url('/playlist') }}">My Playlists</a></li>
                    <li><a href="{{ url('/poll') }}">My Polls</a></li>
                    @if (auth()->user()->hasRole('admin'))
                    <li><a href="{{ url('/admin') }}"><i class="fa fa-gears"></i> Admin</a></li>
                    @endif
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            {{ Auth::user()->name }}
                            <!-- {{ Auth::user()->name }} <div class="media-photo media-round user-profile-image header-icon icon-profile-alt-white"><img width="24" height="24" src="{{ Auth::user()->avatar }}"></div> -->
                             <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu" role="menu">
                          <li><a href="{{ url('/profile/edit') }}">Edit profile</a></li>
                          <li><a href="{{ url('/password/edit') }}">Change password</a></li>
                          <li><a href="{{ url('/logout') }}">Logout</a></li>
                        </ul>
                    </li>
                @endif
            </ul>

          </nav>
        </div>
      </div>
    </header>
