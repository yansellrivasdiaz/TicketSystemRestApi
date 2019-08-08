<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Groups;
/**
 * Note
 *
 * @ORM\Table(name="note")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NoteRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Note
{
    /**
     * @var int
     * @Groups({"ticket_view","note_view"})
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Groups({"ticket_view","note_view"})
     * @ORM\Column(name="note", type="string", length=10000)
     */
    private $note;
    /**
     * @var \DateTime
     * @Groups({"ticket_view","note_view"})
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;
    /**
     * @ORM\ManyToOne(targetEntity="User",inversedBy="notes")
     * @Groups({"ticket_view","note_view"})
     * @ORM\JoinColumn(name="user_id",referencedColumnName="id")
     */
    private $userId;
    /**
     * @var Ticket
     * @ORM\ManyToOne(targetEntity="Ticket",inversedBy="notes")
     * @ORM\JoinColumn(name="ticket_id", referencedColumnName="id", onDelete="cascade")
     */
    private $ticketId;
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
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }
    /**
     * @param string $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }
    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
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
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }
    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }
    /**
     * @return ticket
     */
    public function getTicketId()
    {
        return $this->ticketId;
    }
    /**
     * @param ticket $ticketId
     */
    public function setTicketId($ticketId)
    {
        $this->ticketId = $ticketId;
    }
}