<?php

namespace App\Controller;

use App\Entity\InfoAktuelles;
use App\Entity\InfoAllgemeines;
use App\Entity\Loeschkonzept;
use App\Entity\Mandant;
use App\Entity\Sepa;
use App\Entity\User;
use App\Entity\Verfahrensverzeichnis;
use App\Form\LoeschkonzeptFormType;
use App\Form\MandantAllgemeinFormType;
use App\Form\MandantDsbFormType;
use App\Form\MandantRechnungsadresseFormType;
use App\Form\SepaType;
use App\Form\VerfahrensverzeichnisFormType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route(path: '/', name: 'app_dashboard_start')]
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $latestInfo = $entityManager->getRepository(InfoAllgemeines::class)->getLatestVersion();

        return $this->render('dashboard/index.html.twig', [
            'user' => $this->getUser(),
            'aktuelles' => $entityManager->getRepository(InfoAktuelles::class)->findLatestFour(),
            'allgemeines' => $entityManager->getRepository(InfoAllgemeines::class)->findLatestFour(),
            'version' => $latestInfo?->getVersion() ?? '1.0.0',
        ]);
    }
}