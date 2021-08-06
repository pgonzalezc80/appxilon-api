<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TaskRepository;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     normalizationContext={"groups"="task:read"},
 *     denormalizationContext={"groups"="task:write"},
 *     attributes={
 *          "pagination_items_per_page"=3,
 *          "formats"={"jsonld","json","html","jsonhal","csv"={"text/csv"}}
 *     }
 * )
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 * @ApiFilter(BooleanFilter::class,properties={"status"})
 * @ApiFilter(SearchFilter::class,properties={"title":"partial"})
 */
class Task
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Title for task
     * @Groups({"task:write","task:read","user:read"})
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="datetime_immutable")
     *
     */
    private $createdAt;

    /**
     * Indicates if the task is completed or not
     * @Groups({"task:write","task:read","user:read"})
     * @ORM\Column(type="boolean")
     */
    private $status = false;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"task:write","task:read"})
     */
    private $owner;

    public function __construct(){
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     * @Groups({"task:read"})
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return $this
     * @Groups({"task:write"})
     * @SerializedName("description")
     */
    public function setTextDescription(?string $description): self
    {
        $this->description = nl2br($description);

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return string
     * @Groups({"task:read","user:read"})
     */
    public function getCreatedAtAgo():string
    {
        return Carbon::instance($this->getCreatedAt())->diffForHumans();
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
