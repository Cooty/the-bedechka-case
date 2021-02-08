<?php

namespace App\Service\Admin;

use App\Repository\CrewMemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use \Exception;

class ReorderCrewMembersService
{
    /**
     * @var CrewMemberRepository
     */
    private $crewMemberRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        CrewMemberRepository $crewMemberRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->crewMemberRepository = $crewMemberRepository;
        $this->entityManager = $entityManager;
    }

    private function updateOrder(string $id, int $order)
    {
        $crewMember = $this->crewMemberRepository->find($id);
        $crewMember->setOrderOfAppearance($order);

        $this->entityManager->flush();
    }

    /**
     * @param array $object
     * @throws Exception
     */
    public function reorder(array $object)
    {
        if(empty($object["id"]) || empty($object["order"])) {
            throw new Exception("Invalid data");
        }

        try {
            $this->updateOrder($object["id"], $object["order"]);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

}