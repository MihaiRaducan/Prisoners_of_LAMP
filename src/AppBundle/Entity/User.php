<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
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

    /**
     * @ORM\ManyToMany(targetEntity="GameTable", inversedBy="users")
     * @ORM\JoinTable(name="users_gametables")
     */
    private $gameTables;

    public function __construct()
    {
        parent::__construct();
        $this->players = new ArrayCollection();
        $this->gameTables = new ArrayCollection();
    }

    /**
     * @param GameTable $gameTable
     */
    public function addToGameTable (GameTable $gameTable) {
        if ($this->gameTables->contains($gameTable)) {
            return;
        }
        $this->gameTables->add($gameTable);
        $gameTable->addUser($this);
    }

    /**
     * GameTable $gameTable
     */
    public function removeFromGameTable (GameTable $gameTable) {
        if (!$this->gameTables->contains($gameTable)) {
            return;
        }
        $this->gameTables->removeElement($gameTable);
        $gameTable->removeUser($this);
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

    /**
     * @return mixed
     */
    public function getGameTables()
    {
        return $this->gameTables;
    }

    /**
     * @param mixed $gameTables
     */
    public function setGameTables($gameTables)
    {
        $this->gameTables = $gameTables;
    }

}