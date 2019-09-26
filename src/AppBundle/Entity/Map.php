<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;

/**
 * Map
 *
 * @ORM\Table(name="map")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MapRepository")
 */
class Map
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
     * @var int|null
     *
     * @ORM\Column(name="winner_id", type="integer", nullable=true)
     */
    private $winnerId;

    /**
     * One Map has One GameTable.
     * @OneToOne(targetEntity="GameTable", inversedBy="map")
     * @JoinColumn(name="gameTable_id", referencedColumnName="id")
     */
    private $gameTable;

    /**
     * One Map has Many Tiles
     * @ORM\OneToMany(targetEntity="Tile", mappedBy="map")
     */
    private $tiles;

    /**
     * One Map has Many Edges
     * @ORM\OneToMany(targetEntity="Edge", mappedBy="map")
     */
    private $edges;

    /**
     * One Map has Many Vertices
     * @ORM\OneToMany(targetEntity="Vertex", mappedBy="map")
     */
    private $vertices;

    private $tileIndices34 = [
                [1, 2], [1, 3], [1, 4],
            [2, 1], [2, 2], [2, 3], [2, 4],
        [3, 1], [3, 2], [3, 3], [3, 4], [3, 5],
            [4, 1], [4, 2], [4, 3], [4, 4],
                [5, 2], [5, 3], [5, 4],
    ];

    private $edgeIndices34 = [
                                        [0.5, 1.5], [0.5, 2.0], [0.5, 2.5], [0.5, 3.0], [0.5, 3.5], [0.5, 4.0],
                                [1.0, 1.5],                 [1.0, 2.5],                 [1.0, 3.5],                 [1.0, 4.5],
                        [1.5, 1.0], [1.5, 1.5], [1.5, 2.0], [1.5, 2.5], [1.5, 3.0], [1.5, 3.5], [1.5, 4.0], [1.5, 4.5],
                    [2.0, 0.5],             [2.0, 1.5],                 [2.0, 2.5],                 [2.0, 3.5],                 [2.0, 4.5],
            [2.5, 0.5], [2.5, 1.0], [2.5, 1.5], [2.5, 2.0], [2.5, 2.5], [2.5, 3.0], [2.5, 3.5], [2.5, 4.0], [2.5, 4.5], [2.5, 5.0],
        [3.0, 0.5],             [3.0, 1.5],                 [3.0, 2.5],                 [3.0, 3.5],                 [3.0, 4.5],                 [3.0, 5.5],
            [3.5, 0.5], [3.5, 1.0], [3.5, 1.5], [3.5, 2.0], [3.5, 2.5], [3.5, 3.0], [3.5, 3.5], [3.5, 4.0], [3.5, 4.5], [3.5, 5.0],
                    [4.0, 0.5],             [4.0, 1.5],                 [4.0, 2.5],                 [4.0, 3.5],                 [4.0, 4.5],
                        [4.5, 1.0], [4.5, 1.5], [4.5, 2.0], [4.5, 2.5], [4.5, 3.0], [4.5, 3.5], [4.5, 4.0], [4.5, 4.5],
                                [5.0, 1.5],                 [5.0, 2.5],                 [5.0, 3.5],                 [5.0, 4.5],
                                        [5.5, 1.5], [5.5, 2.0], [5.5, 2.5], [5.5, 3.0], [5.5, 3.5], [5.5, 4.0]
    ];

    /**
     * The default layout mimics the basic starting map from Settlers of Catan
     * 0 = nothing from 0 = wasteland (desert = pale yellow)
     * 1 = plastic from 1 = landfill (wood from forest, dark green),
     * 2 = copper from 2 = ruined plant (bricks from hills, red-brown),
     * 3 = drone from 3 = conflict zone (wool/sheep from pasture, white on light green),
     * 4 = gold from 4 = electronic waste (grain from field, gold/brown),
     * 5 = high-purity silicon from 5 = derelict data-center (ore from mountain, white/grey);
     */
    private $tileTypes34 = [
            1, 3, 3,
          4, 5, 4, 1,
        1, 2, 0, 5, 4,
          4, 5, 1, 3,
            2, 3, 2
    ];

    private $luckyNumbers34 = [
          6, 3, 8,
        2, 4, 5, 10,
      5, 9, null, 6, 9,
        10, 11, 3, 12,
          8, 4, 11
    ];

    /**
     * Map constructor.
     */
    public function __construct($type = null)
    {
        $this->tiles = new ArrayCollection();
        $this->edges = new ArrayCollection();
        $this->vertices = new ArrayCollection();
        if ($type === '3-4') {
            for ($i = 0; $i < 19; $i++) {
                $tile = new Tile($this->tileIndices34[$i][0], $this->tileIndices34[$i][1], $this->tileTypes34[$i], $this->luckyNumbers34[$i]);
                $this->tiles->add($tile);
                $tile->setMap($this);
            }
            foreach ($this->edgeIndices34 as $rowPosIndices) {
                $edge = new Edge($rowPosIndices[0], $rowPosIndices[1]);
                $this->edges->add($edge);
                $edge->setMap($this);
            }
        }

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
     * Set winnerId.
     *
     * @param int|null $winnerId
     *
     * @return Map
     */
    public function setWinnerId($winnerId = null)
    {
        $this->winnerId = $winnerId;

        return $this;
    }

    /**
     * Get winnerId.
     *
     * @return int|null
     */
    public function getWinnerId()
    {
        return $this->winnerId;
    }

    /**
     * @return mixed
     */
    public function getGameTable()
    {
        return $this->gameTable;
    }

    /**
     * @param mixed $gameTable
     */
    public function setGameTable($gameTable)
    {
        $this->gameTable = $gameTable;
    }

    /**
     * @return mixed
     */
    public function getTiles()
    {
        return $this->tiles;
    }

    /**
     * @param mixed $tiles
     */
    public function setTiles($tiles)
    {
        $this->tiles = $tiles;
    }

    /**
     * @return mixed
     */
    public function getEdges()
    {
        return $this->edges;
    }

    /**
     * @param mixed $edges
     */
    public function setEdges($edges)
    {
        $this->edges = $edges;
    }

    /**
     * @return mixed
     */
    public function getVertices()
    {
        return $this->vertices;
    }

    /**
     * @param mixed $vertices
     */
    public function setVertices($vertices)
    {
        $this->vertices = $vertices;
    }


}
