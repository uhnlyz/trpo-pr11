<?php
declare(strict_types=1);
final class OrderValidator
{
    public function validate(array $input, array $deliveryMethods): array
    {
        $errors=[];
        $name=trim((string)($input['name']??''));
        $email=trim((string)($input['email']??''));
        $address=trim((string)($input['address']??''));
        $delivery=(string)($input['delivery']??'');
        if($name==='')$errors['name']='Укажите имя.';
        if(!filter_var($email,FILTER_VALIDATE_EMAIL))$errors['email']='Укажите корректный email.';
        if($address==='')$errors['address']='Укажите адрес или пункт получения.';
        $known=false;foreach($deliveryMethods as $method)if(($method['id']??'')===$delivery)$known=true;
        if(!$known)$errors['delivery']='Выберите доступный способ доставки.';
        return $errors;
    }
}
