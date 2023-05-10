<?php

namespace App\Controller;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;

use App\Entity\Image;
use App\Entity\Game;
use App\Entity\Joueur;
use App\Form\GameType;
//use Doctrine\DBAL\Types\TextType;


class GameController extends AbstractController
{
    #[Route('/game', name: 'app_game')]
    public function index(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $game = new Game();
        $game->setType('test');
        $game->setTitre('testing');
        $game->setEditeur('houssem');
        $game->setNbrJoueur(6);

        $image=new Image();
        $image->setUrl('https://cdn.pixabay.com/photo/2015/10/30/10/03/gold-1013618_960_720.jpg');
        $image->setAlt('test');
        $entityManager->persist($image);
        $game->setImage($image);

        //ajout joueurs
        $joueur1=new Joueur();
        $joueur1->setNom('Houssem');
        $joueur1->setEmail('houssemwtf@gmail.com');
        $joueur1->setBornAt(new \DateTime());
        $joueur1->setScore(5);
        $joueur1->setGame($game);

        $joueur2=new Joueur();
        $joueur2->setNom('Houssem');
        $joueur2->setEmail('houssemwtf@gmail.com');
        $joueur2->setBornAt(new \DateTime());
        $joueur2->setScore(5);
        $joueur2->setGame($game);

        $joueur3=new Joueur();
        $joueur3->setNom('Houssem');
        $joueur3->setEmail('houssemwtf@gmail.com');
        $joueur3->setBornAt(new \DateTime());
        $joueur3->setScore(5);
        $joueur3->setGame($game);

        $entityManager->persist($game);
        $entityManager->persist($joueur1);
        $entityManager->persist($joueur2);
        $entityManager->persist($joueur3);
        

        $entityManager->flush();

        return $this->render('game/index.html.twig', [
            'id' => $game->getId(),
        ]);
    }

    /**
* @Route("/game/{id}", name="game_show")
*/
public function show($id,Request $request)
{
 $game = $this->getDoctrine()
 ->getRepository(Game::class)
 ->find($id);
$jou =$this->getDoctrine()->getManager();
$listJoueurs=$jou->getRepository(Joueur::class)
->findBy(['game'=>$game]);


 if (!$game) {
 throw $this->createNotFoundException(
 'No game found for id '.$id
 );
 }
 $publicPath = $request->getScheme().'://'.$request->getHttpHost().$request->getBasePath().'/uploads/games/';
        
 return $this->render('game/show.html.twig', ['listJoueurs' =>$listJoueurs,'game' =>$game,'publicPath' =>$publicPath]);
 }


 /**
* @Route("/joueur/{id}", name="joueur_show")
*/
public function show2($id)
{
 $jou = $this->getDoctrine()
 ->getRepository(Joueur::class)
 ->find($id);


 if (!$jou) {
 throw $this->createNotFoundException(
 'No player found for id '.$id
 );
 }
 return $this->render('game/show2.html.twig', [
    'joue' =>$jou
 ]);
 }


  /**
* @Route("/image/{id}", name="image_show")
*/
public function show3($id)
{
 $im = $this->getDoctrine()
 ->getRepository(Image::class)
 ->find($id);


 if (!$im) {
 throw $this->createNotFoundException(
 'No image found for id '.$id
 );
 }
 return $this->render('game/show3.html.twig', [
    'image' =>$im
 ]);
 }





#[Route('/add', name: 'Ajout_joueur')]
public function ajouter(Request $request){
    $j = new Joueur();
    $fb = $this->createFormBuilder($j)
    ->add('nom',TextType::class)
    ->add('email',TextType::class)
    ->add('bornAt',DateType::class)
    ->add('score',IntegerType::class)
    ->add('game',EntityType::class,[
        'class' => Game::class,
        'choice_label' => 'type',
    ])
    ->add('Valider',SubmitType::class);

    $form=$fb->getForm();
    $form->handleRequest($request);
    if($form->isSubmitted()){
        $jou = $this->getDoctrine()->getManager();
        $jou->persist($j);
        $jou->flush();
        return $this->redirectToRoute('home');
    }

    return $this->render('game/ajouter.html.twig',
    ['f' => $form->createView()]);

}


    /**
     * @Route("/add2", name="Ajout_game")
     */
    public function Ajout2(Request $request)
    {
        $publicPath = "uploads/games/";
        $game = new Game();
        $form = $this->createForm('App\Form\GameType', $game);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            /*
            * @var UploadedFile $image
            */
            $image = $form ->get('image')->getData();
            $em = $this->getDoctrine()->getManager();
            if ($image){
                $imageName = $game ->getTitre().'.'. $image->guessExtension();
                $image ->move($publicPath,$imageName);
                $game->setImage($imageName);
            }
            
            $em->persist($game);
            $em->flush();
            $session = new Session();
            $session->getFlashBag()->add('notice','game ajouté avec succés');
            return $this->redirectToRoute('home');
        }
        return $this->render(
            'game/ajouter2.html.twig',
            ['f' => $form->createView()]
        );
    }


    /**
     * @Route("/", name="home")
     */

     public function home(){
        $em=$this->getDoctrine()->getManager();
        $repo=$em->getRepository(Joueur::class);
        $jou=$repo->findAll();
        return $this->render('game/home.html.twig',
        ['joueurs'=>$jou]);

     }

     /**
     * @Route("/listJ", name="listJoueur")
     */

     public function list(){
        $em=$this->getDoctrine()->getManager();
        $repo=$em->getRepository(Joueur::class);
        $jou=$repo->findAll();
        return $this->render('game/home.html.twig',
        ['joueurs'=>$jou]);

     }

     /**
     * @Route("/listG", name="listGames")
     */

     public function list2(){
        $em=$this->getDoctrine()->getManager();
        $repo=$em->getRepository(Game::class);
        $jou=$repo->findAll();
        return $this->render('game/gamelist.html.twig',
        ['games'=>$jou]);

     }


  
    /**
     * @Route("/supp{id}", name="joueur_delete")
     */
    public function delete(Request $request,$id): Response
    {
        $c=$this->getDoctrine()
                ->getRepository(Joueur::class)
                ->find($id);
        if (!$c){
            throw $this->createNotFoundException(
                "no dep found for id".$id

            );
        }
        $entityManager=$this->getDoctrine()->getManager();
        $entityManager->remove($c);

        $entityManager->flush();
        return $this->redirectToRoute('home');
        
     }

/**
* @Route("/edit/{id}", name="edit_joueur")
* Method({"GET","POST"})
*/
public function edit(Request $request, $id)
{ $jou = new Joueur();
$jou = $this->getDoctrine()
->getRepository(Joueur::class)
->find($id);
if (!$jou) {
throw $this->createNotFoundException(
'No candidat found for id '.$id
);
}
$fb = $this->createFormBuilder($jou)
->add('nom',TextType::class)
    ->add('email',TextType::class)
    ->add('bornAt',DateType::class)
    ->add('score',IntegerType::class)
    ->add('game',EntityType::class,[
        'class' => Game::class,
        'choice_label' => 'type',
    ])
    ->add('Valider',SubmitType::class);
// générer le formulaire à partir du FormBuilder
$form = $fb->getForm();
$form->handleRequest($request);
if ($form->isSubmitted()) {
$entityManager = $this->getDoctrine()->getManager();
$entityManager->flush();
return $this->redirectToRoute('home');
}
return $this->render('game/ajouter.html.twig',
['f' => $form->createView()] );
}




}