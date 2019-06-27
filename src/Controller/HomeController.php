<?php

namespace App\Controller;

use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends Controller {
    /**
     * @Route("/", name="homepage")
     */
    public function home(AdRepository $adRepo, UserRepository $userRepo) {
        return $this->render('home/home.html.twig', 
        [
            'ads' => $adRepo->findBestAds(3),
            'users' => $userRepo->findBestUser(2)
        ]
        );
    }
}
