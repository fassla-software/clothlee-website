<?php

namespace App\Http\Controllers;

use App\Http\Requests\SellerRegistrationRequest;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\User;
use App\Models\BusinessSetting;
use Auth;
use Hash;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Support\Facades\Notification;

class ShopController extends Controller
{

    public function __construct()
    {
        $this->middleware('user', ['only' => ['index']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shop = Auth::user()->shop;
        return view('seller.shop', compact('shop'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
   public function create()
{
    if (Auth::check()) {
        /*
        if ((Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'customer')) {
            flash(translate('Admin or Customer cannot be a seller'))->error();
            return back();
        }

        if (Auth::user()->user_type == 'seller') {
            flash(translate('This user already a seller'))->error();
            return back();
        }
        */

        return view('auth.' . get_setting('authentication_layout_select') . '.seller_registration');
    } else {
        return view('auth.' . get_setting('authentication_layout_select') . '.seller_registration');
    }
}


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SellerRegistrationRequest $request)
    {
        $user = new User;
        $user->name = $request->shop_name;
        $user->user_type = "seller";
        $user->password = Hash::make($request->password);
		$user->phone = $request->phone;
      
        if ($user->save()) {
            $shop = new Shop;
            $shop->user_id = $user->id;
            $shop->name = $request->shop_name;

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $extension = $file->getClientOriginalExtension();
            $filename = 'logo_' . time() . '_' . uniqid() . '.' . $extension;

            // Move the file to the correct directory
            $destinationPath = public_path('uploads/all/');
            $file->move($destinationPath, $filename);

            // Save the file path in the database
            $shop->logo = $filename;
        }
          
            $shop->address = $request->address;
          	$shop->phone = $request->phone;
          	$shop->facebook = $request->facebook;
          	$shop->instagram = $request->instagram;
          	$shop->tiktok = $request->tiktok;
          	$shop->website = $request->website;
          	$shop->youtube = $request->youtube;
            $shop->slug = preg_replace('/\s+/', '-', str_replace("/", " ", $request->shop_name));
            $shop->save();

            auth()->login($user, false);
          
          // ✅ Auto-create welcome conversation from admin (user_id 9) to the new seller
try {
    $adminId = 9;

    $conversation = \App\Models\Conversation::create([
        'sender_id' => $adminId,
        'receiver_id' => $user->id,
        'title' => 'Welcome',
        'sender_viewed' => 1,
        'receiver_viewed' => 0,
    ]);

    \App\Models\Message::create([
        'conversation_id' => $conversation->id,
        'user_id' => $adminId,
        'message' => 'Welcome to our platform! Your shop has been created. Let us know if you need any help getting started.',
    ]);
} catch (\Exception $e) {
    \Log::error('Seller welcome conversation failed: ' . $e->getMessage());
}

          
            if (BusinessSetting::where('type', 'email_verification')->first()->value == 0) {
                $user->email_verified_at = date('Y-m-d H:m:s');
                $user->save();
            } else {
                try {
                    $user->notify(new EmailVerificationNotification());
                } catch (\Throwable $th) {
                    $shop->delete();
                    $user->delete();
                    flash(translate('Seller registration failed. Please try again later.'))->error();
                    return back();
                }
            }

            flash(translate('Your Brand has been created successfully!'))->success();
            return redirect()->route('seller.dashboard');
        }
        flash(translate('Sorry! Something went wrong.'))->error();
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
