<?php
declare(strict_types=1);
final class CheckoutController
{
    public function __construct(private CartService $cart, private OrderValidator $validator, private JsonOrderRepository $orders, private array $deliveryMethods) {}
    public function form(): array
    {
        if($this->cart->lines()===[])return ['title'=>'Корзина пуста','empty'=>true];
        return ['title'=>'Оформление заказа','empty'=>false,'deliveryMethods'=>$this->deliveryMethods,'errors'=>$_SESSION['checkout_errors']??[],'old'=>$_SESSION['checkout_old']??[],'lines'=>$this->cart->lines(),'total'=>$this->cart->total()];
    }
    public function submit(): void
    {
        $input=['name'=>trim((string)($_POST['name']??'')),'email'=>trim((string)($_POST['email']??'')),'address'=>trim((string)($_POST['address']??'')),'delivery'=>(string)($_POST['delivery']??'')];
        $errors=$this->validator->validate($input,$this->deliveryMethods);
        if($errors!==[]||$this->cart->lines()===[]){$_SESSION['checkout_errors']=$errors;$_SESSION['checkout_old']=$input;header('Location: /checkout');exit;}
        $order=new OrderData($input['name'],$input['email'],$input['address'],$input['delivery'],array_map(static fn($line)=>['product_id'=>$line['product']->id,'name'=>$line['product']->name,'price'=>$line['product']->price,'quantity'=>$line['quantity']],$this->cart->lines()),$this->cart->total());
        $id=$this->orders->create($order);unset($_SESSION['cart'],$_SESSION['checkout_errors'],$_SESSION['checkout_old']);
        header('Location: /order-success?id='.$id);exit;
    }
    public function success(): array
    {
        $id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);$order=$id?$this->orders->find((int)$id):null;
        if($order===null)http_response_code(404);
        return ['title'=>'Заказ оформлен','order'=>$order];
    }
}
