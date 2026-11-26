<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\OrderData;
final class JsonOrderRepository
{
    public function __construct(private string $file) {}
    public function create(OrderData $order): int
    {
        $orders=json_decode((string)file_get_contents($this->file),true,512,JSON_THROW_ON_ERROR);
        $id=1;foreach($orders as $row)$id=max($id,(int)($row['id']??0)+1);
        $orders[]=['id'=>$id,'name'=>$order->name,'email'=>$order->email,'address'=>$order->address,'delivery_id'=>$order->deliveryId,'items'=>$order->items,'total'=>$order->total,'created_at'=>date(DATE_ATOM)];
        $json=json_encode($orders,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR);
        if(file_put_contents($this->file,$json.PHP_EOL,LOCK_EX)===false)throw new RuntimeException('Не удалось сохранить заказ.');
        return $id;
    }
    public function find(int $id): ?array
    {
        $orders=json_decode((string)file_get_contents($this->file),true,512,JSON_THROW_ON_ERROR);
        foreach($orders as $order)if((int)$order['id']===$id)return $order;
        return null;
    }
}
