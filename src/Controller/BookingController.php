<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Form\BookingType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
{
    /**
     * Permet de réserver une annonce
     * 
     * @Route("/ads/{slug}/book", name="booking_create")
     * @IsGranted("ROLE_USER")
     */
    public function book(Ad $ad, Request $request, ObjectManager $manager)
    {
        $booking = new Booking();

        $form = $this->createForm(BookingType::class, $booking);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            $booking->setBooker($user)
                    ->setAd($ad);

            // Si les dates ne sont pas dispo -> message d'erreur
            if($booking->isBookableDates()) {
                $this->addFlash(
                'warning',
                "Les dates que vous avez choisi ne peuvent pas être réservées: elles sont déjà prises."
            );
            } else {
                $manager->persist($booking);
                $manager->flush();

                return $this->redirectToRoute('booking_show', ['id' => $booking->getId(), 'withAlert' => true]);
            }

            
        }
        return $this->render('booking/book.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher la page de réservation
     * 
     * @Route("/booking/{id}", name="booking_show")
     *
     * @param Booking $booking
     * @param Request $request
     * @param ObjectManager $manager
     *
     * @return Response
     */
    public function show(Booking $booking, Request $request, ObjectManager $manager) {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAd($booking->getAd())
                    ->getAuthor($this->getUser());

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre commentaire à bien été enregistré !'
            );
        }

        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
            'form' => $form->createView()
        ]);
    }
}
