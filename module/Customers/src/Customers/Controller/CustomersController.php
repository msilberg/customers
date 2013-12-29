<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 12/24/13
 * Time: 9:12 PM
 */

namespace Customers\Controller;

use Zend\Mvc\Controller\AbstractActionController,
    Zend\View\Model\ViewModel,
    Zend\View\Model\JsonModel,
    Doctrine\ORM\EntityManager,
    Customers\Entity\Customer,
    Customers\Entity\Calls;

require_once("firephp/firephp.php");

class CustomersController extends AbstractActionController{

    protected $routeMatch;

    /**
     * @var array
     */
    protected $cst_arr = array();

    /**
     * @var array
     */
    private $cst_arr_keys = array('id','cname','phone','address','subject','content');

    /**
     * @var int
     */
    protected $cst_arr_ind = 0;

    /**
     * @var array
     */
    protected $view_add;

    /**
     * @var array
     */
    protected $view_edit;

    /**
     * @var array
     */
    protected $data_edit;

    /**
     * @var array
     */
    protected $customers_arr;

    /**
     * @var array
     */
    protected $customers_full_names;

    /**
     * @var array
     */
    protected $calls_arr;

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @param EntityManager $em
     */
    protected function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return array|Doctrine\ORM\EntityManager|object
     */
    protected function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        }
        return $this->em;
    }

    /**
     * @return array
     */
    public function getCustomersArr()
    {
        $this->customers_arr = $this->getEntityManager()->getRepository('Customers\Entity\Customer')->findAll();
        return $this->customers_arr;
    }

    /**
     * @return array
     */
    public function getCallsArr()
    {
        $this->calls_arr = $this->getEntityManager()->createQuery('SELECT b,s FROM Customers\Entity\Calls b JOIN b.customer s')->getResult();
        return $this->calls_arr;
    }

    /**
     * @param null $customer
     * @return array|string
     */
    public function getCustomersFullNames($customer=null)
    {
        if (isset($customer)) return $customer->getFirstName()." ".$customer->getLastName();
        foreach ($this->getCustomersArr() as $customer){
            $this->customers_full_names[$customer->getId()] = $customer->getFirstName()." ".$customer->getLastName();
        }
        return $this->customers_full_names;
    }

    /**
     * @return array
     */
    protected function getCTable()
    {
        foreach ($this->getCustomersArr() as $customer){
            $cst_ext = false; // customer exists in calls
            foreach ($this->getCallsArr() as $call){
                if ($customer->getId() == $call->getCustomer()->getId()){
                    $this->cst_arr[] = array_combine($this->cst_arr_keys, array(
                        ++$this->cst_arr_ind,
                        $this->getCustomersFullNames($customer),
                        $customer->getPhone(),
                        $customer->getAddress(),
                        $call->getSubject(),
                        $call->getContent()
                    ));
                    $cst_ext = true;
                }
            }
            if (!$cst_ext)
                $this->cst_arr[] = array_combine($this->cst_arr_keys, array(
                    ++$this->cst_arr_ind,
                    $this->getCustomersFullNames($customer),
                    '-','-','-','-'
                ));
        }
        return $this->cst_arr;
    }

    /**
     * @param $data
     * @param $obj
     */
    protected function extract_customer_data($data,&$obj){
        foreach ($data as $val){
            if (empty($val['value'])) continue;
            switch ($val['name']){
                case 'id': $obj->setId(intval($val['value']));
                    break;
                case 'fname': $obj->setFirstName($val['value']);
                    break;
                case 'lname': $obj->setLastName($val['value']);
                    break;
                case 'phone': $obj->setPhone($val['value']);
                    break;
                case 'addr': $obj->setAddress($val['value']);
                    break;
                case 'status': $obj->setStatus($val['value']);
                    break;
                default: continue;
            }
        }
    }

    /**
     * @param $data
     * @param $obj
     * @param null $om
     */
    protected function extract_call_data($data,&$obj,$om=null){
        foreach ($data as $val){
            if (empty($val['value'])) continue;
            switch ($val['name']){
                case 'id': $obj->setId(intval($val['value']));
                    break;
                case 'call-subj': $obj->setSubject($val['value']);
                    break;
                case 'call-cont': $obj->setContent($val['value']);
                    break;
                case 'cname-select':
                    if (!isset($om)) continue;
                    $obj->setCustomer($om->getReference('Customers\Entity\Customer', $val['value']));
                    break;
                default: continue;
            }
        }
    }

    public function indexAction()
    {
        return new ViewModel(array(
            'customer' => $this->getCTable()
        ));
    }

    public function addAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()){
            $add_customer = $request->getPost('addcustomer');
            $add_calls = $request->getPost('addcalls');
            $objectManager = $this->getEntityManager();
            if (isset($add_customer)){
                $customer = new Customer();
                $this->extract_customer_data($add_customer,$customer);
                $objectManager->persist($customer);
                $objectManager->flush();
            }elseif (isset($add_calls)){
                $calls = new Calls();
                $this->extract_call_data($add_calls,$calls,$objectManager);
                $objectManager->persist($calls);
                $objectManager->flush();
            }
        }else{
            switch ($request->getUri()->getQuery()){
                case 'call':
                    $this->view_add = array(
                        'add_type' => 2,
                        'title' => 'Add new Call',
                        'customers_names' => $this->getCustomersFullNames(),
                        'cbtn_class' => 'add-call'
                    );
                    break;
                default:
                    $this->view_add = array(
                        'add_type' => 1,
                        'title' => 'Add new Customer',
                        'cbtn_class' => 'add-customer'
                    );
            }
            return new ViewModel($this->view_add);
        }
    }

    public function editAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()){
            $edit_customer = $request->getPost('editcustomer');
            $edit_call = $request->getPost('editcall');
            $save_customer = $request->getPost('savecustomer');
            $save_call = $request->getPost('savecall');
            $get_calls = $request->getPost('getcalls');
            if (isset($edit_customer)){
                $customer = current($this->getEntityManager()->createQuery(
                    sprintf('SELECT a FROM Customers\Entity\Customer a WHERE a.id=%d', intval($edit_customer))
                )->getResult());
                $this->data_edit = array(
                    'customer' => array(
                        'id' => $customer->getId(),
                        'fname' => $customer->getFirstName(),
                        'lname' => $customer->getLastName(),
                        'phone' => $customer->getPhone(),
                        'addr' => $customer->getAddress(),
                        'status' => $customer->getStatus()
                    )
                );
            }elseif (isset($save_customer)){
                $customer = new Customer();
                $this->extract_customer_data($save_customer,$customer);
                $q = $this->getEntityManager()->createQuery(
                    sprintf(
                        "UPDATE Customers\Entity\Customer a
                         SET a.firstName='%s',
                             a.lastName='%s',
                             a.phone='%s',
                             a.address='%s',
                             a.status='%s'
                         WHERE a.id=%d",
                        $customer->getFirstName(),
                        $customer->getLastName(),
                        $customer->getPhone(),
                        $customer->getAddress(),
                        $customer->getStatus(),
                        $customer->getId()
                    )
                );
                $q->execute();
            }elseif (isset($get_calls)){
                $q = $this->getEntityManager()->createQuery(
                    sprintf('SELECT b FROM Customers\Entity\Calls b WHERE b.customer=%d', intval($get_calls))
                )->getResult();
                if (count($q) == 0)
                    $this->data_edit = array('empty');
                else
                    foreach ($q as $val){
                        $this->data_edit[] = array(
                            'id' => $val->getId(),
                            'call-subj' => $val->getSubject()
                        );
                    }
            }elseif (isset($edit_call)){
                $call = current($this->getEntityManager()->createQuery(
                    sprintf('SELECT b FROM Customers\Entity\Calls b WHERE b.id=%d', intval($edit_call))
                )->getResult());
                $this->data_edit = array(
                    'id' => $call->getId(),
                    'call-subj' => $call->getSubject(),
                    'call-cont' => $call->getContent()
                );
            }elseif (isset($save_call)){
                $calls = new Calls();
                $this->extract_call_data($save_call,$calls);
                $q = $this->getEntityManager()->createQuery(
                    sprintf(
                        "UPDATE Customers\Entity\Calls b
                         SET b.subject='%s',
                             b.content='%s'
                         WHERE b.id=%d",
                        $calls->getSubject(),
                        $calls->getContent(),
                        $calls->getId()
                    )
                );
                $q->execute();
            }
            return new JsonModel($this->data_edit);
        }else{
            runFireBug('two');
            switch ($request->getUri()->getQuery()){
                case 'call':
                    $this->view_edit = array(
                        'edit_type' => 2,
                        'title' => 'Edit Calls',
                        'customers_names' => $this->getCustomersFullNames(),
                        'save_btn_class' => 'save-edit-call',
                        'type_name' => 'calls'
                    );
                    break;
                default:
                    $this->view_edit = array(
                        'edit_type' => 1,
                        'title' => 'Edit Customers',
                        'customers_arr' => $this->getCustomersArr(),
                        'save_btn_class' => 'save-edit-customer',
                        'type_name' => 'customers'
                    );
            }
            return new ViewModel($this->view_edit);
        }
    }

    public function deleteAction()
    {
    }

    public function rssAction()
    {
    }
}