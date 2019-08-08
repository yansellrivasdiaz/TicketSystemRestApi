<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Groups;

/**
 * Ticket
 *
 * @ORM\Table(name="ticket")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TicketRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Ticket
{
    /**
     * @var int
     * @Groups({"list_ticket","ticket_view","ticket_report"})
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Groups({"list_ticket","ticket_view","ticket_report"})
     * @Assert\NotBlank()
     * @ORM\Column(name="subject", type="string", length=255)
     */
    private $subject;

    /**
     * @var \DateTime
     * @Groups({"list_ticket","ticket_view","ticket_report"})
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Groups({"list_ticket","ticket_view","ticket_report"})
     * @ORM\Column(name="description", type="string", length=10000)
     */
    private $description;

    /**
     * @var string
     * @Groups({"list_ticket","ticket_view"})
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var \DateTime
     * @Groups({"ticket_view","ticket_report"})
     * @ORM\Column(name="ended_at", type="datetime",nullable=true)
     */
    private $endedAt;

    /**
     * @var int
     * @Groups({"list_ticket","ticket_view","ticket_report"})
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="User",inversedBy="usertickets")
     * @ORM\JoinColumn(name="user_id",referencedColumnName="id", onDelete="cascade")
     */
    private $userId;

    /**
     * @Groups({"ticket_view","ticket_report"})
     * @Assert\NotBlank()
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User",inversedBy="tickets", cascade={"persist","merge"})
     * @ORM\JoinTable(name="employee_ticket",
     *     joinColumns={@ORM\JoinColumn(name="employee_id",referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="ticket_id", referencedColumnName="id")})
     */
    private $employees;

    /**
     * @Groups({"ticket_view"})
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Note", mappedBy="ticketId", cascade={"persist","remove"})
     */
    private $notes;

    /**
     * @var float
     * @Groups({"ticket_report","ticket_view"})
     * @ORM\Column(name="timehours", type="float", nullable=true)
     */
    private $timehours;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->employees = new ArrayCollection();
        $this->status = "Open";
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set subject
     *
     * @param string $subject
     *
     * @return Ticket
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set createdAt
     *
     * @return User
     * @throws \Exception
     * @ORM\PrePersist
     */
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime();
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Ticket
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Ticket
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set endedAt
     *
     * @param \DateTime $endedAt
     *
     * @return Ticket
     */
    public function setEndedAt($endedAt)
    {
        $this->endedAt = $endedAt;

        return $this;
    }

    /**
     * Get endedAt
     *
     * @return \DateTime
     */
    public function getEndedAt()
    {
        return $this->endedAt;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return Ticket
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return mixed
     */
    public function getEmployees()
    {
        return $this->employees;
    }

    /**
     * @param mixed $employees
     */
    public function setEmployees($employees)
    {
        $this->employees = $employees;
    }

    /**
     * Add employee
     *
     * @param \AppBundle\Entity\User $employee
     *
     * @return ticket
     */
    public function addEmployee(\AppBundle\Entity\User $employee)
    {
        $this->employees[] = $employee;
        return $this;
    }
    /**
     * Remove employee
     *
     * @param \AppBundle\Entity\User $employee
     */
    public function removeEmployee(\AppBundle\Entity\User $employee)
    {
        $this->employees->removeElement($employee);
    }

    /**
     * @return mixed
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param mixed $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    /**
     * @return float
     */
    public function getTimehours()
    {
        return $this->timehours;
    }

    /**
     * @param float $timehours
     */
    public function setTimehours($timehours)
    {
        $this->timehours = $timehours;
    }


}

