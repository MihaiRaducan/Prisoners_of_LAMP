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
     * finds whether the user has a player already involved in a game(Table)
     * @return bool
     */
    public function alreadyPlaying () {
        foreach ($this->players as $player) {
            if ($player->getGameTable()->getStatus() == true) {
                return true;
            }
            //TODO: add other conditions type getGame()->getStatus()
        }
        return false;
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