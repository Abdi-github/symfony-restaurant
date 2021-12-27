<?php

namespace App\Entity;

use App\Repository\OrderItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=OrderItemRepository::class)
 */
class OrderItem
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;



    /**
     * @ORM\Column(type="string")
     */
    private $product_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $qty;

    /**
     * @ORM\Column(type="float")
     */
    private $price;



    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $created_at;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    private $ex_cheese;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    private $ex_onion;



    /**
     * @ORM\Column(type="string")
     */
    private $customer_id;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="order_items")
     */
    private $orders;

    /**
     * @ORM\Column(type="string", length=45)
     */
    private $name;








    public function getId(): ?int
    {
        return $this->id;
    }



    public function getProductId(): ?string
    {
        return $this->product_id;
    }

    public function setProductId(string $product_id): self
    {
        $this->product_id = $product_id;

        return $this;
    }

    public function getQty(): ?int
    {
        return $this->qty;
    }

    public function setQty(int $qty): self
    {
        $this->qty = $qty;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }



    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getExCheese(): ?string
    {
        return $this->ex_cheese;
    }

    public function setExCheese(?string $ex_cheese): self
    {
        $this->ex_cheese = $ex_cheese;

        return $this;
    }

    public function getExOnion(): ?string
    {
        return $this->ex_onion;
    }

    public function setExOnion(?string $ex_onion): self
    {
        $this->ex_onion = $ex_onion;

        return $this;
    }



    public function getCustomerId(): ?string
    {
        return $this->customer_id;
    }

    public function setCustomerId(string $customer_id): self
    {
        $this->customer_id = $customer_id;

        return $this;
    }

    public function getOrders(): ?Order
    {
        return $this->orders;
    }

    public function setOrders(?Order $orders): self
    {
        $this->orders = $orders;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
