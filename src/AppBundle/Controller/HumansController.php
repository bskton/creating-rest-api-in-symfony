<?php
/**
 * Created by PhpStorm.
 * User: ilya
 * Date: 12.12.18
 * Time: 22:22
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Person;
use AppBundle\Exception\ValidationException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\ControllerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class HumansController extends AbstractController
{
    use ControllerTrait;

    /**
     * @Rest\View()
     * @return Person[]
     */
    public function getHumansAction()
    {
        $people = $this->getDoctrine()->getRepository('AppBundle:Person')->findAll();

        return $people;
    }

    /**
     * @Rest\View(statusCode=201)
     * @ParamConverter("person", converter="fos_rest.request_body")
     * @Rest\NoRoute()
     *
     * @param Person $person
     * @param ConstraintViolationListInterface $validationErrors
     * @return Person
     */
    public function postHumanAction(Person $person, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($person);
        $em->flush();

        return $person;
    }

    /**
     * @Rest\View()
     * @param Person $person
     * @return \FOS\RestBundle\View\View
     */
    public function deleteHumanAction(?Person $person)
    {
        if (null === $person) {
            return $this->view(null, 404);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($person);
        $em->flush();
    }

    /**
     * @Rest\View()
     * @param Person|null $person
     * @return Person|\FOS\RestBundle\View\View|null
     */
    public function getHumanAction(?Person $person)
    {
        if (null === $person) {
            return $this->view(null, 404);
        }

        return $person;
    }
}