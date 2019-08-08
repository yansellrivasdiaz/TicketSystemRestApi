<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation\Groups;
/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(
 * fields={"email"},
 * errorPath="username",
 * message="Username or Email already taken by other user!!"
 *)
 */
class User implements AdvancedUserInterface, \Serializable
{
    /**
     * @var int
     * @Groups({"list_employee","employee_view","list_ticket","ticket_view","note_view","ticket_report","authenticated_user_info"})
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var string
     * @Groups({"list_employee","employee_view","list_ticket","ticket_view","note_view","ticket_report","authenticated_user_info"})
     * @Assert\NotBlank()
     * @ORM\Column(name="firstname", type="string", length=255)
     */
    private $firstname;
    /**
     * @var string
     * @Groups({"list_employee","employee_view","list_ticket","ticket_view","note_view","ticket_report","authenticated_user_info"})
     * @ORM\Column(name="lastname", type="string", length=255, nullable=true)
     */
    private $lastname;

    /**
     * @var string
     * @Groups({"list_employee","employee_view","list_ticket","ticket_view","note_view","ticket_report","authenticated_user_info"})
     * @Assert\NotBlank()
     * @Assert\Email()
     * @ORM\Column(name="email", type="string", length=191, unique=true)
     */
    private $email;
    /**
     * @var string
     * @Assert\Expression(
     *     "not (this.getId() == null and this.getPassword() == null)",
     *     message="This value should not be blank."
     * )
     * @ORM\Column(name="password", type="string", length=512)
     */
    private $password;
    /**
     *
     * @Assert\Length(max=4096)
     */
    private $plainPassword;
    /**
     * @var \DateTime
     * @Groups({"list_employee","employee_view"})
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     *
     * @ORM\OneToMany(targetEntity="Ticket", mappedBy="userId", cascade={"persist","remove"})
     */
    private $usertickets;
    /**
     * @Groups({"employee_view"})
     * @ORM\ManyToMany(targetEntity="Ticket", mappedBy="employees")
     */
    private $tickets;
    /**
     * @ORM\OneToMany(targetEntity="Note", mappedBy="userId", cascade={"persist","remove"})
     */
    private $notes;

    /**
     * @Groups({"list_employee"})
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->isActive = true;
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
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
        return $this;
    }
    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }
    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
        return $this;
    }
    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }
    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }
    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
    /**
     * Set createdAt
     *
     * @ORM\PrePersist
     * @return User
     * @throws \Exception
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
     * @return mixed
     */
    public function getTickets()
    {
        return $this->tickets;
    }
    /**
     * @param mixed $tickets
     */
    public function setTickets($tickets)
    {
        $this->tickets = $tickets;
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
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }
    /**
     * @param mixed $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }
    /**
     * @return mixed
     */
    public function getUsertickets()
    {
        return $this->usertickets;
    }
    /**
     * @param mixed $usertickets
     */
    public function setUsertickets($usertickets)
    {
        $this->usertickets = $usertickets;
    }
    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }
    /**
     * String representation of object
     * @link https://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->firstname,
            $this->lastname,
            $this->email
        ]);
    }
    /**
     * Constructs the object
     * @link https://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->firstname,
            $this->lastname,
            $this->email
            ) = unserialize($serialized, ['allowed_classes' => false]);
    }
    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        // TODO: Implement getRoles() method.
        return array ("ROLE_USER");
    }
    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
        return null;
    }
    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
    /**
     * Add userticket
     *
     * @param ticket $userticket
     *
     * @return User
     */
    public function addUserticket(ticket $userticket)
    {
        $this->usertickets[] = $userticket;
        return $this;
    }
    /**
     * Remove userticket
     *
     * @param ticket $userticket
     */
    public function removeUserticket(ticket $userticket)
    {
        $this->usertickets->removeElement($userticket);
    }
    function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->firstname." ".$this->lastname;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->email;
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }

    /**
     * @param mixed $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }


}