<?php
/**
 * Created by PhpStorm.
 * User: ilya
 * Date: 03.12.18
 * Time: 22:06
 */

namespace AppBundle\Controller;

use AppBundle\Entity\EntityMerger;
use AppBundle\Entity\Movie;
use AppBundle\Entity\Role;
use AppBundle\Exception\ValidationException;
use Doctrine\Common\Collections\Collection;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\ControllerTrait;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @Security("is_anonymous() or is_authenticated()")
 */
class MoviesController extends AbstractController
{
    use ControllerTrait;

    /**
     * @var EntityMerger
     */
    private $entityMerger;

    /**
     * MoviesController constructor.
     * @param EntityMerger $entityMerger
     */
    public function __construct(EntityMerger $entityMerger)
    {
        $this->entityMerger = $entityMerger;
    }

    /**
     * @Rest\View()
     * @return Movie[]
     */
    public function getMoviesAction()
    {
        $movies = $this->getDoctrine()->getRepository('AppBundle:Movie')->findAll();

        return $movies;
    }

    /**
     * @Rest\View(statusCode=201)
     * @ParamConverter("movie", converter="fos_rest.request_body")
     * @Rest\NoRoute()
     *
     * @param Movie $movie
     * @param ConstraintViolationListInterface $validationErrors
     * @return Movie
     */
    public function postMovieAction(Movie $movie, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($movie);
        $em->flush();

        return $movie;
    }

    /**
     * @Rest\View()
     * @param Movie $movie
     * @return View
     */
    public function deleteMovieAction(?Movie $movie)
    {
        if (null === $movie) {
            return $this->view(null, 404);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($movie);
        $em->flush();
    }

    /**
     * @Rest\View()
     * @param Movie|null $movie
     * @return Movie|View|null
     */
    public function getMovieAction(?Movie $movie)
    {
        if (null === $movie) {
            return $this->view(null, 404);
        }

        return $movie;
    }

    /**
     * @param Movie $movie
     * @Rest\View()
     * @return Collection
     */
    public function getMovieRolesAction(Movie $movie)
    {
        return $movie->getRoles();
    }

    /**
     * @param Movie $movie
     * @param Role $role
     * @param ConstraintViolationListInterface $validationErrors
     * @return Role
     * @Rest\View(statusCode=201)
     * @ParamConverter("role", converter="fos_rest.request_body", options={"deserializationContext"={"groups"={"Deserialize"}}})
     */
    public function postMovieRolesAction(Movie $movie, Role $role, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);
        }

        $role->setMovie($movie);
        $em = $this->getDoctrine()->getManager();
        $em->persist($role);
        $movie->getRoles()->add($role);
        $em->persist($movie);
        $em->flush();

        return $role;
    }

    /**
     * @Rest\NoRoute()
     * @ParamConverter("modifiedMovie", converter="fos_rest.request_body",
     *     options={"validator"={"groups"={"Patch"}}}
     * )
     * @Security("is_authenticated()")
     */
    public function patchMovieAction(
        ?Movie $movie,
        Movie $modifiedMovie,
        ConstraintViolationListInterface $validationErrors
    )
    {
        if (null === $movie) {
            return $this->view(null, 404);
        }

        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);
        }

        $this->entityMerger->merge($movie, $modifiedMovie);

        $em = $this->getDoctrine()->getManager();
        $em->persist($movie);
        $em->flush();

        return $movie;
    }
}
