<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Player", mappedBy="user")
     */
    private $players;

    public function __construct()
    {
        parent::__construct();
        $this->players = new ArrayCollection();
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
        $player->setUser($this);
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
        $player->setUser(null);
    }

    /**
     * @return mixed
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * @param mixed $players
     */
    public function setPlayers($players)
    {
        $this->players = $players;
    }

}