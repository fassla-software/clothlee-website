<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;
use App\Mail\SecondEmailVerifyMailManager;
use App\Utility\SmsUtility;
use Mail;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
{
    $request->validate([
        'phone' => 'required|numeric'
    ]);

    $user = User::where('phone', $request->phone)->first();

    if ($user) {
        $user->verification_code = rand(100000, 999999);
        $user->save();

        SmsUtility::password_reset($user);

        return view('auth.boxed.reset_password_phone', ['phone' => $user->phone]); // Force your view here
    } else {
        flash(translate('No account exists with this phone number'))->error();
        return back();
    }
}
  
  
  
 public function resetPasswordWithPhone(Request $request)
{
    $request->validate([
        'phone' => 'required',
        'code' => 'required|digits:6',
        'password' => 'required|min:6|confirmed',
    ]);

    $user = User::where('phone', $request->phone)
                ->where('verification_code', $request->code)
                ->first();

    if (!$user) {
        return view('auth.boxed.reset_password_phone', [
            'phone' => $request->phone,
        ])->withErrors(['code' => translate('Invalid code or phone number.')]);
    }

    $user->password = bcrypt($request->password);
    $user->verification_code = null;
    $user->save();

    auth()->login($user);

    return redirect('/dashboard')->with('status', translate('Password successfully changed.'));
}



}