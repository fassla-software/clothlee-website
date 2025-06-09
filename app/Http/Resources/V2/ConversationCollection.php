<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ConversationCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
'data' => $this->collection->filter()->values()->map(function($data, $index) {
    $shopName = auth()->id() == $data->sender_id
        ? (optional($data->receiver)->user_type == 'admin'
            ? 'Clothlee'
            : (optional($data->receiver->shop)->name ?? optional($data->receiver)->name ?? ''))
        : (optional($data->sender)->user_type == 'admin'
            ? 'Clothlee'
            : (optional($data->sender->shop)->name ?? optional($data->sender)->name ?? ''));

    if ($index === 0 && $data->sender_id == 9) {
        $shopName = 'Chat with Clothlee';
    }
                return [
                    'id' => $data->id,
                    'receiver_id' => intval($data->receiver_id),
                    'receiver_type' => optional($data->receiver)->user_type ?? '',
                    'shop_id' => optional($data->receiver)->user_type == 'admin' ? 0 : optional($data->receiver->shop)->id,
                  'shop_name' => $shopName,

                 'shop_logo' => optional($data->receiver)->user_type == 'admin'
    ? (uploaded_asset(get_setting('header_logo')) ?: 'https://www.clothlee.com/public/uploads/all/BFasWJJukRLV4AOPQgw8FKqktAHzZ7ws4D2ZpDlM.png')
    : (
        optional($data->receiver->shop) && !empty(optional($data->receiver->shop)->logo)
            ? asset('public/uploads/all/' . optional($data->receiver->shop)->logo)
            : 'https://www.clothlee.com/public/uploads/all/BFasWJJukRLV4AOPQgw8FKqktAHzZ7ws4D2ZpDlM.png'
    ),


                    'title' => $data->title,
                    'sender_viewed' => intval($data->sender_viewed),
                    'receiver_viewed' => intval($data->receiver_viewed),
                    'date' => $data->updated_at,
       'product_image' => $data->product ? uploaded_asset($data->product->thumbnail_img) : null,

                ];
            })->values()
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
