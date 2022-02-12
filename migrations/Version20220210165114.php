<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220210165114 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE boucle_de_revision (id INT AUTO_INCREMENT NOT NULL, etat_des_lieux_id INT DEFAULT NULL, duree INT NOT NULL, nbre_hizb DOUBLE PRECISION NOT NULL, date_debut DATETIME NOT NULL, nombre_pages VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, INDEX IDX_4F6D95681EA7F144 (etat_des_lieux_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE etat_des_lieux (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, sourate_debut VARCHAR(255) NOT NULL, sourate_fin VARCHAR(255) NOT NULL, sourate_debut_verset_debut VARCHAR(255) NOT NULL, sourate_debut_verset_fin VARCHAR(255) NOT NULL, sourate_fin_verset_debut VARCHAR(255) NOT NULL, sourate_fin_verset_fin VARCHAR(255) NOT NULL, jours_de_memo INT NOT NULL, jours_de_debut DATETIME NOT NULL, envoie_mail TINYINT(1) NOT NULL, sourate_supp JSON DEFAULT NULL, INDEX IDX_F7210312A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jours_de_boucle (id INT AUTO_INCREMENT NOT NULL, boucle_de_revision_id INT DEFAULT NULL, date DATETIME NOT NULL, page_debut VARCHAR(255) NOT NULL, page_fin VARCHAR(255) NOT NULL, nombre_page VARCHAR(255) NOT NULL, jours VARCHAR(255) NOT NULL, sourate_debut_boucle_journaliere VARCHAR(255) NOT NULL, sourate_fin_boucle_journaliere VARCHAR(255) NOT NULL, INDEX IDX_987904491FB03C5D (boucle_de_revision_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sourate (id INT AUTO_INCREMENT NOT NULL, etat_des_lieux_id INT DEFAULT NULL, arabic VARCHAR(255) NOT NULL, latin VARCHAR(255) NOT NULL, english VARCHAR(255) NOT NULL, localtion VARCHAR(255) NOT NULL, sajda VARCHAR(255) NOT NULL, ayah INT NOT NULL, INDEX IDX_76D427DD1EA7F144 (etat_des_lieux_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, pays VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE verset (id INT AUTO_INCREMENT NOT NULL, sourate_id INT DEFAULT NULL, numero VARCHAR(255) NOT NULL, juzz VARCHAR(255) NOT NULL, hizb VARCHAR(255) NOT NULL, quart_hizb VARCHAR(255) NOT NULL, INDEX IDX_E508239BB86BC78D (sourate_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE boucle_de_revision ADD CONSTRAINT FK_4F6D95681EA7F144 FOREIGN KEY (etat_des_lieux_id) REFERENCES etat_des_lieux (id)');
        $this->addSql('ALTER TABLE etat_des_lieux ADD CONSTRAINT FK_F7210312A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE jours_de_boucle ADD CONSTRAINT FK_987904491FB03C5D FOREIGN KEY (boucle_de_revision_id) REFERENCES boucle_de_revision (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE sourate ADD CONSTRAINT FK_76D427DD1EA7F144 FOREIGN KEY (etat_des_lieux_id) REFERENCES etat_des_lieux (id)');
        $this->addSql('ALTER TABLE verset ADD CONSTRAINT FK_E508239BB86BC78D FOREIGN KEY (sourate_id) REFERENCES sourate (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE jours_de_boucle DROP FOREIGN KEY FK_987904491FB03C5D');
        $this->addSql('ALTER TABLE boucle_de_revision DROP FOREIGN KEY FK_4F6D95681EA7F144');
        $this->addSql('ALTER TABLE sourate DROP FOREIGN KEY FK_76D427DD1EA7F144');
        $this->addSql('ALTER TABLE verset DROP FOREIGN KEY FK_E508239BB86BC78D');
        $this->addSql('ALTER TABLE etat_des_lieux DROP FOREIGN KEY FK_F7210312A76ED395');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE boucle_de_revision');
        $this->addSql('DROP TABLE etat_des_lieux');
        $this->addSql('DROP TABLE jours_de_boucle');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE sourate');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE verset');
    }
}
