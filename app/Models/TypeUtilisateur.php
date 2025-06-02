<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\CategorieUtilisateur;
use App\Models\Utilisateur; // Added import
use PDO;

/**
 * Class TypeUtilisateur
 *
 * Represents the type_utilisateur table.
 *
 * @package App\Models
 */
class TypeUtilisateur extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'type_utilisateur';

    /**
     * @var string The ID of the user type.
     */
    public string $id;

    /**
     * @var string The label of the user type.
     */
    public string $libelle;

    /**
     * @var string|null The ID of the CategorieUtilisateur this TypeUtilisateur belongs to.
     */
    public ?string $categorie_utilisateur_id;

    /**
     * TypeUtilisateur constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Gets the ID of the user type.
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Sets the ID of the user type.
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * Gets the label of the user type.
     * @return string
     */
    public function getLibelle(): string
    {
        return $this->libelle;
    }

    /**
     * Sets the label of the user type.
     * @param string $libelle
     */
    public function setLibelle(string $libelle): void
    {
        $this->libelle = $libelle;
    }

    /**
     * Gets the ID of the CategorieUtilisateur.
     * @return string|null
     */
    public function getCategorieUtilisateurId(): ?string
    {
        return $this->categorie_utilisateur_id;
    }

    /**
     * Sets the ID of the CategorieUtilisateur.
     * @param string|null $categorie_utilisateur_id
     */
    public function setCategorieUtilisateurId(?string $categorie_utilisateur_id): void
    {
        $this->categorie_utilisateur_id = $categorie_utilisateur_id;
    }

    /**
     * Gets the related CategorieUtilisateur for this TypeUtilisateur.
     *
     * @return CategorieUtilisateur|null
     */
    public function getCategorieUtilisateur(): ?CategorieUtilisateur
    {
        if (empty($this->categorie_utilisateur_id)) {
            return null;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM categorie_utilisateur WHERE id = :categorie_id");
        $stmt->bindParam(':categorie_id', $this->categorie_utilisateur_id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $categorieUtilisateur = new CategorieUtilisateur($this->pdo);
        $categorieUtilisateur->id = $data['id'];
        $categorieUtilisateur->libelle = $data['libelle'];
        // Assuming CategorieUtilisateur only has id and libelle based on previous definitions.
        // If it had more properties, they would be populated here.

        return $categorieUtilisateur;
    }

    /**
     * Gets all Utilisateur records related to this TypeUtilisateur.
     *
     * @return Utilisateur[]
     */
    public function getUtilisateurs(): array
    {
        if (empty($this->id)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE id_type_utilisateur = :type_id");
        $stmt->bindParam(':type_id', $this->id);
        $stmt->execute();
        $usersData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $users = [];
        if ($usersData) {
            foreach ($usersData as $data) {
                $user = new Utilisateur($this->pdo);
                $user->id = $data['id'];
                $user->nom_utilisateur = $data['nom_utilisateur'];
                $user->mot_de_passe = $data['mot_de_passe'];
                $user->email = $data['email'] ?? null;
                $user->date_creation_compte = $data['date_creation_compte'] ?? null;
                $user->date_derniere_connexion = $data['date_derniere_connexion'] ?? null;
                $user->type_utilisateur_id = $data['type_utilisateur_id'] ?? null;
                $user->groupe_utilisateur_id = $data['groupe_utilisateur_id'] ?? null;
                $user->niveau_acces_donnees_id = $data['niveau_acces_donnees_id'] ?? null;
                $users[] = $user;
            }
        }
        return $users;
    }
}
