<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * GameTable
 *
 * @ORM\Table(name="game_table")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GameTableRepository")
 */
class GameTable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Assert\Choice({"2-4", "5-6"})
     * @ORM\Column(name="map_type", type="string", length=8)
     */
    private $mapType;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="gameTables")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="Player", mappedBy="gameTable")
     */
    private $players;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->players = new ArrayCollection();
    }

    /**
     * @param User $user
     */
    public function addUser(User $user)
    {
        if ($this->users->contains($user)) {
            return;
        }
        $this->users->add($user);
        $user->addToGameTable($this);
    }

    /**
     * @param User $user
     */
    public function removeUser(User $user)
    {
        if (!$this->users->contains($user)) {
            return;
        }
        $this->users->removeElement($user);
        $user->removeFromGameTable($this);
    }

    /**
     * @param Player $player
     */
    public function addPlayer(Player $player)
    {
        if ($this->players->contains($player)) {
            return;
        }
        $this->players->add($player);
        $player->setGameTable($this);
    }

    /**
     * @param Player $player
     */
    public function removePlayer(Player $player)
    {
        if (!$this->players->contains($player)) {
            return;
        }
        $this->players->removeElement($player);
        $player->setGameTable(null);
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set mapType.
     *
     * @param string $mapType
     *
     * @return GameTable
     */
    public function setMapType($mapType)
    {
        $this->mapType = $mapType;

        return $this;
    }

    /**
     * Get mapType.
     *
     * @return string
     */
    public function getMapType()
    {
        return $this->mapType;
    }

    /**
     * Set status.
     *
     * @param bool $status
     *
     * @return GameTable
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param mixed $users
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }
}