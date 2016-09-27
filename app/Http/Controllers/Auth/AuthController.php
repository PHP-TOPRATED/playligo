<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\LogEmail;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Socialite;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';
    protected $redirectAfterLogout = '/';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware($this->guestMiddleware(), ['except' => ['logout', 'ajaxLogin']]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data, $fb = false)
    {
      $rules = [
          'name' => 'required|max:255',
          'email' => 'required|email|max:255|unique:users',
          'password' => 'required|min:6|confirmed',
      ];

      if ($fb) unset($rules['password']);

      return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
      $invite_code = session()->get('invite_code', '');

      return User::create([
          'name' => $data['name'],
          'email' => $data['email'],
          'invite_code' => $invite_code,
          'password' => bcrypt($data['password']),
      ]);
    }

    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }
        // Create user
        $user = $this->create($request->all());

        // Attach role to user
        $user->attachRole(config('entrust.member_role_id'));

        // Send autoresponder
        $email = new LogEmail;

        $email->sendNewUser($user);

        Auth::guard($this->getGuard())->login($user);

        $redirect = '/';
        if (session()->has('last_page')) {
            $redirect = session()->pull('last_page');
        } else {
            $redirect = $this->redirectPath();
        }

        return response()->json(['redirect' => $redirect, 'message'=> 'Successful sign up! Signing in...']);

        // return redirect($this->redirectPath());
    }

    /**
     * Direct to social login
     *
     * @param  $provider
     */
    public function getSocialAuth($provider)
    {
        if (!config("services.$provider")) {
            abort('404');
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Process callback from social page
     *
     * @param  $provider
     */
    public function getSocialAuthCallback($provider=null)
    {
        if ($user = Socialite::with($provider)->user()) {
            $authUser = $this->findOrCreateUser($user);

            if (!empty($authUser)) {
              Auth::login($authUser, true);
            }

            if (session()->has('last_page')) {
              return redirect(session()->pull('last_page'));
            } else {
              return redirect($this->redirectTo);
            }

        } else {
            return 'something went wrong';
        }
    }

    /**
     * Find or create new user using Facebook
     *
     * @param  $facebookUser
     */
    private function findOrCreateUser($facebookUser)
    {
        $authUser = User::where('facebook_id', $facebookUser->id)->first();

        if ($authUser){
            return $authUser;
        }

        $input = [
            'name' => $facebookUser->name,
            'email' => $facebookUser->email,
            'facebook_id' => $facebookUser->id,
            'avatar' => $facebookUser->avatar
        ];

        $validator = $this->validator($input, true);

        if ($validator->fails()) {
          throw new \Exception($validator->errors(), 422);
        }

        $user = User::create($input);

        // Attach role to user
        $user->attachRole(config('entrust.member_role_id'));

        // Send autoresponder
        $email = new LogEmail;

        $email->sendNewUser($user);

        return $user;

    }

    public function ajaxLogin()
    {
        return view('auth.login_ajax');
    }

    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->loginUsername() => 'required', 'password' => 'required'
        ]);

        try {
          User::where('email', '=', $request->input('email'))->whereIn('status', [1])->firstOrFail();
        }
        catch (ModelNotFoundException $e)
        {
          throw new \Exception('Account is not active', 422);
        }
    }

    protected function handleUserWasAuthenticated(Request $request, $throttles)
    {
        if ($throttles) {
            $this->clearLoginAttempts($request);
        }

        if (method_exists($this, 'authenticated')) {
            return $this->authenticated($request, Auth::guard($this->getGuard())->user());
        }

        $redirect = '/';
        if (session()->has('last_page')) {
            $redirect = session()->pull('last_page');
        } else {
            $redirect = $this->redirectPath();
        }

        if ($request->ajax() || $request->wantsJson()) {
          return response()->json(['redirect' => $redirect, 'message'=> 'Signing in...']);
        } else {
          return redirect()->intended($this->redirectPath());
        }
    }

    protected function sendFailedLoginResponse(Request $request)
    {
      if ($request->ajax() || $request->wantsJson()) {
        return response()->json([$this->loginUsername() => $this->getFailedLoginMessage()], 422);
      } else {
        return redirect()->back()
            ->withInput($request->only($this->loginUsername(), 'remember'))
            ->withErrors([
                $this->loginUsername() => $this->getFailedLoginMessage(),
            ]);

      }
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        $throttles = $this->isUsingThrottlesLoginsTrait();

        if ($throttles && $lockedOut = $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->getCredentials($request);

        if (Auth::guard($this->getGuard())->attempt($credentials, $request->has('remember'))) {
            return $this->handleUserWasAuthenticated($request, $throttles);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        if ($throttles && ! $lockedOut) {
            $this->incrementLoginAttempts($request);
        }

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        Auth::guard($this->getGuard())->logout();
        \Session::flush();

        return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : '/');
    }

}
