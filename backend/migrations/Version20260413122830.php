<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260413122830 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE conversacion_chat (id INT AUTO_INCREMENT NOT NULL, fecha_inicio DATETIME NOT NULL, usuario_id INT NOT NULL, INDEX IDX_7A43BE0EDB38439E (usuario_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE likes (id INT AUTO_INCREMENT NOT NULL, fecha DATETIME NOT NULL, usuario_id INT NOT NULL, publicacion_id INT NOT NULL, INDEX IDX_49CA4E7DDB38439E (usuario_id), INDEX IDX_49CA4E7D9ACBB5E7 (publicacion_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE mapa_usuario (id INT AUTO_INCREMENT NOT NULL, estilo_color VARCHAR(50) DEFAULT NULL, ultima_actualizacion DATETIME NOT NULL, usuario_id INT NOT NULL, UNIQUE INDEX UNIQ_D22C7C47DB38439E (usuario_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE mensaje_chat (id INT AUTO_INCREMENT NOT NULL, rol VARCHAR(20) NOT NULL, contenido LONGTEXT NOT NULL, fecha_envio DATETIME NOT NULL, conversacion_chat_id INT NOT NULL, INDEX IDX_7F8F02EBF94C7B5C (conversacion_chat_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE pais (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(100) NOT NULL, continente VARCHAR(50) NOT NULL, codigo_iso VARCHAR(3) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE publicacion (id INT AUTO_INCREMENT NOT NULL, descripcion LONGTEXT DEFAULT NULL, imagen_url VARCHAR(255) DEFAULT NULL, fecha_publicacion DATETIME NOT NULL, usuario_id INT NOT NULL, pais_id INT NOT NULL, INDEX IDX_62F2085FDB38439E (usuario_id), INDEX IDX_62F2085FC604D5C6 (pais_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE resena (id INT AUTO_INCREMENT NOT NULL, titulo VARCHAR(150) NOT NULL, contenido LONGTEXT NOT NULL, puntuacion INT NOT NULL, fecha_resena DATETIME NOT NULL, usuario_id INT NOT NULL, pais_id INT NOT NULL, INDEX IDX_50A7E40ADB38439E (usuario_id), INDEX IDX_50A7E40AC604D5C6 (pais_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE usuario (id INT AUTO_INCREMENT NOT NULL, nombre_usuario VARCHAR(50) NOT NULL, correo_electronico VARCHAR(100) NOT NULL, contraseña_hash VARCHAR(255) NOT NULL, biografia LONGTEXT DEFAULT NULL, fecha_registro DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE visita_pais (id INT AUTO_INCREMENT NOT NULL, fecha_visita DATE NOT NULL, usuario_id INT NOT NULL, pais_id INT NOT NULL, INDEX IDX_68BDC358DB38439E (usuario_id), INDEX IDX_68BDC358C604D5C6 (pais_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE conversacion_chat ADD CONSTRAINT FK_7A43BE0EDB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT FK_49CA4E7DDB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT FK_49CA4E7D9ACBB5E7 FOREIGN KEY (publicacion_id) REFERENCES publicacion (id)');
        $this->addSql('ALTER TABLE mapa_usuario ADD CONSTRAINT FK_D22C7C47DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE mensaje_chat ADD CONSTRAINT FK_7F8F02EBF94C7B5C FOREIGN KEY (conversacion_chat_id) REFERENCES conversacion_chat (id)');
        $this->addSql('ALTER TABLE publicacion ADD CONSTRAINT FK_62F2085FDB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE publicacion ADD CONSTRAINT FK_62F2085FC604D5C6 FOREIGN KEY (pais_id) REFERENCES pais (id)');
        $this->addSql('ALTER TABLE resena ADD CONSTRAINT FK_50A7E40ADB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE resena ADD CONSTRAINT FK_50A7E40AC604D5C6 FOREIGN KEY (pais_id) REFERENCES pais (id)');
        $this->addSql('ALTER TABLE visita_pais ADD CONSTRAINT FK_68BDC358DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE visita_pais ADD CONSTRAINT FK_68BDC358C604D5C6 FOREIGN KEY (pais_id) REFERENCES pais (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE conversacion_chat DROP FOREIGN KEY FK_7A43BE0EDB38439E');
        $this->addSql('ALTER TABLE likes DROP FOREIGN KEY FK_49CA4E7DDB38439E');
        $this->addSql('ALTER TABLE likes DROP FOREIGN KEY FK_49CA4E7D9ACBB5E7');
        $this->addSql('ALTER TABLE mapa_usuario DROP FOREIGN KEY FK_D22C7C47DB38439E');
        $this->addSql('ALTER TABLE mensaje_chat DROP FOREIGN KEY FK_7F8F02EBF94C7B5C');
        $this->addSql('ALTER TABLE publicacion DROP FOREIGN KEY FK_62F2085FDB38439E');
        $this->addSql('ALTER TABLE publicacion DROP FOREIGN KEY FK_62F2085FC604D5C6');
        $this->addSql('ALTER TABLE resena DROP FOREIGN KEY FK_50A7E40ADB38439E');
        $this->addSql('ALTER TABLE resena DROP FOREIGN KEY FK_50A7E40AC604D5C6');
        $this->addSql('ALTER TABLE visita_pais DROP FOREIGN KEY FK_68BDC358DB38439E');
        $this->addSql('ALTER TABLE visita_pais DROP FOREIGN KEY FK_68BDC358C604D5C6');
        $this->addSql('DROP TABLE conversacion_chat');
        $this->addSql('DROP TABLE likes');
        $this->addSql('DROP TABLE mapa_usuario');
        $this->addSql('DROP TABLE mensaje_chat');
        $this->addSql('DROP TABLE pais');
        $this->addSql('DROP TABLE publicacion');
        $this->addSql('DROP TABLE resena');
        $this->addSql('DROP TABLE usuario');
        $this->addSql('DROP TABLE visita_pais');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
