<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToOne;
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
     * @Assert\Choice({"3-4", "5-6"})
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
     * One GameTable has Many Players
     * @ORM\OneToMany(targetEntity="Player", mappedBy="gameTable")
     */
    private $players;

    /**
     * One GameTable has One Map.
     * @OneToOne(targetEntity="Map", mappedBy="gameTable")
     */
    private $map;

    public function __construct()
    {
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
     * @return array
     */
    public function trimmedPlayers() {
        $maxPlayers = substr($this->getMapType(), -1);
        $trimmed = [];
        while (count($this->players) > intval($maxPlayers)) {
            $unluckyPlayer = $this->players[count($this->players) - 1];
            $this->players->removeElement($unluckyPlayer);
            $trimmed[] = $unluckyPlayer;
        }
        return $trimmed;
    }

    /**
     * @return array
     */
    public function resetSameColorPlayers() {
        $unluckyPlayers = [];
        for ($i = 0; $i<count($this->players); $i++) {
            $currentColor = $this->players[$i]->getColor();
            if ($currentColor !== null) {
                for ($j = $i + 1; $j<count($this->players); $j++) {
                    $unluckyPlayer = $this->players[$j];
                    if ($unluckyPlayer->getColor() == $currentColor) {
                        $unluckyPlayer->setColor(null);
                        $unluckyPlayers[] = $unluckyPlayer;
                    }
                }
            }
        }
        return $unluckyPlayers;
    }

    /**
     * @return array
     */
    public function resetSameDicePlayers() {
        $unluckyPlayers = [];
        for ($i = 0; $i<count($this->players); $i++) {
            $currentInitialDice = $this->players[$i]->getInitialDice();
            if ($currentInitialDice !== null) {
                for ($j = $i + 1; $j<count($this->players); $j++) {
                    $unluckyPlayer = $this->players[$j];
                    if ($unluckyPlayer->getInitialDice() == $currentInitialDice) {
                        $unluckyPlayer->setInitialDice(null);
                        $unluckyPlayers[] = $unluckyPlayer;
                    }
                }
            }
        }
        return $unluckyPlayers;
    }

    /**
     * determines whether a GameTable is full
     * @return bool
     */
    public function isFull() {
        $maxPlayers = substr($this->mapType, -1);
        if (count($this->players) < intval($maxPlayers)) {
            return false;
        }
        return true;
    }

    /**
     * checks if more than the minimum required number of players for given mapType have set their color and initial dice roll
     * @return bool
     */
    public function enoughPlayersReady() {
        $minPlayers = substr($this->mapType, 0, 1);
        if (count($this->players) < intval($minPlayers)) {
            return false;
        }
        foreach ($this->players as $player) {
            if ($player->getColor() === null || $player->getInitialDice() === null) {
                return false;
            }
        }
        return true;
    }

    /**
     * generates the turn order for each player based on all the initial dice rolls
     * rsort->sorts the array in reverse numerical order (old keys are not kept)
     * array_search-> returns the first key for the searched value (keys start at 0)
     */
    public function setPlayerOrder() {
        foreach ($this->players as $player) {
            $diceRolls[] = $player->getInitialDice();
        }
        rsort($diceRolls);

        foreach ($this->players as $player) {
            $turnOrder = 1 + array_search($player->getInitialDice(), $diceRolls);
            $player->setTurnOrder($turnOrder);
        }
        return;
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
    public function getMap()
    {
        return $this->map;
    }

    /**
     * @param $map
     */
    public function setMap($map)
    {
        $this->map = $map;
    }

}
