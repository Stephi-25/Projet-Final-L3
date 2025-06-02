<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\Traitement;
use App\Models\Utilisateur;
use PDO;

/**
 * Class AccesTraitement
 *
 * Represents the acces_traitement table.
 * Composite PK: (traitement_id, utilisateur_id).
 *
 * @package App\Models
 */
class AccesTraitement extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'acces_traitement';

    /**
     * @var string The ID of the traitement (FK to traitement.id, part of CPK).
     */
    public string $traitement_id;

    /**
     * @var string The ID of the utilisateur (FK to utilisateur.id, part of CPK).
     */
    public string $utilisateur_id;

    /**
     * @var string|null The date of access.
     */
    public ?string $date_accesion; // DDL specifies DATE

    /**
     * @var string|null The time of access.
     */
    public ?string $heure_accesion; // DDL specifies TIME

    /**
     * AccesTraitement constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    // Getters and Setters

    public function getTraitementId(): string
    {
        return $this->traitement_id;
    }

    public function setTraitementId(string $traitement_id): void
    {
        $this->traitement_id = $traitement_id;
    }

    public function getUtilisateurId(): string
    {
        return $this->utilisateur_id;
    }

    public function setUtilisateurId(string $utilisateur_id): void
    {
        $this->utilisateur_id = $utilisateur_id;
    }

    public function getDateAccesion(): ?string
    {
        return $this->date_accesion;
    }

    public function setDateAccesion(?string $date_accesion): void
    {
        $this->date_accesion = $date_accesion;
    }

    public function getHeureAccesion(): ?string
    {
        return $this->heure_accesion;
    }

    public function setHeureAccesion(?string $heure_accesion): void
    {
        $this->heure_accesion = $heure_accesion;
    }

    // Relationship methods

    /**
     * Gets the related Traitement.
     * @return Traitement|null
     */
    public function getTraitement(): ?Traitement
    {
        if (empty($this->traitement_id)) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM traitement WHERE id = :id");
        $stmt->bindParam(':id', $this->traitement_id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $traitement = new Traitement($this->pdo);
        $traitement->id = $data['id'];
        $traitement->libelle = $data['libelle'];
        // $traitement->description = $data['description'] ?? null; // Traitement DDL not specified, assume lean
        return $traitement;
    }

    /**
     * Gets the related Utilisateur.
     * @return Utilisateur|null
     */
    public function getUtilisateur(): ?Utilisateur
    {
        if (empty($this->utilisateur_id)) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE id = :id");
        $stmt->bindParam(':id', $this->utilisateur_id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $utilisateur = new Utilisateur($this->pdo);
        // Populate Utilisateur based on its defined (corrected) properties
        $utilisateur->id = $data['id'];
        $utilisateur->nom_utilisateur = $data['nom_utilisateur'];
        $utilisateur->mot_de_passe = $data['mot_de_passe'];
        $utilisateur->email = $data['email'] ?? null;
        $utilisateur->date_creation_compte = $data['date_creation_compte'] ?? null;
        $utilisateur->date_derniere_connexion = $data['date_derniere_connexion'] ?? null;
        $utilisateur->type_utilisateur_id = $data['type_utilisateur_id'] ?? null;
        $utilisateur->groupe_utilisateur_id = $data['groupe_utilisateur_id'] ?? null;
        $utilisateur->niveau_acces_donnees_id = $data['niveau_acces_donnees_id'] ?? null;
        return $utilisateur;
    }
}
