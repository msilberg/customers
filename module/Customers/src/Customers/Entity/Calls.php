<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 12/23/13
 * Time: 12:19 PM
 */

namespace Customers\Entity;

use Doctrine\ORM\Mapping as ORM,
    InvalidArgumentException;

/**
 * @ORM\Entity
 * @ORM\Table(name="t_calls")
 */
class Calls {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Customer")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     */
    protected $customer;

    /** @ORM\Column(type="string") */
    protected $subject;

    /** @ORM\Column(type="string") */
    protected $content;

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @param mixed $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @param $customer
     */
    public function setCustomer($customer)
    {
        if($customer === null){
            if($this->customer !== null) {
                $this->customer->getId()->removeElement($this);
            }
            $this->customer = null;
        }else{
            if($this->customer !== null) {
                $this->customer->getId()->removeElement($this);
            }
            $this->customer = $customer;
        }
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->customer;
    }

}