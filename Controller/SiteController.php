<?php

namespace Site\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Bundles\StoreBundle\Entity\Product;
use Bundles\StoreBundle\Entity\Comment;
use Bundles\StoreBundle\Entity\Orders;
use Bundles\StoreBundle\Entity\Prodtoorder;
use Bundles\StoreBundle\Entity\Brend;
use Bundles\StoreBundle\Entity\Category;
use Symfony\Component\HttpFoundation\Request;

class SiteController extends Controller
{
    public function mainAction()
    {
        $category = $this->getDoctrine()->getRepository('BundlesStoreBundle:Category')->findByParent(null);
        return $this->render('SiteShopBundle:Site:main.html.twig', array('category'=>$category));
    }
    
    public function razdelAction($slug)
    {
        $category = $this->getDoctrine()->getRepository('BundlesStoreBundle:Category')->findOneBySlug($slug);
        return $this->render('SiteShopBundle:Site:razdel.html.twig', array('category'=>$category));
    }
    
    
    
    public function categoryAction($slug)
    {
        $category = $this->getDoctrine()->getRepository('BundlesStoreBundle:Category')->findOneBySlug($slug);
        return $this->render('SiteShopBundle:Site:category.html.twig', array('category'=>$category));
    }
    
    public function productAction($id)
    {
        $req = $this->getRequest();
        $message = null;
        
        
        if($req->get('comment') && $req->get('submit')){
        $comment = new Comment;
        $comment->setComment($req->get('comment'));
        
        $product = $this->getDoctrine()->getRepository('BundlesStoreBundle:Product')->findOneById($id);
        
        $comment->setProduct($product);
        
        
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($comment);
        $em->flush();
        $message = "Ваш комментарий добавлен";
        
        }
        
        $product = $this->getDoctrine()->getRepository('BundlesStoreBundle:Product')->findOneById($id);
        $foto = $product->getPhoto();
        return $this->render('SiteShopBundle:Site:product.html.twig', array('product'=>$product, 'message'=>$message));
    }
    
    public function productsAction()
    {
        return $this->render('SiteShopBundle:Site:products.html.twig');
    }
    
    public function cartAction(){
        $request = Request::createFromGlobals();
        $sess = $this->get('session');
        $cart = $sess->get('cart_prod');
        //dump($cart); видны все
        $products = $this->getDoctrine()->getRepository('BundlesStoreBundle:Product')->findById($cart);
        
        return $this->render('SiteShopBundle:Site:cart.html.twig', array('products'=>$products));
    }
    
    public function cartOrderAction(){
        $req=$this->getRequest();
        $cartProductsId= $req->get('cartProductsId');
        $order = new Orders;
        $user = $this->getDoctrine()->getRepository('BundlesStoreBundle:User')->findOneByName('Павел');
        $order->setUser($user);
        $order->setDate(new \DateTime());
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($order);
        $em->flush();
        
        foreach ($cartProductsId as $key=>$val){
            
            $product = $this->getDoctrine()->getRepository('BundlesStoreBundle:Product')->findOneById($val);
            $prodtoorder = new Prodtoorder;
            
            $prodtoorder->setQuantity("1");
            $prodtoorder->setPrice($product->getPrice());
            $prodtoorder->setProduct($product);
            $prodtoorder->setOrders($order);
           
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($prodtoorder);
            $em->flush();
        }
        
        
        
        return $this->render('SiteShopBundle:Site:cartOrder.html.twig', ['message'=>'Заказ успешно оформлен']);
    }
    
    public function parametersAction()
    {
        $product = $this->getDoctrine()->getRepository('BundlesStoreBundle:Product')->findOneById("4");
        
        return $this->render('SiteShopBundle:Site:parameters.html.twig', array('product'=>$product));
    }
    
    public function newprodAction($slug)
    {
        
        
        return $this->render('SiteShopBundle:Site:newprod.html.twig', array('category_slug'=>$slug));
    }
    
    public function addprodAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $req = $this->getRequest();
        $title = $req->get('title');
        $description = $req->get('description');
        $price = $req->get('price');
        $brend = $req->get('brend');
        $category_slug = $req->get('category_slug');
        $brendDb = $this->getDoctrine()->getRepository('BundlesStoreBundle:Brend')->findOneByName($brend);
        if(!$brendDb){
            $brendDb = new Brend;
            $brendDb->setName($brend);
            
            $em->persist($brendDb);
            $em->flush();
        }
        $category = $this->getDoctrine()->getRepository('BundlesStoreBundle:Category')->findOneBySlug($category_slug);
        $product = new Product;
        $product->setTitle($title);
        $product->setDescription($description);
        $product->setPrice($price);
        $product->setBrend($brendDb);
        $product->setCategory($category);
        $em->persist($product);
        $em->flush();
        $products = $this->getDoctrine()->getRepository('BundlesStoreBundle:Product')->findByCategory($category);
        $message = 'Товар успешно добавлен';
        return $this->render('SiteShopBundle:Site:addprod.html.twig', array('message'=>$message, 'products'=>$products ));
    }
    
    public function editprodAction($id)
    {
        $product = $this->getDoctrine()->getRepository('BundlesStoreBundle:Product')->findOneById($id);
        return $this->render('SiteShopBundle:Site:editprod.html.twig', array('product'=>$product));
    }
    
     public function saveprodAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $req = $this->getRequest();
        $title = $req->get('title');
        $description = $req->get('description');
        $price = $req->get('price');
        $brend = $req->get('brend');
        
        $product = $this->getDoctrine()->getRepository('BundlesStoreBundle:Product')->findOneById($id);
        $brendDb = $this->getDoctrine()->getRepository('BundlesStoreBundle:Brend')->findOneByName($brend);
        if(!$brendDb){
            $brendDb = new Brend;
            $brendDb->setName($brend);
            $em->persist($brendDb);
            $em->flush();
        }
                
        $product->setTitle($title);
        $product->setDescription($description);
        $product->setPrice($price);
        $product->setBrend($brendDb);
        
        
        $em->flush();
        return $this->render('SiteShopBundle:Site:saveprod.html.twig', array('product'=>$product));
    }
}
