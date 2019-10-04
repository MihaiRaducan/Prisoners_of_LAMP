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

    private $tileIndices34 = [
                [1, 2], [1, 3], [1, 4],
            [2, 1], [2, 2], [2, 3], [2, 4],
        [3, 1], [3, 2], [3, 3], [3, 4], [3, 5],
            [4, 1], [4, 2], [4, 3], [4, 4],
                [5, 2], [5, 3], [5, 4],
    ];

    private $tileIndices34ClockwiseArrangements = [
        [
            [1, 2], [1, 3], [1, 4], [2, 4], [3, 5], [4, 4], [5, 4], [5, 3], [5, 2], [4, 1], [3, 1], [2, 1],
            [2, 2], [2, 3], [3, 4], [4, 3], [4, 2], [3, 2], [3, 3]
        ],
        [
            [1, 4], [2, 4], [3, 5], [4, 4], [5, 4], [5, 3], [5, 2], [4, 1], [3, 1], [2, 1], [1, 2], [1, 3],
            [2, 3], [3, 4], [4, 3], [4, 2], [3, 2], [2, 2], [3, 3]
        ],
        [

        ]
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

    /**
     * according to the rules of Catan, this array should not be randomized.
     * the lucky numbers have to be arranged in a certain order starting from one of the corners;
     * (the lucky number tokens have letters on their back and should be placed in alphabetical order)
     */
    private $luckyNumbers34 = [
              6, 3, 8,
           2, 4, 5, 10,
        5, 9, null, 6, 9,
          10, 11, 3, 12,
              8, 4, 11
    ];

    /**
     * One Map has Many Edges
     * @ORM\OneToMany(targetEntity="Edge", mappedBy="map")
     */
    private $edges;

    private $edgeIndices34 = [
                                                [0.5, 1.5], [0.5, 2.0], [0.5, 2.5], [0.5, 3.0], [0.5, 3.5], [0.5, 4.0],
                                        [1.0, 1.5],                 [1.0, 2.5],                 [1.0, 3.5],                 [1.0, 4.5],
                                [1.5, 1.0], [1.5, 1.5], [1.5, 2.0], [1.5, 2.5], [1.5, 3.0], [1.5, 3.5], [1.5, 4.0], [1.5, 4.5],
                        [2.0, 0.5],                 [2.0, 1.5],                 [2.0, 2.5],                   [2.0, 3.5],                 [2.0, 4.5],
                [2.5, 0.5], [2.5, 1.0], [2.5, 1.5], [2.5, 2.0], [2.5, 2.5], [2.5, 3.0], [2.5, 3.5], [2.5, 4.0], [2.5, 4.5], [2.5, 5.0],
        [3.0, 0.5],                 [3.0, 1.5],                 [3.0, 2.5],                 [3.0, 3.5],                 [3.0, 4.5],                 [3.0, 5.5],
                [3.5, 0.5], [3.5, 1.0], [3.5, 1.5], [3.5, 2.0], [3.5, 2.5], [3.5, 3.0], [3.5, 3.5], [3.5, 4.0], [3.5, 4.5], [3.5, 5.0],
                        [4.0, 0.5],                 [4.0, 1.5],                 [4.0, 2.5],                 [4.0, 3.5],                 [4.0, 4.5],
                                [4.5, 1.0], [4.5, 1.5], [4.5, 2.0], [4.5, 2.5], [4.5, 3.0], [4.5, 3.5], [4.5, 4.0], [4.5, 4.5],
                                        [5.0, 1.5],                 [5.0, 2.5],                 [5.0, 3.5],                 [5.0, 4.5],
                                                   [5.5, 1.5], [5.5, 2.0], [5.5, 2.5], [5.5, 3.0], [5.5, 3.5], [5.5, 4.0]
    ];

    /**
     * One Map has Many Vertices
     * @ORM\OneToMany(targetEntity="Vertex", mappedBy="map")
     */
    private $vertices;

    private $innerVertexIndices34 = [
                                [1.3, 2.5],         [1.3, 3.5],
                    [1.7, 1.5],         [1.7, 2.5],         [1.7, 3.5],
                    [2.3, 1.5],         [2.3, 2.5],         [2.3, 3.5],
        [2.7, 1.5],         [2.7, 2.5],         [2.7, 3.5],         [2.7, 4.5],
        [3.3, 1.5],         [3.3, 2.5],         [3.3, 3.5],         [3.3, 4.5],
                    [3.7, 1.5],         [3.7, 2.5],         [3.7, 3.5],
                    [4.3, 1.5],         [4.3, 2.5],         [4.3, 3.5],
                                [4.7, 2.5],         [4.7, 3.5],
    ];

    /**
     * the vertices on the edge of the map can have ports, clockwise
     */
    private $verticesOnMapEdges34 = [
        [
            [0.7, 1.5], [0.3, 1.5], [0.7, 2.5], [0.3, 2.5], [0.7, 3.5],
        ],
        [
            [0.3, 3.5], [0.7, 4.5], [1.3, 4.5], [1.7, 4.5], [2.3, 4.5],
        ],
        [
            [2.7, 5.5], [3.3, 5.5], [3.7, 4.5], [4.3, 4.5], [4.7, 4.5],
        ],
        [
            [5.3, 4.5], [5.7, 3.5], [5.3, 3.5], [5.7, 2.5], [5.3, 2.5],
        ],
        [
            [5.7, 1.5], [5.3, 1.5], [4.7, 1.5], [4.3, 0.5], [3.7, 0.5],
        ],
        [
            [3.3, 0.5], [2.7, 0.5], [2.3, 0.5], [1.7, 0.5], [1.3, 1.5],
        ],
    ];

    /**
     * the map Edges and the ports they can have
     * NOTE: the 3-4 player map and 4-5 player map use the same edges with ports;
     * NOTE: the 4-5 player map uses 4 additional edge spacers
     * [port_type, port_inclination]
     * port_types correspond to resource types except port_type = 0 which exchanges all resources
     */
    private $portsOnMapEdges34 = [
        [
            [0, 0], [0, -60], [null, null], [3, 60], [3, 0]
        ],
        [
            [null, null], [null, null], [0, 0], [0, -60], [null, null]
        ],
        [
            [0, 0], [0, -60], [null, null], [2, 60], [2, 0]
        ],
        [
            [null, null], [null, null], [1, 0], [1, -60], [null, null]
        ],
        [
            [0, 0], [0, -60], [null, null], [4, 60], [4, 0]
        ],
        [
            [null, null], [null, null], [5, 0], [5, -60], [null, null]
        ]
    ];

    /**
     * a map has 6 edges with various combinations of ports
     * these are the vertices where those edges connect
     * [rowIndex, posIndex, inclination]
     */
    private $mapEdgeConnectors34 = [
        [0.3, 3.5, 0], [0.7, 1.5, -60], [2.7, 5.5, 60], [3.3, 0.5, 60], [5.3, 4.5, -60], [5.7, 1.5, 0],
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
            $this->createRandomTiles($this->tileIndices34, $this->tileTypes34, $this->luckyNumbers34);
            $this->createEdges($this->edgeIndices34);
            $this->createInnerVertices($this->innerVertexIndices34);
            for ($i = 0; $i < 6; $i++) {
                for ($j = 0; $j < 5; $j++) {
                    $vertex = new Vertex($this->verticesOnMapEdges34[$i][$j][0], $this->verticesOnMapEdges34[$i][$j][1]);
                    $vertex->setPortType($this->portsOnMapEdges34[$i][$j][0]);
                    $vertex->setPortInclination($i*60 + $this->portsOnMapEdges34[$i][$j][1]);
                    $this->vertices->add($vertex);
                    $vertex->setMap($this);
                }
            }
        }
    }

    private function createRandomTiles (array $tileIndices, array $tileTypes, array $luckyNumbers) {
        foreach ($tileIndices as $key => $rowPosIndices) {
            $tile = new Tile($rowPosIndices[0], $rowPosIndices[1], $tileTypes[$key], $luckyNumbers[$key]);
            $this->tiles->add($tile);
            $tile->setMap($this);
        }
    }

    private function createEdges(array $edgeIndices) {
        foreach ($edgeIndices as $rowPosIndices) {
            $edge = new Edge($rowPosIndices[0], $rowPosIndices[1]);
            $this->edges->add($edge);
            $edge->setMap($this);
        }
    }

    private function createInnerVertices(array $innerVertexIndices) {
        foreach ($innerVertexIndices as $rowPosIndices) {
            $vertex = new Vertex($rowPosIndices[0], $rowPosIndices[1]);
            $this->vertices->add($vertex);
            $vertex->setMap($this);
        }
    }

    private function createVerticesOnMapEdges(array $verticesOnMapEdges, array $portsOnMapEdges) {

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

    /**
     * @return array
     */
    public function getMapEdgeConnectors34()
    {
        return $this->mapEdgeConnectors34;
    }

}
