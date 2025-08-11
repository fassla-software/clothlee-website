<?php
namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SmsRequest;
use App\Models\Shop;
use App\Imports\BulkSmsImport;
use Illuminate\Http\Request;
use App\Services\SendSmsService;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SmsRequestController extends Controller
{
    public function index()
    {
        return view('seller.notification.send_sms_request');
    }
  
    public function store(Request $request)
    {	
        $phones = explode(',', $request->phones);
        $userShop = User::with('shop')->where('id', auth()->id())->first();
    
        foreach($phones as $phone) {
            SmsRequest::create([
                'shop_id' => $userShop->shop->id,
                'phone' => trim($phone),
                'message' => $request->message,        
            ]);
        }
        
        flash()->success("The message request sent to admin");
        return redirect()->back();
    }
  
    // FIXED: Changed method name from bulkStore to bulkUpload to match route
    public function bulkStore(Request $request)
    {
        try {
            // Add debug logging
            Log::info('Bulk upload started', [
                'file' => $request->hasFile('bulk_file'),
                'file_name' => $request->file('bulk_file')?->getClientOriginalName(),
                'file_size' => $request->file('bulk_file')?->getSize(),
            ]);

            $request->validate([
                'bulk_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
                'default_message' => 'nullable|string|max:500',
            ]);

            $userShop = User::with('shop')->where('id', auth()->id())->first();

            if (!$userShop || !$userShop->shop) {
                throw new \Exception('User shop not found');
            }

            DB::beginTransaction();
            
            // FIXED: Create import instance and import file
            $import = new BulkSmsImport($request->input('default_message'));
            Excel::import($import, $request->file('bulk_file'));
            
            $rows = $import->getRows();
            $fileName = $request->file('bulk_file')->getClientOriginalName();
            
            Log::info('Import processed', [
                'rows_count' => count($rows),
                'file_name' => $fileName
            ]);
            
            if (empty($rows)) {
                throw new \Exception('No valid data found in the uploaded file. Please check if your file has a "phone" column.');
            }
            
            if (count($rows) > 1000) {
                throw new \Exception('Maximum 1000 records allowed per upload.');
            }
            
            $successCount = 0;
            $errors = [];
            
            foreach ($rows as $index => $row) {
                try {
                    $phone = $row['phone'];
                    $message = $row['message'];
                    
                    if (empty($phone)) {
                        throw new \Exception('Missing phone number');
                    }
                    if (empty($message)) {
                        throw new \Exception('Missing message');
                    }
                    
                    SmsRequest::create([
                        'shop_id' => $userShop->shop->id,
                        'phone' => $phone,
                        'message' => $message,
                    ]);
                    
                    $successCount++;
                    
                } catch (\Exception $e) {
                    $errors[] = [
                        'row' => $index + 2,
                        'phone' => $row['phone'] ?? 'N/A',
                        'error' => $e->getMessage()
                    ];
                }
            }
            
            DB::commit();
            
            $message = "Successfully processed {$successCount} SMS requests";
            if (!empty($errors)) {
                $message .= ". " . count($errors) . " rows had errors.";
            }
            
            Log::info('Bulk upload completed', [
                'success_count' => $successCount,
                'error_count' => count($errors)
            ]);

            // FIXED: Return JSON response for AJAX
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => [
                        'success_count' => $successCount,
                        'error_count' => count($errors),
                        'errors' => $errors
                    ]
                ]);
            }
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error in bulk upload', ['errors' => $e->errors()]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk upload error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Upload failed: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Upload failed: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    private function normalizePhone($phone)
    {
        if (empty($phone)) return null;
        
        // Remove all non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Add Egypt country code if needed (optional)
        if (strlen($phone) === 10 && strpos($phone, '0') === 0) {
            $phone = '2' . $phone; 
        }
        
        return $phone;
    }
  
  
  public function getAllRequestedShops()
	{
   		 $shops = Shop::whereHas('smsRequests')->with('smsRequests' , function ($q){
         	$q->where('status','pending');
         })->get();

   		 return view('backend.sellers.sms_requests' ,compact('shops') );
	}
  
  public function getShopRequest(Request $request)
  {
    
  	$shopRequests = SmsRequest::where('shop_id', $request->shop_id)
    ->where('status','pending')  
    ->get()
    ->map(function ($req) {
        return [
            "id" => $req->id,
            "phone" => $req->phone,
            "message" => $req->message,
            "status" => $req->status,
        ];
    });
    return response()->json(['status'=>"success" , "requests"=>$shopRequests ]);
  }
  
  public function updateStatus(Request $request)
  {
    
    $sms= SmsRequest::find($request->request_id);
    
    if($request->status == "approved")
    {
  
      (new SendSmsService())->sendSMS($sms->phone, env('APP_NAME'), $sms->message,7);
      
      $sms->update(['status'=>"sent"]);
      
    }
   	else
    {
    	$sms->update(['status'=>"rejected"]);
     
    }
    
    return response()->json(["status"=>"success",'shop_id'=>$sms->shop_id]);
    
    
  }
  
  
  public function sendAll(Request $request)
    
  {
    try {
    $requests = SmsRequest::where('shop_id', $request->shop_id)
                ->where('status', 'pending')
                ->get();

    foreach ($requests as $req) {
        (new SendSmsService())->sendSMS(
            $req->phone,
            env('APP_NAME'),
            $req->message,
            7
        );

        $req->update(['status' => 'sent']);
    }

    return response()->json(["status" => "success"]);
} catch (Throwable $e) {
    Log::error('SMS sending failed', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ]);

    return response()->json([
        "status" => "error",
        "message" => "An error occurred while sending SMS."
    ], 500);
}
  
}
  public function downloadFile()
{
    $path = 'downloads/sms_requests.xlsx';

    if (!Storage::disk('public')->exists($path)) {
        return abort(404, 'File not found.');
    }

    return Storage::disk('public')->download($path);
}
}