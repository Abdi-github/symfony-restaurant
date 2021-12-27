<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;





    /**
     * @ORM\Column(type="float")
     */
    private $price;


    /**
     * @ORM\Column(type="string")
     */
    private $payment_intent_id;


    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $created_at;
    /**
     * 
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updated_at;




    /**
     * @ORM\Column(type="string", length=45, options={"default":"pending"})
     */
    private $payment_status;

    /**
     * @ORM\Column(type="string", length=45)
     */
    private $order_no;



    /**
     * @ORM\Column(type="string", length=45)
     */
    private $table_no;

    /**
     * @ORM\OneToMany(targetEntity=OrderItem::class, mappedBy="orders")
     */
    private $order_items;






    public function __construct()
    {
        $this->order_items = new ArrayCollection();
        $this->items = new ArrayCollection();
    }




    public function getId(): ?int
    {
        return $this->id;
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

    public function getPaymentIntentId(): ?string
    {
        return $this->payment_intent_id;
    }

    public function setPaymentIntentId(string $payment_intent_id): self
    {
        $this->payment_intent_id = $payment_intent_id;

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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }






    public function getPaymentStatus(): ?string
    {
        return $this->payment_status;
    }

    public function setPaymentStatus(string $payment_status): self
    {
        $this->payment_status = $payment_status;

        return $this;
    }

    public function getOrderNo(): ?string
    {
        return $this->order_no;
    }

    public function setOrderNo(string $order_no): self
    {
        $this->order_no = $order_no;

        return $this;
    }



    public function getTableNo(): ?string
    {
        return $this->table_no;
    }

    public function setTableNo(string $table_no): self
    {
        $this->table_no = $table_no;

        return $this;
    }

    /**
     * @return Collection|OrderItem[]
     */
    public function getOrderItems(): Collection
    {
        return $this->order_items;
    }

    public function addOrderItem(OrderItem $orderItem): self
    {
        if (!$this->order_items->contains($orderItem)) {
            $this->order_items[] = $orderItem;
            $orderItem->setOrders($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): self
    {
        if ($this->order_items->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getOrders() === $this) {
                $orderItem->setOrders(null);
            }
        }

        return $this;
    }
}
