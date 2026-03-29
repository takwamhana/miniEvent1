<?php

namespace App\Entity;

use App\Repository\WebauthnCredentialRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WebauthnCredentialRepository::class)]
class WebauthnCredential
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'webauthnCredentials')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $credentialId = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $type = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $transports = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $attestationType = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $trustPath = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $aaguid = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $publicKey = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $userHandle = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $signatureCounter = null;

    #[ORM\Column]
    private ?bool $uvInitialized = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCredentialId(): ?string
    {
        return $this->credentialId;
    }

    public function setCredentialId(string $credentialId): static
    {
        $this->credentialId = $credentialId;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getTransports(): ?string
    {
        return $this->transports;
    }

    public function setTransports(string $transports): static
    {
        $this->transports = $transports;

        return $this;
    }

    public function getAttestationType(): ?string
    {
        return $this->attestationType;
    }

    public function setAttestationType(string $attestationType): static
    {
        $this->attestationType = $attestationType;

        return $this;
    }

    public function getTrustPath(): ?string
    {
        return $this->trustPath;
    }

    public function setTrustPath(string $trustPath): static
    {
        $this->trustPath = $trustPath;

        return $this;
    }

    public function getAaguid(): ?string
    {
        return $this->aaguid;
    }

    public function setAaguid(string $aaguid): static
    {
        $this->aaguid = $aaguid;

        return $this;
    }

    public function getPublicKey(): ?string
    {
        return $this->publicKey;
    }

    public function setPublicKey(string $publicKey): static
    {
        $this->publicKey = $publicKey;

        return $this;
    }

    public function getUserHandle(): ?string
    {
        return $this->userHandle;
    }

    public function setUserHandle(string $userHandle): static
    {
        $this->userHandle = $userHandle;

        return $this;
    }

    public function getSignatureCounter(): ?string
    {
        return $this->signatureCounter;
    }

    public function setSignatureCounter(string $signatureCounter): static
    {
        $this->signatureCounter = $signatureCounter;

        return $this;
    }

    public function isUvInitialized(): ?bool
    {
        return $this->uvInitialized;
    }

    public function setUvInitialized(bool $uvInitialized): static
    {
        $this->uvInitialized = $uvInitialized;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
