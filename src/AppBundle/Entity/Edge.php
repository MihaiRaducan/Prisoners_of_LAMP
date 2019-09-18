<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Edge
 *
 * @ORM\Table(name="edge")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EdgeRepository")
 */
class Edge
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
     *
     * @ORM\Column(name="row_index", type="decimal", precision=2, scale=1)
     */
    private $rowIndex;

    /**
     * @var string
     *
     * @ORM\Column(name="pos_index", type="decimal", precision=2, scale=1)
     */
    private $posIndex;

    /**
     * Many Edges have One Map
     * @ORM\ManyToOne(targetEntity="Map", inversedBy="edges")
     * @ORM\JoinColumn(name="map_id", referencedColumnName="id")
     */
    private $map;

    public function __construct($rowIndex = null, $posIndex = null)
    {
        $this->setRowIndex($rowIndex);
        $this->setPosIndex($posIndex);
    }

    public function inclination(){

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
     * Set rowIndex.
     *
     * @param string $rowIndex
     *
     * @return Edge
     */
    public function setRowIndex($rowIndex)
    {
        $this->rowIndex = $rowIndex;

        return $this;
    }

    /**
     * Get rowIndex.
     *
     * @return string
     */
    public function getRowIndex()
    {
        return $this->rowIndex;
    }

    /**
     * Set posIndex.
     *
     * @param string $posIndex
     *
     * @return Edge
     */
    public function setPosIndex($posIndex)
    {
        $this->posIndex = $posIndex;

        return $this;
    }

    /**
     * Get posIndex.
     *
     * @return string
     */
    public function getPosIndex()
    {
        return $this->posIndex;
    }

    /**
     * @return mixed
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     * @param mixed $map
     */
    public function setMap($map)
    {
        $this->map = $map;
    }

}
