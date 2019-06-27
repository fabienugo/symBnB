<?php

namespace App\Service;

use Twig\Environment;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Classe de pagination qui extrait toute notion de calcul et de récupération de données de nos controllers
 * 
 * Elle nécessite après instanciation qu'on lui passe l'entité sur laquelle on souhaite travaillerq
 * 
 */
class Pagination {

    /**
     * Le nom de l'entité sur laquelle on veut effectuer une pagination
     *
     * @var string
     */
    private $entityClass;

    /**
     * Le nombre de renseignement à récupérer
     * 
     * @var integer
     */
    private $limit = 10;

    /**
     * Page courante
     * 
     * @var integer
     */
    private $currentPage = 1;

    /**
     * Manager de doctrine pour récupérer le repository de l'entité
     *
     * @var ObjectManager
     */
    private $manager;

    /**
     * Le moteur de template de twig pour générer le rendu de la pagination
     *
     * @var Twig\Environment
     */
    private $twig;

    /**
     * Le nom de la route que l'on veut utiliser pour les boutons de la navigation
     *
     * @var string
     */
    private $route;

    /**
     * Le chemin vers le template qui contient la pagination
     *
     * @var string
     */
    private $templatePath;

    /**
     * Constructeur du service de pagination
     * 
     * N'oubliez pas de configurer votre fichier services.yaml afin que symfony sache quelle valeur utiliser pour le $templatePath
     *
     * @param ObjectManager $manager
     * @param Environment $twig
     * @param RequestStack $request
     * @param string $templatePath
     */
    public function __construct(ObjectManager $manager, Environment $twig, RequestStack $request, $templatePath){
        $this->manager = $manager;
        $this->twig = $twig;
        $this->route = $request->getCurrentRequest()->attributes->get('_route');
        $this->templatePath = $templatePath;
    }

    /**
     * Permet d'afficher le rendu de la pagination au sein d'un template twig
     * 
     * On se sert ici de notre moteur de rendu afin de compoler le template qui se trouve au chemin de notre propriété $templatePath,
     * en lui passant les variables :
     * - page => La page actuelle sur laquelle on se trouve
     * - pages => le nombre total de pages qui existent
     * - route => le nom de la route à utiliser pour les liens de navigation
     * 
     * Attention : cette fonction ne retourne rien, elle affiche directement le rendu
     *
     * @return void
     */
    public function display() {
        $this->twig->display('admin/partials/pagination.html.twig', [
            'page' => $this->currentPage,
            'pages' => $this->getPages(),
            'route' => $this->route,
        ]);
    }

    /**
     * Permet de récupérer le nombre de pages qui existent sur une entité particulière
     * 
     * Elle se sert de Doctrine pour récupérer le repository qui correspond à l'entité que l'on souhaite paginer (voir la propriété $entityClass)
     * puis elle trouve le nombre total d'enregistrements grâce à la fonction findAll() du repository
     * 
     * @throws Exception si la propriété $entityClass n'est pas configurée
     *
     * @return int
     */
    public function getPages() {
        if(empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer ! Utilisez la méthode setEntityClass() de votre objet Pagination");
        }
        $repo = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());

        $pages = ceil($total / $this->limit);
        return $pages;
    }

    /**
     * Permet de récupérer les données paginées pour une entité spécifique
     * 
     * Elle se sert de Doctrine afin de récupérer le repository pour l'entité spécifiée puis grâce au repository et à sa fonction findBy()
     * on récupère les données dans une certaines limite en partant d'un offset
     * 
     * @throws \Exception si la propriété $entityClass n'est pas définie
     *
     * @return array
     */
    public function getData() {
        if(empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer !  Utilisez la méthode setEntityClass() de votre objet Pagination");
        }
        // Calculer l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;
        // Demander au repository de trouver les éléments
        $repo = $this->manager->getRepository($this->entityClass);
        $data = $repo->findBy([], [], $this->limit, $offset);
        // Renvoyer les élements en question
        return $data;
    }

    public function setTemplatePath($templatePath) {
        $this->templatePath = $templatePath;
        return $this;
    }

    public function getTemplatePath() {
        return $this->templatePath;
    }

    public function setRoute($route) {
        $this->route = $route;
        return $this;
    }

    public function getRoute() {
        return $this->route;
    }

    public function setEntityClass($entityClass) {
        $this->entityClass = $entityClass;
        return $this;
    }

    public function getEntityClass() {
        return $this->entityClass;
    }

    public function setLimit($limit) {
        $this->limit = $limit;
        return $this;
    }

    public function getLimit() {
        return $this->limit;
    }

    public function setPage($page) {
        $this->currentPage = $page;
        return $this;
    }

    public function getPage() {
        return $this->currentPage;;
    }
}
