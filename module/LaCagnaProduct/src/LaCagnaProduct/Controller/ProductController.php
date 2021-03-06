<?php
/**
* Zend Framework (http://framework.zend.com/)
*
* @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
* @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
* @license   http://framework.zend.com/license/new-bsd New BSD License
*/

namespace LaCagnaProduct\Controller;
use Zend\Mvc\Controller\AbstractActionController;;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;


class ProductController extends AbstractActionController
{
    protected $em;

    public function bytypeAction()
    {
        $type = $this->params('type', false);
        $productsListing = $this->getServiceLocator()->get('ProductsListing');
        $products = $productsListing->byType($type);
        return array('products' => $products);
    }

    public function bycategoryslugAction()
    {
        $slug = $this->params('slug', false);
        $productsListing = $this->getServiceLocator()->get('ProductsListing');
        $products = $productsListing->byCategorySlug($slug);
        return array('products' => $products);
    }
    public function bycategorycodeAction()
    {
        $code = $this->params('code', false);
        $productsListing = $this->getServiceLocator()->get('ProductsListing');
        $products = $productsListing->byCategoryCode($code);

        $productManager = $this->getServiceLocator()->get('ProductManager');
        $categories     = $productManager->Categories();
        /*$category = $categories->byCategoryCode($code);
        $children = $category->getChildren();*/

        return array(
                    'products' => $products,
                /*'children' => $children*/);
    }
    public function indexAction()
    {
        $id   = $this->params()->fromPost('id', FALSE);
        $name = $this->params('name', FALSE);
        $cocktailFactory = $this->getServiceLocator()->get('cocktail');
        $cocktail = $cocktailFactory->get($name);
        return array('cocktail' => $cocktail);
    }
    public function addAction()
    {
        if (!$this->isAllowed('product', 'add')) {
            throw new \BjyAuthorize\Exception\UnAuthorizedException('Grow a beard first!');
        }
        /**
        * get data from our form
        */
        $id          = $this->params()->fromPost('id', FALSE);
        $name        = $this->params()->fromPost('name', FALSE);
        $code        = $this->params()->fromPost('code', FALSE);
        $type        = $this->params()->fromPost('type', FALSE);

        if(!$code)
            $code = preg_replace("/[^A-Za-z0-9]/", "", $name);

        $ProductManager = $this->getServiceLocator()->get('ProductManager');
        $ProductsListing = $this->getServiceLocator()->get('ProductsListing');
        $Categories = $this->getServiceLocator()->get('Categories');

        $cocktailRepository = $this->getEntityManager()
                                ->getRepository('LaCagnaProduct\Entity\Product');
        $typeRepository = $this->getEntityManager()
                                ->getRepository('LaCagnaProduct\Entity\Type');
        // initialize view result
        $viewElements = array();

        $viewElements['types'] = $ProductsListing->getTypes();
        $viewElements['categories'] = $Categories->getList();
        $viewElements['ingredients'] = $ProductManager->Ingredients()->getList();



        //echo '<pre>';\Doctrine\Common\Util\Debug::dump($ProductManager->getIngedients()->getList());die();
        // in case we receive something from form
        if($this->request->isPost() && $name)
        {

            $cocktail = $cocktailRepository->findOneByTitle($name);

            // We cannot find catalog: create a new one
            if(!$cocktail)
            {
                $cocktail = new \LaCagnaProduct\Entity\Product();
            }

            // Change properties
            $cocktail->setTitle($name);


            $cocktail->setCode($code);
            //$cocktail->setVersion($catalogversion);
            $type = $typeRepository->find($type);
            $cocktail->setType($type);
            // apply modifications
            $this->getEntityManager()->persist($cocktail);

            // save data to database
            $this->getEntityManager()->flush();

            // assign result to view
            $viewElements['cocktail'] = $cocktail;
        }

        return new ViewModel($viewElements);
    }

    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        }
        return $this->em;
    }
}
