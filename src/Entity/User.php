<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("username",  message="Username already exist!")
 */
class User implements UserInterface, TwoFactorInterface
{
    public function __construct()
    {
        $this->uuid = Uuid::v4();
        $this->isActive = false;
        $this->createAt = new \DateTime("now");
    }
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
    * @ORM\Column(type="string", length=255)
    */
    protected $uuid;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank
     * @Assert\Length(min=6)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\NotCompromisedPassword
     * @Assert\Length(min=20)
     * @Assert\Regex(
     *     pattern="/(?:[^`!@#$%^&*\-_=+'\/.,]*[`!@#$%^&*\-_=+'\/.,]){2}/",
     *     message="Minimum two special characters required"
     * )
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank
     * @Assert\Email
     */
    private $email;

    /**
     * @ORM\Column(name="googleAuthenticatorSecret", type="string", nullable=true)
     */
    private $googleAuthenticatorSecret;
    
    /**
    * @ORM\Column(name="is_active", type="boolean")
    */
    private $isActive;
    
    
    /**
    * @ORM\Column(name="create_at", type="datetime")
    */
    private $createAt;
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid()
    {
        return $this->uuid;
    }
    
    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
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
    
    public function isGoogleAuthenticatorEnabled(): bool
    {
        return $this->googleAuthenticatorSecret ? true : false;
    }

    public function getGoogleAuthenticatorUsername(): string
    {
        return $this->username;
    }

    public function getGoogleAuthenticatorSecret(): ?string
    {
        return $this->googleAuthenticatorSecret;
    }

    public function setGoogleAuthenticatorSecret(?string $googleAuthenticatorSecret): void
    {
        $this->googleAuthenticatorSecret = $googleAuthenticatorSecret;
    }
    
    public function getIsActive(): ?string
    {
        return $this->isActive;
    }

    public function setIsActive(string $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
    
    public function getCreateAt(): ?string
    {
        return $this->createAt;
    }
    
    public function getRoles()
    {
        return ['USER_ROLE'];
    }
    
    public function getSalt() {}
    public function eraseCredentials() {}
}
