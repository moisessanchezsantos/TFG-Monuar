<?php

namespace App\DataFixtures;

use App\Entity\Pais;
use App\Entity\Usuario;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $usuario1 = new Usuario();
        $usuario1->setNombreUsuario('moises');
        $usuario1->setCorreoElectronico('moises@email.com');
        $usuario1->setContraseñaHash('hash123');
        $usuario1->setBiografia('Me encanta viajar');
        $usuario1->setFechaRegistro(new \DateTimeImmutable());
        $manager->persist($usuario1);

        $usuario2 = new Usuario();
        $usuario2->setNombreUsuario('admin');
        $usuario2->setCorreoElectronico('admin@email.com');
        $usuario2->setContraseñaHash('hash456');
        $usuario2->setBiografia('Administrador');
        $usuario2->setFechaRegistro(new \DateTimeImmutable());
        $manager->persist($usuario2);

        $pais1 = new Pais();
        $pais1->setNombre('España');
        $pais1->setContinente('Europa');
        $pais1->setCodigoIso('ESP');
        $manager->persist($pais1);

        $pais2 = new Pais();
        $pais2->setNombre('Japón');
        $pais2->setContinente('Asia');
        $pais2->setCodigoIso('JPN');
        $manager->persist($pais2);

        $pais3 = new Pais();
        $pais3->setNombre('México');
        $pais3->setContinente('América');
        $pais3->setCodigoIso('MEX');
        $manager->persist($pais3);

        $mapa1 = new \App\Entity\MapaUsuario();
        $mapa1->setEstiloColor('azul');
        $mapa1->setUltimaActualizacion(new \DateTimeImmutable());
        $mapa1->setUsuario($usuario1);
        $manager->persist($mapa1);

        $mapa2 = new \App\Entity\MapaUsuario();
        $mapa2->setEstiloColor('verde');
        $mapa2->setUltimaActualizacion(new \DateTimeImmutable());
        $mapa2->setUsuario($usuario2);
        $manager->persist($mapa2);

        $visita1 = new \App\Entity\VisitaPais();
        $visita1->setFechaVisita(new \DateTimeImmutable('2025-04-10'));
        $visita1->setUsuario($usuario1);
        $visita1->setPais($pais1);
        $manager->persist($visita1);

        $visita2 = new \App\Entity\VisitaPais();
        $visita2->setFechaVisita(new \DateTimeImmutable('2025-04-11'));
        $visita2->setUsuario($usuario1);
        $visita2->setPais($pais2);
        $manager->persist($visita2);

        $visita3 = new \App\Entity\VisitaPais();
        $visita3->setFechaVisita(new \DateTimeImmutable('2025-04-12'));
        $visita3->setUsuario($usuario2);
        $visita3->setPais($pais3);
        $manager->persist($visita3);

        $manager->flush();
    }
}