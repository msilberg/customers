<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 12/23/13
 * Time: 12:11 PM
 */

namespace Customers\Entity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="t_customer")
 */
class Customer {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @ORM\OneToMany(targetEntity="Calls", mappedBy="customer", cascade={"remove"})
     */
    protected $id;

    /** @ORM\Column(type="string") */
    protected $firstName;

    /**
     * @ORM\Column(type="string")
     */
    protected $lastName;

    /** @ORM\Column(type="string") */
    protected $phone;

    /** @ORM\Column(type="string", nullable=true) */
    protected $address;

    /** @ORM\Column(type="string", nullable=true) */
    protected $status;

    public function __construct()
    {
        $this->id = new ArrayCollection();
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
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
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

}