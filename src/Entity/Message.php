<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 */
class Message
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @Assert\Email(
     *     message = "The email {{ value }} is not a valid email.",
     * )
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     */
    private $email;
    
    /**
     * @Assert\Length(
     *     max="1000",
     *     maxMessage = "Message can't be longer than {{ limit }} characters"
     * )
     * @Assert\NotBlank
     * @ORM\Column(type="text")
     */
    private $message;
    
    /**
     * @ORM\Column(type="uuid", unique=true)
     */
    private $uuid;
    
    
    public function __construct()
    {
        $this->setUuid(Uuid::uuid4()->toString());
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getEmail(): ?string
    {
        return $this->email;
    }
    
    public function setEmail(string $email): self
    {
        $this->email = $email;
        
        return $this;
    }
    
    public function getMessage(): ?string
    {
        return $this->message;
    }
    
    public function setMessage(string $message): self
    {
        $this->message = $message;
        
        return $this;
    }
    
    public function toArray(): array
    {
        return [
            'uuid'    => $this->getUuid(),
            'email'   => $this->getEmail(),
            'message' => $this->getMessage(),
        ];
    }
    
    public function getUuid()
    {
        return $this->uuid;
    }
    
    public function setUuid($uuid): self
    {
        $this->uuid = $uuid;
        
        return $this;
    }
}
