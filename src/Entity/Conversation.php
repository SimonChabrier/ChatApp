<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Stof;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ConversationRepository::class)
 */
class Conversation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"private_conversation"})
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="conversations", cascade={"persist"}, fetch="EAGER")
     * @Groups({"private_conversation"})
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="conversation", fetch="EAGER")
     * @Groups({"private_conversation"})
     */
    private $messages;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"private_conversation"})
     */
    private $status = 1;

    /**
     * @ORM\Column(type="datetime")
     * @Stof\Timestampable(on="create")
     * @Groups({"private_conversation"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Stof\Timestampable(on="update")
     * @Groups({"private_conversation"})
     */
    private $updatedAt;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addConversation($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeConversation($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setConversation($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getConversation() === $this) {
                $message->setConversation(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * retourne le topic mercure privé de la conversation
     * pour initialiser les searchparams de l'url d'abonnement au topic mercure
     * Eg : /conversation/1
     * @return string
     */
    public function getMercurePrivateTopic(): string
    {
        return '/conversation/' . $this->getId();
    }

    /**
     * Retourne le statut de la conversation 
     * sous forme de string pour l'affichage
     */
    public function getStatus(): string
    {
        return $this->status ? 'active' : 'inactive';
    }
}
