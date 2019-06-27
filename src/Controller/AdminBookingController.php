<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Service\Pagination;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBookingController extends AbstractController
{
    /**
     * @Route("/admin/booking/{page<\d+>?1}", name="admin_booking_index")
     */
    public function index(BookingRepository $repo, Pagination $pagination, $page)
    {
        $pagination->setEntityClass(Booking::class)
                   ->setPage($page);

        return $this->render('admin/booking/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Permet de modifier une réservation
     *
     * @Route("admin/booking/{id}/edit", name="admin_booking_edit")
     * 
     * @param Booking $booking
     * @param ObjectManager $manager
     * @param Request $request
     * 
     * @return Response
     */
    public function edit(Booking $booking, ObjectManager $manager, Request $request) {
        $form = $this->createForm(AdminBookingType::class, $booking);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {
            $booking->setAmount(0);

            $manager->persist($booking);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le commentaire n° {$booking->getId()} a bien été modifié !"
            );

            return $this->redirectToRoute('admin_booking_index');
        }


        return $this->render('admin/booking/edit.html.twig', [
            'booking' => $booking,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Permet de supprimer une réservation
     *
     * @Route("/admin/booking/{id}/delete", name="admin_booking_delete")
     * 
     * @param Booking $booking
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Booking $booking, ObjectManager $manager) {
        $manager->remove($booking);
        $manager->flush();

        $this->addFlash(
            'success',
            "La réservation de {$booking->getBooker()->getFullName()} a bien été supprimé !"
        );

        return $this->redirectToRoute('admin_booking_index');
    }
}
