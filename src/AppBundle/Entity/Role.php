<?php
/**
 * Created by PhpStorm.
 * User: ilya
 * Date: 13.12.18
 * Time: 19:44
 */

namespace AppBundle\Entity;

use AppBundle\Annotation as App;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="role")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RoleRepository")
 */
class Role
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Person
     *
     * @ORM\ManyToOne(targetEntity="Person")
     * @App\DeserializeEntity(type="AppBundle\Entity\Person", idField="id", idGetter="getId", setter="setPerson")
     */
    private $person;

    /**
     * @var string
     *
     * @ORM\Column(name="played_name", type="string", length=100)
     * @Assert\NotBlank()
     */
    private $playedName;

    /**
     * @var Movie
     *
     * @ORM\ManyToOne(targetEntity="Movie", inversedBy="roles")
     */
    private $movie;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Person
     */
    public function getPerson(): Person
    {
        return $this->person;
    }

    /**
     * @param Person $person
     */
    public function setPerson(Person $person): void
    {
        $this->person = $person;
    }

    /**
     * @return string
     */
    public function getPlayedName(): string
    {
        return $this->playedName;
    }

    /**
     * @param string $playedName
     */
    public function setPlayedName(string $playedName): void
    {
        $this->playedName = $playedName;
    }

    /**
     * @return Movie
     */
    public function getMovie(): Movie
    {
        return $this->movie;
    }

    /**
     * @param Movie $movie
     */
    public function setMovie(Movie $movie): void
    {
        $this->movie = $movie;
    }
}