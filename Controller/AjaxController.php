<?php

namespace Site\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AjaxController extends Controller
{
    
       /**
     * @Route("/ajax_add_to_cart", name="ajax_add_to_cart")
     */
    public function addToCartAction()
    {
        $request = Request::createFromGlobals();
        if($request->get('id')){
            $sess = $this->get('session');
            $sess->start();   
            $cart = $sess->get('cart_prod');
            
            //if(!@in_array($request->get('id') ,$cart)){
                $cart[] = $request->get('id');
                $sess->set('cart_prod' , $cart );
            //}
                
        }
        
        return new JsonResponse(
                [
                
                'error' => false,
                'message' => 'Успешно добавлено!'
            ]);
        
    }  
    
    
    
}
