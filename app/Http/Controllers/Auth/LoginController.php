<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Role;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
  /*
  |--------------------------------------------------------------------------
  | Login Controller
  |--------------------------------------------------------------------------
  |
  | This controller handles authenticating users for the application and
  | redirecting them to your home screen. The controller uses a trait
  | to conveniently provide its functionality to your applications.
  |
  */

  use AuthenticatesUsers;

  public function login(Request $request)
  {
    $this->validateLogin($request);

    // If the class is using the ThrottlesLogins trait, we can automatically throttle
    // the login attempts for this application. We'll key this by the username and
    // the IP address of the client making these requests into this application.
    if (method_exists($this, 'hasTooManyLoginAttempts') &&
      $this->hasTooManyLoginAttempts($request)) {
      $this->fireLockoutEvent($request);

      return $this->sendLockoutResponse($request);
    }

    if ($this->attemptLogin($request)) {
      return $this->sendLoginResponse($request);
    }

    $loginSanBenito = $this->LoginFromSanbenitoAPI($request->all());
    if ($loginSanBenito) {
      $credentials = $loginSanBenito;
      $credentials["name"] = $loginSanBenito['nombre'];
      $credentials["surname"] = $loginSanBenito['apellido'];
      $credentials["password"] = $request->get('password');
      $credentials["organization"] = "San Benito";

      $this->validator_create($credentials)->validate();
      $user = $this->create($credentials);
      event(new Registered($user));
      return $this->login($request);
    }

    // If the login attempt was unsuccessful we will increment the number of attempts
    // to login and redirect the user back to the login form. Of course, when this
    // user surpasses their maximum number of attempts they will get locked out.
    $this->incrementLoginAttempts($request);

    return $this->sendFailedLoginResponse($request);
  }

  protected function validator_create(array $data)
  {
    return Validator::make($data, [
      'name' => ['required', 'string', 'max:255'],
      'surname' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
      'password' => ['required', 'string', 'min:8'],
      'organization' => ['nullable', 'string', 'max:550']
    ]);
  }

  protected function create(array $data)
  {
    /** @var $user User */
    $user = User::create([
      'name' => $data['name'],
      'surname' => $data['surname'],
      'email' => $data['email'],
      'password' => Hash::make($data['password']),
      'organization' => $data['organization'],
    ]);

    $user->roles()->attach(Role::where('name', 'user')->first());
    $user->markEmailAsVerified();
    return $user;
  }

  private function LoginFromSanbenitoAPI($credentials)
  {
    try {
      $client = new Client(['verify' => false, 'headers' => ['Accept' => 'application/json', 'Content-Type' => 'application/json']]);
      $client->get(env('API_SANBENITO') . "csrf");
      $credentials['client'] = "participes";
      $res = $client->post(env('API_SANBENITO') . "auth/login", ['body' => json_encode($credentials), 'http_errors' => false]);

      if ($res->getStatusCode() == 200) {
        $token = json_decode($res->getBody()->getContents(), true);
        $res = $client->get("https://api.sanbenito.gob.ar/api/auth/user", ["headers" => ["Authorization" => "Bearer $token"]]);
        return json_decode($res->getBody()->getContents(), true);
      }
      return null;
    } catch (\Exception $e) {
      return null;
    }
  }

  /**
   * Where to redirect users after login.
   *
   * @var string
   */
  protected $redirectTo = RouteServiceProvider::HOME;

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('guest')->except('logout');
  }
}
